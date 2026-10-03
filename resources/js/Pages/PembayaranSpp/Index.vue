<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    transaksi: Object,
    filters: Object,
});

const searchQuery = ref('');
const searchResults = ref([]);
const selectedSiswa = ref(null);
const unPaidBills = ref([]);

const paymentForm = useForm({
    tagihan_spp_id: '',
    jumlah_dibayar: '',
    metode_pembayaran: 'tunai',
    nomor_referensi_bank: '',
    catatan: '',
});

const searchSiswa = async () => {
    if (searchQuery.value.length < 2) return;
    const res = await fetch(route('tu.pembayaran-spp.cari-siswa') + `?q=${searchQuery.value}`);
    searchResults.value = await res.json();
};

const selectSiswa = (siswa) => {
    selectedSiswa.value = siswa;
    searchResults.value = [];
    searchQuery.value = `${siswa.nis} - ${siswa.nama_lengkap}`;
    // Memuat tagihan siswa yang belum lunas
    fetch(route('tu.pembayaran-spp.cari-siswa') + `?siswa_id=${siswa.id}`)
        .then(() => {
            unPaidBills.value = siswa.tagihan_belum_lunas || [];
        });
};

const submitPayment = () => {
    paymentForm.post(route('tu.pembayaran-spp.store'), {
        onSuccess: (page) => {
            paymentForm.reset();
            const trxId = page.props.flash?.transaksi_id;
            if (trxId) {
                window.open(route('cetak-kuitansi.kuitansi', trxId), '_blank');
            }
        },
    });
};

const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <Head title="Loket Pembayaran SPP" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Loket Kasir Pembayaran SPP</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Form Input Pembayaran -->
                    <div class="rounded-lg bg-white p-6 shadow-sm lg:col-span-1">
                        <h3 class="mb-4 font-bold text-gray-900">Transaksi Pembayaran Baru</h3>

                        <div class="relative mb-4">
                            <label class="block text-sm font-medium text-gray-700">Cari Siswa (NIS / Nama)</label>
                            <input
                                v-model="searchQuery"
                                @input="searchSiswa"
                                type="text"
                                placeholder="Ketik NIS atau nama..."
                                class="mt-1 w-full rounded-md border-gray-300"
                            />
                            <!-- Dropdown Hasil Pencarian -->
                            <div v-if="searchResults.length > 0" class="absolute z-10 mt-1 max-h-56 w-full overflow-y-auto rounded-md border bg-white shadow-lg">
                                <div
                                    v-for="s in searchResults"
                                    :key="s.id"
                                    @click="selectSiswa(s)"
                                    class="cursor-pointer border-b px-3 py-2 text-sm hover:bg-gray-100"
                                >
                                    <div class="font-medium text-gray-900">{{ s.nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500">{{ s.nis }} - {{ s.kelas?.nama_kelas }}</div>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submitPayment" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ID Tagihan SPP</label>
                                <input v-model="paymentForm.tagihan_spp_id" type="number" required placeholder="Masukkan ID Tagihan..." class="mt-1 w-full rounded-md border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nominal Bayar (Rp)</label>
                                <input v-model="paymentForm.jumlah_dibayar" type="number" min="1000" required class="mt-1 w-full rounded-md border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                                <select v-model="paymentForm.metode_pembayaran" class="mt-1 w-full rounded-md border-gray-300">
                                    <option value="tunai">Tunai (Loket)</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                            <div v-if="paymentForm.metode_pembayaran === 'transfer'">
                                <label class="block text-sm font-medium text-gray-700">Nomor Referensi Bank</label>
                                <input v-model="paymentForm.nomor_referensi_bank" type="text" class="mt-1 w-full rounded-md border-gray-300" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                                <textarea v-model="paymentForm.catatan" class="mt-1 w-full rounded-md border-gray-300"></textarea>
                            </div>
                            <button type="submit" :disabled="paymentForm.processing" class="w-full rounded-md bg-indigo-600 py-2.5 font-medium text-white hover:bg-indigo-700">
                                Proses & Cetak Kuitansi
                            </button>
                        </form>
                    </div>

                    <!-- Riwayat Transaksi -->
                    <div class="rounded-lg bg-white p-6 shadow-sm lg:col-span-2">
                        <h3 class="mb-4 font-bold text-gray-900">Riwayat Pembayaran Terbaru</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">No. Transaksi</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Siswa</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nominal</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Metode</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Kuitansi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="trx in props.transaksi.data" :key="trx.id">
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ trx.nomor_transaksi }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ trx.tagihan?.siswa?.nama_lengkap }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-emerald-600">{{ formatRupiah(trx.jumlah_dibayar) }}</td>
                                    <td class="px-4 py-3 text-xs uppercase">{{ trx.metode_pembayaran }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a :href="route('cetak-kuitansi.kuitansi', trx.id)" target="_blank" class="text-xs text-indigo-600 hover:underline">PDF</a>
                                        <span class="mx-1 text-gray-300">|</span>
                                        <a :href="route('cetak-kuitansi.thermal', trx.id)" target="_blank" class="text-xs text-indigo-600 hover:underline">Thermal</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
