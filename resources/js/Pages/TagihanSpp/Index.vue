<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    tagihan: Object,
    filters: Object,
    auth: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const isGenerateModalOpen = ref(false);

const generateForm = useForm({
    tahun_ajaran: '2026/2027',
    bulan: new Date().getMonth() + 1,
    tahun: new Date().getFullYear(),
    jatuh_tempo: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 10).toISOString().slice(0, 10),
});

watch([search, status], ([sVal, stVal]) => {
    router.get(
        route(route().current()),
        { search: sVal, status: stVal },
        { preserveState: true, replace: true }
    );
});

const submitGenerate = () => {
    generateForm.post(route('admin.tagihan-spp.generate-bulanan'), {
        onSuccess: () => (isGenerateModalOpen.value = false),
    });
};

const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <Head title="Daftar Tagihan SPP" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen Tagihan SPP</h2>
                <button
                    v-if="['admin', 'bendahara'].includes(props.auth.user.role)"
                    @click="isGenerateModalOpen = true"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
                >
                    ⚡ Generate Tagihan Bulanan
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Filter Bar -->
                <div class="mb-4 flex gap-4">
                    <input v-model="search" type="text" placeholder="Cari NIS, Nama Siswa, No Tagihan..." class="w-72 rounded-md border-gray-300" />
                    <select v-model="status" class="rounded-md border-gray-300">
                        <option value="">Semua Status</option>
                        <option value="belum_lunas">Belum Lunas</option>
                        <option value="sebagian">Sebagian</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">No. Tagihan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Periode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Total Tagihan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Sisa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in props.tagihan.data" :key="item.id">
                                    <td class="whitespace-nowrap px-6 py-4 font-mono text-sm text-gray-600">{{ item.nomor_tagihan }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ item.siswa?.nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500">{{ item.siswa?.nis }} - {{ item.siswa?.kelas?.nama_kelas }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">Bulan {{ item.bulan }} / {{ item.tahun }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 font-medium">{{ formatRupiah(item.total_harus_bayar) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 font-semibold text-rose-600">{{ formatRupiah(item.sisa_tagihan) }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            :class="{
                                                'bg-green-100 text-green-800': item.status === 'lunas',
                                                'bg-amber-100 text-amber-800': item.status === 'sebagian',
                                                'bg-red-100 text-red-800': item.status === 'belum_lunas'
                                            }"
                                            class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                                        >
                                            {{ item.status.replace('_', ' ') }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Generate -->
        <div v-if="isGenerateModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Generate Tagihan Bulanan</h3>
                <form @submit.prevent="submitGenerate" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tahun Ajaran</label>
                        <input v-model="generateForm.tahun_ajaran" type="text" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bulan (1-12)</label>
                            <input v-model="generateForm.bulan" type="number" min="1" max="12" required class="mt-1 w-full rounded-md border-gray-300" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun</label>
                            <input v-model="generateForm.tahun" type="number" min="2020" required class="mt-1 w-full rounded-md border-gray-300" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                        <input v-model="generateForm.jatuh_tempo" type="date" required class="mt-1 w-full rounded-md border-gray-300" />
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="isGenerateModalOpen = false" class="rounded-md border px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" :disabled="generateForm.processing" class="rounded-md bg-emerald-600 px-4 py-2 text-sm text-white">Mulai Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
