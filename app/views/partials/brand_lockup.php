<?php
declare(strict_types=1);
/** @var string $brandHref */
/** @var string $brandVariant nav|landing */
$blHref = isset($brandHref) ? (string) $brandHref : 'index.php?page=landing';
$blVariant = isset($brandVariant) && (string) $brandVariant === 'landing' ? 'landing' : 'nav';
$gid = 'tc-logo-' . bin2hex(random_bytes(4));
?>
<a class="brand-lockup brand-lockup--<?php echo htmlspecialchars($blVariant, ENT_QUOTES, 'UTF-8'); ?>" href="<?php echo htmlspecialchars($blHref, ENT_QUOTES, 'UTF-8'); ?>" aria-label="TrailConnect home">
    <span class="brand-lockup__mark" aria-hidden="true">
        <svg class="brand-lockup__svg" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" focusable="false">
            <defs>
                <linearGradient id="<?php echo htmlspecialchars($gid, ENT_QUOTES, 'UTF-8'); ?>-mass" x1="6" y1="4" x2="42" y2="44" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#f5e6a6"/>
                    <stop offset="45%" stop-color="#4fa882"/>
                    <stop offset="100%" stop-color="#1e5568"/>
                </linearGradient>
                <linearGradient id="<?php echo htmlspecialchars($gid, ENT_QUOTES, 'UTF-8'); ?>-trail" x1="4" y1="38" x2="44" y2="10" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#fff9dc"/>
                    <stop offset="100%" stop-color="#7dcea0"/>
                </linearGradient>
            </defs>
            <path fill="url(#<?php echo htmlspecialchars($gid, ENT_QUOTES, 'UTF-8'); ?>-mass)" d="M4 40 L17 9 L25 20 L33 6 L44 27 L44 40 Z"/>
            <path fill="none" stroke="url(#<?php echo htmlspecialchars($gid, ENT_QUOTES, 'UTF-8'); ?>-trail)" stroke-width="2.25" stroke-linecap="round" d="M9 37 C16 32 22 26 26 24 C31 21 38 16 42 11"/>
            <circle cx="9" cy="37" r="3.25" fill="#ffd93d" stroke="rgba(0,0,0,0.2)" stroke-width="0.5"/>
            <circle cx="42" cy="11" r="3.25" fill="#ffd93d" stroke="rgba(0,0,0,0.2)" stroke-width="0.5"/>
        </svg>
    </span>
    <span class="brand-lockup__wordmark">
        <span class="brand-lockup__trail">Trail</span><span class="brand-lockup__connect">Connect</span>
    </span>
</a>
