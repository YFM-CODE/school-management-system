<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({
    canResetPassword: { type: Boolean, default: false },
    status: { type: String, default: '' },
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const lihat = ref(false)

function kirim() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
  <Head title="Masuk - SPP Sekolah" />

  <div class="login">
    <aside class="side">
      <Link href="/" class="brand">SPP Sekolah</Link>
      <div class="side-body">
        <h2>Pembayaran SPP yang dapat ditelusuri dari tarif hingga kas.</h2>
        <p>Masuk sesuai peran Anda: wali murid, staf TU, bendahara, kepala sekolah, komite, atau admin.</p>
      </div>
    </aside>

    <main class="main">
      <form class="card" novalidate @submit.prevent="kirim">
        <h1>Masuk</h1>
        <p class="sub">Gunakan akun yang terdaftar di sekolah.</p>

        <!-- Pesan status sesi atau error umum -->
        <p v-if="status" class="alert-info">{{ status }}</p>
        <p v-if="form.errors.email || form.errors.password" class="alert" role="alert">
          {{ form.errors.email || form.errors.password }}
        </p>

        <div class="field">
          <label for="email">Email</label>
          <input
            id="email"
            v-model.trim="form.email"
            type="email"
            autocomplete="username"
            required
            autofocus
            :aria-invalid="!!form.errors.email"
            placeholder="nama@sekolah.sch.id"
          />
        </div>

        <div class="field">
          <label for="password">Kata sandi</label>
          <div class="pw">
            <input
              id="password"
              v-model="form.password"
              :type="lihat ? 'text' : 'password'"
              autocomplete="current-password"
              required
              :aria-invalid="!!form.errors.password"
            />
            <button type="button" class="toggle" :aria-pressed="lihat" @click="lihat = !lihat">
              {{ lihat ? 'Sembunyikan' : 'Tampilkan' }}
            </button>
          </div>
        </div>

        <label class="check">
          <input v-model="form.remember" type="checkbox" />
          <span>Ingat saya di perangkat ini</span>
        </label>

        <button class="submit" type="submit" :disabled="form.processing">
          {{ form.processing ? 'Memproses…' : 'Masuk' }}
        </button>

        <p class="help">Lupa kata sandi? Hubungi admin atau staf TU sekolah.</p>
      </form>
    </main>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;600;800&display=swap');

.login {
  --ink: #12262b; --muted: #55696d; --bg: #f2f5f4; --surface: #fff; --line: #d3dcdb;
  --brand: #0e6b63; --brand-dark: #0a4f49; --danger: #b3261e;
  font-family: 'Schibsted Grotesk', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
  color: var(--ink); background: var(--bg); min-height: 100vh; line-height: 1.6;
  display: grid; grid-template-columns: 0.9fr 1.1fr;
}

.side { background: var(--brand); color: #fff; padding: 32px 48px; display: flex; flex-direction: column; justify-content: space-between; }
.brand { color: #fff; font-weight: 800; font-size: 1.1rem; text-decoration: none; }
.side-body { max-width: 38ch; padding-bottom: 48px; }
.side h2 { font-size: clamp(1.5rem, 2.6vw, 2.1rem); line-height: 1.2; letter-spacing: -0.01em; margin: 0 0 16px; }
.side p { margin: 0; color: rgba(255, 255, 255, 0.85); }

.main { display: grid; place-items: center; padding: 32px 24px; }
.card { width: 100%; max-width: 400px; }
h1 { font-size: 2rem; font-weight: 800; letter-spacing: -0.02em; margin: 0 0 4px; }
.sub { margin: 0 0 24px; color: var(--muted); }

.field { margin-bottom: 18px; display: flex; flex-direction: column; gap: 6px; }
label { font-weight: 600; font-size: 0.92rem; }
input[type='email'], input[type='password'], input[type='text'] {
  width: 100%; padding: 11px 12px; font: inherit; color: var(--ink); background: var(--surface);
  border: 1px solid var(--line); border-radius: 6px;
}
input:focus-visible, button:focus-visible, a:focus-visible { outline: 3px solid #f0b429; outline-offset: 2px; }
input[aria-invalid='true'] { border-color: var(--danger); }

.pw { position: relative; }
.pw input { padding-right: 104px; }
.toggle {
  position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
  background: none; border: 0; color: var(--brand); font: inherit; font-size: 0.85rem; font-weight: 600; cursor: pointer; padding: 6px 8px;
}

.alert { background: #fbeceb; border: 1px solid #efc3c0; color: var(--danger); padding: 10px 12px; border-radius: 6px; margin: 0 0 18px; font-size: 0.92rem; }
.alert-info { background: #eef8f5; border: 1px solid #bde6dc; color: var(--brand-dark); padding: 10px 12px; border-radius: 6px; margin: 0 0 18px; font-size: 0.92rem; }

.check { display: flex; align-items: center; gap: 8px; font-weight: 400; margin-bottom: 22px; cursor: pointer; }
.check input { width: 16px; height: 16px; accent-color: var(--brand); }

.submit {
  width: 100%; padding: 12px; font: inherit; font-weight: 600; color: #fff; background: var(--brand);
  border: 0; border-radius: 6px; cursor: pointer;
}
.submit:hover:not(:disabled) { background: var(--brand-dark); }
.submit:disabled { opacity: 0.65; cursor: not-allowed; }
.help { margin: 20px 0 0; color: var(--muted); font-size: 0.88rem; }

@media (max-width: 800px) {
  .login { grid-template-columns: 1fr; }
  .side { padding: 20px 24px; }
  .side-body { display: none; }
}
</style>