<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use Smalot\PdfParser\Parser as PdfParser;
use thiagoalessio\TesseractOCR\TesseractOCR;

class LayananEkstraksiDokumen
{
    /**
     * Ekstrak isi dokumen dari berbagai format file
     *
     * @param string $filePath Path file di storage
     * @param string $originalName Nama asli file
     * @return string|null Isi dokumen yang diekstrak
     */
    public function ekstrakIsiDokumen(string $filePath, string $originalName): ?string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        \Log::info('[Ekstraksi] Mulai ekstraksi dokumen', [
            'file' => $filePath,
            'original_name' => $originalName,
            'ekstensi' => $extension,
        ]);

        try {
            $result = match ($extension) {
                'pdf' => $this->ekstrakPDF($filePath),
                'docx', 'doc' => $this->ekstrakDOCX($filePath),
                'jpg', 'jpeg', 'png', 'bmp', 'tiff' => $this->ekstrakGambar($filePath),
                default => null,
            };

            if ($result === null) {
                \Log::warning('[Ekstraksi] Hasil ekstraksi null — text_content tidak akan tersimpan', [
                    'file' => $filePath,
                    'ekstensi' => $extension,
                    'kemungkinan_penyebab' => match($extension) {
                        'pdf' => 'PDF scan tanpa teks digital dan OCR tidak tersedia/gagal',
                        'docx', 'doc' => 'File DOCX tidak bisa dibaca atau kosong',
                        default => 'Format tidak didukung: ' . $extension,
                    },
                ]);
            } else {
                \Log::info('[Ekstraksi] Ekstraksi berhasil', [
                    'file' => $filePath,
                    'jumlah_karakter' => strlen($result),
                    'preview_100_char' => substr($result, 0, 100),
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('[Ekstraksi] Exception: ' . $e->getMessage(), [
                'file' => $filePath,
                'original_name' => $originalName,
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Ekstrak teks dari file PDF menggunakan smalot/pdfparser (pure PHP)
     * Jika PDF adalah hasil scan (tidak ada teks), fallback ke OCR via Tesseract
     *
     * @param string $filePath
     * @return string|null
     */
    private function ekstrakPDF(string $filePath): ?string
    {
        $fullPath = Storage::disk('nas_storage')->path('arsip_dokumen/' . $filePath);
        
        if (!file_exists($fullPath)) {
            \Log::warning('File PDF tidak ditemukan: ' . $fullPath);
            return null;
        }

        // Step 1: Coba ekstrak teks digital langsung
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();

            if (!empty(trim($text))) {
                \Log::info('[Ekstraksi] PDF teks digital berhasil', [
                    'file' => $filePath,
                    'jumlah_karakter' => strlen($text),
                ]);
                return $this->bersihkanTeks($text);
            }
            \Log::info('[Ekstraksi] PDF tidak mengandung teks digital, coba OCR', ['file' => $filePath]);
        } catch (\Exception $e) {
            \Log::warning('[Ekstraksi] Gagal parse PDF dengan smalot: ' . $e->getMessage(), ['file' => $filePath]);
        }

        // Step 2: Fallback ke OCR menggunakan Tesseract
        \Log::info('[Ekstraksi] Fallback ke OCR untuk PDF scan', ['file' => $filePath]);
        return $this->ekstrakOCR($fullPath);
    }

    /**
     * Ekstrak teks dari file gambar/scan menggunakan Tesseract OCR
     * Jika file adalah PDF, convert ke gambar terlebih dahulu
     *
     * @param string $fullPath Path lengkap ke file
     * @return string|null
     */
    private function ekstrakOCR(string $fullPath): ?string
    {
        try {
            $tesseractPath = config('ai.tesseract_path', 'C:\Program Files\Tesseract-OCR\tesseract.exe');

            if (!file_exists($tesseractPath)) {
                \Log::warning('[Ekstraksi] Tesseract tidak ditemukan di: ' . $tesseractPath);
                return null;
            }

            // Jika file adalah PDF, convert ke gambar terlebih dahulu
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            if ($extension === 'pdf') {
                $imagePath = $this->convertPdfToImage($fullPath);
                if (!$imagePath) {
                    return null;
                }
                $ocrPath = $imagePath;
            } else {
                $ocrPath = $fullPath;
            }

            $tesseract = new TesseractOCR($ocrPath);
            $tesseract->executable($tesseractPath);
            
            // Set bahasa Inggris
            $tesseract->lang('eng');
            
            // OCR options
            $tesseract->psm(3); // Automatic page segmentation, but no OSD
            
            $text = $tesseract->run();
            
            // Cleanup temporary image jika ada
            if (isset($imagePath) && file_exists($imagePath)) {
                @unlink($imagePath);
            }
            
            if (empty(trim($text))) {
                \Log::warning('[Ekstraksi] OCR tidak menghasilkan teks', ['file' => $fullPath]);
                return null;
            }

            \Log::info('[Ekstraksi] OCR berhasil', ['jumlah_karakter' => strlen($text)]);
            return $this->bersihkanTeks($text);
        } catch (\Exception $e) {
            \Log::error('[Ekstraksi] Gagal OCR: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Convert PDF ke gambar PNG menggunakan ImageMagick
     *
     * @param string $pdfPath Path ke file PDF
     * @return string|null Path ke file gambar temporary
     */
    private function convertPdfToImage(string $pdfPath): ?string
    {
        try {
            // Cari ImageMagick convert command
            $convertPath = $this->cariImageMagick();
            if (!$convertPath) {
                \Log::warning('ImageMagick tidak ditemukan, tidak bisa convert PDF ke gambar');
                return null;
            }

            // Copy PDF ke temp lokal dulu untuk menghindari masalah path jaringan
            $tempDir = sys_get_temp_dir();
            $localPdf = $tempDir . DIRECTORY_SEPARATOR . 'ocr_input_' . uniqid() . '.pdf';
            if (!copy($pdfPath, $localPdf)) {
                \Log::warning('Gagal copy PDF ke temp lokal: ' . $pdfPath);
                return null;
            }

            // Buat temporary output path
            $outputName = 'ocr_output_' . uniqid() . '.png';
            $outputPath = $tempDir . DIRECTORY_SEPARATOR . $outputName;

            // Convert PDF page 1 ke PNG
            // Format: magick -density 300 input.pdf[0] output.png
            $command = sprintf(
                '"%s" -density 300 "%s[0]" "%s" 2>&1',
                $convertPath,
                $localPdf,
                $outputPath
            );

            exec($command, $output, $returnCode);
            
            // Cleanup input PDF lokal
            @unlink($localPdf);
            
            if ($returnCode !== 0 || !file_exists($outputPath)) {
                \Log::warning('Gagal convert PDF ke gambar: ' . implode("\n", $output));
                return null;
            }

            \Log::info('PDF berhasil diconvert ke gambar: ' . $outputPath);
            return $outputPath;
        } catch (\Exception $e) {
            \Log::error('Gagal convert PDF: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cari path ImageMagick convert command
     *
     * @return string|null
     */
    private function cariImageMagick(): ?string
    {
        // Cek beberapa kemungkinan path
        $possiblePaths = [
            'magick', // ImageMagick 7+
            'convert', // ImageMagick 6 atau Windows built-in
            'C:\Program Files\ImageMagick-7.1.2-Q16-HDRI\magick.exe',
            'C:\Program Files\ImageMagick-7.1.2-Q16\magick.exe',
            'C:\Program Files\ImageMagick-7.1.2-Q8\magick.exe',
        ];

        foreach ($possiblePaths as $path) {
            if ($this->cekCommandTersedia($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Cek apakah command tersedia di sistem
     *
     * @param string $command
     * @return bool
     */
    private function cekCommandTersedia(string $command): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $output = shell_exec('where ' . escapeshellarg($command) . ' 2>nul');
        } else {
            $output = shell_exec('which ' . escapeshellarg($command) . ' 2>&1');
        }
        return !empty($output);
    }

    /**
     * Ekstrak teks dari file DOCX
     *
     * @param string $filePath
     * @return string|null
     */
    private function ekstrakDOCX(string $filePath): ?string
    {
        try {
            $fullPath = Storage::disk('nas_storage')->path('arsip_dokumen/' . $filePath);
            
            if (!file_exists($fullPath)) {
                return null;
            }

            $phpWord = PhpWord::load($fullPath);
            $text = '';
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            
            return $this->bersihkanTeks($text);
        } catch (\Exception $e) {
            \Log::error('Gagal mengekstrak DOCX: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ekstrak teks dari gambar menggunakan Tesseract OCR
     *
     * @param string $filePath
     * @return string|null
     */
    private function ekstrakGambar(string $filePath): ?string
    {
        $fullPath = Storage::disk('nas_storage')->path('arsip_dokumen/' . $filePath);
        
        if (!file_exists($fullPath)) {
            \Log::warning('File gambar tidak ditemukan: ' . $fullPath);
            return null;
        }

        return $this->ekstrakOCR($fullPath);
    }

    /**
     * Bersihkan teks yang diekstrak
     *
     * @param string $text
     * @return string
     */
    private function bersihkanTeks(string $text): string
    {
        // Normalisasi line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Hapus karakter whitespace berlebih (tapi jaga newline untuk paragraf)
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Hapus karakter kontrol (kecuali newline dan tab)
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Trim whitespace
        return trim($text);
    }

    /**
     * Ekstrak ringkasan dokumen (excerpt)
     *
     * @param string $text
     * @param int $panjang
     * @return string
     */
    public function ekstrakRingkasan(string $text, int $panjang = 500): string
    {
        if (empty($text)) {
            return '';
        }

        if (strlen($text) <= $panjang) {
            return $text;
        }

        return substr($text, 0, $panjang) . '...';
    }
}
