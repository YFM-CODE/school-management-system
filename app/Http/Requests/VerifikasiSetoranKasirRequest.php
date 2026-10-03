<?php

namespace App\Http\Requests;

use App\Enums\StatusSetoranKasir;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifikasiSetoranKasirRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Mengandalkan middleware role di routes/web.php
    }

    public function rules(): array
    {
        return [
            'total_fisik' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(StatusSetoranKasir::class)],
            'catatan_bendahara' => [
                'nullable',
                'string',
                'max:500',
                Rule::requiredIf(fn () => $this->input('status') === StatusSetoranKasir::DITOLAK->value),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'total_fisik.required' => 'Jumlah fisik uang tunai wajib dihitung dan diisi.',
            'total_fisik.numeric' => 'Jumlah fisik uang tunai harus berupa angka.',
            'total_fisik.min' => 'Jumlah fisik uang tunai tidak boleh minus.',
            'status.required' => 'Status verifikasi setoran wajib ditentukan.',
            'catatan_bendahara.required' => 'Catatan wajib diberikan jika setoran ditolak karena selisih uang fisik.',
            'catatan_bendahara.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
