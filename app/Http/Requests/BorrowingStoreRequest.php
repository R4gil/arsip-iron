<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BorrowingStoreRequest extends FormRequest
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
                'exists:archives,id',
                Rule::unique('borrowings')->where(function ($query) {
                    return $query->where('archive_id', $this->archive_id)
                        ->where('status', 'dipinjam');
                })->ignore($this->route('borrowing')),
            ],
            'nip' => 'nullable|string|max:100',
            'unit_kerja' => 'nullable|string|max:191',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'keterangan' => 'nullable|string',
        ];
    }
}
