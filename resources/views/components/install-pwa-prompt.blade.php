{{-- 
    PWA Install Prompt - Clean Version
    Logic:
    - If native install available: Show "Install Sekarang" button only
    - If not available: Show manual instructions + "Mengerti" button
--}}
<style>
    #pwa-install-prompt * {
        box-sizing: border-box;
    }
    .pwa-logo-box {
        width: 56px !important;
        height: 56px !important;
        min-width: 56px !important;
        max-width: 56px !important;
        min-height: 56px !important;
        max-height: 56px !important;
        background: #d9cea9;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .pwa-logo-box img {
        width: 36px !important;
        height: 36px !important;
        max-width: 36px !important;
        max-height: 36px !important;
        object-fit: contain;
    }
    .pwa-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }
    .pwa-header-text h3 {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0;
        line-height: 1.3;
    }
    .pwa-header-text p {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
    }
    .pwa-instructions {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #4b5563;
        background: #f9fafb;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .pwa-instructions svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }
    .pwa-instructions strong {
        color: #1f2937;
    }
    .pwa-buttons {
        display: flex;
        gap: 12px;
    }
    .pwa-btn-primary {
        flex: 1;
        background: linear-gradient(135deg, #800000 0%, rgba(128,0,0,0.9) 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 6px -1px rgba(128, 0, 0, 0.25);
    }
    .pwa-btn-secondary {
        flex: 1;
        background: #f3f4f6;
        color: #4b5563;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 14px;
        border: none;
        cursor: pointer;
    }
</style>

<div id="pwa-install-prompt" 
     style="display: none; position: fixed; inset: 0; z-index: 9998; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); justify-content: center; align-items: flex-end;">
    
    <div id="pwa-prompt-card" 
         style="background: white; width: 100%; max-width: 32rem; border-radius: 24px 24px 0 0; box-shadow: 0 -10px 40px rgba(0,0,0,0.2); transform: translateY(100%); transition: transform 0.3s ease;">
        
        {{-- Handle bar --}}
        <div style="display: flex; justify-content: center; padding: 12px 0 8px;">
            <div style="width: 48px; height: 6px; background: #d1d5db; border-radius: 9999px;"></div>
        </div>
        
        {{-- Content --}}
        <div style="padding: 0 24px 32px;">
            {{-- Header --}}
            <div class="pwa-header">
                <div class="pwa-logo-box">
                    <img src="{{ asset('img/logo_kasau.png') }}" alt="Logo">
                </div>
                <div class="pwa-header-text">
                    <h3>Tambahkan ke Home Screen</h3>
                    <p>Akses lebih cepat & mudah</p>
                </div>
            </div>

            {{-- iOS Instructions (only shown if no native prompt) --}}
            <div id="ios-instructions" class="pwa-instructions" style="display: none;">
                <span>Ketuk</span>
                <svg fill="none" stroke="#3b82f6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <span>lalu pilih</span>
                <strong>"Add to Home Screen"</strong>
            </div>

            {{-- Android/Desktop Instructions (only shown if no native prompt) --}}
            <div id="android-instructions" class="pwa-instructions" style="display: none;">
                <span>Ketuk</span>
                <svg fill="#4b5563" viewBox="0 0 24 24">
                    <circle cx="12" cy="5" r="2"></circle>
                    <circle cx="12" cy="12" r="2"></circle>
                    <circle cx="12" cy="19" r="2"></circle>
                </svg>
                <span>lalu pilih</span>
                <strong>"Install App"</strong>
            </div>

            {{-- Buttons --}}
            <div class="pwa-buttons">
                {{-- Install button - shown when native prompt available --}}
                <button id="pwa-install-btn" class="pwa-btn-primary" style="display: none;">
                    Install Sekarang
                </button>
                
                {{-- OK button - shown when native prompt NOT available --}}
                <button id="pwa-ok-btn" class="pwa-btn-primary" style="display: none;">
                    Mengerti
                </button>
                
                {{-- Later button - always shown --}}
                <button id="pwa-later-btn" class="pwa-btn-secondary">
                    Nanti
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const REMIND_HOURS = 24;
    const STORAGE_KEY = 'pwa_prompt_dismissed';
    let deferredPrompt = null;
    
    function isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true;
    }
    
    function isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    }
    
    function shouldShow() {
        if (isStandalone()) return false;
        
        const dismissed = localStorage.getItem(STORAGE_KEY);
        if (dismissed) {
            const hours = (Date.now() - parseInt(dismissed)) / (1000 * 60 * 60);
            if (hours < REMIND_HOURS) return false;
        }
        return true;
    }
    
    function show() {
        var prompt = document.getElementById('pwa-install-prompt');
        var card = document.getElementById('pwa-prompt-card');
        var ios = document.getElementById('ios-instructions');
        var android = document.getElementById('android-instructions');
        var installBtn = document.getElementById('pwa-install-btn');
        var okBtn = document.getElementById('pwa-ok-btn');
        
        if (!prompt) return;
        
        // Logic: 
        // - If native install available (deferredPrompt exists): Show install button only, hide instructions
        // - If NOT available: Show instructions + Mengerti button
        
        if (deferredPrompt) {
            // Native install available - just show install button
            installBtn.style.display = 'block';
            okBtn.style.display = 'none';
            ios.style.display = 'none';
            android.style.display = 'none';
        } else {
            // Native install NOT available - show manual instructions
            installBtn.style.display = 'none';
            okBtn.style.display = 'block';
            
            if (isIOS()) {
                ios.style.display = 'flex';
                android.style.display = 'none';
            } else {
                ios.style.display = 'none';
                android.style.display = 'flex';
            }
        }
        
        prompt.style.display = 'flex';
        
        // Animate in
        setTimeout(function() {
            card.style.transform = 'translateY(0)';
        }, 50);
    }
    
    function hide() {
        var prompt = document.getElementById('pwa-install-prompt');
        var card = document.getElementById('pwa-prompt-card');
        if (!prompt) return;
        
        localStorage.setItem(STORAGE_KEY, Date.now().toString());
        card.style.transform = 'translateY(100%)';
        setTimeout(function() { prompt.style.display = 'none'; }, 300);
    }
    
    function install() {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(function(result) {
            if (result.outcome === 'accepted') {
                localStorage.removeItem(STORAGE_KEY);
            }
            deferredPrompt = null;
            hide();
        });
    }
    
    // Capture the native install prompt when available
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        console.log('PWA: Native install prompt captured');
        
        // If popup is already showing, update it
        var installBtn = document.getElementById('pwa-install-btn');
        var okBtn = document.getElementById('pwa-ok-btn');
        var ios = document.getElementById('ios-instructions');
        var android = document.getElementById('android-instructions');
        
        if (installBtn && okBtn) {
            installBtn.style.display = 'block';
            okBtn.style.display = 'none';
            if (ios) ios.style.display = 'none';
            if (android) android.style.display = 'none';
        }
    });
    
    window.addEventListener('appinstalled', function() {
        console.log('PWA: App installed successfully');
        localStorage.removeItem(STORAGE_KEY);
        hide();
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        var installBtn = document.getElementById('pwa-install-btn');
        var okBtn = document.getElementById('pwa-ok-btn');
        var laterBtn = document.getElementById('pwa-later-btn');
        var prompt = document.getElementById('pwa-install-prompt');
        
        if (installBtn) installBtn.addEventListener('click', install);
        if (okBtn) okBtn.addEventListener('click', hide);
        if (laterBtn) laterBtn.addEventListener('click', hide);
        if (prompt) {
            prompt.addEventListener('click', function(e) {
                if (e.target.id === 'pwa-install-prompt') hide();
            });
        }
        
        setTimeout(function() { if (shouldShow()) show(); }, 2500);
    });
})();
</script>
