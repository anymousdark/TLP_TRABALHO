<?php
/**
 * Animais Africanos Animados - SVG + CSS
 * Usage: <?php echo animal_elefante(); ?>
 */

function animal_elefante($size = 80) {
    return <<<SVG
    <svg width="$size" height="$size" viewBox="0 0 100 100" class="animal animal-elefante">
        <g class="animal-body">
            <ellipse cx="50" cy="60" rx="30" ry="25" fill="#6B7280"/>
            <circle cx="50" cy="35" r="20" fill="#6B7280"/>
            <ellipse cx="42" cy="30" rx="5" ry="8" fill="#9CA3AF" transform="rotate(-15 42 30)"/>
            <ellipse cx="58" cy="30" rx="5" ry="8" fill="#9CA3AF" transform="rotate(15 58 30)"/>
            <circle cx="43" cy="32" r="3" fill="white"/>
            <circle cx="57" cy="32" r="3" fill="white"/>
            <circle cx="44" cy="31" r="1.5" fill="#1F2937"/>
            <circle cx="58" cy="31" r="1.5" fill="#1F2937"/>
            <path d="M45 40 Q50 48 55 40" stroke="#4B5563" stroke-width="1.5" fill="none" stroke-linecap="round"/>
            <path d="M32 55 Q25 45 28 38" stroke="#6B7280" stroke-width="6" fill="none" stroke-linecap="round" class="animal-tromba"/>
            <rect x="40" y="80" width="8" height="12" rx="3" fill="#6B7280"/>
            <rect x="52" y="80" width="8" height="12" rx="3" fill="#6B7280"/>
            <path d="M20 55 Q10 50 12 45" stroke="#6B7280" stroke-width="5" fill="none" stroke-linecap="round" class="animal-orelha"/>
            <path d="M80 55 Q90 50 88 45" stroke="#6B7280" stroke-width="5" fill="none" stroke-linecap="round" class="animal-orelha"/>
            <path d="M15 48 Q12 42 15 38" stroke="#6B7280" stroke-width="2.5" fill="none" stroke-linecap="round"/>
            <path d="M85 48 Q88 42 85 38" stroke="#6B7280" stroke-width="2.5" fill="none" stroke-linecap="round"/>
        </g>
    </svg>
SVG;
}

function animal_leao($size = 80) {
    return <<<SVG
    <svg width="$size" height="$size" viewBox="0 0 100 100" class="animal animal-leao">
        <g class="animal-body">
            <ellipse cx="50" cy="60" rx="28" ry="22" fill="#D97706"/>
            <circle cx="50" cy="32" r="22" fill="#D97706"/>
            <circle cx="50" cy="32" r="18" fill="#F59E0B"/>
            <circle cx="43" cy="28" r="3.5" fill="white"/>
            <circle cx="57" cy="28" r="3.5" fill="white"/>
            <circle cx="44" cy="27" r="1.8" fill="#1F2937"/>
            <circle cx="58" cy="27" r="1.8" fill="#1F2937"/>
            <ellipse cx="50" cy="36" rx="6" ry="4" fill="#92400E"/>
            <circle cx="50" cy="34" r="2" fill="#1F2937"/>
            <path d="M28 32 Q20 20 25 14 Q30 18 32 28" fill="#D97706" class="animal-juba"/>
            <path d="M72 32 Q80 20 75 14 Q70 18 68 28" fill="#D97706" class="animal-juba"/>
            <path d="M35 18 Q30 8 35 4 Q40 10 38 18" fill="#D97706" class="animal-juba"/>
            <path d="M65 18 Q70 8 65 4 Q60 10 62 18" fill="#D97706" class="animal-juba"/>
            <path d="M42 14 Q45 4 50 2 Q55 4 58 14" fill="#D97706" class="animal-juba"/>
            <rect x="40" y="78" width="8" height="14" rx="3" fill="#D97706"/>
            <rect x="52" y="78" width="8" height="14" rx="3" fill="#D97706"/>
            <path d="M30 58 Q22 55 20 50" stroke="#D97706" stroke-width="5" fill="none" stroke-linecap="round"/>
            <path d="M70 58 Q78 55 80 50" stroke="#D97706" stroke-width="5" fill="none" stroke-linecap="round"/>
            <path d="M18 48 L12 46 L14 52" stroke="#D97706" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M82 48 L88 46 L86 52" stroke="#D97706" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        </g>
    </svg>
SVG;
}

function animal_girafa($size = 80) {
    return <<<SVG
    <svg width="$size" height="$size" viewBox="0 0 80 120" class="animal animal-girafa">
        <g class="animal-body">
            <ellipse cx="40" cy="55" rx="18" ry="15" fill="#F59E0B"/>
            <rect x="36" y="15" width="8" height="35" rx="4" fill="#F59E0B" class="animal-pescoco"/>
            <circle cx="40" cy="12" r="12" fill="#F59E0B"/>
            <circle cx="36" cy="10" r="2.5" fill="white"/>
            <circle cx="44" cy="10" r="2.5" fill="white"/>
            <circle cx="37" cy="9" r="1.3" fill="#1F2937"/>
            <circle cx="45" cy="9" r="1.3" fill="#1F2937"/>
            <circle cx="31" cy="30" r="2" fill="#92400E"/>
            <circle cx="49" cy="30" r="2" fill="#92400E"/>
            <circle cx="33" cy="40" r="2" fill="#92400E"/>
            <circle cx="47" cy="40" r="2" fill="#92400E"/>
            <circle cx="32" cy="50" r="2" fill="#92400E"/>
            <circle cx="48" cy="50" r="2" fill="#92400E"/>
            <circle cx="30" cy="38" r="1.5" fill="#92400E"/>
            <circle cx="50" cy="38" r="1.5" fill="#92400E"/>
            <path d="M35 18 Q40 24 45 18" stroke="#92400E" stroke-width="1.2" fill="none"/>
            <rect x="32" y="67" width="6" height="18" rx="2" fill="#F59E0B"/>
            <rect x="42" y="67" width="6" height="18" rx="2" fill="#F59E0B"/>
            <rect x="30" y="83" width="8" height="5" rx="1" fill="#92400E"/>
            <rect x="42" y="83" width="8" height="5" rx="1" fill="#92400E"/>
            <path d="M25 52 Q15 48 12 42" stroke="#F59E0B" stroke-width="4" fill="none" stroke-linecap="round"/>
            <path d="M55 52 Q65 48 68 42" stroke="#F59E0B" stroke-width="4" fill="none" stroke-linecap="round"/>
            <line x1="10" y1="40" x2="6" y2="38" stroke="#F59E0B" stroke-width="2" stroke-linecap="round"/>
            <line x1="70" y1="40" x2="74" y2="38" stroke="#F59E0B" stroke-width="2" stroke-linecap="round"/>
        </g>
    </svg>
SVG;
}

function animal_zebra($size = 80) {
    return <<<SVG
    <svg width="$size" height="$size" viewBox="0 0 100 100" class="animal animal-zebra">
        <g class="animal-body">
            <ellipse cx="50" cy="55" rx="30" ry="22" fill="white"/>
            <circle cx="50" cy="30" r="20" fill="white"/>
            <rect x="30" y="38" width="3" height="30" rx="1" fill="#1F2937" transform="rotate(-10 30 38)"/>
            <rect x="38" y="36" width="3" height="32" rx="1" fill="#1F2937" transform="rotate(-5 38 36)"/>
            <rect x="46" y="35" width="3" height="33" rx="1" fill="#1F2937"/>
            <rect x="54" y="36" width="3" height="32" rx="1" fill="#1F2937" transform="rotate(5 54 36)"/>
            <rect x="62" y="38" width="3" height="30" rx="1" fill="#1F2937" transform="rotate(10 62 38)"/>
            <rect x="34" y="58" width="3" height="18" rx="1" fill="#1F2937" transform="rotate(-15 34 58)"/>
            <rect x="46" y="56" width="3" height="20" rx="1" fill="#1F2937"/>
            <rect x="58" y="58" width="3" height="18" rx="1" fill="#1F2937" transform="rotate(15 58 58)"/>
            <circle cx="42" cy="27" r="3" fill="white"/>
            <circle cx="58" cy="27" r="3" fill="white"/>
            <circle cx="43" cy="26" r="1.5" fill="#1F2937"/>
            <circle cx="59" cy="26" r="1.5" fill="#1F2937"/>
            <path d="M44 34 Q50 40 56 34" stroke="#1F2937" stroke-width="1.5" fill="none"/>
            <path d="M28 28 Q18 22 20 16" stroke="white" stroke-width="4" fill="none" stroke-linecap="round"/>
            <path d="M72 28 Q82 22 80 16" stroke="white" stroke-width="4" fill="none" stroke-linecap="round"/>
            <rect x="36" y="73" width="8" height="16" rx="3" fill="white"/>
            <rect x="56" y="73" width="8" height="16" rx="3" fill="white"/>
            <rect x="36" y="85" width="8" height="4" rx="1" fill="#1F2937"/>
            <rect x="56" y="85" width="8" height="4" rx="1" fill="#1F2937"/>
            <path d="M20 50 Q12 48 10 42" stroke="white" stroke-width="3" fill="none" stroke-linecap="round"/>
            <path d="M80 50 Q88 48 90 42" stroke="white" stroke-width="3" fill="none" stroke-linecap="round"/>
        </g>
    </svg>
SVG;
}

function animal_macaco($size = 80) {
    return <<<SVG
    <svg width="$size" height="$size" viewBox="0 0 100 100" class="animal animal-macaco">
        <g class="animal-body">
            <ellipse cx="50" cy="60" rx="25" ry="28" fill="#8B5CF6"/>
            <circle cx="50" cy="30" r="22" fill="#8B5CF6"/>
            <circle cx="48" cy="36" r="14" fill="#C4B5FD"/>
            <circle cx="43" cy="28" r="3.5" fill="white"/>
            <circle cx="57" cy="28" r="3.5" fill="white"/>
            <circle cx="44" cy="27" r="1.8" fill="#1F2937"/>
            <circle cx="58" cy="27" r="1.8" fill="#1F2937"/>
            <circle cx="50" cy="36" r="4" fill="#C4B5FD"/>
            <circle cx="50" cy="35" r="2" fill="#1F2937"/>
            <path d="M40 42 Q50 50 60 42" stroke="#7C3AED" stroke-width="1.5" fill="none" stroke-linecap="round"/>
            <circle cx="32" cy="20" r="8" fill="#8B5CF6" class="animal-orelha"/>
            <circle cx="68" cy="20" r="8" fill="#8B5CF6" class="animal-orelha"/>
            <circle cx="32" cy="20" r="4" fill="#C4B5FD"/>
            <circle cx="68" cy="20" r="4" fill="#C4B5FD"/>
            <rect x="40" y="85" width="8" height="14" rx="3" fill="#8B5CF6"/>
            <rect x="52" y="85" width="8" height="14" rx="3" fill="#8B5CF6"/>
            <path d="M25 55 Q10 50 8 40" stroke="#8B5CF6" stroke-width="5" fill="none" stroke-linecap="round" class="animal-braco"/>
            <path d="M75 55 Q90 50 92 40" stroke="#8B5CF6" stroke-width="5" fill="none" stroke-linecap="round" class="animal-braco"/>
            <path d="M8 40 Q4 35 8 30" stroke="#8B5CF6" stroke-width="3" fill="none" stroke-linecap="round"/>
            <path d="M92 40 Q96 35 92 30" stroke="#8B5CF6" stroke-width="3" fill="none" stroke-linecap="round"/>
        </g>
    </svg>
SVG;
}

function animal_rinoceronte($size = 80) {
    return <<<SVG
    <svg width="$size" height="$size" viewBox="0 0 110 90" class="animal animal-rino">
        <g class="animal-body">
            <ellipse cx="55" cy="55" rx="40" ry="28" fill="#6B7280"/>
            <ellipse cx="55" cy="35" rx="30" ry="24" fill="#6B7280"/>
            <path d="M30 25 Q20 15 15 20 Q18 28 25 30" fill="#4B5563"/>
            <circle cx="45" cy="30" r="3.5" fill="white"/>
            <circle cx="60" cy="30" r="3.5" fill="white"/>
            <circle cx="46" cy="29" r="1.8" fill="#1F2937"/>
            <circle cx="61" cy="29" r="1.8" fill="#1F2937"/>
            <path d="M25 32 Q20 38 22 42" stroke="#6B7280" stroke-width="2" fill="none"/>
            <path d="M18 26 Q12 24 10 20 Q8 16 12 14 Q16 12 18 18 Z" fill="#6B7280"/>
            <rect x="38" y="78" width="10" height="12" rx="3" fill="#6B7280"/>
            <rect x="55" y="78" width="10" height="12" rx="3" fill="#6B7280"/>
            <path d="M15 55 Q5 50 2 45" stroke="#6B7280" stroke-width="6" fill="none" stroke-linecap="round"/>
            <path d="M95 55 Q105 50 108 45" stroke="#6B7280" stroke-width="6" fill="none" stroke-linecap="round"/>
            <path d="M35 20 L32 12 L38 14 Z" fill="#9CA3AF"/>
            <path d="M20 40 Q5 35 8 30" stroke="#6B7280" stroke-width="2" fill="none"/>
            <path d="M90 40 Q105 35 102 30" stroke="#6B7280" stroke-width="2" fill="none"/>
        </g>
    </svg>
SVG;
}
?>
