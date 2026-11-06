import React from 'react';
import { Building2, Video, ArrowRight, Sparkles, CheckCircle2, ShieldCheck, Zap } from 'lucide-react';
import { StarfieldButton } from '../common/StarfieldButton';
import { CursorRingField } from '../common/CursorRingField';

interface RegisterRolePageProps {
  onSelectBrandRegister: () => void;
  onSelectClipperRegister: () => void;
  onOpenSignInModal: () => void;
  onNavigateHome: () => void;
}

export const RegisterRolePage: React.FC<RegisterRolePageProps> = ({
  onSelectBrandRegister,
  onSelectClipperRegister,
  onOpenSignInModal,
  onNavigateHome,
}) => {
  return (
    <div className="min-h-screen bg-[#070708] text-[#F7F7F5] selection:bg-violet-600 selection:text-white flex flex-col justify-between relative overflow-hidden py-12 px-4 sm:px-6 lg:px-8">
      {/* Interactive WebGL Cursor Ring Field Animation */}
      <div className="absolute inset-0 z-0 pointer-events-none opacity-45">
        <CursorRingField 
          background="#070708"
          colors={['#7C3AED', '#3B82F6', '#06B6D4', '#0B0B18']}
          density={240}
          dotSize={100}
          speed={5}
          cameraDistance={150}
          ring={{ radius: 14, width: 8, push: 45, turbulence: 85 }}
        />
      </div>

      {/* Background Aesthetic Gradients */}
      <div className="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[650px] h-[450px] bg-violet-600/10 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute top-1/3 left-1/4 w-[350px] h-[350px] bg-blue-600/10 rounded-full blur-[100px] pointer-events-none" />
      <div className="absolute bottom-1/4 right-1/4 w-[350px] h-[350px] bg-cyan-600/10 rounded-full blur-[100px] pointer-events-none" />

      {/* Top Navbar Brand Header */}
      <div className="max-w-5xl mx-auto w-full flex items-center justify-between z-10">
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
          <span className="font-display font-bold text-2xl tracking-wider text-white">
            GEZUX
          </span>
        </div>

        <button
          onClick={onOpenSignInModal}
          className="px-4 py-2 rounded-xl text-xs font-semibold text-[#D4D4D8] hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 transition-all cursor-pointer"
        >
          Sign In
        </button>
      </div>

      {/* Main Content Area */}
      <div className="max-w-4xl mx-auto w-full my-auto py-8 z-10">
        <div className="text-center mb-10 sm:mb-12">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-violet-500/10 border border-violet-500/20 text-violet-400 text-xs font-semibold mb-4 shadow-sm">
            <Sparkles className="w-3.5 h-3.5" />
            <span>Join the Video Clipping Revolution</span>
          </div>
          <h1 className="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white tracking-tight">
            Create your GEZUX account
          </h1>
          <p className="text-base sm:text-lg text-[#A1A1AA] mt-3 max-w-xl mx-auto">
            Choose how you want to use GEZUX.
          </p>
        </div>

        {/* Two Choice Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
          
          {/* Card 1: Brand */}
          <div 
            onClick={onSelectBrandRegister}
            className="group relative flex flex-col justify-between p-8 rounded-2xl bg-[#0E0E14] border border-white/10 hover:border-blue-500/60 hover:bg-[#12121E] transition-all duration-300 cursor-pointer shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1"
          >
            <div className="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/15 transition-all" />
            <div>
              <div className="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 mb-6 group-hover:scale-110 transition-transform shadow-inner">
                <Building2 className="w-7 h-7" />
              </div>
              <div className="flex items-center justify-between mb-2">
                <h2 className="text-2xl font-display font-bold text-white group-hover:text-blue-300 transition-colors">
                  Brand
                </h2>
                <span className="text-[11px] font-mono uppercase tracking-wider px-2.5 py-1 rounded-md bg-blue-500/10 text-blue-400 border border-blue-500/20">
                  Advertiser
                </span>
              </div>
              <p className="text-sm text-[#A1A1AA] leading-relaxed mb-6">
                Run campaigns and manage content distribution. Source viral clips at guaranteed performance rates.
              </p>

              <ul className="space-y-2.5 mb-8">
                <li className="flex items-center gap-2.5 text-xs text-[#D4D4D8]">
                  <CheckCircle2 className="w-4 h-4 text-blue-400 shrink-0" />
                  <span>Launch video clipping bounty campaigns</span>
                </li>
                <li className="flex items-center gap-2.5 text-xs text-[#D4D4D8]">
                  <CheckCircle2 className="w-4 h-4 text-blue-400 shrink-0" />
                  <span>Review & approve high-performing clips</span>
                </li>
                <li className="flex items-center gap-2.5 text-xs text-[#D4D4D8]">
                  <CheckCircle2 className="w-4 h-4 text-blue-400 shrink-0" />
                  <span>Real-time view telemetry & ROI telemetry</span>
                </li>
              </ul>
            </div>

            <button
              onClick={(e) => {
                e.stopPropagation();
                onSelectBrandRegister();
              }}
              className="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-lg shadow-blue-600/25 transition-all cursor-pointer"
            >
              <span>Create Brand Account</span>
              <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </button>
          </div>

          {/* Card 2: Clipper */}
          <div 
            onClick={onSelectClipperRegister}
            className="group relative flex flex-col justify-between p-8 rounded-2xl bg-[#0E0E14] border border-white/10 hover:border-violet-500/60 hover:bg-[#12121E] transition-all duration-300 cursor-pointer shadow-xl hover:shadow-violet-500/10 hover:-translate-y-1"
          >
            <div className="absolute top-0 right-0 w-32 h-32 bg-violet-500/5 rounded-full blur-2xl group-hover:bg-violet-500/15 transition-all" />
            <div>
              <div className="w-14 h-14 rounded-2xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center text-violet-400 mb-6 group-hover:scale-110 transition-transform shadow-inner">
                <Video className="w-7 h-7" />
              </div>
              <div className="flex items-center justify-between mb-2">
                <h2 className="text-2xl font-display font-bold text-white group-hover:text-violet-300 transition-colors">
                  Clipper
                </h2>
                <span className="text-[11px] font-mono uppercase tracking-wider px-2.5 py-1 rounded-md bg-violet-500/10 text-violet-400 border border-violet-500/20">
                  Creator
                </span>
              </div>
              <p className="text-sm text-[#A1A1AA] leading-relaxed mb-6">
                Create clips and earn from campaigns. Monetize short-form video content on TikTok, Reels, and Shorts.
              </p>

              <ul className="space-y-2.5 mb-8">
                <li className="flex items-center gap-2.5 text-xs text-[#D4D4D8]">
                  <CheckCircle2 className="w-4 h-4 text-violet-400 shrink-0" />
                  <span>Access top verified brand campaigns</span>
                </li>
                <li className="flex items-center gap-2.5 text-xs text-[#D4D4D8]">
                  <CheckCircle2 className="w-4 h-4 text-violet-400 shrink-0" />
                  <span>Earn competitive reward rates per 1,000 views</span>
                </li>
                <li className="flex items-center gap-2.5 text-xs text-[#D4D4D8]">
                  <CheckCircle2 className="w-4 h-4 text-violet-400 shrink-0" />
                  <span>Fast payouts via Stripe Connect marketplace</span>
                </li>
              </ul>
            </div>

            <button
              onClick={(e) => {
                e.stopPropagation();
                onSelectClipperRegister();
              }}
              className="w-full py-3 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-semibold text-sm flex items-center justify-center gap-2 shadow-lg shadow-violet-600/25 transition-all cursor-pointer"
            >
              <span>Create Clipper Account</span>
              <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </button>
          </div>

        </div>

        {/* Bottom Sign In Link */}
        <div className="mt-10 text-center">
          <p className="text-sm text-[#A1A1AA]">
            Already have an account?{' '}
            <button
              onClick={onOpenSignInModal}
              className="text-violet-400 hover:text-violet-300 font-semibold underline underline-offset-4 transition-colors cursor-pointer"
            >
              Sign in
            </button>
          </p>
        </div>

      </div>

      {/* Footer Security Badges */}
      <div className="max-w-5xl mx-auto w-full flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#71717A] pt-6 border-t border-white/5 z-10">
        <div className="flex items-center gap-4">
          <span className="flex items-center gap-1">
            <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
            Strict Role-Enforced Security
          </span>
          <span className="flex items-center gap-1">
            <Zap className="w-3.5 h-3.5 text-violet-400" />
            Stripe Connect Marketplace
          </span>
        </div>
        <div>
          © {new Date().getFullYear()} GEZUX Inc. All rights reserved.
        </div>
      </div>
    </div>
  );
};
