import React, { useState, useEffect } from 'react';
import { UserRole } from '../../types';
import { AuthCharacter } from './AuthCharacter';
import { CursorRingField } from '../common/CursorRingField';
import { 
  Lock, 
  Mail, 
  User as UserIcon, 
  Eye, 
  EyeOff, 
  ArrowRight, 
  ShieldCheck, 
  Sparkles, 
  Video, 
  Building2, 
  CheckCircle2, 
  AlertCircle,
  RefreshCw,
  ArrowLeft
} from 'lucide-react';

export type AuthMode = 'brand-login' | 'brand-register' | 'clipper-login' | 'clipper-register' | 'verify-email' | 'forgot-password';

interface AuthPageProps {
  initialMode: AuthMode;
  onLoginSuccess: (role: UserRole, email: string, redirectUrl?: string) => void;
  onNavigateHome: () => void;
  onSwitchAuthMode: (mode: AuthMode) => void;
}

export const AuthPage: React.FC<AuthPageProps> = ({
  initialMode,
  onLoginSuccess,
  onNavigateHome,
  onSwitchAuthMode,
}) => {
  const [mode, setMode] = useState<AuthMode>(initialMode);

  // Sync mode with props
  useEffect(() => {
    setMode(initialMode);
  }, [initialMode]);

  // Derived role context
  const isBrand = mode === 'brand-login' || mode === 'brand-register';
  const isRegister = mode === 'brand-register' || mode === 'clipper-register';
  const isLogin = mode === 'brand-login' || mode === 'clipper-login';

  // Public Configuration
  const [googleOAuthEnabled, setGoogleOAuthEnabled] = useState<boolean>(false);
  const [codeLength, setCodeLength] = useState<number>(6);

  // Form Fields
  const [name, setName] = useState<string>('');
  const [email, setEmail] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [passwordConfirmation, setPasswordConfirmation] = useState<string>('');
  const [rememberMe, setRememberMe] = useState<boolean>(true);
  const [otpCode, setOtpCode] = useState<string>('');

  // UI & Interaction States
  const [showPassword, setShowPassword] = useState<boolean>(false);
  const [showPasswordConfirm, setShowPasswordConfirm] = useState<boolean>(false);
  const [isEmailFocused, setIsEmailFocused] = useState<boolean>(false);
  const [isPasswordFocused, setIsPasswordFocused] = useState<boolean>(false);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  // Resend OTP Cooldown
  const [resendCooldown, setResendCooldown] = useState<number>(0);

  // Load public auth settings
  useEffect(() => {
    fetch('/auth/config', { credentials: 'same-origin' })
      .then(res => res.json())
      .then(data => {
        if (data) {
          setGoogleOAuthEnabled(!!data.google_oauth_enabled);
          if (data.code_length) setCodeLength(data.code_length);
        }
      })
      .catch(() => {});
  }, []);

  // Cooldown countdown timer
  useEffect(() => {
    if (resendCooldown > 0) {
      const timer = setTimeout(() => setResendCooldown(prev => prev - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [resendCooldown]);

  // Reset error when mode changes
  useEffect(() => {
    setErrorMessage(null);
    setSuccessMessage(null);
    setShowPassword(false);
    setShowPasswordConfirm(false);
  }, [mode]);

  // 1. Handle Sign In (Role-Protected)
  const handleSignIn = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMessage(null);
    setSuccessMessage(null);
    setIsSubmitting(true);

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const portalRole = isBrand ? 'brand' : 'clipper';

      const response = await fetch('/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
          email,
          password,
          remember: rememberMe,
          portal: portalRole,
          intended_role: portalRole,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || (data.errors ? Object.values(data.errors).flat()[0] as string : 'Sign in failed.'));
      }

      setSuccessMessage('Authentication verified. Redirecting to your dashboard...');
      const roleEnum: UserRole = data.user?.role?.toUpperCase() === 'BRAND' ? 'BRAND' : data.user?.role?.toUpperCase() === 'ADMIN' ? 'ADMIN' : 'CLIPPER';
      
      setTimeout(() => {
        onLoginSuccess(roleEnum, data.user?.email || email, data.redirect_url);
      }, 700);

    } catch (err: any) {
      setErrorMessage(err.message || 'Authentication error. Please check your credentials.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // 2. Handle Registration
  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMessage(null);
    setSuccessMessage(null);

    if (password !== passwordConfirmation) {
      setErrorMessage('Passwords do not match.');
      return;
    }

    setIsSubmitting(true);

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const roleToRegister = isBrand ? 'brand' : 'clipper';

      const response = await fetch('/auth/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
          name,
          email,
          role: roleToRegister,
          password,
          password_confirmation: passwordConfirmation,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || (data.errors ? Object.values(data.errors).flat()[0] as string : 'Registration failed.'));
      }

      setSuccessMessage('Account created! Please enter the 6-digit verification code sent to your email.');
      setResendCooldown(60);
      onSwitchAuthMode('verify-email');

    } catch (err: any) {
      setErrorMessage(err.message || 'Registration failed. Please review your details.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // 3. Handle Email Verification Code
  const handleVerifyOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMessage(null);
    setSuccessMessage(null);
    setIsSubmitting(true);

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/auth/verify-email', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
          email,
          code: otpCode,
        }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || (data.errors ? Object.values(data.errors).flat()[0] as string : 'Verification failed.'));
      }

      setSuccessMessage('Email verified successfully! Welcome to GEZUX.');
      const roleEnum: UserRole = isBrand ? 'BRAND' : 'CLIPPER';

      setTimeout(() => {
        onLoginSuccess(roleEnum, email, data.redirect_url);
      }, 700);

    } catch (err: any) {
      setErrorMessage(err.message || 'Invalid or expired verification code.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // 4. Handle Resend Verification Code
  const handleResendOtp = async () => {
    if (resendCooldown > 0 || !email) return;
    setErrorMessage(null);
    setSuccessMessage(null);
    setIsSubmitting(true);

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/auth/resend-verification', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ email }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to resend verification code.');
      }

      setSuccessMessage('A new verification code has been dispatched to your email.');
      setResendCooldown(60);

    } catch (err: any) {
      setErrorMessage(err.message || 'Failed to resend code.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // 5. Handle Forgot Password
  const handleForgotPassword = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMessage(null);
    setSuccessMessage(null);
    setIsSubmitting(true);

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/auth/forgot-password', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ email }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Password reset request failed.');
      }

      setSuccessMessage('If an account matches that email, a password recovery code has been sent.');

    } catch (err: any) {
      setErrorMessage(err.message || 'Password reset failed.');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="min-h-screen bg-[#070708] text-[#F7F7F5] selection:bg-violet-600 selection:text-white flex flex-col justify-between relative overflow-hidden py-8 px-4 sm:px-6 lg:px-8">
      
      {/* Interactive WebGL Cursor Ring Field Animation */}
      <div className="absolute inset-0 z-0 pointer-events-none opacity-45">
        <CursorRingField 
          background="#070708"
          colors={isBrand ? ['#3B82F6', '#60A5FA', '#1D4ED8', '#0B0B18'] : ['#06B6D4', '#3B82F6', '#7C3AED', '#0B0B18']}
          density={220}
          dotSize={95}
          speed={5}
          cameraDistance={150}
          ring={{ radius: 14, width: 8, push: 40, turbulence: 80 }}
        />
      </div>

      {/* Background Ambient Glows */}
      <div className={`absolute top-1/4 left-1/3 w-[500px] h-[500px] rounded-full blur-[140px] pointer-events-none transition-colors duration-700 ${
        isBrand ? 'bg-blue-600/10' : 'bg-cyan-600/10'
      }`} />
      <div className="absolute bottom-10 right-1/3 w-[450px] h-[450px] bg-violet-600/10 rounded-full blur-[140px] pointer-events-none" />

      {/* Top Bar Header */}
      <div className="max-w-6xl mx-auto w-full flex items-center justify-between z-10">
        <div 
          onClick={onNavigateHome}
          className="flex items-center gap-3 cursor-pointer group select-none"
        >
          <div className="w-10 h-10 rounded-xl bg-black border border-white/20 flex items-center justify-center p-2 shadow-lg transition-transform group-hover:scale-105">
            <img 
              src="/images/gezux-favicon.jpg" 
              alt="GEZUX Logo" 
              className="w-full h-full object-contain invert"
            />
          </div>
          <div className="flex flex-col">
            <span className="font-display font-bold text-xl tracking-wider text-white">
              GEZUX
            </span>
            <span className={`text-[10px] font-mono uppercase tracking-widest ${isBrand ? 'text-blue-400' : 'text-cyan-400'}`}>
              {isBrand ? 'Brand Portal' : 'Clipper Studio'}
            </span>
          </div>
        </div>

        <button
          onClick={onNavigateHome}
          className="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-[#A1A1AA] hover:text-white bg-white/5 hover:bg-white/10 border border-white/5 transition-all cursor-pointer"
        >
          <ArrowLeft className="w-3.5 h-3.5" />
          <span>Back to Home</span>
        </button>
      </div>

      {/* Main Two-Column Animated Experience */}
      <div className="max-w-6xl mx-auto w-full my-auto py-8 z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
          
          {/* Left Column: Animated Character + Bag Environment */}
          <div className="lg:col-span-5 flex flex-col items-center justify-center text-center">
            <AuthCharacter
              role={isBrand ? 'brand' : 'clipper'}
              isPasswordFocused={isPasswordFocused}
              isEmailFocused={isEmailFocused}
              isSubmitting={isSubmitting}
              isSuccess={!!successMessage}
              isError={!!errorMessage}
              activeMode={isLogin ? 'signin' : isRegister ? 'signup' : mode === 'verify-email' ? 'verify-email' : 'forgot-password'}
            />

            <div className="mt-6 max-w-sm">
              <h3 className="text-lg font-display font-bold text-white">
                {isBrand ? 'GEZUX Enterprise Portal' : 'GEZUX Creator Network'}
              </h3>
              <p className="text-xs text-[#A1A1AA] mt-1 leading-relaxed">
                {isBrand 
                  ? 'Access real-time video telemetry, manage bounty budgets, and review creator clips with zero friction.' 
                  : 'Monetize short-form video clips, connect verified social accounts, and receive secure payouts.'}
              </p>
            </div>
          </div>

          {/* Right Column: Emerging Authentication Card */}
          <div className="lg:col-span-7 flex justify-center">
            <div className={`w-full max-w-md bg-[#0E0E14] border ${isBrand ? 'border-blue-500/20' : 'border-violet-500/20'} rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden backdrop-blur-xl animate-in fade-in zoom-in-95 duration-500`}>
              
              {/* Top Accent Line */}
              <div className={`absolute top-0 left-0 right-0 h-1 bg-gradient-to-r ${
                isBrand ? 'from-blue-500 via-indigo-500 to-violet-500' : 'from-violet-500 via-cyan-500 to-blue-500'
              }`} />

              {/* Header Title & Subtitle */}
              <div className="mb-6">
                <div className="flex items-center gap-2 mb-2">
                  <div className={`w-7 h-7 rounded-lg flex items-center justify-center ${
                    isBrand ? 'bg-blue-500/10 text-blue-400' : 'bg-violet-500/10 text-violet-400'
                  }`}>
                    {isBrand ? <Building2 className="w-4 h-4" /> : <Video className="w-4 h-4" />}
                  </div>
                  <span className={`text-xs font-mono font-bold uppercase tracking-wider ${
                    isBrand ? 'text-blue-400' : 'text-violet-400'
                  }`}>
                    {isBrand ? 'Brand Access' : 'Clipper Access'}
                  </span>
                </div>

                <h1 className="text-2xl font-display font-bold text-white tracking-tight">
                  {mode === 'brand-login' && 'Sign In to Brand Portal'}
                  {mode === 'clipper-login' && 'Sign In to Creator Studio'}
                  {mode === 'brand-register' && 'Create Brand Account'}
                  {mode === 'clipper-register' && 'Create Creator Account'}
                  {mode === 'verify-email' && 'Verify Email Address'}
                  {mode === 'forgot-password' && 'Reset Password'}
                </h1>
                
                <p className="text-xs text-[#A1A1AA] mt-1">
                  {mode === 'brand-login' && 'Enter your company credentials to access campaigns.'}
                  {mode === 'clipper-login' && 'Enter your creator credentials to access your clips & wallet.'}
                  {mode === 'brand-register' && 'Start launching viral video campaigns with verified clippers.'}
                  {mode === 'clipper-register' && 'Start monetizing your clipping skills across TikTok, Reels & Shorts.'}
                  {mode === 'verify-email' && `We've sent a 6-digit code to ${email || 'your email'}.`}
                  {mode === 'forgot-password' && 'Enter your email to receive password recovery instructions.'}
                </p>
              </div>

              {/* Alert Feedback Messages */}
              {errorMessage && (
                <div className="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-start gap-2.5 text-rose-300 text-xs animate-in fade-in duration-200">
                  <AlertCircle className="w-4 h-4 text-rose-400 shrink-0 mt-0.5" />
                  <span>{errorMessage}</span>
                </div>
              )}

              {successMessage && (
                <div className="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-start gap-2.5 text-emerald-300 text-xs animate-in fade-in duration-200">
                  <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" />
                  <span>{successMessage}</span>
                </div>
              )}

              {/* FORM 1: LOGIN (Brand or Clipper) */}
              {isLogin && (
                <form onSubmit={handleSignIn} className="space-y-4">
                  {/* Email Field */}
                  <div>
                    <label className="block text-xs font-medium text-[#D4D4D8] mb-1.5">
                      {isBrand ? 'Business Email' : 'Creator Email'}
                    </label>
                    <div className="relative">
                      <Mail className="w-4 h-4 text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                      <input
                        type="email"
                        required
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        onFocus={() => setIsEmailFocused(true)}
                        onBlur={() => setIsEmailFocused(false)}
                        placeholder={isBrand ? 'partner@company.com' : 'creator@email.com'}
                        className="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#14141E] border border-white/10 text-white text-sm placeholder-[#52525B] focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                      />
                    </div>
                  </div>

                  {/* Password Field */}
                  <div>
                    <div className="flex items-center justify-between mb-1.5">
                      <label className="text-xs font-medium text-[#D4D4D8]">
                        Password
                      </label>
                      <button
                        type="button"
                        onClick={() => onSwitchAuthMode('forgot-password')}
                        className="text-xs text-violet-400 hover:text-violet-300 transition-colors"
                      >
                        Forgot password?
                      </button>
                    </div>
                    <div className="relative">
                      <Lock className="w-4 h-4 text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                      <input
                        type={showPassword ? 'text' : 'password'}
                        required
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        onFocus={() => setIsPasswordFocused(true)}
                        onBlur={() => setIsPasswordFocused(false)}
                        placeholder="••••••••••••"
                        className="w-full pl-10 pr-11 py-2.5 rounded-xl bg-[#14141E] border border-white/10 text-white text-sm placeholder-[#52525B] focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                      />
                      <button
                        type="button"
                        onClick={() => setShowPassword(!showPassword)}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-[#71717A] hover:text-white p-1 transition-colors"
                        aria-label={showPassword ? "Hide password" : "Show password"}
                      >
                        {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                      </button>
                    </div>
                  </div>

                  {/* Remember Me Checkbox */}
                  <div className="flex items-center">
                    <input
                      id="remember-me"
                      type="checkbox"
                      checked={rememberMe}
                      onChange={(e) => setRememberMe(e.target.checked)}
                      className="w-4 h-4 rounded bg-[#14141E] border-white/10 text-violet-600 focus:ring-violet-500 focus:ring-offset-0"
                    />
                    <label htmlFor="remember-me" className="ml-2 text-xs text-[#A1A1AA]">
                      Remember this browser session
                    </label>
                  </div>

                  {/* Submit Button */}
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className={`w-full py-3 px-4 rounded-xl font-semibold text-sm text-white flex items-center justify-center gap-2 shadow-lg transition-all cursor-pointer ${
                      isBrand 
                        ? 'bg-blue-600 hover:bg-blue-500 shadow-blue-600/25' 
                        : 'bg-violet-600 hover:bg-violet-500 shadow-violet-600/25'
                    } ${isSubmitting ? 'opacity-70 cursor-not-allowed' : ''}`}
                  >
                    {isSubmitting ? (
                      <>
                        <RefreshCw className="w-4 h-4 animate-spin" />
                        <span>Signing in...</span>
                      </>
                    ) : (
                      <>
                        <span>{isBrand ? 'Sign In to Brand Portal' : 'Sign In to Creator Studio'}</span>
                        <ArrowRight className="w-4 h-4" />
                      </>
                    )}
                  </button>

                  {/* Optional Google Login */}
                  {googleOAuthEnabled && (
                    <div className="pt-2">
                      <div className="relative flex py-2 items-center">
                        <div className="flex-grow border-t border-white/10" />
                        <span className="flex-shrink mx-3 text-xs text-[#71717A]">or</span>
                        <div className="flex-grow border-t border-white/10" />
                      </div>
                      <a
                        href={`/auth/google/redirect?portal=${isBrand ? 'brand' : 'clipper'}`}
                        className="w-full py-2.5 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-[#D4D4D8] hover:text-white font-semibold text-xs flex items-center justify-center gap-2 transition-all"
                      >
                        <svg className="w-4 h-4" viewBox="0 0 24 24">
                          <path fill="#EA4335" d="M12 5c1.7 0 3 .6 4 1.5l3-3C17.2 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.4 9 5 12 5z" />
                          <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z" />
                          <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 12 0 14.5s.7 4.8 1.9 7.2l3.7-2.9z" />
                          <path fill="#34A853" d="M12 24c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.4-6.4-5.2L1.9 17C3.7 20.7 7.5 24 12 24z" />
                        </svg>
                        <span>Continue with Google</span>
                      </a>
                    </div>
                  )}

                  {/* Switch to Registration */}
                  <div className="pt-4 border-t border-white/5 text-center">
                    <p className="text-xs text-[#A1A1AA]">
                      {isBrand ? "Don't have a Brand account? " : "Don't have a Clipper account? "}
                      <button
                        type="button"
                        onClick={() => onSwitchAuthMode(isBrand ? 'brand-register' : 'clipper-register')}
                        className="text-violet-400 hover:text-violet-300 font-semibold underline underline-offset-2 transition-colors cursor-pointer"
                      >
                        {isBrand ? 'Create Brand account' : 'Create Clipper account'}
                      </button>
                    </p>
                  </div>
                </form>
              )}

              {/* FORM 2: REGISTRATION (Brand or Clipper) */}
              {isRegister && (
                <form onSubmit={handleRegister} className="space-y-4">
                  {/* Name Field */}
                  <div>
                    <label className="block text-xs font-medium text-[#D4D4D8] mb-1.5">
                      {isBrand ? 'Company / Brand Name' : 'Creator Display Name'}
                    </label>
                    <div className="relative">
                      {isBrand ? (
                        <Building2 className="w-4 h-4 text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                      ) : (
                        <UserIcon className="w-4 h-4 text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                      )}
                      <input
                        type="text"
                        required
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        placeholder={isBrand ? 'Apex Media Group' : 'Alex Rivers'}
                        className="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#14141E] border border-white/10 text-white text-sm placeholder-[#52525B] focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                      />
                    </div>
                  </div>

                  {/* Email Field */}
                  <div>
                    <label className="block text-xs font-medium text-[#D4D4D8] mb-1.5">
                      {isBrand ? 'Business Email Address' : 'Email Address'}
                    </label>
                    <div className="relative">
                      <Mail className="w-4 h-4 text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                      <input
                        type="email"
                        required
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        onFocus={() => setIsEmailFocused(true)}
                        onBlur={() => setIsEmailFocused(false)}
                        placeholder={isBrand ? 'partner@company.com' : 'creator@email.com'}
                        className="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#14141E] border border-white/10 text-white text-sm placeholder-[#52525B] focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                      />
                    </div>
                  </div>

                  {/* Password Field */}
                  <div>
                    <label className="block text-xs font-medium text-[#D4D4D8] mb-1.5">
                      Password (min 8 characters)
                    </label>
                    <div className="relative">
                      <Lock className="w-4 h-4 text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                      <input
                        type={showPassword ? 'text' : 'password'}
                        required
                        minLength={8}
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        onFocus={() => setIsPasswordFocused(true)}
                        onBlur={() => setIsPasswordFocused(false)}
                        placeholder="••••••••••••"
                        className="w-full pl-10 pr-11 py-2.5 rounded-xl bg-[#14141E] border border-white/10 text-white text-sm placeholder-[#52525B] focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                      />
                      <button
                        type="button"
                        onClick={() => setShowPassword(!showPassword)}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-[#71717A] hover:text-white p-1 transition-colors"
                        aria-label={showPassword ? "Hide password" : "Show password"}
                      >
                        {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                      </button>
                    </div>
                  </div>

                  {/* Confirm Password Field */}
                  <div>
                    <label className="block text-xs font-medium text-[#D4D4D8] mb-1.5">
                      Confirm Password
                    </label>
                    <div className="relative">
                      <Lock className="w-4 h-4 text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                      <input
                        type={showPasswordConfirm ? 'text' : 'password'}
                        required
                        minLength={8}
                        value={passwordConfirmation}
                        onChange={(e) => setPasswordConfirmation(e.target.value)}
                        onFocus={() => setIsPasswordFocused(true)}
                        onBlur={() => setIsPasswordFocused(false)}
                        placeholder="••••••••••••"
                        className="w-full pl-10 pr-11 py-2.5 rounded-xl bg-[#14141E] border border-white/10 text-white text-sm placeholder-[#52525B] focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                      />
                      <button
                        type="button"
                        onClick={() => setShowPasswordConfirm(!showPasswordConfirm)}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-[#71717A] hover:text-white p-1 transition-colors"
                        aria-label={showPasswordConfirm ? "Hide password" : "Show password"}
                      >
                        {showPasswordConfirm ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                      </button>
                    </div>
                  </div>

                  {/* Submit Button */}
                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className={`w-full py-3 px-4 rounded-xl font-semibold text-sm text-white flex items-center justify-center gap-2 shadow-lg transition-all cursor-pointer ${
                      isBrand 
                        ? 'bg-blue-600 hover:bg-blue-500 shadow-blue-600/25' 
                        : 'bg-violet-600 hover:bg-violet-500 shadow-violet-600/25'
                    } ${isSubmitting ? 'opacity-70 cursor-not-allowed' : ''}`}
                  >
                    {isSubmitting ? (
                      <>
                        <RefreshCw className="w-4 h-4 animate-spin" />
                        <span>Creating account...</span>
                      </>
                    ) : (
                      <>
                        <span>{isBrand ? 'Create Brand Account' : 'Create Creator Account'}</span>
                        <ArrowRight className="w-4 h-4" />
                      </>
                    )}
                  </button>

                  {/* Switch to Login */}
                  <div className="pt-4 border-t border-white/5 text-center">
                    <p className="text-xs text-[#A1A1AA]">
                      {isBrand ? 'Already have a Brand account? ' : 'Already have a Clipper account? '}
                      <button
                        type="button"
                        onClick={() => onSwitchAuthMode(isBrand ? 'brand-login' : 'clipper-login')}
                        className="text-violet-400 hover:text-violet-300 font-semibold underline underline-offset-2 transition-colors cursor-pointer"
                      >
                        Sign in
                      </button>
                    </p>
                  </div>
                </form>
              )}

              {/* FORM 3: EMAIL VERIFICATION CODE */}
              {mode === 'verify-email' && (
                <form onSubmit={handleVerifyOtp} className="space-y-4">
                  <div>
                    <label className="block text-xs font-medium text-[#D4D4D8] mb-1.5">
                      6-Digit Security Code
                    </label>
                    <input
                      type="text"
                      maxLength={6}
                      required
                      value={otpCode}
                      onChange={(e) => setOtpCode(e.target.value.replace(/\D/g, ''))}
                      placeholder="123456"
                      className="w-full text-center tracking-[0.4em] font-mono text-xl py-3 rounded-xl bg-[#14141E] border border-white/10 text-white placeholder-[#52525B] focus:outline-none focus:ring-2 focus:ring-violet-500"
                    />
                  </div>

                  <button
                    type="submit"
                    disabled={isSubmitting || otpCode.length < 6}
                    className="w-full py-3 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-lg shadow-violet-600/25 transition-all cursor-pointer disabled:opacity-50"
                  >
                    {isSubmitting ? (
                      <>
                        <RefreshCw className="w-4 h-4 animate-spin" />
                        <span>Verifying...</span>
                      </>
                    ) : (
                      <>
                        <span>Confirm & Enter Dashboard</span>
                        <CheckCircle2 className="w-4 h-4" />
                      </>
                    )}
                  </button>

                  <div className="pt-3 flex items-center justify-between text-xs text-[#A1A1AA]">
                    <button
                      type="button"
                      onClick={() => onSwitchAuthMode(isBrand ? 'brand-login' : 'clipper-login')}
                      className="hover:text-white transition-colors"
                    >
                      ← Back to sign in
                    </button>
                    <button
                      type="button"
                      disabled={resendCooldown > 0 || isSubmitting}
                      onClick={handleResendOtp}
                      className="text-violet-400 hover:text-violet-300 font-semibold disabled:opacity-50"
                    >
                      {resendCooldown > 0 ? `Resend in ${resendCooldown}s` : 'Resend code'}
                    </button>
                  </div>
                </form>
              )}

              {/* FORM 4: FORGOT PASSWORD */}
              {mode === 'forgot-password' && (
                <form onSubmit={handleForgotPassword} className="space-y-4">
                  <div>
                    <label className="block text-xs font-medium text-[#D4D4D8] mb-1.5">
                      Account Email Address
                    </label>
                    <div className="relative">
                      <Mail className="w-4 h-4 text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                      <input
                        type="email"
                        required
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="your@email.com"
                        className="w-full pl-10 pr-4 py-2.5 rounded-xl bg-[#14141E] border border-white/10 text-white text-sm placeholder-[#52525B] focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                      />
                    </div>
                  </div>

                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="w-full py-3 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-lg shadow-violet-600/25 transition-all cursor-pointer"
                  >
                    {isSubmitting ? (
                      <>
                        <RefreshCw className="w-4 h-4 animate-spin" />
                        <span>Sending recovery code...</span>
                      </>
                    ) : (
                      <>
                        <span>Send Recovery Code</span>
                        <ArrowRight className="w-4 h-4" />
                      </>
                    )}
                  </button>

                  <div className="pt-3 text-center">
                    <button
                      type="button"
                      onClick={() => onSwitchAuthMode(isBrand ? 'brand-login' : 'clipper-login')}
                      className="text-xs text-violet-400 hover:text-violet-300 font-semibold transition-colors"
                    >
                      ← Back to sign in
                    </button>
                  </div>
                </form>
              )}

            </div>
          </div>

        </div>
      </div>

      {/* Footer Security Badges */}
      <div className="max-w-6xl mx-auto w-full flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#71717A] pt-4 border-t border-white/5 z-10">
        <div className="flex items-center gap-4">
          <span className="flex items-center gap-1">
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
            256-Bit TLS Encryption
          </span>
          <span className="flex items-center gap-1">
            <Sparkles className="w-3.5 h-3.5 text-violet-400" />
            Server-Side Role Guard
          </span>
        </div>
        <div>
          © {new Date().getFullYear()} GEZUX Inc. All rights reserved.
        </div>
      </div>

    </div>
  );
};
