<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    setoran: Object,
    auth: Object,
});

const isModalVerifikasiOpen = ref(false);
const selectedSetoran = ref(null);

const formVerifikasi = useForm({
    total_fisik: '',
    status: 'diterima',
    catatan_bendahara: '',
});

const tutupKasir = () => {
    if (confirm('Lakukan tutup kasir sekarang dan serahkan uang tunai ke Bendahara?')) {
        router.post(route('tu.setoran-kasir.tutup-kasir'));
    }
};

const openModalVerifikasi = (item) => {
    selectedSetoran.value = item;
    formVerifikasi.total_fisik = item.total_tercatat;
    formVerifikasi.status = 'diterima';
    formVerifikasi.catatan_bendahara = '';
    isModalVerifikasiOpen.value = true;
};

const submitVerifikasi = () => {
    formVerifikasi.post(route('bendahara.setoran-kasir.verifikasi', selectedSetoran.value.id), {
        onSuccess: () => (isModalVerifikasiOpen.value = false),
    });
};

const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <Head title="Rekonsiliasi & Setoran Kasir" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Setoran & Rekonsiliasi Kasir</h2>
                <button
                    v-if="props.auth.user.role === 'staf_tu'"
                    @click="tutupKasir"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    🔒 Tutup Kasir Hari Ini
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">No. Setoran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Staf Loket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Tercatat Sistem</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Fisik Diterima</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in props.setoran.data" :key="item.id">
                                    <td class="whitespace-nowrap px-6 py-4 font-mono text-sm text-gray-600">{{ item.nomor_setoran }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ item.staf_tu?.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold">{{ formatRupiah(item.total_tercatat) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-emerald-600">
                                        {{ item.total_fisik ? formatRupiah(item.total_fisik) : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            :class="{
                                                'bg-green-100 text-green-800': item.status === 'diterima',
                                                'bg-amber-100 text-amber-800': item.status === 'menunggu_verifikasi',
                                                'bg-red-100 text-red-800': item.status === 'ditolak'
                                            }"
                                            class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                                        >
                                            {{ item.status.replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <button
                                            v-if="props.auth.user.role === 'bendahara' && item.status === 'menunggu_verifikasi'"
                                            @click="openModalVerifikasi(item)"
                                            class="font-semibold text-indigo-600 hover:text-indigo-900"
                                        >
                                            Verifikasi Fisik
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Verifikasi Bendahara -->
        <div v-if="isModalVerifikasiOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Verifikasi Uang Fisik Kasir</h3>
                <form @submit.prevent="submitVerifikasi" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Uang Fisik Diterima (Rp)</label>
                        <input v-model="formVerifikasi.total_fisik" type="number" min="0" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keputusan</label>
                        <select v-model="formVerifikasi.status" class="mt-1 w-full rounded-md border-gray-300">
                            <option value="diterima">Cocok / Diterima</option>
                            <option value="ditolak">Tolak (Selisih / Belum Sesuai)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan Verifikasi</label>
                        <textarea v-model="formVerifikasi.catatan_bendahara" class="mt-1 w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="isModalVerifikasiOpen = false" class="rounded-md border px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" :disabled="formVerifikasi.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">Simpan Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
