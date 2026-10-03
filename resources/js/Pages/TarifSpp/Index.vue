<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    tarif: Object,
    filters: Object,
    auth: Object,
});

const isModalFormOpen = ref(false);
const isModalKomiteOpen = ref(false);
const editMode = ref(false);
const selectedTarif = ref(null);

const form = useForm({
    id: null,
    tahun_ajaran: '',
    tingkat: '10',
    nominal_standar: '',
    keterangan: '',
});

const formKomite = useForm({
    nomor_berita_acara: '',
    tanggal_kesepakatan: new Date().toISOString().slice(0, 10),
    status_persetujuan: 'disetujui',
    dokumen_ba_pdf: null,
    catatan_komite: '',
});

const openCreateModal = () => {
    editMode.value = false;
    form.reset();
    isModalFormOpen.value = true;
};

const openEditModal = (item) => {
    editMode.value = true;
    form.id = item.id;
    form.tahun_ajaran = item.tahun_ajaran;
    form.tingkat = item.tingkat;
    form.nominal_standar = item.nominal_standar;
    form.keterangan = item.keterangan || '';
    isModalFormOpen.value = true;
};

const submitForm = () => {
    if (editMode.value) {
        form.put(route('admin.tarif-spp.update', form.id), {
            onSuccess: () => (isModalFormOpen.value = false),
        });
    } else {
        form.post(route('admin.tarif-spp.store'), {
            onSuccess: () => (isModalFormOpen.value = false),
        });
    }
};

const destroyTarif = (id) => {
    if (confirm('Yakin ingin menghapus tarif SPP ini?')) {
        router.delete(route('admin.tarif-spp.destroy', id));
    }
};

const openKomiteModal = (item) => {
    selectedTarif.value = item;
    formKomite.reset();
    isModalKomiteOpen.value = true;
};

const submitKomite = () => {
    formKomite.post(route('komite.tarif-spp.setujui-komite', selectedTarif.value.id), {
        onSuccess: () => (isModalKomiteOpen.value = false),
    });
};

const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <Head title="Manajemen Tarif SPP" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen Tarif SPP</h2>
                <button
                    v-if="props.auth.user.role === 'admin'"
                    @click="openCreateModal"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    + Tambah Tarif
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
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tahun Ajaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tingkat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nominal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status Komite</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in props.tarif.data" :key="item.id">
                                    <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ item.tahun_ajaran }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-600">Kelas {{ item.tingkat }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-emerald-600">{{ formatRupiah(item.nominal_standar) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            v-if="item.persetujuan_komite?.status_persetujuan === 'disetujui'"
                                            class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800"
                                        >
                                            Disetujui Komite
                                        </span>
                                        <span
                                            v-else-if="item.persetujuan_komite?.status_persetujuan === 'ditolak'"
                                            class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800"
                                        >
                                            Ditolak Komite
                                        </span>
                                        <span v-else class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                                            Menunggu Kesepakatan
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <button
                                            v-if="props.auth.user.role === 'komite_sekolah'"
                                            @click="openKomiteModal(item)"
                                            class="mr-3 font-semibold text-indigo-600 hover:text-indigo-900"
                                        >
                                            Pengesahan BA
                                        </button>
                                        <button
                                            v-if="props.auth.user.role === 'admin'"
                                            @click="openEditModal(item)"
                                            class="mr-3 text-amber-600 hover:text-amber-900"
                                        >
                                            Ubah
                                        </button>
                                        <button
                                            v-if="props.auth.user.role === 'admin'"
                                            @click="destroyTarif(item.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="props.tarif.data.length === 0">
                                    <td colspan="5" class="py-6 text-center text-sm text-gray-500">Belum ada tarif terdaftar.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah/Edit Tarif -->
        <div v-if="isModalFormOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-bold text-gray-900">{{ editMode ? 'Ubah Tarif SPP' : 'Tambah Tarif SPP' }}</h3>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tahun Ajaran</label>
                        <input v-model="form.tahun_ajaran" type="text" placeholder="2026/2027" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tingkat</label>
                        <select v-model="form.tingkat" class="mt-1 w-full rounded-md border-gray-300">
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nominal Standar (Rp)</label>
                        <input v-model="form.nominal_standar" type="number" min="0" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <textarea v-model="form.keterangan" class="mt-1 w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="isModalFormOpen = false" class="rounded-md border px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Komite Persetujuan -->
        <div v-if="isModalKomiteOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Pengesahan Berita Acara Tarif</h3>
                <form @submit.prevent="submitKomite" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Berita Acara</label>
                        <input v-model="formKomite.nomor_berita_acara" type="text" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Kesepakatan</label>
                        <input v-model="formKomite.tanggal_kesepakatan" type="date" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keputusan Komite</label>
                        <select v-model="formKomite.status_persetujuan" class="mt-1 w-full rounded-md border-gray-300">
                            <option value="disetujui">Setujui Tarif</option>
                            <option value="ditolak">Tolak Tarif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unggah Berita Acara (PDF, Max 5MB)</label>
                        <input type="file" @input="formKomite.dokumen_ba_pdf = $event.target.files[0]" accept="application/pdf" class="mt-1 w-full text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan Komite</label>
                        <textarea v-model="formKomite.catatan_komite" class="mt-1 w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="isModalKomiteOpen = false" class="rounded-md border px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" :disabled="formKomite.processing" class="rounded-md bg-emerald-600 px-4 py-2 text-sm text-white">Simpan Pengesahan</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
