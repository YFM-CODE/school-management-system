<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    dispensasi: Object,
    auth: Object,
});

const isModalAjukanOpen = ref(false);
const isModalPersetujuanOpen = ref(false);
const selectedDispensasi = ref(null);

const formAjukan = useForm({
    tagihan_spp_id: '',
    batas_waktu_baru: '',
    alasan_penundaan: '',
    dokumen_pendukung: null,
});

const formPersetujuan = useForm({
    status: 'disetujui',
    catatan_pimpinan: '',
});

const submitAjukan = () => {
    formAjukan.post(route('tu.dispensasi-spp.store'), {
        onSuccess: () => (isModalAjukanOpen.value = false),
    });
};

const openModalPersetujuan = (item) => {
    selectedDispensasi.value = item;
    formPersetujuan.reset();
    isModalPersetujuanOpen.value = true;
};

const submitPersetujuan = () => {
    formPersetujuan.patch(route('kepsek.dispensasi-spp.persetujuan', selectedDispensasi.value.id), {
        onSuccess: () => (isModalPersetujuanOpen.value = false),
    });
};
</script>

<template>
    <Head title="Pengajuan & Persetujuan Dispensasi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Dispensasi Pembayaran SPP</h2>
                <button
                    v-if="props.auth.user.role === 'staf_tu'"
                    @click="isModalAjukanOpen = true"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Ajukan Dispensasi
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
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Tagihan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Batas Waktu Baru</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Alasan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in props.dispensasi.data" :key="item.id">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ item.tagihan?.siswa?.nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500">{{ item.tagihan?.siswa?.nis }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">Bulan {{ item.tagihan?.bulan }}/{{ item.tagihan?.tahun }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 font-semibold text-rose-600">{{ item.batas_waktu_baru }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ item.alasan_penundaan }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            :class="{
                                                'bg-green-100 text-green-800': item.status === 'disetujui',
                                                'bg-amber-100 text-amber-800': item.status === 'menunggu_persetujuan',
                                                'bg-red-100 text-red-800': item.status === 'ditolak'
                                            }"
                                            class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                                        >
                                            {{ item.status.replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <button
                                            v-if="props.auth.user.role === 'kepala_sekolah' && item.status === 'menunggu_persetujuan'"
                                            @click="openModalPersetujuan(item)"
                                            class="font-semibold text-indigo-600 hover:text-indigo-900"
                                        >
                                            Proses Persetujuan
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ajukan Dispensasi -->
        <div v-if="isModalAjukanOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Form Pengajuan Dispensasi</h3>
                <form @submit.prevent="submitAjukan" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID Tagihan SPP</label>
                        <input v-model="formAjukan.tagihan_spp_id" type="number" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jatuh Tempo Baru yang Diajukan</label>
                        <input v-model="formAjukan.batas_waktu_baru" type="date" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alasan Penundaan</label>
                        <textarea v-model="formAjukan.alasan_penundaan" required class="mt-1 w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dokumen Surat Keterangan / Bukti</label>
                        <input type="file" @input="formAjukan.dokumen_pendukung = $event.target.files[0]" class="mt-1 w-full text-sm" />
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="isModalAjukanOpen = false" class="rounded-md border px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" :disabled="formAjukan.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Keputusan Kepala Sekolah -->
        <div v-if="isModalPersetujuanOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Keputusan Kepala Sekolah</h3>
                <form @submit.prevent="submitPersetujuan" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keputusan</label>
                        <select v-model="formPersetujuan.status" class="mt-1 w-full rounded-md border-gray-300">
                            <option value="disetujui">Setujui Dispensasi</option>
                            <option value="ditolak">Tolak Dispensasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan Pimpinan</label>
                        <textarea v-model="formPersetujuan.catatan_pimpinan" class="mt-1 w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="isModalPersetujuanOpen = false" class="rounded-md border px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" :disabled="formPersetujuan.processing" class="rounded-md bg-emerald-600 px-4 py-2 text-sm text-white">Simpan Keputusan</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
