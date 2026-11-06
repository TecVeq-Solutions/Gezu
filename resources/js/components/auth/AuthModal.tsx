import React, { useState } from 'react';
import { UserRole } from '../../types';
import { X, Lock, Mail, User, Sparkles, Video, ArrowRight, Eye, EyeOff, ShieldCheck } from 'lucide-react';

interface AuthModalProps {
  isOpen: boolean;
  onClose: () => void;
  onLoginSuccess: (role: UserRole, email: string) => void;
}

export const AuthModal: React.FC<AuthModalProps> = ({
  isOpen,
  onClose,
  onLoginSuccess
}) => {
  const [mode, setMode] = useState<'signin' | 'signup'>('signin');
  const [email, setEmail] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [showPassword, setShowPassword] = useState<boolean>(false);
  const [selectedRole, setSelectedRole] = useState<'CLIPPER' | 'BRAND' | 'BOTH'>('CLIPPER');
  const [isPasswordFocused, setIsPasswordFocused] = useState<boolean>(false);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false);

  if (!isOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    setTimeout(() => {
      setIsSubmitting(false);
      const role: UserRole = selectedRole === 'BRAND' ? 'BRAND' : 'CLIPPER';
      onLoginSuccess(role, email || 'demo@gezux.com');
      onClose();
    }, 900);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-200">
      <div className="relative w-full max-w-md bg-[#0B0B10] rounded-2xl border border-white/15 p-6 sm:p-8 shadow-2xl overflow-hidden">
        
        {/* Glow */}
        <div className="absolute top-0 right-0 w-48 h-48 bg-violet-600/15 blur-[80px] rounded-full pointer-events-none" />

        {/* Close Button */}
        <button 
          onClick={onClose}
          className="absolute top-4 right-4 p-1.5 text-[#A1A1AA] hover:text-white rounded-lg hover:bg-white/5 transition-colors"
        >
          <X className="w-5 h-5" />
        </button>

        {/* Animated Avatar / Geometric Character (Section 1 in lines 4851-4890) */}
        <div className="flex flex-col items-center justify-center pt-2 pb-4">
          <div className="relative w-20 h-20 rounded-full bg-[#121218] border-2 border-violet-500/40 p-2 flex items-center justify-center shadow-lg shadow-violet-950/40 transition-all duration-300">
            
            {/* Geometric interactive SVG avatar */}
            <svg 
              className="w-full h-full text-violet-400" 
              viewBox="0 0 100 100" 
              fill="none" 
              xmlns="http://www.w3.org/2000/svg"
            >
              {/* Outer geometric shield */}
              <rect x="15" y="15" width="70" height="70" rx="18" stroke="currentColor" strokeWidth="4" className="text-white/20" />
              
              {/* Face background */}
              <circle cx="50" cy="50" r="28" fill="#18181F" />

              {/* Eyes */}
              {isPasswordFocused ? (
                // Hands covering eyes / Closed state during password entry!
                <>
                  <path d="M35 52 Q40 48 45 52" stroke="#A855F7" strokeWidth="4" strokeLinecap="round" />
                  <path d="M55 52 Q60 48 65 52" stroke="#A855F7" strokeWidth="4" strokeLinecap="round" />
                  {/* Hands covering */}
                  <circle cx="38" cy="52" r="7" fill="#7C3AED" />
                  <circle cx="62" cy="52" r="7" fill="#7C3AED" />
                </>
              ) : (
                // Open watching eyes tracking input
                <>
                  <circle cx="40" cy="48" r="5" fill="#F7F7F5" />
                  <circle cx="60" cy="48" r="5" fill="#F7F7F5" />
                  <circle cx="41" cy="49" r="2.5" fill="#7C3AED" className="animate-pulse" />
                  <circle cx="61" cy="49" r="2.5" fill="#7C3AED" className="animate-pulse" />
                  {/* Smile */}
                  <path d="M43 60 Q50 66 57 60" stroke="#F7F7F5" strokeWidth="3" strokeLinecap="round" />
                </>
              )}
            </svg>

            {/* Status indicator */}
            <span className="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-[#0B0B10]" />
          </div>

          <span className="font-display font-bold text-lg text-white mt-3">
            {mode === 'signin' ? 'Welcome back to GEZUX' : 'Create your GEZUX account'}
          </span>
          <p className="text-xs text-[#A1A1AA] text-center mt-0.5">
            {mode === 'signin' ? 'Sign in to access your creator studio or brand hub' : 'Join the global performance clipping marketplace'}
          </p>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit} className="space-y-4 text-xs">
          
          {/* Role selection if signing up */}
          {mode === 'signup' && (
            <div>
              <label className="text-[#D4D4D8] font-mono block mb-1.5">
                What are you here to do?
              </label>
              <div className="grid grid-cols-3 gap-2">
                <button
                  type="button"
                  onClick={() => setSelectedRole('CLIPPER')}
                  className={`p-2.5 rounded-xl border text-center transition-all flex flex-col items-center gap-1 ${selectedRole === 'CLIPPER' ? 'bg-violet-600/30 border-violet-500 text-white font-bold' : 'bg-[#121218] border-white/10 text-[#A1A1AA]'}`}
                >
                  <Video className="w-4 h-4 text-violet-400" />
                  <span className="text-[11px]">Clip Videos</span>
                </button>
                <button
                  type="button"
                  onClick={() => setSelectedRole('BRAND')}
                  className={`p-2.5 rounded-xl border text-center transition-all flex flex-col items-center gap-1 ${selectedRole === 'BRAND' ? 'bg-blue-600/30 border-blue-500 text-white font-bold' : 'bg-[#121218] border-white/10 text-[#A1A1AA]'}`}
                >
                  <Sparkles className="w-4 h-4 text-blue-400" />
                  <span className="text-[11px]">Fund Reach</span>
                </button>
                <button
                  type="button"
                  onClick={() => setSelectedRole('BOTH')}
                  className={`p-2.5 rounded-xl border text-center transition-all flex flex-col items-center gap-1 ${selectedRole === 'BOTH' ? 'bg-emerald-600/30 border-emerald-500 text-white font-bold' : 'bg-[#121218] border-white/10 text-[#A1A1AA]'}`}
                >
                  <ShieldCheck className="w-4 h-4 text-emerald-400" />
                  <span className="text-[11px]">Both</span>
                </button>
              </div>
            </div>
          )}

          {/* Email input */}
          <div>
            <label className="text-[#D4D4D8] font-mono block mb-1">Email Address:</label>
            <div className="relative">
              <input
                type="email"
                required
                placeholder="name@company.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="w-full bg-[#121218] border border-white/10 rounded-lg pl-9 pr-3 py-2.5 text-white focus:outline-none focus:border-violet-500"
              />
              <Mail className="w-4 h-4 text-[#71717A] absolute left-3 top-3" />
            </div>
          </div>

          {/* Password input (triggers avatar reaction) */}
          <div>
            <div className="flex justify-between items-center mb-1">
              <label className="text-[#D4D4D8] font-mono">Password:</label>
              {mode === 'signin' && (
                <button type="button" className="text-[11px] text-violet-400 hover:underline">
                  Forgot?
                </button>
              )}
            </div>
            <div className="relative">
              <input
                type={showPassword ? 'text' : 'password'}
                required
                placeholder="••••••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                onFocus={() => setIsPasswordFocused(true)}
                onBlur={() => setIsPasswordFocused(false)}
                className="w-full bg-[#121218] border border-white/10 rounded-lg pl-9 pr-9 py-2.5 text-white focus:outline-none focus:border-violet-500"
              />
              <Lock className="w-4 h-4 text-[#71717A] absolute left-3 top-3" />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-3 top-3 text-[#71717A] hover:text-white"
              >
                {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
              </button>
            </div>
          </div>

          {/* Submit Button */}
          <button
            type="submit"
            disabled={isSubmitting}
            className="w-full py-3 rounded-xl font-semibold text-xs text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 shadow-lg shadow-violet-900/30 transition-all flex items-center justify-center gap-1.5 cursor-pointer mt-2"
          >
            {isSubmitting ? (
              <span>Authenticating...</span>
            ) : mode === 'signin' ? (
              <>
                Sign In
                <ArrowRight className="w-4 h-4" />
              </>
            ) : (
              <>
                Create Free Account
                <ArrowRight className="w-4 h-4" />
              </>
            )}
          </button>

          {/* Mode Switcher */}
          <div className="text-center pt-2">
            {mode === 'signin' ? (
              <p className="text-[#71717A] text-[11px]">
                Don't have an account?{' '}
                <button
                  type="button"
                  onClick={() => setMode('signup')}
                  className="text-violet-400 hover:underline font-semibold"
                >
                  Sign up now
                </button>
              </p>
            ) : (
              <p className="text-[#71717A] text-[11px]">
                Already have an account?{' '}
                <button
                  type="button"
                  onClick={() => setMode('signin')}
                  className="text-violet-400 hover:underline font-semibold"
                >
                  Sign in
                </button>
              </p>
            )}
          </div>

        </form>

      </div>
    </div>
  );
};
