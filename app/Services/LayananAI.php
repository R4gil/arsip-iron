<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LayananAI
{
    protected string $provider;
    protected array $config;

    public function __construct()
    {
        $this->provider = config('ai.provider', 'openai');
        $this->config = $this->getConfig();
    }

    /**
     * Kirim prompt ke AI dan dapatkan jawaban
     *
     * @param string $prompt
     * @return string|null
     */
    public function kirimPrompt(string $prompt): ?string
    {
        Log::info('Mengirim prompt ke AI', ['provider' => $this->provider, 'prompt_length' => strlen($prompt)]);
        
        try {
            return match ($this->provider) {
                'openai' => $this->kirimKeOpenAI($prompt),
                'gemini' => $this->kirimKeGemini($prompt),
                'ollama' => $this->kirimKeOllama($prompt),
                default => $this->kirimKeOpenAI($prompt),
            };
        } catch (\Exception $e) {
            Log::error('Gagal mengirim prompt ke AI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Kirim prompt ke OpenAI
     *
     * @param string $prompt
     * @return string|null
     */
    private function kirimKeOpenAI(string $prompt): ?string
    {
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            Log::warning('OPENAI_API_KEY tidak dikonfigurasi');
            return null;
        }

        $model = env('OPENAI_MODEL', 'gpt-3.5-turbo');
        $maxTokens = env('OPENAI_MAX_TOKENS', 1000);
        $temperature = env('OPENAI_TEMPERATURE', 0.7);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Anda adalah asisten AI yang membantu menjawab pertanyaan berdasarkan konteks arsip yang diberikan. Jawab dalam bahasa Indonesia yang formal dan profesional.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => $maxTokens,
                'temperature' => $temperature,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::error('OpenAI API error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Exception saat mengirim ke OpenAI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Kirim prompt ke Gemini (placeholder untuk implementasi masa depan)
     *
     * @param string $prompt
     * @return string|null
     */
    private function kirimKeGemini(string $prompt): ?string
    {
        // Placeholder untuk integrasi Gemini
        Log::info('Integrasi Gemini belum diimplementasikan');
        return null;
    }

    /**
     * Kirim prompt ke Ollama
     *
     * @param string $prompt
     * @return string|null
     */
    private function kirimKeOllama(string $prompt): ?string
    {
        $startTime = microtime(true);
        $baseUrl = config('ai.ollama.base_url', 'http://127.0.0.1:11434');
        $model = config('ai.ollama.model', 'gemma3:4b');
        $maxTokens = config('ai.ollama.max_tokens', 2000);
        $temperature = config('ai.ollama.temperature', 0.3);

        $systemPrompt = "Kamu adalah Arsi, asisten AI pada aplikasi IRON SMART. Selalu jawab menggunakan Bahasa Indonesia yang alami, ramah, dan profesional. Jangan menggunakan bahasa Inggris kecuali pengguna memintanya. Jawablah seperti rekan kerja yang membantu mencari informasi arsip atau menjawab pertanyaan umum.

Jika pertanyaan terkait arsip dan informasi tersedia dari konteks arsip yang diberikan, gunakan informasi tersebut sebagai sumber utama jawaban Anda. Jika informasi tidak ditemukan dalam arsip atau pertanyaan bersifat umum, jawab berdasarkan pengetahuan umum Anda dengan sopan.

PENTING:
- Jangan menolak pertanyaan hanya karena tidak ada arsip yang relevan
- Jadikan diri Anda sebagai asisten digital yang membantu berbagai pertanyaan
- Jangan menggunakan frasa seperti berdasarkan konteks yang diberikan, berdasarkan arsip di atas, atau frasa serupa
- Jawab langsung dan alami tanpa menyebutkan sumber di awal jawaban
- Jika menggunakan arsip, tampilkan referensi di akhir jawaban dalam format: Referensi: [Nama Arsip - Nomor Surat]";

        Log::info('Mengirim ke Ollama', [
            'url' => $baseUrl,
            'model' => $model,
            'prompt_length' => strlen($prompt),
            'system_prompt_length' => strlen($systemPrompt),
        ]);

        try {
            $requestStart = microtime(true);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(120)->post("{$baseUrl}/api/chat", [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'stream' => false,
                'options' => [
                    'num_predict' => (int) $maxTokens,
                    'temperature' => (float) $temperature,
                ]
            ]);
            $requestTime = round((microtime(true) - $requestStart) * 1000, 2);

            Log::info('Ollama response received', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'content_type' => $response->header('Content-Type'),
                'request_time_ms' => $requestTime,
            ]);

            // Validasi response sebelum parsing JSON
            $body = $response->body();
            $contentType = $response->header('Content-Type', '');
            
            // Log 500 karakter pertama untuk debugging
            Log::info('Ollama response body preview', [
                'body_preview' => substr($body, 0, 500),
                'body_length' => strlen($body),
            ]);

            // Cek jika response adalah HTML (bukan JSON)
            if (strpos($body, '<!DOCTYPE') !== false || strpos($body, '<html') !== false) {
                Log::error('Ollama returned HTML instead of JSON', [
                    'status' => $response->status(),
                    'content_type' => $contentType,
                    'body_preview' => substr($body, 0, 500),
                    'total_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ]);
                return null;
            }

            // Cek Content-Type
            if (strpos($contentType, 'application/json') === false && strpos($contentType, 'text/json') === false) {
                Log::warning('Ollama response Content-Type is not JSON', [
                    'content_type' => $contentType,
                    'body_preview' => substr($body, 0, 500),
                ]);
            }

            if ($response->successful()) {
                $parseStart = microtime(true);
                
                // Cek jika body valid JSON sebelum parsing
                json_decode($body);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('Ollama response is not valid JSON', [
                        'json_error' => json_last_error_msg(),
                        'body_preview' => substr($body, 0, 500),
                        'parse_time_ms' => round((microtime(true) - $parseStart) * 1000, 2),
                    ]);
                    return null;
                }
                
                $data = $response->json();
                $content = $data['message']['content'] ?? null;
                $parseTime = round((microtime(true) - $parseStart) * 1000, 2);
                
                if ($content) {
                    $totalTime = round((microtime(true) - $startTime) * 1000, 2);
                    Log::info('Ollama success', [
                        'content_length' => strlen($content),
                        'parse_time_ms' => $parseTime,
                        'total_time_ms' => $totalTime,
                    ]);
                    return $content;
                } else {
                    Log::error('Ollama response missing content', ['response' => $data]);
                    return null;
                }
            }

            $errorBody = $response->body();
            Log::error('Ollama API error', [
                'status' => $response->status(),
                'content_type' => $contentType,
                'body' => substr($errorBody, 0, 500),
                'total_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ]);
            return null;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Ollama connection error', [
                'message' => $e->getMessage(),
                'total_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ]);
            return null;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Ollama request error', [
                'message' => $e->getMessage(),
                'total_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Exception saat mengirim ke Ollama', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'total_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ]);
            return null;
        }
    }

    /**
     * Dapatkan konfigurasi berdasarkan provider
     *
     * @return array
     */
    private function getConfig(): array
    {
        return match ($this->provider) {
            'openai' => [
                'api_key' => env('OPENAI_API_KEY'),
                'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
                'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
                'temperature' => env('OPENAI_TEMPERATURE', 0.7),
            ],
            'gemini' => [
                'api_key' => env('GEMINI_API_KEY'),
                'model' => env('GEMINI_MODEL', 'gemini-pro'),
            ],
            'ollama' => [
                'base_url' => config('ai.ollama.base_url', 'http://127.0.0.1:11434'),
                'model' => config('ai.ollama.model', 'gemma3:4b'),
                'max_tokens' => config('ai.ollama.max_tokens', 2000),
                'temperature' => config('ai.ollama.temperature', 0.3),
            ],
            default => [],
        };
    }

    /**
     * Cek apakah AI tersedia dan terkonfigurasi
     *
     * @return bool
     */
    public function cekKetersediaan(): bool
    {
        return match ($this->provider) {
            'openai' => !empty(config('ai.openai.api_key')),
            'gemini' => !empty(config('ai.gemini.api_key')),
            'ollama' => !empty(config('ai.ollama.base_url')),
            default => false,
        };
    }

    /**
     * Dapatkan nama provider yang aktif
     *
     * @return string
     */
    public function dapatkanProvider(): string
    {
        return $this->provider;
    }

    /**
     * Ganti provider AI
     *
     * @param string $provider
     * @return void
     */
    public function gantiProvider(string $provider): void
    {
        $this->provider = $provider;
        $this->config = $this->getConfig();
    }
}
