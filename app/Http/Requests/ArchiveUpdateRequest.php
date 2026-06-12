<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArchiveUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $archiveId = $this->route('archive')->id ?? null;

        return [
            'nomor_arsip' => ['required', 'string', 'max:191', Rule::unique('archives', 'nomor_arsip')->ignore($archiveId)],
            'nama_arsip' => 'required|string|max:191',
            'uraian' => 'nullable|string',
            'classification_id' => 'required|exists:classifications,id',
            'location_id' => 'required|exists:locations,id',
            'cabinet_id' => 'required|exists:cabinets,id',
            'rack_id' => 'required|exists:racks,id',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'tanggal_arsip' => 'required|date',
            'status' => 'required|in:tersedia,dipinjam,inaktif',
        ];
    }
}
