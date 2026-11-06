import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { UserRole, Campaign, ClipSubmission, SocialAccount, Wallet, WithdrawalRequest, FraudFlag } from './types';
import { 
  INITIAL_CAMPAIGNS, 
  INITIAL_SUBMISSIONS, 
  INITIAL_SOCIAL_ACCOUNTS, 
  INITIAL_WALLET, 
  INITIAL_WITHDRAWALS, 
  INITIAL_FRAUD_FLAGS 
} from './lib/mockData';

import { Navbar } from './components/common/Navbar';
import { Footer } from './components/common/Footer';
import { Hero } from './components/marketing/Hero';
import { ActivityBar } from './components/marketing/ActivityBar';
import { HowItWorks } from './components/marketing/HowItWorks';
import { RoleSelector } from './components/marketing/RoleSelector';
import { CampaignMarketplacePreview } from './components/marketing/CampaignMarketplacePreview';
import { TrustSection } from './components/marketing/TrustSection';
import { FinalCTA } from './components/marketing/FinalCTA';
import { PricingCalculatorModal } from './components/marketing/PricingCalculatorModal';
import { SubmitClipModal } from './components/common/SubmitClipModal';
import { AuthPage, AuthMode } from './components/auth/AuthPage';
import { AuthRoleChooserModal } from './components/auth/AuthRoleChooserModal';
import { RegisterRolePage } from './components/auth/RegisterRolePage';
import { MarketplacePage } from './components/marketplace/MarketplacePage';
import { ClipperDashboard } from './components/clipper/ClipperDashboard';
import { BrandDashboard } from './components/brand/BrandDashboard';
import { AdminDashboard } from './components/admin/AdminDashboard';

import { ForBrandsPage } from './components/marketing/ForBrandsPage';
import { ForClippersPage } from './components/marketing/ForClippersPage';
import { FAQPage } from './components/marketing/FAQPage';
import { LegalModal } from './components/marketing/LegalModal';
import { NotificationDrawer } from './components/common/NotificationDrawer';

type ActiveViewType = 
  | 'home' 
  | 'marketplace' 
  | 'clipper' 
  | 'brand' 
  | 'admin' 
  | 'for-brands' 
  | 'for-clippers' 
  | 'faq' 
  | 'register' 
  | 'brand-login' 
  | 'brand-register' 
  | 'clipper-login' 
  | 'clipper-register' 
  | 'verify-email' 
  | 'forgot-password';

const App: React.FC = () => {
  const [currentRole, setCurrentRole] = useState<UserRole>('GUEST');
  const [currentUserEmail, setCurrentUserEmail] = useState<string | undefined>(undefined);
  const [isSignInModalOpen, setIsSignInModalOpen] = useState<boolean>(false);

  const resolvePathToView = (path: string): ActiveViewType => {
    const cleanPath = path.toLowerCase();
    if (cleanPath === '/admin' || cleanPath.startsWith('/admin')) return 'admin';
    if (cleanPath === '/brand/login') return 'brand-login';
    if (cleanPath === '/brand/register') return 'brand-register';
    if (cleanPath === '/clipper/login') return 'clipper-login';
    if (cleanPath === '/clipper/register') return 'clipper-register';
    if (cleanPath === '/register') return 'register';
    if (cleanPath === '/verify-email') return 'verify-email';
    if (cleanPath === '/forgot-password') return 'forgot-password';
    if (cleanPath === '/marketplace') return 'marketplace';
    if (cleanPath === '/for-brands') return 'for-brands';
    if (cleanPath === '/for-clippers') return 'for-clippers';
    if (cleanPath === '/faq') return 'faq';
    return 'home';
  };

  const [activeView, setActiveView] = useState<ActiveViewType>(() => {
    if (typeof window !== 'undefined') {
      const path = window.location.pathname;
      if (path === '/login') {
        return 'home';
      }
      return resolvePathToView(path);
    }
    return 'home';
  });

  const [isDarkMode, setIsDarkMode] = useState<boolean>(true);
  
  // Dynamic App State with LocalStorage Persistence
  const [campaigns, setCampaigns] = useState<Campaign[]>(() => {
    if (typeof window !== 'undefined') {
      const saved = localStorage.getItem('gezux_campaigns');
      if (saved) {
        try { return JSON.parse(saved); } catch (e) {}
      }
    }
    return INITIAL_CAMPAIGNS;
  });

  const [submissions, setSubmissions] = useState<ClipSubmission[]>(() => {
    if (typeof window !== 'undefined') {
      const saved = localStorage.getItem('gezux_submissions');
      if (saved) {
        try { return JSON.parse(saved); } catch (e) {}
      }
    }
    return INITIAL_SUBMISSIONS;
  });

  const [socialAccounts, setSocialAccounts] = useState<SocialAccount[]>(INITIAL_SOCIAL_ACCOUNTS);
  
  const [wallet, setWallet] = useState<Wallet>(() => {
    if (typeof window !== 'undefined') {
      const saved = localStorage.getItem('gezux_wallet');
      if (saved) {
        try { return JSON.parse(saved); } catch (e) {}
      }
    }
    return INITIAL_WALLET;
  });

  const [withdrawals, setWithdrawals] = useState<WithdrawalRequest[]>(() => {
    if (typeof window !== 'undefined') {
      const saved = localStorage.getItem('gezux_withdrawals');
      if (saved) {
        try { return JSON.parse(saved); } catch (e) {}
      }
    }
    return INITIAL_WITHDRAWALS;
  });

  const [fraudFlags, setFraudFlags] = useState<FraudFlag[]>(INITIAL_FRAUD_FLAGS);

  // Sync state changes to localStorage
  useEffect(() => {
    try { localStorage.setItem('gezux_campaigns', JSON.stringify(campaigns)); } catch (e) {}
  }, [campaigns]);

  useEffect(() => {
    try { localStorage.setItem('gezux_submissions', JSON.stringify(submissions)); } catch (e) {}
  }, [submissions]);

  useEffect(() => {
    try { localStorage.setItem('gezux_wallet', JSON.stringify(wallet)); } catch (e) {}
  }, [wallet]);

  useEffect(() => {
    try { localStorage.setItem('gezux_withdrawals', JSON.stringify(withdrawals)); } catch (e) {}
  }, [withdrawals]);

  // Modals & Drawers
  const [isCalculatorOpen, setIsCalculatorOpen] = useState<boolean>(false);
  const [isSubmitClipOpen, setIsSubmitClipOpen] = useState<boolean>(false);
  const [isNotificationOpen, setIsNotificationOpen] = useState<boolean>(false);
  const [legalModalType, setLegalModalType] = useState<'terms' | 'privacy' | 'refund' | null>(null);
  const [selectedCampForSubmission, setSelectedCampForSubmission] = useState<Campaign | null>(null);

  // Auto-open Sign In modal if landing directly on /login
  useEffect(() => {
    if (typeof window !== 'undefined' && window.location.pathname === '/login') {
      setIsSignInModalOpen(true);
      window.history.replaceState(null, '', '/');
    }
  }, []);

  // Fetch Live Database Campaigns
  useEffect(() => {
    const fetchLiveCampaigns = async () => {
      try {
        const res = await fetch('/api/campaigns');
        if (res.ok) {
          const json = await res.json();
          if (json.data && Array.isArray(json.data) && json.data.length > 0) {
            setCampaigns(prev => {
              // Merge live campaigns with user-created campaigns that aren't yet in db
              const dbIds = new Set(json.data.map((c: Campaign) => c.id));
              const userOnly = prev.filter(c => !dbIds.has(c.id));
              return [...userOnly, ...json.data];
            });
          }
        }
      } catch (err) {
        console.warn('Could not fetch live campaigns, using fallback data.', err);
      }
    };
    fetchLiveCampaigns();
  }, []);

  // Popstate history listener
  useEffect(() => {
    const handlePopState = () => {
      const path = window.location.pathname;
      if (path === '/login') {
        setIsSignInModalOpen(true);
        setActiveView('home');
      } else {
        setActiveView(resolvePathToView(path));
      }
    };
    window.addEventListener('popstate', handlePopState);
    return () => window.removeEventListener('popstate', handlePopState);
  }, []);

  // Theme effect
  useEffect(() => {
    const root = document.documentElement;
    if (isDarkMode) {
      root.classList.add('dark');
      root.classList.remove('light');
    } else {
      root.classList.remove('dark');
      root.classList.add('light');
    }
  }, [isDarkMode]);

  // Scroll to top on view change
  useEffect(() => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, [activeView]);

  // Handler: Handle submission from modal
  const handleClipSubmitted = (newSub: ClipSubmission) => {
    setSubmissions(prev => [newSub, ...prev]);
    
    // Update campaign metrics
    setCampaigns(prev => prev.map(c => {
      if (c.id === newSub.campaignId) {
        return {
          ...c,
          totalSubmissions: c.totalSubmissions + 1,
          verifiedViews: c.verifiedViews + newSub.eligibleViews
        };
      }
      return c;
    }));

    // Update wallet pending earnings
    setWallet(prev => ({
      ...prev,
      pendingBalance: prev.pendingBalance + newSub.estimatedReward,
      transactions: [
        {
          id: `tx-${Date.now().toString().slice(-4)}`,
          type: 'campaign_reward',
          amount: newSub.estimatedReward,
          currency: 'USD',
          status: 'pending',
          description: `View Telemetry Reward — ${newSub.campaignTitle}`,
          date: new Date().toISOString().replace('T', ' ').slice(0, 16),
          referenceId: newSub.id
        },
        ...prev.transactions
      ]
    }));
  };

  // Handler: Create new Campaign from Brand Wizard
  const handleCreateCampaign = (newCamp: Campaign) => {
    setCampaigns(prev => [newCamp, ...prev]);
  };

  // Handler: Request Withdrawal
  const handleRequestWithdrawal = (amount: number, method: string, destination: string) => {
    const newWd: WithdrawalRequest = {
      id: `wd-${Date.now().toString().slice(-4)}`,
      amount,
      currency: 'USD',
      method: method as any,
      destination,
      status: 'completed',
      requestedAt: new Date().toISOString().replace('T', ' ').slice(0, 16)
    };

    setWithdrawals(prev => [newWd, ...prev]);
    setWallet(prev => ({
      ...prev,
      availableBalance: prev.availableBalance - amount,
      totalWithdrawn: prev.totalWithdrawn + amount,
      transactions: [
        {
          id: `tx-${Date.now().toString().slice(-4)}`,
          type: 'withdrawal',
          amount: -amount,
          currency: 'USD',
          status: 'completed',
          description: `Payout Transfer via ${method} (${destination})`,
          date: new Date().toISOString().replace('T', ' ').slice(0, 16),
          referenceId: newWd.id
        },
        ...prev.transactions
      ]
    }));
  };

  // Handler: Clear Fraud Flag
  const handleClearFraudFlag = (id: string) => {
    setFraudFlags(prev => prev.filter(f => f.id !== id));
  };

  // Handler: Login Success
  const handleLoginSuccess = (role: UserRole, email: string, redirectUrl?: string) => {
    setCurrentRole(role);
    setCurrentUserEmail(email);
    if (redirectUrl) {
      window.location.href = redirectUrl;
      return;
    }
    if (role === 'CLIPPER') window.location.href = '/clipper';
    else if (role === 'BRAND') window.location.href = '/brand';
    else if (role === 'ADMIN') window.location.href = '/admin';
  };

  // Handle navigate view
  const handleNavigateView = (view: ActiveViewType) => {
    setActiveView(view);
    if (typeof window !== 'undefined') {
      const pathMap: Record<ActiveViewType, string> = {
        'home': '/',
        'marketplace': '/marketplace',
        'for-brands': '/for-brands',
        'for-clippers': '/for-clippers',
        'faq': '/faq',
        'register': '/register',
        'brand-login': '/brand/login',
        'brand-register': '/brand/register',
        'clipper-login': '/clipper/login',
        'clipper-register': '/clipper/register',
        'verify-email': '/verify-email',
        'forgot-password': '/forgot-password',
        'clipper': '/clipper',
        'brand': '/brand',
        'admin': '/admin',
      };
      const targetPath = pathMap[view] || '/';
      window.history.pushState(null, '', targetPath);
    }
  };

  // Handle Role Selection from Sign In Modal
  const handleSelectSignInRole = (role: 'brand' | 'clipper') => {
    setIsSignInModalOpen(false);
    if (role === 'brand') {
      handleNavigateView('brand-login');
    } else {
      handleNavigateView('clipper-login');
    }
  };

  const isAuthView = [
    'register',
    'brand-login',
    'brand-register',
    'clipper-login',
    'clipper-register',
    'verify-email',
    'forgot-password'
  ].includes(activeView);

  // If Admin View is active, render the dedicated Fullscreen Admin Control Center
  if (activeView === 'admin') {
    return (
      <div className="min-h-screen bg-[#070708] text-[#F7F7F5] selection:bg-violet-600 selection:text-white">
        <AdminDashboard
          campaigns={campaigns}
          submissions={submissions}
          fraudFlags={fraudFlags}
          withdrawals={withdrawals}
          onApproveWithdrawal={() => {}}
          onClearFraudFlag={handleClearFraudFlag}
          onExitAdmin={() => handleNavigateView('home')}
        />
      </div>
    );
  }

  return (
    <div className={`min-h-screen ${isDarkMode ? 'bg-[#070708] text-[#F7F7F5]' : 'bg-[#FAFAFB] text-[#09090B]'} selection:bg-violet-500 selection:text-white relative transition-colors duration-300`}>
      
      {/* Top Navbar (Rendered only on non-auth marketing and platform views) */}
      {!isAuthView && (
        <Navbar
          currentRole={currentRole}
          setCurrentRole={setCurrentRole}
          activeView={activeView}
          setActiveView={handleNavigateView}
          onOpenCalculator={() => setIsCalculatorOpen(true)}
          onOpenNotifications={() => setIsNotificationOpen(true)}
          onOpenSignInModal={() => setIsSignInModalOpen(true)}
          onNavigateToRegister={() => handleNavigateView('register')}
          isDarkMode={isDarkMode}
          onToggleTheme={() => setIsDarkMode(!isDarkMode)}
          currentUserEmail={currentUserEmail}
        />
      )}

      {/* Main View Router */}
      <main>
        {activeView === 'home' && (
          <>
            <Hero
              onExploreCampaigns={() => handleNavigateView('marketplace')}
              onLaunchCampaign={() => handleNavigateView('brand-login')}
              onOpenCalculator={() => setIsCalculatorOpen(true)}
            />
            <ActivityBar />
            <HowItWorks />
            <RoleSelector
              onSelectClipper={() => handleNavigateView('clipper-login')}
              onSelectBrand={() => handleNavigateView('brand-login')}
            />
            <CampaignMarketplacePreview
              campaigns={campaigns}
              onSelectCampaign={(camp) => {
                setSelectedCampForSubmission(camp);
                setIsSubmitClipOpen(true);
              }}
              onExploreAll={() => handleNavigateView('marketplace')}
            />
            <TrustSection />
            <FinalCTA
              onLaunchCampaign={() => handleNavigateView('brand-login')}
              onStartClipping={() => handleNavigateView('clipper-login')}
            />
          </>
        )}

        {activeView === 'marketplace' && (
          <MarketplacePage
            campaigns={campaigns}
            onSelectCampaignForSubmission={(camp) => {
              setSelectedCampForSubmission(camp);
              setIsSubmitClipOpen(true);
            }}
            onLaunchCampaign={() => handleNavigateView('brand-login')}
          />
        )}

        {activeView === 'for-brands' && (
          <ForBrandsPage
            onLaunchCampaign={() => handleNavigateView('brand-login')}
            onOpenCalculator={() => setIsCalculatorOpen(true)}
          />
        )}

        {activeView === 'for-clippers' && (
          <ForClippersPage
            onExploreCampaigns={() => handleNavigateView('marketplace')}
            onOpenAuth={() => setIsSignInModalOpen(true)}
          />
        )}

        {activeView === 'faq' && (
          <FAQPage
            onExploreCampaigns={() => handleNavigateView('marketplace')}
            onLaunchCampaign={() => handleNavigateView('brand-login')}
          />
        )}

        {activeView === 'clipper' && (
          <ClipperDashboard
            campaigns={campaigns}
            submissions={submissions}
            socialAccounts={socialAccounts}
            wallet={wallet}
            onOpenSubmitClip={() => {
              setSelectedCampForSubmission(null);
              setIsSubmitClipOpen(true);
            }}
            onRequestWithdrawal={handleRequestWithdrawal}
            onExploreMarketplace={() => handleNavigateView('marketplace')}
          />
        )}

        {/* UNIFIED REGISTER ROLE SELECTION PAGE (/register) */}
        {activeView === 'register' && (
          <RegisterRolePage
            onSelectBrandRegister={() => handleNavigateView('brand-register')}
            onSelectClipperRegister={() => handleNavigateView('clipper-register')}
            onOpenSignInModal={() => setIsSignInModalOpen(true)}
            onNavigateHome={() => handleNavigateView('home')}
          />
        )}

        {/* DEDICATED ANIMATED AUTHENTICATION EXPERIENCE (Brand Login/Register & Clipper Login/Register) */}
        {(activeView === 'brand-login' || 
          activeView === 'brand-register' || 
          activeView === 'clipper-login' || 
          activeView === 'clipper-register' || 
          activeView === 'verify-email' || 
          activeView === 'forgot-password') && (
          <AuthPage
            initialMode={activeView as AuthMode}
            onLoginSuccess={handleLoginSuccess}
            onNavigateHome={() => handleNavigateView('home')}
            onSwitchAuthMode={(mode) => handleNavigateView(mode as ActiveViewType)}
          />
        )}
      </main>

      {/* Footer (Rendered only on non-auth marketing views) */}
      {!isAuthView && (
        <Footer 
          onNavigate={(view) => handleNavigateView(view as any)} 
          onOpenLegal={(type) => setLegalModalType(type)}
        />
      )}

      {/* Global Sign In Role Chooser Modal */}
      <AuthRoleChooserModal
        isOpen={isSignInModalOpen}
        onClose={() => setIsSignInModalOpen(false)}
        onSelectRole={handleSelectSignInRole}
      />

      {/* Pricing ROI Calculator Modal */}
      <PricingCalculatorModal
        isOpen={isCalculatorOpen}
        onClose={() => setIsCalculatorOpen(false)}
        onLaunchCampaign={() => {
          setIsCalculatorOpen(false);
          handleNavigateView('brand-login');
        }}
      />

      {/* Submit Clip Modal */}
      <SubmitClipModal
        isOpen={isSubmitClipOpen}
        onClose={() => setIsSubmitClipOpen(false)}
        campaigns={campaigns}
        selectedCampaign={selectedCampForSubmission}
        onSubmit={handleClipSubmitted}
      />

      {/* Notifications Drawer */}
      <NotificationDrawer
        isOpen={isNotificationOpen}
        onClose={() => setIsNotificationOpen(false)}
      />

      {/* Legal & Policy Modals */}
      <LegalModal
        type={legalModalType}
        onClose={() => setLegalModalType(null)}
      />

    </div>
  );
};

// Mount root component
const container = document.getElementById('app');
if (container) {
  const root = createRoot(container);
  root.render(<App />);
}
