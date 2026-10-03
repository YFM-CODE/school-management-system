<?php

namespace App\Http\Requests;

use App\Enums\StatusPersetujuanTarif;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanPersetujuanTarifRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Mengandalkan middleware role di routes/web.php
    }

    public function rules(): array
    {
        return [
            'nomor_berita_acara' => ['required', 'string', 'max:100'],
            'tanggal_kesepakatan' => ['required', 'date'],
            'status_persetujuan' => ['required', Rule::enum(StatusPersetujuanTarif::class)],
            'dokumen_ba_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'catatan_komite' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_berita_acara.required' => 'Nomor Berita Acara (BA) wajib dicantumkan.',
            'nomor_berita_acara.max' => 'Nomor Berita Acara maksimal 100 karakter.',
            'tanggal_kesepakatan.required' => 'Tanggal kesepakatan rapat wajib dipilih.',
            'tanggal_kesepakatan.date' => 'Format tanggal kesepakatan tidak valid.',
            'status_persetujuan.required' => 'Keputusan persetujuan wajib ditentukan.',
            'dokumen_ba_pdf.file' => 'Dokumen Berita Acara harus berupa file.',
            'dokumen_ba_pdf.mimes' => 'Dokumen Berita Acara wajib berformat PDF.',
            'dokumen_ba_pdf.max' => 'Ukuran dokumen Berita Acara maksimal 5 MB.',
            'catatan_komite.max' => 'Catatan komite maksimal 1000 karakter.',
        ];
    }
}
