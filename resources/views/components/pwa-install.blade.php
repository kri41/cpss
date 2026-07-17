{{--
    Komponen PWA Install Prompt
    - Tidak muncul jika sudah pernah dismiss atau sudah install
    - Menggunakan localStorage key 'dataraga_pwa_dismissed'
    - Otomatis hilang jika event 'appinstalled' terpicu
--}}
<div
    x-data="pwaInstall()"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-400"
    x-transition:enter-start="opacity-0 translate-y-6"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-6"
    class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9998] w-[calc(100%-2rem)] max-w-sm"
>
    <div class="bg-white border border-blue-100 rounded-2xl shadow-2xl px-4 py-4 flex items-center gap-3"
         style="box-shadow: 0 8px 40px rgba(37,99,235,0.15);">
        <div class="shrink-0 w-11 h-11 rounded-xl overflow-hidden border border-gray-100 shadow-sm">
            <img src="/storage/logo.png" alt="Dataraga" class="w-full h-full object-contain">
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900">Pasang Dataraga</p>
            <p class="text-xs text-gray-500 mt-0.5">Akses cepat dari layar utama, tersedia offline.</p>
        </div>
        <div class="shrink-0 flex items-center gap-1.5">
            <button
                @click="install()"
                class="px-3 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
            >Pasang</button>
            <button
                @click="dismiss()"
                class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition"
                title="Nanti saja"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
function pwaInstall() {
    return {
        show: false,
        deferredPrompt: null,

        init() {
            // Tidak tampil jika sudah dismiss / install / tidak support
            if (localStorage.getItem('dataraga_pwa_dismissed')) return;
            if (window.matchMedia('(display-mode: standalone)').matches) return;
            if (navigator.standalone === true) return; // iOS

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                // Tunda sedikit agar tidak langsung muncul saat buka halaman
                setTimeout(() => { this.show = true; }, 3000);
            });

            // Tandai sudah install — sembunyikan prompt
            window.addEventListener('appinstalled', () => {
                this.show = false;
                localStorage.setItem('dataraga_pwa_dismissed', '1');
                this.deferredPrompt = null;
            });
        },

        async install() {
            if (!this.deferredPrompt) return;
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                localStorage.setItem('dataraga_pwa_dismissed', '1');
            }
            this.show = false;
            this.deferredPrompt = null;
        },

        dismiss() {
            this.show = false;
            localStorage.setItem('dataraga_pwa_dismissed', '1');
        }
    }
}
</script>
