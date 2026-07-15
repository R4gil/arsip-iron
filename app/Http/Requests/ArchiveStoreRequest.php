<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArchiveStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_surat' => 'required|string|max:191|unique:arsip,nomor_surat',
            'nama_arsip' => 'required|string|max:191',
            'jenis_arsip_id' => 'required|exists:jenis_arsip,id',
            'lokasi_id' => 'required|exists:lokasi_simpan,id',
            'tahun_arsip' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'tanggal_arsip' => 'required|date',
            'status' => 'required|in:Aktif,Inaktif',
            'status_ketersediaan' => 'required|in:Tersedia,Dipinjam',
            'perihal_surat' => 'nullable|string',
            'file_arsip' => 'nullable|file',
        ];
    }
}
