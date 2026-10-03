<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    tagihan: Array,
});

const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
</script>

<template>
    <Head title="Kartu Tagihan SPP Saya" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Kartu SPP & Riwayat Pembayaran</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="space-y-4">
                    <div
                        v-for="item in props.tagihan"
                        :key="item.id"
                        class="flex flex-col justify-between rounded-lg border bg-white p-5 shadow-sm sm:flex-row sm:items-center"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900">Bulan {{ item.bulan }} / {{ item.tahun }}</span>
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
                            </div>
                            <div class="mt-1 text-sm text-gray-500">Jatuh Tempo: {{ item.jatuh_tempo }}</div>
                            <div v-if="item.potongan > 0" class="text-xs text-emerald-600">Diskon/Keringanan: {{ formatRupiah(item.potongan) }}</div>
                        </div>

                        <div class="mt-4 text-right sm:mt-0">
                            <div class="text-xs text-gray-500">Sisa Tagihan</div>
                            <div class="text-lg font-bold" :class="item.sisa_tagihan > 0 ? 'text-rose-600' : 'text-emerald-600'">
                                {{ formatRupiah(item.sisa_tagihan) }}
                            </div>
                            <div v-if="item.transaksi && item.transaksi.length > 0" class="mt-2 flex justify-end gap-2">
                                <a
                                    v-for="trx in item.transaksi"
                                    :key="trx.id"
                                    :href="route('cetak-kuitansi.kuitansi', trx.id)"
                                    target="_blank"
                                    class="rounded bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-200"
                                >
                                    📄 Kuitansi Bayar
                                </a>
                            </div>
                        </div>
                    </div>

                    <div v-if="props.tagihan.length === 0" class="rounded-lg bg-white p-12 text-center text-gray-500 shadow-sm">
                        Belum ada data tagihan SPP yang diterbitkan.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
