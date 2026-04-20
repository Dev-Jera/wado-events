<x-filament-panels::page>
    {{-- PWA manifest so the browser offers "Add to Home Screen" on this page too --}}
    @push('styles')
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    @endpush

    <div class="sp-wrap">
        <div class="sp-head">
            <h2>Scanner</h2>
            <a href="{{ route('tickets.verify.index', ['scanner_only' => 1, 'back' => url('/dashboard/scanner-page')]) }}" target="_blank" rel="noopener">Open full page</a>
        </div>

        {{-- Install to Home Screen banner --}}
        <div class="sp-install-banner" id="sp-install-banner">
            <div class="sp-install-text">
                <strong>Add scanner to your home screen</strong>
                <span>for one-tap access at the gate</span>
            </div>
            <div class="sp-install-actions">
                <button type="button" class="sp-install-btn" id="sp-install-btn">Install</button>
                <button type="button" class="sp-install-dismiss" id="sp-install-dismiss" aria-label="Dismiss">✕</button>
            </div>
        </div>

        <iframe
            class="sp-frame"
            src="{{ route('tickets.verify.index', ['embedded' => 1, 'back' => url('/dashboard/scanner-page')]) }}"
            title="Ticket Scanner"
            loading="lazy"
        ></iframe>
    </div>

    <style>
        .sp-wrap {
            display: grid;
            gap: .75rem;
            font-family: var(--wado-admin-font, 'Quicksand', 'Nunito', 'Plus Jakarta Sans', 'Segoe UI', sans-serif);
        }

        .sp-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }
        .sp-head h2 { margin: 0; color: #0f172a; font-size: 1rem; font-weight: 700; }
        .sp-head a  { color: #0a4fbe; font-size: .78rem; font-weight: 600; text-decoration: none; }

        /* Install banner */
        .sp-install-banner {
            display: none; /* shown by JS when prompt is available */
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            background: #e8f0ff;
            border: 1px solid #c9d9f8;
            border-radius: 10px;
            padding: .65rem 1rem;
        }
        .sp-install-text { display: flex; flex-direction: column; gap: .1rem; }
        .sp-install-text strong { font-size: .82rem; color: #0d1b3e; font-weight: 700; }
        .sp-install-text span   { font-size: .74rem; color: #475569; }
        .sp-install-actions { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }
        .sp-install-btn {
            background: #0a4fbe; color: #fff; border: none; border-radius: 7px;
            padding: .4rem .9rem; font-size: .78rem; font-weight: 700; cursor: pointer;
        }
        .sp-install-btn:hover { background: #083f98; }
        .sp-install-dismiss {
            background: none; border: none; color: #94a3b8;
            font-size: .9rem; cursor: pointer; padding: .2rem .4rem;
        }

        .sp-frame {
            width: 100%;
            min-height: 78vh;
            border: 1px solid #dbe5f2;
            border-radius: 12px;
            background: #fff;
        }

        @media (max-width: 640px) {
            .sp-head { align-items: flex-start; flex-direction: column; }
            .sp-head a { display: inline-flex; justify-content: center; width: 100%; }
            .sp-frame { min-height: 86vh; }
            .sp-install-banner { flex-direction: column; align-items: flex-start; }
        }
    </style>

    <script>
    (function () {
        let deferredPrompt = null;
        const banner    = document.getElementById('sp-install-banner');
        const installBtn= document.getElementById('sp-install-btn');
        const dismissBtn= document.getElementById('sp-install-dismiss');

        // Show banner when browser fires beforeinstallprompt (Android/Chrome)
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (banner) banner.style.display = 'flex';
        });

        installBtn?.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            if (banner) banner.style.display = 'none';
        });

        dismissBtn?.addEventListener('click', () => {
            if (banner) banner.style.display = 'none';
        });

        // Hide if already installed (standalone mode)
        if (window.matchMedia('(display-mode: standalone)').matches) {
            if (banner) banner.style.display = 'none';
        }
    })();
    </script>
</x-filament-panels::page>
