<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean, default: true },
    canRegister: { type: Boolean, default: false },
    laravelVersion: String,
    phpVersion: String,
});

const alur = [
    { judul: 'Tarif SPP ditetapkan', isi: 'Admin menyusun usulan tarif per tahun ajaran dan tingkat kelas.' },
    { judul: 'Komite menyetujui', isi: 'Ketua komite mengesahkan nominal melalui berita acara pleno dan berkas dokumen resmi.' },
    { judul: 'Tagihan terbit', isi: 'Sistem menerbitkan tagihan bulanan siswa secara otomatis, terhitung bersama potongan program keringanan.' },
    { judul: 'Pembayaran di loket', isi: 'Staf TU memproses transaksi kasir dan langsung mencetak kuitansi bertanda bukti sah.' },
    { judul: 'Setoran diverifikasi', isi: 'Bendahara mencocokkan rekonsiliasi total sistem dengan uang fisik saat tutup kasir.' },
];

const peran = [
    { nama: 'Wali Murid / Siswa', tugas: 'Memantau status tagihan berjalan, sisa tunggakan, dan riwayat lembar kuitansi.' },
    { nama: 'Staf Tata Usaha', tugas: 'Mengoperasikan loket pembayaran harian dan menyerahkan rekap setoran kas.' },
    { nama: 'Bendahara Sekolah', tugas: 'Memverifikasi kesesuaian kas fisik dari kasir loket dan mencatat rekonsiliasi kas masuk.' },
    { nama: 'Kepala Sekolah', tugas: 'Meninjau serta memberikan persetujuan dispensasi penundaan pembayaran.' },
    { nama: 'Komite Sekolah', tugas: 'Mengesahkan usulan besaran tarif SPP sebelum diberlakukan.' },
    { nama: 'Administrator', tugas: 'Mengelola data pengguna, rombel kelas, master tarif, serta program beasiswa/keringanan.' },
];
</script>

<template>
    <Head title="Sistem Manajemen Pembayaran SPP" />

    <div class="page">
        <!-- HEADER -->
        <header class="bar">
            <span class="brand">SPP SEKOLAH</span>
            <nav v-if="canLogin">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="btn btn-ghost"
                >
                    Ke Dashboard
                </Link>
                <Link
                    v-else
                    :href="route('login')"
                    class="btn btn-ghost"
                >
                    Masuk
                </Link>
            </nav>
        </header>

        <main>
            <!-- HERO -->
            <section class="hero">
                <div class="hero-text">
                    <h1>Setiap rupiah SPP tercatat, dari tarif sampai ke kas.</h1>
                    <p>
                        Sistem administrasi iuran sekolah yang menghubungkan komite, tata usaha, 
                        bendahara, pimpinan, dan wali murid dalam satu alur pembukuan yang tertib dan akuntabel.
                    </p>
                    <div class="cta">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="btn btn-solid"
                        >
                            Buka Dashboard
                        </Link>
                        <Link
                            v-else
                            :href="route('login')"
                            class="btn btn-solid"
                        >
                            Masuk ke Sistem
                        </Link>
                        <a href="#alur" class="btn btn-ghost">Lihat Alur Pembayaran</a>
                    </div>
                </div>

                <!-- KOMPONEN VISUAL KUITANSI FISIK -->
                <aside class="receipt" aria-label="Contoh kuitansi pembayaran">
                    <div class="r-head">
                        <span>KUITANSI PEMBAYARAN</span>
                        <span class="r-no">KW-2026-000418</span>
                    </div>
                    <dl>
                        <div>
                            <dt>Siswa</dt>
                            <dd>Aditya Pratama · XII RPL 1</dd>
                        </div>
                        <div>
                            <dt>Periode</dt>
                            <dd>Oktober 2026</dd>
                        </div>
                        <div>
                            <dt>Nominal Standar</dt>
                            <dd class="num">Rp 250.000</dd>
                        </div>
                        <div>
                            <dt>Keringanan / Beasiswa</dt>
                            <dd class="num minus">− Rp 75.000</dd>
                        </div>
                        <div class="total">
                            <dt>Jumlah Dibayar</dt>
                            <dd class="num">Rp 175.000</dd>
                        </div>
                    </dl>
                    <div class="r-foot">
                        <span>Loket TU · Pembayaran Tunai</span>
                        <span class="stamp">LUNAS</span>
                    </div>
                </aside>
            </section>

            <!-- TAHAPAN ALUR -->
            <section id="alur" class="section">
                <h2>Lima tahap dari penetapan tarif hingga setoran kas</h2>
                <ol class="alur">
                    <li v-for="(a, i) in alur" :key="a.judul">
                        <span class="no">{{ i + 1 }}</span>
                        <div>
                            <h3>{{ a.judul }}</h3>
                            <p>{{ a.isi }}</p>
                        </div>
                    </li>
                </ol>
            </section>

            <!-- KERINGANAN & DISPENSASI -->
            <section class="section duo">
                <div class="panel">
                    <h2>Keringanan SPP</h2>
                    <p>
                        Program pemotongan biaya pendidikan dengan penetapan resmi. Nilai potongan berupa persentase maupun nominal langsung diaplikasikan pada penerbitan tagihan siswa penerima bantuan.
                    </p>
                </div>
                <div class="panel panel-alt">
                    <h2>Dispensasi Pembayaran</h2>
                    <p>
                        Wali murid dapat mengajukan penundaan tanggal jatuh tempo pembayaran dengan dokumen pendukung. Persetujuan diproses langsung oleh Kepala Sekolah.
                    </p>
                </div>
            </section>

            <!-- DISTRIBUSI PERAN -->
            <section class="section">
                <h2>Setiap peran fokus pada kewenangan kerjanya</h2>
                <div class="peran">
                    <article v-for="p in peran" :key="p.nama">
                        <h3>{{ p.nama }}</h3>
                        <p>{{ p.tugas }}</p>
                    </article>
                </div>
            </section>
        </main>

        <!-- FOOTER -->
        <footer class="foot">
            <span>&copy; {{ new Date().getFullYear() }} Sistem Informasi SPP Sekolah. Seluruh hak cipta dilindungi.</span>
            <Link v-if="canLogin && !$page.props.auth.user" :href="route('login')">Masuk Petugas</Link>
        </footer>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;600;800&display=swap');
.page {
    --ink: #12262b;
    --muted: #55696d;
    --bg: #f2f5f4;
    --surface: #ffffff;
    --line: #d3dcdb;
    --brand: #0e6b63;
    --brand-dark: #0a4f49;
    --stamp: #b3261e;
    font-family: 'Schibsted Grotesk', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: var(--ink);
    background: var(--bg);
    min-height: 100vh;
    line-height: 1.6;
}

.bar, .hero, .section, .foot {
    max-width: 1080px;
    margin: 0 auto;
    padding-left: 24px;
    padding-right: 24px;
}

.bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 24px;
    padding-bottom: 24px;
}

.brand {
    font-weight: 800;
    font-size: 1.05rem;
    letter-spacing: 0.05em;
    color: var(--ink);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 22px;
    border-radius: 6px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    font-size: 0.95rem;
    transition: all 0.15s ease-in-out;
}

.btn-solid {
    background: var(--brand);
    color: #ffffff;
}

.btn-solid:hover {
    background: var(--brand-dark);
}

.btn-ghost {
    border-color: var(--line);
    color: var(--ink);
    background: transparent;
}

.btn-ghost:hover {
    border-color: var(--ink);
}

.btn:focus-visible, a:focus-visible {
    outline: 2px solid var(--brand);
    outline-offset: 2px;
}

/* Hero Section */
.hero {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 56px;
    align-items: center;
    padding-top: 56px;
    padding-bottom: 72px;
}

h1 {
    font-size: clamp(2rem, 4.4vw, 3.2rem);
    line-height: 1.12;
    font-weight: 800;
    letter-spacing: -0.02em;
    margin: 0 0 20px;
}

.hero-text p {
    color: var(--muted);
    font-size: 1.1rem;
    max-width: 52ch;
    margin: 0 0 32px;
}

.cta {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* Elemen Kuitansi Cetak */
.receipt {
    background: var(--surface);
    border: 1px solid var(--line);
    padding: 24px 24px 20px;
    transform: rotate(1.2deg);
    box-shadow: 0 20px 40px -20px rgba(18, 38, 43, 0.25);
    position: relative;
    animation: muncul 0.6s ease-out both;
}

/* Efek potongan bergerigi di bagian bawah kertas */
.receipt::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: -8px;
    height: 8px;
    background: linear-gradient(-45deg, transparent 6px, var(--surface) 0) 0 0 / 12px 8px,
                linear-gradient(45deg, transparent 6px, var(--surface) 0) 0 0 / 12px 8px;
    background-repeat: repeat-x;
}

.r-head {
    display: flex;
    justify-content: space-between;
    font-weight: 800;
    font-size: 0.9rem;
    padding-bottom: 12px;
    border-bottom: 2px dashed var(--line);
}

.r-no {
    font-weight: 500;
    color: var(--muted);
    font-size: 0.85rem;
    font-variant-numeric: tabular-nums;
}

dl {
    margin: 14px 0 0;
}

dl div {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 6px 0;
    font-size: 0.92rem;
}

dt {
    color: var(--muted);
}

dd {
    margin: 0;
    text-align: right;
    font-weight: 500;
}

.num {
    font-variant-numeric: tabular-nums;
}

.minus {
    color: var(--brand);
}

.total {
    border-top: 1px solid var(--ink);
    margin-top: 8px;
    padding-top: 12px !important;
    font-weight: 800;
    font-size: 1.05rem;
}

.total dt {
    color: var(--ink);
}

.r-foot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
    font-size: 0.85rem;
    color: var(--muted);
}

.stamp {
    color: var(--stamp);
    border: 2px solid var(--stamp);
    padding: 2px 12px;
    font-weight: 800;
    font-size: 1rem;
    transform: rotate(-6deg);
    border-radius: 4px;
    letter-spacing: 0.08em;
}

@keyframes muncul {
    from {
        opacity: 0;
        transform: translateY(16px) rotate(1.2deg);
    }
    to {
        opacity: 1;
        transform: rotate(1.2deg);
    }
}

/* Bagian Alur */
.section {
    padding-top: 56px;
    padding-bottom: 56px;
}

h2 {
    font-size: clamp(1.4rem, 2.8vw, 1.85rem);
    line-height: 1.25;
    margin: 0 0 32px;
    letter-spacing: -0.01em;
    max-width: 26ch;
}

h3 {
    margin: 0 0 4px;
    font-size: 1.05rem;
    font-weight: 700;
}

.alur {
    list-style: none;
    margin: 0;
    padding: 0;
    max-width: 720px;
}

.alur li {
    display: flex;
    gap: 20px;
    padding: 20px 0;
    border-top: 1px solid var(--line);
}

.alur li:last-child {
    border-bottom: 1px solid var(--line);
}

.no {
    flex: none;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--brand);
    color: #ffffff;
    display: grid;
    place-items: center;
    font-weight: 700;
    font-size: 0.9rem;
}

.alur p, .peran p, .panel p {
    margin: 0;
    color: var(--muted);
    font-size: 0.95rem;
}

/* Modul Berdampingan */
.duo {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.panel {
    padding: 32px;
    background: var(--brand);
    color: #ffffff;
    border-radius: 6px;
}

.panel h2 {
    color: #ffffff;
    margin-bottom: 12px;
}

.panel p {
    color: rgba(255, 255, 255, 0.88);
}

.panel-alt {
    background: var(--ink);
}

/* Grid Peran */
.peran {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border-top: 1px solid var(--line);
}

.peran article {
    padding: 24px 24px 24px 0;
    border-bottom: 1px solid var(--line);
}

/* Footer */
.foot {
    display: flex;
    justify-content: space-between;
    padding-top: 32px;
    padding-bottom: 48px;
    color: var(--muted);
    font-size: 0.88rem;
    border-top: 1px solid var(--line);
}

.foot a {
    color: var(--brand);
    font-weight: 600;
    text-decoration: none;
}

.foot a:hover {
    text-decoration: underline;
}

/* Responsivitas Layar */
@media (max-width: 860px) {
    .hero {
        grid-template-columns: 1fr;
        gap: 40px;
        padding-top: 32px;
    }
    .duo {
        grid-template-columns: 1fr;
    }
    .peran {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 540px) {
    .peran {
        grid-template-columns: 1fr;
    }
    .foot {
        flex-direction: column;
        gap: 12px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .receipt {
        animation: none;
    }
}
</style>
