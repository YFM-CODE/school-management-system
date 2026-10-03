<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    keringanan: Object,
    filters: Object,
});

const isModalOpen = ref(false);
const editMode = ref(false);

const form = useForm({
    id: null,
    nama_program: '',
    tipe_potongan: 'persen',
    nilai_potongan: '',
    syarat_ketentuan: '',
    aktif: true,
});

const openModalCreate = () => {
    editMode.value = false;
    form.reset();
    isModalOpen.value = true;
};

const openModalEdit = (item) => {
    editMode.value = true;
    form.id = item.id;
    form.nama_program = item.nama_program;
    form.tipe_potongan = item.tipe_potongan;
    form.nilai_potongan = item.nilai_potongan;
    form.syarat_ketentuan = item.syarat_ketentuan || '';
    form.aktif = Boolean(item.aktif);
    isModalOpen.value = true;
};

const submit = () => {
    if (editMode.value) {
        form.put(route('admin.keringanan-spp.update', form.id), {
            onSuccess: () => (isModalOpen.value = false),
        });
    } else {
        form.post(route('admin.keringanan-spp.store'), {
            onSuccess: () => (isModalOpen.value = false),
        });
    }
};

const destroyItem = (id) => {
    if (confirm('Yakin ingin menghapus program keringanan ini?')) {
        router.delete(route('admin.keringanan-spp.destroy', id));
    }
};
</script>

<template>
    <Head title="Program Keringanan SPP" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Program Keringanan / Beasiswa SPP</h2>
                <button @click="openModalCreate" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    + Tambah Program
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
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Nama Program</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Potongan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in props.keringanan.data" :key="item.id">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ item.nama_program }}</td>
                                    <td class="px-6 py-4 font-semibold text-emerald-600">
                                        {{ item.tipe_potongan === 'persen' ? `${item.nilai_potongan}%` : `Rp ${Number(item.nilai_potongan).toLocaleString('id-ID')}` }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="item.aktif ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'" class="rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                            {{ item.aktif ? 'Aktif' : 'Non-aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <button @click="openModalEdit(item)" class="mr-3 text-amber-600 hover:text-amber-900">Ubah</button>
                                        <button @click="destroyItem(item.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-bold text-gray-900">{{ editMode ? 'Ubah Program Keringanan' : 'Tambah Program Keringanan' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Program</label>
                        <input v-model="form.nama_program" type="text" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipe Potongan</label>
                            <select v-model="form.tipe_potongan" class="mt-1 w-full rounded-md border-gray-300">
                                <option value="persen">Persentase (%)</option>
                                <option value="nominal">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nilai Potongan</label>
                            <input v-model="form.nilai_potongan" type="number" min="1" required class="mt-1 w-full rounded-md border-gray-300" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Syarat & Ketentuan</label>
                        <textarea v-model="form.syarat_ketentuan" class="mt-1 w-full rounded-md border-gray-300"></textarea>
                    </div>
                    <div class="flex items-center">
                        <input v-model="form.aktif" id="aktif" type="checkbox" class="rounded border-gray-300 text-indigo-600" />
                        <label for="aktif" class="ml-2 text-sm text-gray-700">Program Aktif</label>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="isModalOpen = false" class="rounded-md border px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
