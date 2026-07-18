{{--
    PWA Install Prompt
    - Android/Chrome: menampilkan tombol install via beforeinstallprompt
    - iOS Safari: menampilkan panduan manual "Share → Add to Home Screen"
    - Tidak muncul jika sudah pernah dismiss, sudah install, atau bukan browser yang mendukung
--}}
<div x-data="pwaInstall()" x-cloak>

    {{-- Android / Desktop: tombol install --}}
    <div
        x-show="show && !isIos"
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
                <button @click="install()" class="px-3 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Pasang</button>
                <button @click="dismiss()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition" title="Nanti saja">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- iOS Safari: panduan manual --}}
    <div
        x-show="show && isIos"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0 translate-y-6"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-6"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9998] w-[calc(100%-2rem)] max-w-sm"
    >
        <div class="bg-white border border-blue-100 rounded-2xl shadow-2xl px-4 py-4"
             style="box-shadow: 0 8px 40px rgba(37,99,235,0.15);">
            <div class="flex items-center gap-3 mb-3">
                <div class="shrink-0 w-11 h-11 rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                    <img src="/storage/logo.png" alt="Dataraga" class="w-full h-full object-contain">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900">Pasang di iPhone</p>
                    <p class="text-xs text-gray-500 mt-0.5">Tambahkan ke layar utama kamu.</p>
                </div>
                <button @click="dismiss()" class="shrink-0 p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition" title="Nanti saja">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="bg-blue-50 rounded-xl px-3 py-2.5 text-xs text-blue-900 space-y-1.5">
                <p class="flex items-center gap-2">
                    <span class="shrink-0 w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-[10px]">1</span>
                    <span>Tap ikon <strong>Share</strong>
                        <svg class="inline w-4 h-4 mx-0.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    di bar bawah Safari</span>
                </p>
                <p class="flex items-center gap-2">
                    <span class="shrink-0 w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-[10px]">2</span>
                    <span>Pilih <strong>"Add to Home Screen"</strong></span>
                </p>
                <p class="flex items-center gap-2">
                    <span class="shrink-0 w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-[10px]">3</span>
                    <span>Tap <strong>Add</strong> — selesai!</span>
                </p>
            </div>
        </div>
    </div>

</div>

<script>
function pwaInstall() {
    return {
        show: false,
        isIos: false,
        deferredPrompt: null,

        init() {
            if (localStorage.getItem('dataraga_pwa_dismissed')) return;
            if (window.matchMedia('(display-mode: standalone)').matches) return;
            if (navigator.standalone === true) return;

            const ua = navigator.userAgent;
            // iOS Safari: iPhone/iPad tapi bukan Chrome/Firefox mobile di iOS
            this.isIos = /iPhone|iPad|iPod/i.test(ua) && !/CriOS|FxiOS/i.test(ua);

            if (this.isIos) {
                setTimeout(() => { this.show = true; }, 4000);
                return;
            }

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                setTimeout(() => { this.show = true; }, 3000);
            });

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
