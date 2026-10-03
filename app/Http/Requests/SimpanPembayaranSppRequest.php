<?php

namespace App\Http\Requests;

use App\Enums\MetodePembayaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanPembayaranSppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Mengandalkan middleware role di routes/web.php
    }

    public function rules(): array
    {
        return [
            'tagihan_spp_id' => ['required', 'exists:tagihan_spp,id'],
            'jumlah_dibayar' => ['required', 'numeric', 'min:1000'],
            'metode_pembayaran' => ['required', Rule::enum(MetodePembayaran::class)],
            'nomor_referensi_bank' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf(fn () => $this->input('metode_pembayaran') === MetodePembayaran::TRANSFER->value),
            ],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'tagihan_spp_id.required' => 'Tagihan SPP wajib dipilih.',
            'tagihan_spp_id.exists' => 'Tagihan SPP yang dipilih tidak ditemukan.',
            'jumlah_dibayar.required' => 'Nominal yang dibayar wajib diisi.',
            'jumlah_dibayar.numeric' => 'Nominal yang dibayar harus berupa angka.',
            'jumlah_dibayar.min' => 'Nominal pembayaran minimal Rp 1.000.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'nomor_referensi_bank.required' => 'Nomor referensi atau bukti transfer wajib diisi untuk metode pembayaran transfer.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
