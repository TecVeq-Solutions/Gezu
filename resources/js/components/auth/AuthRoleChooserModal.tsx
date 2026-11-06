import React, { useEffect, useRef } from 'react';
import { X, Building2, Video, ArrowRight, Sparkles } from 'lucide-react';
import { CursorRingField } from '../common/CursorRingField';

interface AuthRoleChooserModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSelectRole: (role: 'brand' | 'clipper') => void;
}

export const AuthRoleChooserModal: React.FC<AuthRoleChooserModalProps> = ({
  isOpen,
  onClose,
  onSelectRole,
}) => {
  const modalRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape' && isOpen) {
        onClose();
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen, onClose]);

  if (!isOpen) return null;

  return (
    <div 
      className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 dark:bg-black/80 backdrop-blur-md animate-in fade-in duration-200"
      onClick={(e) => {
        if (e.target === e.currentTarget) onClose();
      }}
      role="dialog"
      aria-modal="true"
      aria-labelledby="role-chooser-title"
    >
      <div 
        ref={modalRef}
        className="relative w-full max-w-lg bg-white dark:bg-[#0E0E14] border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl p-6 md:p-8 text-gray-900 dark:text-[#F7F7F5] overflow-hidden"
      >
        {/* Subtle Interactive WebGL Particle Field */}
        <div className="absolute inset-0 pointer-events-none opacity-20 dark:opacity-30 z-0">
          <CursorRingField 
            background="transparent"
            colors={['#7C3AED', '#3B82F6', '#06B6D4']}
            density={160}
            dotSize={85}
            speed={4}
            cameraDistance={150}
            ring={{ radius: 12, width: 7, push: 35, turbulence: 70 }}
          />
        </div>

        {/* Subtle Ambient Background Glow */}
        <div className="absolute top-0 right-0 w-64 h-64 bg-violet-600/10 rounded-full blur-3xl pointer-events-none" />
        <div className="absolute bottom-0 left-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl pointer-events-none" />

        {/* Close Button */}
        <button
          onClick={onClose}
          className="absolute top-5 right-5 p-2 rounded-xl text-gray-400 hover:text-gray-900 dark:text-[#A1A1AA] dark:hover:text-white bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 border border-gray-200 dark:border-white/5 transition-all focus:outline-none focus:ring-2 focus:ring-violet-500 cursor-pointer"
          aria-label="Close dialog"
        >
          <X className="w-4 h-4" />
        </button>

        {/* Modal Header */}
        <div className="text-center mb-7 relative z-10">
          <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-violet-100 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/20 text-violet-700 dark:text-violet-400 text-xs font-semibold mb-3">
            <Sparkles className="w-3.5 h-3.5" />
            <span>GEZUX Access Portal</span>
          </div>
          <h2 id="role-chooser-title" className="text-2xl font-display font-bold text-gray-950 dark:text-white tracking-tight">
            Sign in to GEZUX
          </h2>
          <p className="text-sm text-gray-500 dark:text-[#A1A1AA] mt-1">
            Choose how you're signing in.
          </p>
        </div>

        {/* Role Cards Grid */}
        <div className="grid grid-cols-1 gap-4 relative z-10">
          
          {/* 1. Brand Option */}
          <div 
            onClick={() => {
              onSelectRole('brand');
              onClose();
            }}
            className="group relative flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 rounded-xl bg-gray-50 dark:bg-[#14141E] border border-gray-200 dark:border-white/10 hover:border-blue-500/50 hover:bg-blue-50/50 dark:hover:bg-[#181826] transition-all cursor-pointer shadow-sm dark:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            tabIndex={0}
            role="button"
            onKeyDown={(e) => {
              if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                onSelectRole('brand');
                onClose();
              }
            }}
          >
            <div className="flex items-start gap-3.5 mb-3 sm:mb-0">
              <div className="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 flex items-center justify-center shrink-0 text-blue-600 dark:text-blue-400 group-hover:scale-105 transition-transform">
                <Building2 className="w-5 h-5" />
              </div>
              <div>
                <div className="flex items-center gap-2">
                  <h3 className="font-semibold text-gray-950 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-300 transition-colors">Brand</h3>
                  <span className="text-[10px] uppercase font-mono px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20">Advertiser</span>
                </div>
                <p className="text-xs text-gray-500 dark:text-[#A1A1AA] mt-1 max-w-[280px]">
                  Launch campaigns, manage content, and track your distribution.
                </p>
              </div>
            </div>
            <button 
              className="w-full sm:w-auto px-3.5 py-2 rounded-lg bg-blue-600/10 dark:bg-blue-600/20 group-hover:bg-blue-600 text-blue-700 dark:text-blue-300 group-hover:text-white text-xs font-semibold flex items-center justify-center gap-1.5 transition-all shrink-0"
              tabIndex={-1}
            >
              <span>Continue as Brand</span>
              <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
            </button>
          </div>

          {/* 2. Clipper Option */}
          <div 
            onClick={() => {
              onSelectRole('clipper');
              onClose();
            }}
            className="group relative flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 rounded-xl bg-gray-50 dark:bg-[#14141E] border border-gray-200 dark:border-white/10 hover:border-violet-500/50 hover:bg-violet-50/50 dark:hover:bg-[#181826] transition-all cursor-pointer shadow-sm dark:shadow-md focus:outline-none focus:ring-2 focus:ring-violet-500"
            tabIndex={0}
            role="button"
            onKeyDown={(e) => {
              if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                onSelectRole('clipper');
                onClose();
              }
            }}
          >
            <div className="flex items-start gap-3.5 mb-3 sm:mb-0">
              <div className="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/20 flex items-center justify-center shrink-0 text-violet-600 dark:text-violet-400 group-hover:scale-105 transition-transform">
                <Video className="w-5 h-5" />
              </div>
              <div>
                <div className="flex items-center gap-2">
                  <h3 className="font-semibold text-gray-950 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-300 transition-colors">Clipper</h3>
                  <span className="text-[10px] uppercase font-mono px-2 py-0.5 rounded bg-violet-100 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-500/20">Creator</span>
                </div>
                <p className="text-xs text-gray-500 dark:text-[#A1A1AA] mt-1 max-w-[280px]">
                  Create clips, join campaigns, and earn from verified performance.
                </p>
              </div>
            </div>
            <button 
              className="w-full sm:w-auto px-3.5 py-2 rounded-lg bg-violet-600/10 dark:bg-violet-600/20 group-hover:bg-violet-600 text-violet-700 dark:text-violet-300 group-hover:text-white text-xs font-semibold flex items-center justify-center gap-1.5 transition-all shrink-0"
              tabIndex={-1}
            >
              <span>Continue as Clipper</span>
              <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
            </button>
          </div>

        </div>

        {/* Footer info */}
        <div className="mt-6 pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-center text-center relative z-10">
          <p className="text-xs text-gray-500 dark:text-[#71717A]">
            Need a new account?{' '}
            <a 
              href="/register" 
              onClick={(e) => {
                e.preventDefault();
                onClose();
                if (typeof window !== 'undefined') {
                  window.history.pushState(null, '', '/register');
                  window.dispatchEvent(new PopStateEvent('popstate'));
                }
              }}
              className="text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300 font-semibold underline underline-offset-2"
            >
              Create Account
            </a>
          </p>
        </div>

      </div>
    </div>
  );
};
