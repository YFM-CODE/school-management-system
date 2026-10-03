<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    role: { type: String, required: true },
    ringkasan: { type: Array, default: () => [] },
    tagihan: { type: Array, default: () => [] },
});

const konfigurasi = {
    admin: {
        label: 'Administrator',
        sapaan: 'Kelola master data dan konfigurasi utama sistem SPP.',
        menu: [
            { judul: 'Tarif SPP', isi: 'Atur usulan nominal per tingkat dan tahun ajaran.', rute: 'admin.tarif-spp.index' },
            { judul: 'Program Keringanan', isi: 'Kelola beasiswa dan potongan biaya SPP.', rute: 'admin.keringanan-spp.index' },
            { judul: 'Tagihan Siswa', isi: 'Terbitkan tagihan bulanan dan pantau status bayar.', rute: 'admin.tagihan-spp.index' },
        ],
    },
    staf_tu: {
        label: 'Staf Tata Usaha',
        sapaan: 'Layani pembayaran iuran di loket dan serahkan setoran kasir harian.',
        menu: [
            { judul: 'Loket Pembayaran', isi: 'Catat pembayaran siswa dan cetak kuitansi.', rute: 'tu.pembayaran-spp.index' },
            { judul: 'Setoran Kasir', isi: 'Tutup kasir harian dan serahkan uang tunai ke bendahara.', rute: 'tu.setoran-kasir.index' },
            { judul: 'Dispensasi Pembayaran', isi: 'Input pengajuan permohonan penundaan bayar.', rute: 'tu.dispensasi-spp.index' },
        ],
    },
    bendahara: {
        label: 'Bendahara Sekolah',
        sapaan: 'Verifikasi setoran uang fisik dan rekonsiliasi penerimaan kas.',
        menu: [
            { judul: 'Verifikasi Setoran', isi: 'Periksa fisik kas loket dan catat selisih setoran.', rute: 'bendahara.setoran-kasir.index' },
            { judul: 'Tagihan & Kas Masuk', isi: 'Generate tagihan massal dan monitoring arus kas.', rute: 'bendahara.tagihan-spp.index' },
        ],
    },
    kepala_sekolah: {
        label: 'Kepala Sekolah',
        sapaan: 'Tinjau dan proses permohonan yang menunggu persetujuan pimpinan.',
        menu: [
            { judul: 'Persetujuan Dispensasi', isi: 'Setujui atau tolak surat penundaan bayar tagihan.', rute: 'kepsek.dispensasi-spp.index' },
            { judul: 'Program Keringanan', isi: 'Pantau siswa penerima bantuan dan beasiswa.', rute: 'kepsek.keringanan-spp.index' },
            { judul: 'Laporan Rekapitulasi', isi: 'Rekapitulasi total penerimaan dan tunggakan.', rute: 'kepsek.laporan-spp.index' },
        ],
    },
    komite_sekolah: {
        label: 'Komite Sekolah',
        sapaan: 'Tinjau dan sahkan usulan tarif SPP melalui Berita Acara resmi.',
        menu: [
            { judul: 'Pengesahan Tarif SPP', isi: 'Tinjau usulan tarif dan simpan keputusan Berita Acara.', rute: 'komite.tarif-spp.index' },
        ],
    },
    siswa: {
        label: 'Siswa / Wali Murid',
        sapaan: 'Pantau kewajiban iuran SPP dan riwayat lembar kuitansi resmi.',
        menu: [
            { judul: 'Kartu Tagihan Saya', isi: 'Daftar tagihan bulanan dan status pelunasan.', rute: 'siswa.tagihan-saya.index' },
            { judul: 'Status Dispensasi', isi: 'Lihat progres persetujuan penundaan bayar.', rute: 'siswa.dispensasi-saya.index' },
        ],
    },
};

const cfg = computed(() => konfigurasi[props.role]);
const page = usePage();
const nama = computed(() => page.props.auth?.user?.nama ?? page.props.auth?.user?.name ?? '');

const rupiah = (n) => 'Rp ' + Number(n ?? 0).toLocaleString('id-ID');
const nilai = (r) => (r.tipe === 'rupiah' ? rupiah(r.nilai) : r.nilai);
const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const tanggal = (d) => (d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-');
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="dash-header">Dashboard {{ cfg?.label }}</h2>
        </template>

        <div class="dash">
            <div class="wrap">
                <p v-if="!cfg" class="empty">
                    Peran akun Anda ({{ role }}) belum memiliki konfigurasi dashboard. Hubungi Administrator.
                </p>

                <template v-else>
                    <section class="intro">
                        <h1>Selamat datang<span v-if="nama">, {{ nama }}</span>.</h1>
                        <p>{{ cfg.sapaan }}</p>
                    </section>

                    <!-- Ringkasan Metrik Dinamis -->
                    <dl v-if="ringkasan.length" class="stats">
                        <div v-for="r in ringkasan" :key="r.label">
                            <dd class="stat-num">{{ nilai(r) }}</dd>
                            <dt>{{ r.label }}</dt>
                        </div>
                    </dl>

                    <!-- Khusus Siswa: Tagihan Belum Lunas -->
                    <section v-if="role === 'siswa'" class="blok">
                        <h2>Tagihan yang belum lunas</h2>
                        <p v-if="!tagihan.length" class="empty">Tidak ada tagihan yang belum lunas. Seluruh kewajiban telah terpenuhi.</p>
                        <ul v-else class="tagihan">
                            <li v-for="t in tagihan" :key="t.id">
                                <div>
                                    <strong>{{ t.siswa }}</strong>
                                    <span class="muted">SPP Bulan {{ BULAN[t.bulan - 1] }} {{ t.tahun }} &middot; Jatuh tempo {{ tanggal(t.jatuh_tempo) }}</span>
                                </div>
                                <span class="sisa">{{ rupiah(t.sisa_tagihan) }}</span>
                            </li>
                        </ul>
                    </section>

                    <!-- Daftar Menu Navigasi Peran -->
                    <section class="blok">
                        <h2>Menu Akses</h2>
                        <nav class="menu" aria-label="Menu utama">
                            <component
                                :is="route().has(m.rute) ? Link : 'div'"
                                v-for="m in cfg.menu"
                                :key="m.rute"
                                :href="route().has(m.rute) ? route(m.rute) : undefined"
                                :class="['item', { off: !route().has(m.rute) }]"
                            >
                                <span class="item-body">
                                    <span class="item-title">{{ m.judul }}</span>
                                    <span class="item-desc">{{ m.isi }}</span>
                                </span>
                                <span class="item-act">{{ route().has(m.rute) ? 'Buka' : 'Segera hadir' }}</span>
                            </component>
                        </nav>
                    </section>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;600;800&display=swap');

.dash-header { font-family: 'Schibsted Grotesk', system-ui, sans-serif; font-size: 1.1rem; font-weight: 800; color: #12262b; margin: 0; }
.dash {
    --ink: #12262b; --muted: #55696d; --bg: #f2f5f4; --line: #d3dcdb;
    --brand: #0e6b63;
    font-family: 'Schibsted Grotesk', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
    color: var(--ink); background: var(--bg); line-height: 1.6;
    min-height: calc(100vh - 8rem); padding: 48px 24px;
}
.wrap { max-width: 880px; margin: 0 auto; }
.muted { color: var(--muted); }
.intro { margin-bottom: 32px; }
h1 { font-size: clamp(1.6rem, 3.6vw, 2.4rem); line-height: 1.15; font-weight: 800; letter-spacing: -0.02em; margin: 0 0 10px; }
.intro p { margin: 0; color: var(--muted); max-width: 60ch; }
h2 { font-size: 1.15rem; margin: 0 0 12px; font-weight: 700; }
.blok { margin-top: 40px; }
.stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin: 0; border-top: 2px solid var(--ink); }
.stats > div { padding: 18px 16px 18px 0; border-bottom: 1px solid var(--line); }
.stat-num { margin: 0; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; color: var(--ink); }
.stats dt { color: var(--muted); font-size: 0.88rem; margin-top: 2px; }
.tagihan { list-style: none; margin: 0; padding: 0; border-top: 1px solid var(--line); }
.tagihan li { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px 0; border-bottom: 1px solid var(--line); }
.tagihan li > div { display: flex; flex-direction: column; }
.tagihan .muted { font-size: 0.88rem; }
.sisa { font-weight: 800; font-variant-numeric: tabular-nums; white-space: nowrap; color: #b3261e; }
.menu { display: grid; border-top: 1px solid var(--line); }
.item { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 22px 8px 22px 0; border-bottom: 1px solid var(--line); color: inherit; text-decoration: none; transition: background 0.15s ease; }
.item-body { display: flex; flex-direction: column; gap: 2px; }
.item-title { font-weight: 800; font-size: 1.05rem; }
.item-desc { color: var(--muted); max-width: 56ch; font-size: 0.92rem; }
.item-act { flex: none; padding: 8px 18px; border: 1px solid var(--line); border-radius: 6px; font-weight: 600; font-size: 0.9rem; }
a.item:hover .item-act { background: var(--brand); border-color: var(--brand); color: #fff; }
a.item:focus-visible { outline: 3px solid #f0b429; outline-offset: 2px; }
.item.off { opacity: 0.55; cursor: not-allowed; }
.empty { color: var(--muted); }
@media (max-width: 560px) {
    .dash { padding: 32px 16px; }
    .item { flex-direction: column; align-items: flex-start; gap: 12px; }
}
</style>