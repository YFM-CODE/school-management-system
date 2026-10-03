<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanPengajuanDispensasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Mengandalkan middleware role di routes/web.php
    }

    public function rules(): array
    {
        return [
            'tagihan_spp_id' => ['required', 'exists:tagihan_spp,id'],
            'batas_waktu_baru' => ['required', 'date', 'after:today'],
            'alasan_penundaan' => ['required', 'string', 'min:10', 'max:1000'],
            'dokumen_pendukung' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'tagihan_spp_id.required' => 'Tagihan SPP yang dimintakan dispensasi wajib dipilih.',
            'tagihan_spp_id.exists' => 'Tagihan SPP tidak ditemukan.',
            'batas_waktu_baru.required' => 'Batas waktu jatuh tempo baru wajib ditentukan.',
            'batas_waktu_baru.date' => 'Format batas waktu tidak valid.',
            'batas_waktu_baru.after' => 'Batas waktu baru harus melewati hari ini.',
            'alasan_penundaan.required' => 'Alasan penundaan pembayaran wajib diisi.',
            'alasan_penundaan.min' => 'Jelaskan alasan penundaan sekurang-kurangnya 10 karakter.',
            'dokumen_pendukung.file' => 'Dokumen pendukung harus berupa berkas file.',
            'dokumen_pendukung.mimes' => 'Format dokumen pendukung harus berupa PDF, JPG, atau PNG.',
            'dokumen_pendukung.max' => 'Ukuran dokumen pendukung maksimal 2 MB.',
        ];
    }
}
