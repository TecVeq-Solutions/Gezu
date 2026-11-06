import React, { useState, useEffect } from 'react';
import { UserRole } from '../../types';
import { Sparkles, ArrowRight, ShieldCheck, Video, Calculator, Menu, X, Sun, Moon, LogIn, User, ChevronDown, Bell } from 'lucide-react';
import { StarfieldButton } from './StarfieldButton';

interface NavbarProps {
  currentRole: UserRole;
  setCurrentRole: (role: UserRole) => void;
  activeView: any;
  setActiveView: (view: any) => void;
  onOpenCalculator: () => void;
  onOpenNotifications: () => void;
  onOpenSignInModal: () => void;
  onNavigateToRegister: () => void;
  isDarkMode: boolean;
  onToggleTheme: () => void;
  currentUserEmail?: string;
}

export const Navbar: React.FC<NavbarProps> = ({
  currentRole,
  setCurrentRole,
  activeView,
  setActiveView,
  onOpenCalculator,
  onOpenNotifications,
  onOpenSignInModal,
  onNavigateToRegister,
  isDarkMode,
  onToggleTheme,
  currentUserEmail
}) => {
  const [scrolled, setScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [roleDropdownOpen, setRoleDropdownOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const handleRoleSelect = (role: UserRole) => {
    setCurrentRole(role);
    if (role === 'CLIPPER') setActiveView('clipper');
    else if (role === 'BRAND') setActiveView('brand');
    else if (role === 'ADMIN') setActiveView('admin');
    else setActiveView('home');
    setRoleDropdownOpen(false);
    setMobileMenuOpen(false);
  };

  const getRoleBadge = (role: UserRole) => {
    switch (role) {
      case 'CLIPPER':
        return { label: 'Clipper Studio', color: 'text-violet-400 bg-violet-500/10 border-violet-500/30' };
      case 'BRAND':
        return { label: 'Brand Hub', color: 'text-blue-400 bg-blue-500/10 border-blue-500/30' };
      case 'ADMIN':
        return { label: 'Admin Command', color: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30' };
      default:
        return { label: 'Visitor Mode', color: 'text-[#A1A1AA] bg-white/5 border-white/10' };
    }
  };

  const currentBadge = getRoleBadge(currentRole);

  return (
    <header className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${scrolled ? (isDarkMode ? 'bg-[#070708]/90 backdrop-blur-xl border-b border-white/[0.08] shadow-2xl py-3' : 'bg-[#FAFAFB]/90 backdrop-blur-xl border-b border-black/[0.08] shadow-lg py-3') : 'bg-transparent py-4'}`}>
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between gap-4">
          
          {/* Left: Brand Identity */}
          <div 
            onClick={() => setActiveView('home')}
            className="flex items-center gap-3 cursor-pointer group select-none shrink-0"
          >
            <div className="w-9 h-9 rounded-xl bg-black border border-white/20 flex items-center justify-center p-1.5 shadow-md transition-transform group-hover:scale-105">
              <img 
                src="/images/gezux-favicon.jpg" 
                alt="GEZUX Logo" 
                className="w-full h-full object-contain invert"
              />
            </div>
            <span className={`font-display font-bold text-xl tracking-wider ${isDarkMode ? 'text-[#F7F7F5]' : 'text-[#09090B]'}`}>
              GEZUX
            </span>
          </div>

          {/* Center: Desktop Navigation Links */}
          <nav className="hidden md:flex items-center gap-1 lg:gap-2 shrink-0">
            <button
              onClick={() => setActiveView('home')}
              className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer ${activeView === 'home' ? (isDarkMode ? 'text-white bg-white/[0.08]' : 'text-black bg-black/[0.06]') : (isDarkMode ? 'text-[#A1A1AA] hover:text-white' : 'text-[#71717A] hover:text-black')}`}
            >
              Overview
            </button>
            <button
              onClick={() => setActiveView('marketplace')}
              className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer ${activeView === 'marketplace' ? (isDarkMode ? 'text-white bg-white/[0.08]' : 'text-black bg-black/[0.06]') : (isDarkMode ? 'text-[#A1A1AA] hover:text-white' : 'text-[#71717A] hover:text-black')}`}
            >
              Campaigns
            </button>
            <button
              onClick={() => setActiveView('for-brands')}
              className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer ${activeView === 'for-brands' ? (isDarkMode ? 'text-blue-400 bg-blue-500/10' : 'text-blue-600 bg-blue-50') : (isDarkMode ? 'text-[#A1A1AA] hover:text-white' : 'text-[#71717A] hover:text-black')}`}
            >
              For Brands
            </button>
            <button
              onClick={() => setActiveView('for-clippers')}
              className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer ${activeView === 'for-clippers' ? (isDarkMode ? 'text-violet-400 bg-violet-500/10' : 'text-violet-600 bg-violet-50') : (isDarkMode ? 'text-[#A1A1AA] hover:text-white' : 'text-[#71717A] hover:text-black')}`}
            >
              For Clippers
            </button>
            <button
              onClick={() => setActiveView('faq')}
              className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer ${activeView === 'faq' ? (isDarkMode ? 'text-white bg-white/[0.08]' : 'text-black bg-black/[0.06]') : (isDarkMode ? 'text-[#A1A1AA] hover:text-white' : 'text-[#71717A] hover:text-black')}`}
            >
              FAQ
            </button>
            <button
              onClick={onOpenCalculator}
              className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer ${isDarkMode ? 'text-[#A1A1AA] hover:text-white' : 'text-[#71717A] hover:text-black'}`}
            >
              <Calculator className="w-3.5 h-3.5 text-violet-500" />
              Calculator
            </button>
          </nav>

          {/* Right: Desktop Action Controls */}
          <div className="hidden lg:flex items-center gap-2.5 shrink-0 whitespace-nowrap">
            
            {/* Notification Bell */}
            <button
              onClick={onOpenNotifications}
              title="Notifications"
              className={`p-2 rounded-xl border transition-all cursor-pointer relative ${isDarkMode ? 'bg-[#121218] border-white/10 text-[#D4D4D8] hover:text-white' : 'bg-white border-black/10 text-slate-700 hover:text-black'}`}
            >
              <Bell className="w-4 h-4" />
              <span className="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-violet-500 animate-pulse" />
            </button>

            {/* Theme Switcher Button */}
            <button
              onClick={onToggleTheme}
              title={isDarkMode ? "Switch to Light Theme" : "Switch to Dark Theme"}
              className={`p-2 rounded-xl border transition-all cursor-pointer ${isDarkMode ? 'bg-[#121218] border-white/10 text-amber-300 hover:text-amber-200' : 'bg-white border-black/10 text-violet-600 hover:bg-slate-50'}`}
            >
              {isDarkMode ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
            </button>

            {/* Auth Buttons / User Pill */}
            {currentUserEmail ? (
              <div className={`flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-mono ${isDarkMode ? 'bg-[#121218] border-white/10 text-white' : 'bg-white border-black/10 text-black'}`}>
                <User className="w-3.5 h-3.5 text-emerald-400" />
                <span className="truncate max-w-[110px]">{currentUserEmail}</span>
              </div>
            ) : (
              <>
                {/* Unified Sign In Button -> Opens Role Chooser Popup */}
                <button
                  onClick={onOpenSignInModal}
                  className={`px-3.5 py-1.5 text-xs font-semibold rounded-xl border transition-all flex items-center gap-1.5 cursor-pointer ${isDarkMode ? 'text-[#D4D4D8] hover:text-white bg-white/5 hover:bg-white/10 border-white/10' : 'text-slate-800 hover:text-black bg-slate-100 hover:bg-slate-200 border-black/10'}`}
                >
                  <LogIn className="w-3.5 h-3.5 text-violet-400" />
                  Sign In
                </button>

                {/* Unified Register Button -> Navigates to /register */}
                <button
                  onClick={onNavigateToRegister}
                  className={`px-3.5 py-1.5 text-xs font-semibold rounded-xl border transition-all flex items-center gap-1.5 cursor-pointer ${isDarkMode ? 'text-white bg-violet-600/20 hover:bg-violet-600/30 border-violet-500/30 text-violet-300' : 'text-violet-700 bg-violet-50 hover:bg-violet-100 border-violet-200'}`}
                >
                  <Sparkles className="w-3.5 h-3.5 text-violet-400" />
                  Register
                </button>
              </>
            )}

            {/* Primary Action Button */}
            <StarfieldButton
              size="sm"
              variant="primary"
              onClick={() => {
                setCurrentRole('BRAND');
                setActiveView('brand');
              }}
              icon={<ArrowRight className="w-3.5 h-3.5" />}
              iconPosition="right"
            >
              Launch Campaign
            </StarfieldButton>

          </div>

          {/* Mobile Right Controls: Theme + Menu Button */}
          <div className="lg:hidden flex items-center gap-2">
            <button
              onClick={onToggleTheme}
              className={`p-2 rounded-xl border transition-all ${isDarkMode ? 'bg-[#121218] border-white/10 text-amber-300' : 'bg-white border-black/10 text-violet-600'}`}
            >
              {isDarkMode ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
            </button>
            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className={`p-2 rounded-xl border transition-all ${isDarkMode ? 'bg-[#121218] border-white/10 text-white' : 'bg-white border-black/10 text-black'}`}
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </button>
          </div>

        </div>
      </div>

      {/* Neat & Clean Mobile Drawer */}
      {mobileMenuOpen && (
        <div className={`lg:hidden border-b transition-all animate-in slide-in-from-top-4 duration-200 px-5 pt-4 pb-6 space-y-4 shadow-2xl ${isDarkMode ? 'bg-[#0B0B10] border-white/10 text-white' : 'bg-white border-black/10 text-black'}`}>
          
          {/* Navigation Links */}
          <div className="space-y-1">
            <button
              onClick={() => { setActiveView('home'); setMobileMenuOpen(false); }}
              className={`w-full px-3 py-2 rounded-xl text-left text-xs font-semibold flex items-center justify-between ${activeView === 'home' ? 'bg-violet-600/15 text-violet-400 font-bold' : (isDarkMode ? 'text-[#D4D4D8] hover:bg-white/5' : 'text-slate-800 hover:bg-slate-100')}`}
            >
              <span>Overview</span>
              <span className="text-[10px] text-[#71717A]">Home</span>
            </button>

            <button
              onClick={() => { setActiveView('marketplace'); setMobileMenuOpen(false); }}
              className={`w-full px-3 py-2 rounded-xl text-left text-xs font-semibold flex items-center justify-between ${activeView === 'marketplace' ? 'bg-violet-600/15 text-violet-400 font-bold' : (isDarkMode ? 'text-[#D4D4D8] hover:bg-white/5' : 'text-slate-800 hover:bg-slate-100')}`}
            >
              <span>Campaigns</span>
              <span className="text-[10px] text-[#71717A]">Bounties</span>
            </button>

            <button
              onClick={() => { setActiveView('for-brands'); setMobileMenuOpen(false); }}
              className={`w-full px-3 py-2 rounded-xl text-left text-xs font-semibold flex items-center justify-between ${activeView === 'for-brands' ? 'bg-blue-600/15 text-blue-400 font-bold' : (isDarkMode ? 'text-[#D4D4D8] hover:bg-white/5' : 'text-slate-800 hover:bg-slate-100')}`}
            >
              <span>For Brands</span>
              <span className="text-[10px] text-[#71717A]">Sponsors</span>
            </button>

            <button
              onClick={() => { setActiveView('for-clippers'); setMobileMenuOpen(false); }}
              className={`w-full px-3 py-2 rounded-xl text-left text-xs font-semibold flex items-center justify-between ${activeView === 'for-clippers' ? 'bg-violet-600/15 text-violet-400 font-bold' : (isDarkMode ? 'text-[#D4D4D8] hover:bg-white/5' : 'text-slate-800 hover:bg-slate-100')}`}
            >
              <span>For Clippers</span>
              <span className="text-[10px] text-[#71717A]">Creators</span>
            </button>

            <button
              onClick={() => { setActiveView('faq'); setMobileMenuOpen(false); }}
              className={`w-full px-3 py-2 rounded-xl text-left text-xs font-semibold flex items-center justify-between ${activeView === 'faq' ? 'bg-white/10 text-white font-bold' : (isDarkMode ? 'text-[#D4D4D8] hover:bg-white/5' : 'text-slate-800 hover:bg-slate-100')}`}
            >
              <span>FAQ & Help</span>
              <span className="text-[10px] text-[#71717A]">Auditing</span>
            </button>

            <button
              onClick={() => { onOpenCalculator(); setMobileMenuOpen(false); }}
              className={`w-full px-3 py-2 rounded-xl text-left text-xs font-semibold flex items-center justify-between ${isDarkMode ? 'text-[#D4D4D8] hover:bg-white/5' : 'text-slate-800 hover:bg-slate-100'}`}
            >
              <span className="flex items-center gap-2"><Calculator className="w-3.5 h-3.5 text-violet-400" /> ROI Calculator</span>
              <span className="text-[10px] text-[#71717A]">Tool</span>
            </button>
          </div>

          {/* Auth & Primary CTA on Mobile */}
          <div className="pt-3 border-t border-white/[0.08] flex flex-col gap-2">
            {currentUserEmail ? (
              <div className="py-2.5 px-3 rounded-xl bg-white/5 border border-white/10 text-xs font-mono truncate text-center">
                {currentUserEmail}
              </div>
            ) : (
              <div className="flex items-center gap-2">
                <button
                  onClick={() => { 
                    setMobileMenuOpen(false); 
                    onOpenSignInModal();
                  }}
                  className={`flex-1 py-2.5 rounded-xl text-xs font-semibold border flex items-center justify-center gap-1.5 cursor-pointer ${isDarkMode ? 'bg-[#121218] border-white/10 text-white' : 'bg-slate-100 border-black/10 text-black'}`}
                >
                  <LogIn className="w-4 h-4 text-violet-400" />
                  Sign In
                </button>

                <button
                  onClick={() => {
                    setMobileMenuOpen(false);
                    onNavigateToRegister();
                  }}
                  className="flex-1 py-2.5 rounded-xl text-xs font-semibold text-white bg-violet-600 hover:bg-violet-500 text-center shadow-md cursor-pointer"
                >
                  Register
                </button>
              </div>
            )}

            <button
              onClick={() => {
                setCurrentRole('BRAND');
                setActiveView('brand');
                setMobileMenuOpen(false);
              }}
              className="w-full py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-violet-600 to-indigo-600 text-center shadow-lg shadow-violet-900/30 cursor-pointer"
            >
              Launch Campaign
            </button>
          </div>

        </div>
      )}
    </header>
  );
};
