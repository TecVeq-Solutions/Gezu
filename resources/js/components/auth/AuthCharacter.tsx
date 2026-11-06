import React, { useEffect, useState } from 'react';

export interface AuthCharacterProps {
  role?: 'brand' | 'clipper';
  isPasswordFocused: boolean;
  isEmailFocused?: boolean;
  isSubmitting: boolean;
  isSuccess: boolean;
  isError: boolean;
  activeMode: 'signin' | 'signup' | 'verify-email' | 'forgot-password';
}

export const AuthCharacter: React.FC<AuthCharacterProps> = ({
  role = 'clipper',
  isPasswordFocused,
  isEmailFocused = false,
  isSubmitting,
  isSuccess,
  isError,
  activeMode,
}) => {
  const [prefersReducedMotion, setPrefersReducedMotion] = useState<boolean>(false);

  useEffect(() => {
    if (typeof window !== 'undefined') {
      const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
      setPrefersReducedMotion(mediaQuery.matches);
      const listener = (e: MediaQueryListEvent) => setPrefersReducedMotion(e.matches);
      mediaQuery.addEventListener('change', listener);
      return () => mediaQuery.removeEventListener('change', listener);
    }
  }, []);

  const isBrand = role === 'brand';

  return (
    <div className="relative w-full max-w-[360px] lg:max-w-[420px] aspect-square flex items-center justify-center select-none transition-all">
      
      {/* 1. Background Ambient Glow */}
      <div 
        className={`absolute inset-0 rounded-full blur-[80px] transition-all duration-700 pointer-events-none ${
          isError 
            ? 'bg-rose-500/25' 
            : isSuccess 
            ? 'bg-emerald-500/30' 
            : isPasswordFocused 
            ? isBrand ? 'bg-blue-600/25' : 'bg-indigo-600/25' 
            : isBrand ? 'bg-blue-600/20' : 'bg-cyan-500/20'
        }`} 
      />

      {/* 2. Futuristic Stage Ring with Hologram Grid */}
      <div className={`absolute bottom-6 w-3/4 h-8 bg-gradient-to-r from-transparent ${isBrand ? 'via-blue-500/20' : 'via-cyan-500/20'} to-transparent rounded-full blur-sm border-t ${isBrand ? 'border-blue-500/40' : 'border-cyan-500/40'}`} />

      {/* 3. Character + Bag Animated SVG */}
      <svg
        viewBox="0 0 320 320"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        className={`w-full h-full drop-shadow-2xl transition-transform duration-500 ${
          prefersReducedMotion ? '' : isSubmitting ? 'animate-pulse' : 'hover:scale-105'
        }`}
      >
        <defs>
          <linearGradient id="charSuit" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stopColor={isBrand ? '#1E1B4B' : '#042F2E'} />
            <stop offset="50%" stopColor={isBrand ? '#0F172A' : '#0F172A'} />
            <stop offset="100%" stopColor="#020617" />
          </linearGradient>

          <linearGradient id="charBag" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stopColor={isBrand ? '#3B82F6' : '#06B6D4'} />
            <stop offset="100%" stopColor="#7C3AED" />
          </linearGradient>

          <linearGradient id="visorBrand" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stopColor="#60A5FA" />
            <stop offset="100%" stopColor="#818CF8" />
          </linearGradient>

          <linearGradient id="visorClipper" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stopColor="#22D3EE" />
            <stop offset="100%" stopColor="#C084FC" />
          </linearGradient>
        </defs>

        {/* 4. THE TECH BAG (Carried on back, reveals energy conduits) */}
        <g className="transition-all duration-500">
          {/* Bag Base Structure */}
          <rect x="68" y="125" width="56" height="115" rx="14" fill="url(#charBag)" stroke={isBrand ? '#93C5FD' : '#A5F3FC'} strokeWidth="2" opacity="0.95" />
          <rect x="196" y="125" width="56" height="115" rx="14" fill="url(#charBag)" stroke={isBrand ? '#93C5FD' : '#A5F3FC'} strokeWidth="2" opacity="0.95" />
          
          {/* Reinforced Straps */}
          <path d="M98 130 L125 175 L120 220" stroke={isBrand ? '#2563EB' : '#0891B2'} strokeWidth="8" strokeLinecap="round" />
          <path d="M222 130 L195 175 L200 220" stroke={isBrand ? '#2563EB' : '#0891B2'} strokeWidth="8" strokeLinecap="round" />

          {/* Bag Energy Orbs */}
          <circle cx="96" cy="180" r="10" fill="#09090B" stroke={isBrand ? '#60A5FA' : '#22D3EE'} strokeWidth="2" />
          <circle cx="96" cy="180" r="5" fill={isBrand ? '#3B82F6' : '#06B6D4'} className={prefersReducedMotion ? '' : 'animate-ping'} />

          <circle cx="224" cy="180" r="10" fill="#09090B" stroke={isBrand ? '#60A5FA' : '#22D3EE'} strokeWidth="2" />
          <circle cx="224" cy="180" r="5" fill={isBrand ? '#3B82F6' : '#06B6D4'} className={prefersReducedMotion ? '' : 'animate-ping'} />
        </g>

        {/* 5. CHARACTER TORSO & SUIT */}
        <g>
          {/* Shoulders & Torso */}
          <path
            d="M105 175 C105 138, 215 138, 215 175 L225 260 C225 270, 95 270, 95 260 Z"
            fill="url(#charSuit)"
            stroke={isBrand ? '#1E3A8A' : '#115E59'}
            strokeWidth="3"
          />

          {/* Cyber Armor Plate */}
          <path
            d="M130 170 L190 170 L180 230 L140 230 Z"
            fill="#0F172A"
            stroke={isBrand ? '#3B82F6' : '#0D9488'}
            strokeWidth="2"
          />

          {/* Armor Status Glow */}
          <rect 
            x="145" 
            y="195" 
            width="30" 
            height="4" 
            rx="2" 
            fill={isError ? '#F43F5E' : isSuccess ? '#10B981' : isBrand ? '#3B82F6' : '#22D3EE'} 
          />
        </g>

        {/* 6. HEAD & VISOR */}
        <g className={`transition-transform duration-300 ${
          isPasswordFocused 
            ? 'rotate-[-8deg] origin-[160px_130px]' 
            : isEmailFocused 
            ? 'rotate-[4deg] origin-[160px_130px]' 
            : ''
        }`}>
          {/* Neck */}
          <rect x="145" y="130" width="30" height="20" rx="4" fill="#0F172A" />

          {/* Head Helmet */}
          <circle cx="160" cy="100" r="42" fill="#111827" stroke="#374151" strokeWidth="3" />

          {/* Visor Frame */}
          <rect
            x="128"
            y="82"
            width="64"
            height="34"
            rx="12"
            fill="#030712"
            stroke={isError ? '#F43F5E' : isSuccess ? '#10B981' : isBrand ? '#3B82F6' : '#06B6D4'}
            strokeWidth="2.5"
          />

          {/* 7. EYE & PRIVACY REACTIVITY */}
          {isPasswordFocused ? (
            /* PASSWORD FOCUS: "I'm Not Looking!" Shield State */
            <g className="transition-all duration-300">
              {/* Privacy Shield Across Visor */}
              <rect x="132" y="93" width="56" height="13" rx="4" fill={isBrand ? '#2563EB' : '#0891B2'} opacity="0.95" />
              <text x="160" y="103" fill="#FFFFFF" fontSize="7" fontWeight="bold" textAnchor="middle" letterSpacing="1">
                SECURE
              </text>
              {/* Closed Eyes Curved Line */}
              <path d="M142 98 Q148 103 154 98" stroke="#FFFFFF" strokeWidth="2.5" strokeLinecap="round" />
              <path d="M166 98 Q172 103 178 98" stroke="#FFFFFF" strokeWidth="2.5" strokeLinecap="round" />
            </g>
          ) : isSuccess ? (
            /* SUCCESS EXPRESSION */
            <g>
              <path d="M140 98 Q147 90 154 98" stroke="#34D399" strokeWidth="3" strokeLinecap="round" />
              <path d="M166 98 Q173 90 180 98" stroke="#34D399" strokeWidth="3" strokeLinecap="round" />
              <circle cx="160" cy="106" r="3" fill="#10B981" />
            </g>
          ) : isError ? (
            /* ERROR / ALERT EXPRESSION */
            <g>
              <path d="M141 95 L153 103" stroke="#F43F5E" strokeWidth="3" strokeLinecap="round" />
              <path d="M153 95 L141 103" stroke="#F43F5E" strokeWidth="3" strokeLinecap="round" />
              <path d="M167 95 L179 103" stroke="#F43F5E" strokeWidth="3" strokeLinecap="round" />
              <path d="M179 95 L167 103" stroke="#F43F5E" strokeWidth="3" strokeLinecap="round" />
            </g>
          ) : isEmailFocused ? (
            /* EMAIL FOCUSED: Attentive Look Directly Toward Form */
            <g className="transition-all duration-200">
              <circle cx="150" cy="99" r="6" fill="#1E1B4B" />
              <circle cx="152" cy="99" r="3.5" fill={isBrand ? '#60A5FA' : '#22D3EE'} className={prefersReducedMotion ? '' : 'animate-pulse'} />
              <circle cx="153" cy="98" r="1.5" fill="#FFFFFF" />

              <circle cx="176" cy="99" r="6" fill="#1E1B4B" />
              <circle cx="178" cy="99" r="3.5" fill={isBrand ? '#60A5FA' : '#22D3EE'} className={prefersReducedMotion ? '' : 'animate-pulse'} />
              <circle cx="179" cy="98" r="1.5" fill="#FFFFFF" />
            </g>
          ) : (
            /* NEUTRAL ATTENTIVE GAZE */
            <g className="transition-all duration-200">
              <circle cx="147" cy="99" r="6" fill="#1E1B4B" />
              <circle cx="149" cy="99" r="3.5" fill={isBrand ? '#60A5FA' : '#22D3EE'} />
              <circle cx="150" cy="98" r="1.5" fill="#FFFFFF" />

              <circle cx="173" cy="99" r="6" fill="#1E1B4B" />
              <circle cx="175" cy="99" r="3.5" fill={isBrand ? '#60A5FA' : '#22D3EE'} />
              <circle cx="176" cy="98" r="1.5" fill="#FFFFFF" />
            </g>
          )}
        </g>

        {/* 8. ARMS & PRIVACY COVERAGE */}
        {isPasswordFocused ? (
          /* Arms Raised to Cover Face / Visor */
          <g className="transition-all duration-300">
            <path d="M95 195 C95 160, 130 115, 142 110" stroke={isBrand ? '#1E3A8A' : '#134E4A'} strokeWidth="16" strokeLinecap="round" />
            <circle cx="142" cy="110" r="10" fill={isBrand ? '#3B82F6' : '#0D9488'} stroke="#FFFFFF" strokeWidth="2" />

            <path d="M225 195 C225 160, 190 115, 178 110" stroke={isBrand ? '#1E3A8A' : '#134E4A'} strokeWidth="16" strokeLinecap="round" />
            <circle cx="178" cy="110" r="10" fill={isBrand ? '#3B82F6' : '#0D9488'} stroke="#FFFFFF" strokeWidth="2" />
          </g>
        ) : (
          /* Normal Relaxed Arms */
          <g className="transition-all duration-300">
            <path d="M100 175 L80 240" stroke={isBrand ? '#1E1B4B' : '#042F2E'} strokeWidth="14" strokeLinecap="round" />
            <circle cx="80" cy="242" r="8" fill={isBrand ? '#2563EB' : '#0891B2'} />

            <path d="M220 175 L240 240" stroke={isBrand ? '#1E1B4B' : '#042F2E'} strokeWidth="14" strokeLinecap="round" />
            <circle cx="240" cy="242" r="8" fill={isBrand ? '#2563EB' : '#0891B2'} />
          </g>
        )}
      </svg>

      {/* 9. Floating Status Indicator Pill */}
      <div className="absolute -bottom-2 px-3 py-1 rounded-full bg-[#0E0E14]/90 border border-white/10 backdrop-blur-md shadow-lg flex items-center gap-2">
        <span 
          className={`w-2 h-2 rounded-full ${
            isError 
              ? 'bg-rose-500 animate-ping' 
              : isSuccess 
              ? 'bg-emerald-400' 
              : isPasswordFocused 
              ? 'bg-amber-400' 
              : isBrand 
              ? 'bg-blue-400 animate-pulse' 
              : 'bg-cyan-400 animate-pulse'
          }`} 
        />
        <span className="text-[11px] font-mono text-[#D4D4D8]">
          {isPasswordFocused 
            ? 'Privacy Mode: Eyes Shielded' 
            : isSubmitting 
            ? 'Encrypting Handshake...' 
            : isSuccess 
            ? 'Access Authorized' 
            : isError 
            ? 'Authentication Error' 
            : isBrand 
            ? 'GEZUX Brand Security' 
            : 'GEZUX Creator Security'}
        </span>
      </div>

    </div>
  );
};
