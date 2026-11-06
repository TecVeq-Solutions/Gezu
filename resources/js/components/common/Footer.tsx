import React from 'react';

interface FooterProps {
  onNavigate: (view: 'home' | 'marketplace' | 'clipper' | 'brand' | 'for-brands' | 'for-clippers' | 'faq') => void;
  onOpenLegal: (type: 'terms' | 'privacy' | 'refund') => void;
}

export const Footer: React.FC<FooterProps> = ({ onNavigate, onOpenLegal }) => {
  return (
    <footer className="bg-gray-50 dark:bg-[#070708] border-t border-black/[0.08] dark:border-white/[0.08] pt-16 pb-12 text-xs text-gray-600 dark:text-[#A1A1AA]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div className="grid grid-cols-2 md:grid-cols-5 gap-8 mb-12">
          
          {/* Brand Col */}
          <div className="col-span-2 space-y-4">
            <div className="flex items-center gap-3 cursor-pointer" onClick={() => onNavigate('home')}>
              <div className="w-8 h-8 rounded-lg bg-black border border-white/20 flex items-center justify-center p-1 shadow-sm">
                <img 
                  src="/images/gezux-favicon.jpg" 
                  alt="GEZUX Logo" 
                  className="w-full h-full object-contain invert"
                />
              </div>
              <span className="font-display font-bold text-lg tracking-wider text-gray-900 dark:text-white">
                GEZUX
              </span>
            </div>
            <p className="text-xs text-gray-500 dark:text-[#71717A] max-w-sm leading-relaxed">
              The global performance marketplace for video distribution. Content is the asset, creators are the distribution network, and verified views are the measurable outcome.
            </p>
            <div className="flex items-center gap-2 pt-2">
              {['TikTok', 'Instagram', 'YouTube', 'X', 'LinkedIn'].map((platform, i) => (
                <span 
                  key={i} 
                  className="px-2.5 py-1 rounded bg-white dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 text-[11px] font-mono text-gray-700 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white hover:border-violet-500/40 cursor-pointer transition-colors shadow-xs"
                >
                  {platform}
                </span>
              ))}
            </div>
          </div>

          {/* PLATFORM */}
          <div className="space-y-3">
            <span className="text-[11px] font-mono uppercase tracking-widest text-gray-900 dark:text-[#F7F7F5] font-bold block">
              Platform
            </span>
            <ul className="space-y-2">
              <li>
                <button onClick={() => onNavigate('marketplace')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  Campaigns
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('home')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  How It Works
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('for-brands')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  For Brands
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('for-clippers')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  For Clippers
                </button>
              </li>
            </ul>
          </div>

          {/* COMPANY & RESOURCES */}
          <div className="space-y-3">
            <span className="text-[11px] font-mono uppercase tracking-widest text-gray-900 dark:text-[#F7F7F5] font-bold block">
              Resources
            </span>
            <ul className="space-y-2">
              <li>
                <button onClick={() => onNavigate('for-clippers')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  Creator Guidelines
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('for-brands')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  Campaign Guidelines
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('faq')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  Help & FAQs
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('faq')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  Verification Rules
                </button>
              </li>
            </ul>
          </div>

          {/* LEGAL */}
          <div className="space-y-3">
            <span className="text-[11px] font-mono uppercase tracking-widest text-gray-900 dark:text-[#F7F7F5] font-bold block">
              Legal
            </span>
            <ul className="space-y-2">
              <li>
                <button onClick={() => onOpenLegal('terms')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  Terms of Service
                </button>
              </li>
              <li>
                <button onClick={() => onOpenLegal('privacy')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  Privacy Policy
                </button>
              </li>
              <li>
                <button onClick={() => onOpenLegal('refund')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  Refund Eligibility
                </button>
              </li>
              <li>
                <button onClick={() => onOpenLegal('terms')} className="hover:text-black dark:hover:text-white transition-colors cursor-pointer">
                  Campaign Rules
                </button>
              </li>
            </ul>
          </div>

        </div>

        {/* Bottom Bar with qualified financial disclosures */}
        <div className="pt-8 border-t border-gray-200 dark:border-white/[0.08] flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] text-gray-500 dark:text-[#71717A]">
          <div>
            © 2026 GEZUX. All rights reserved. Built for performance-driven creator distribution.
          </div>
          <div className="font-mono text-[10px] text-right">
            Reward rates are determined by campaign rules & eligible verified views.
          </div>
        </div>

      </div>
    </footer>
  );
};
