<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PeminjamanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'archive_id' => [
                'required',
                'exists:arsip,id',
                Rule::unique('peminjaman_arsip')->where(function ($query) {
                    return $query->where('arsip_id', $this->archive_id)
                        ->where('status_pinjam', 'Dipinjam');
                })->ignore($this->route('peminjaman')),
            ],
            'nip' => 'nullable|string|max:100',
            'unit_kerja' => 'nullable|string|max:191',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'keterangan' => 'nullable|string',
        ];
    }
}
