import React, { useState, useEffect, useMemo } from 'react';
import { 
  Campaign, 
  ClipSubmission, 
  FraudFlag, 
  WithdrawalRequest, 
  UserRecord, 
  AuditLogEntry, 
  TelemetryRecord 
} from '../../types';
import { 
  INITIAL_USERS, 
  INITIAL_AUDIT_LOGS, 
  INITIAL_TELEMETRY_RECORDS 
} from '../../lib/mockData';
import { 
  ShieldCheck, 
  ShieldAlert, 
  CheckCircle2, 
  XCircle, 
  AlertTriangle, 
  Users, 
  DollarSign, 
  Eye, 
  Film, 
  Search, 
  Filter, 
  TrendingUp, 
  Activity, 
  Layers, 
  Database, 
  Settings, 
  Lock, 
  ExternalLink, 
  Check, 
  X, 
  RefreshCw, 
  ArrowUpRight, 
  Building2, 
  Video, 
  KeyRound, 
  UserCheck, 
  UserX, 
  Sliders, 
  Clock, 
  FileText, 
  CreditCard, 
  Shield, 
  ChevronRight, 
  BarChart3, 
  Radio, 
  ArrowLeft,
  ChevronDown,
  Terminal,
  Zap,
  Sparkles,
  Bug,
  Trash2,
  Copy,
  AlertCircle,
  Command as CommandIcon
} from 'lucide-react';
import { PerformanceAreaChart } from '../common/PerformanceAreaChart';
import { ChannelDistributionDonut } from '../common/ChannelDistributionDonut';

interface AdminDashboardProps {
  campaigns: Campaign[];
  submissions: ClipSubmission[];
  fraudFlags: FraudFlag[];
  withdrawals: WithdrawalRequest[];
  onApproveWithdrawal?: (id: string) => void;
  onClearFraudFlag?: (id: string) => void;
  onExitAdmin?: () => void;
}

interface ServerLogItem {
  id: string;
  timestamp: string;
  environment: string;
  level: string;
  message: string;
  stacktrace: string;
}

type AdminNavKey = 
  | 'overview_dashboard'
  | 'overview_command'
  | 'overview_activity'
  | 'people_users'
  | 'people_brands'
  | 'people_clippers'
  | 'people_staff'
  | 'people_verification'
  | 'mkt_campaigns'
  | 'mkt_reviews'
  | 'mkt_submissions'
  | 'verif_telemetry'
  | 'verif_suspicious'
  | 'verif_health'
  | 'fin_overview'
  | 'fin_withdrawals'
  | 'fin_ledger'
  | 'fin_fees'
  | 'fraud_dashboard'
  | 'fraud_queue'
  | 'sys_audit'
  | 'sys_error_logs'
  | 'sys_settings';

export const AdminDashboard: React.FC<AdminDashboardProps> = ({
  campaigns: initialCampaigns,
  submissions: initialSubmissions,
  fraudFlags: initialFraudFlags,
  withdrawals: initialWithdrawals,
  onExitAdmin
}) => {
  // Navigation State
  const [currentNav, setCurrentNav] = useState<AdminNavKey>('overview_dashboard');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [isCommandPaletteOpen, setIsCommandPaletteOpen] = useState<boolean>(false);

  // Local Mutable State for Admin Operations
  const [campaignsList, setCampaignsList] = useState<Campaign[]>(initialCampaigns);
  const [submissionsList, setSubmissionsList] = useState<ClipSubmission[]>(initialSubmissions);
  const [withdrawalsList, setWithdrawalsList] = useState<WithdrawalRequest[]>(initialWithdrawals);
  const [fraudFlagsList, setFraudFlagsList] = useState<FraudFlag[]>(initialFraudFlags);
  const [usersList, setUsersList] = useState<UserRecord[]>(INITIAL_USERS);
  const [auditLogsList, setAuditLogsList] = useState<AuditLogEntry[]>(INITIAL_AUDIT_LOGS);
  const [telemetryList, setTelemetryList] = useState<TelemetryRecord[]>(INITIAL_TELEMETRY_RECORDS);

  // Modals & Action States
  const [selectedWithdrawal, setSelectedWithdrawal] = useState<WithdrawalRequest | null>(null);
  const [selectedCampaignForReview, setSelectedCampaignForReview] = useState<Campaign | null>(null);
  const [selectedFraudFlag, setSelectedFraudFlag] = useState<FraudFlag | null>(null);
  const [selectedUserForEdit, setSelectedUserForEdit] = useState<UserRecord | null>(null);
  const [rejectionReason, setRejectionReason] = useState<string>('');
  const [actionSuccessToast, setActionSuccessToast] = useState<string | null>(null);

  // Platform Fee Configuration State
  const [brandDepositFee, setBrandDepositFee] = useState<number>(5.0);
  const [minWithdrawalThreshold, setMinWithdrawalThreshold] = useState<number>(20.0);
  const [autoApprovalThreshold, setAutoApprovalThreshold] = useState<number>(50.0);

  // Server Error Logs State
  const [serverLogs, setServerLogs] = useState<ServerLogItem[]>([]);
  const [selectedServerLog, setSelectedServerLog] = useState<ServerLogItem | null>(null);
  const [logFilterLevel, setLogFilterLevel] = useState<string>('ALL');
  const [logSearchTerm, setLogSearchTerm] = useState<string>('');
  const [isLogLoading, setIsLogLoading] = useState<boolean>(false);
  const [logFileSize, setLogFileSize] = useState<string>('0 KB');

  // Fetch Server Logs
  const fetchServerLogs = async () => {
    setIsLogLoading(true);
    try {
      const res = await fetch('/api/admin/logs');
      if (res.ok) {
        const json = await res.json();
        if (json.logs && Array.isArray(json.logs)) {
          setServerLogs(json.logs);
          setLogFileSize(json.file_size || '0 KB');
        }
      }
    } catch (err) {
      console.warn('Could not fetch server logs:', err);
    } finally {
      setIsLogLoading(false);
    }
  };

  useEffect(() => {
    fetchServerLogs();
  }, []);

  const handleClearLogs = async () => {
    if (!confirm('Are you sure you want to clear all server logs?')) return;
    try {
      const res = await fetch('/api/admin/logs/clear', { method: 'POST' });
      if (res.ok) {
        setServerLogs([]);
        setLogFileSize('0 KB');
        showToast('Application logs cleared.');
      }
    } catch (err) {
      showToast('Failed to clear logs.');
    }
  };

  // Toast Helper
  const showToast = (msg: string) => {
    setActionSuccessToast(msg);
    setTimeout(() => setActionSuccessToast(null), 4000);
  };

  // Keyboard shortcut Ctrl+K / Cmd+K
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        setIsCommandPaletteOpen(prev => !prev);
      }
      if (e.key === 'Escape') {
        setIsCommandPaletteOpen(false);
        setSelectedWithdrawal(null);
        setSelectedCampaignForReview(null);
        setSelectedFraudFlag(null);
        setSelectedUserForEdit(null);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  // Add Audit Entry Helper
  const logAdminAction = (action: string, targetType: string, targetId: string, details: string) => {
    const newLog: AuditLogEntry = {
      id: `aud-${Date.now().toString().slice(-4)}`,
      adminName: 'Sarah Chen (Admin)',
      adminEmail: 'sarah.admin@gezux.io',
      role: 'Super Admin',
      action,
      targetType,
      targetId,
      details,
      timestamp: new Date().toISOString().replace('T', ' ').slice(0, 19),
      ipAddress: '192.0.2.45'
    };
    setAuditLogsList(prev => [newLog, ...prev]);
  };

  // HANDLER: Approve Withdrawal
  const handleApproveWithdrawal = (wd: WithdrawalRequest) => {
    setWithdrawalsList(prev => prev.map(item => item.id === wd.id ? { ...item, status: 'completed' } : item));
    logAdminAction('PAYOUT_DISBURSED', 'WithdrawalRequest', wd.id, `Disbursed $${wd.amount.toFixed(2)} via ${wd.method} to ${wd.destination}`);
    setSelectedWithdrawal(null);
    showToast(`Payout of $${wd.amount.toFixed(2)} approved and dispatched.`);
  };

  // HANDLER: Reject Withdrawal
  const handleRejectWithdrawal = (wd: WithdrawalRequest) => {
    setWithdrawalsList(prev => prev.map(item => item.id === wd.id ? { ...item, status: 'rejected' } : item));
    logAdminAction('PAYOUT_REJECTED', 'WithdrawalRequest', wd.id, `Rejected payout request of $${wd.amount.toFixed(2)}: ${rejectionReason || 'Compliance failure'}`);
    setSelectedWithdrawal(null);
    setRejectionReason('');
    showToast(`Withdrawal request #${wd.id} marked as rejected.`);
  };

  // HANDLER: Approve Campaign
  const handleApproveCampaign = (camp: Campaign) => {
    setCampaignsList(prev => prev.map(c => c.id === camp.id ? { ...c, status: 'active' } : c));
    logAdminAction('CAMPAIGN_APPROVED', 'Campaign', camp.id, `Approved campaign "${camp.title}" with $${camp.totalBudget.toFixed(2)} pool`);
    setSelectedCampaignForReview(null);
    showToast(`Campaign "${camp.title}" is now LIVE.`);
  };

  // HANDLER: Reject Campaign
  const handleRejectCampaign = (camp: Campaign) => {
    setCampaignsList(prev => prev.map(c => c.id === camp.id ? { ...c, status: 'cancelled' } : c));
    logAdminAction('CAMPAIGN_REJECTED', 'Campaign', camp.id, `Rejected campaign "${camp.title}": ${rejectionReason || 'Violates brand guidelines'}`);
    setSelectedCampaignForReview(null);
    setRejectionReason('');
    showToast(`Campaign "${camp.title}" rejected and returned to brand.`);
  };

  // HANDLER: Clear Fraud Flag
  const handleClearFraudFlag = (flag: FraudFlag) => {
    setFraudFlagsList(prev => prev.filter(f => f.id !== flag.id));
    logAdminAction('FRAUD_FLAG_CLEARED', 'FraudFlag', flag.id, `Cleared false positive for ${flag.creatorHandle} on ${flag.campaignTitle}`);
    setSelectedFraudFlag(null);
    showToast(`Fraud flag on ${flag.creatorHandle} dismissed.`);
  };

  // HANDLER: Ban / Suspend from Fraud Flag
  const handleConfirmFraudAndSuspend = (flag: FraudFlag) => {
    setFraudFlagsList(prev => prev.map(f => f.id === flag.id ? { ...f, status: 'confirmed' } : f));
    setUsersList(prev => prev.map(u => u.username === flag.creatorHandle.replace('@', '') ? { ...u, status: 'suspended' } : u));
    logAdminAction('USER_SUSPENDED_FRAUD', 'User', flag.creatorHandle, `Suspended account for verified inorganic botting: ${flag.reason}`);
    setSelectedFraudFlag(null);
    showToast(`Account ${flag.creatorHandle} suspended and penalties applied.`);
  };

  // HANDLER: Toggle User Status
  const handleToggleUserStatus = (user: UserRecord) => {
    const nextStatus = user.status === 'active' ? 'suspended' : 'active';
    setUsersList(prev => prev.map(u => u.id === user.id ? { ...u, status: nextStatus } : u));
    logAdminAction(nextStatus === 'suspended' ? 'USER_SUSPENDED' : 'USER_REACTIVATED', 'User', user.id, `Updated status to ${nextStatus} for ${user.email}`);
    showToast(`User ${user.name} is now ${nextStatus}.`);
  };

  // HANDLER: Toggle User Verification
  const handleToggleUserVerification = (user: UserRecord) => {
    const nextVerif = !user.verified;
    setUsersList(prev => prev.map(u => u.id === user.id ? { ...u, verified: nextVerif } : u));
    logAdminAction(nextVerif ? 'USER_VERIFIED' : 'USER_UNVERIFIED', 'User', user.id, `Identity badge updated to ${nextVerif} for ${user.email}`);
    showToast(`User ${user.name} verification status toggled.`);
  };

  // Computed Metrics
  const pendingWithdrawalsCount = useMemo(() => withdrawalsList.filter(w => w.status === 'pending').length, [withdrawalsList]);
  const activeFraudCount = useMemo(() => fraudFlagsList.filter(f => f.status === 'under_review').length, [fraudFlagsList]);
  const pendingCampaignsCount = useMemo(() => campaignsList.filter(c => c.status === 'pending_review').length, [campaignsList]);
  const totalVerifiedViews = useMemo(() => campaignsList.reduce((acc, c) => acc + c.verifiedViews, 0), [campaignsList]);
  const totalFundedBudget = useMemo(() => campaignsList.reduce((acc, c) => acc + c.totalBudget, 0), [campaignsList]);

  // Filtered Users
  const filteredUsers = useMemo(() => {
    return usersList.filter(u => 
      u.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      u.email.toLowerCase().includes(searchQuery.toLowerCase()) ||
      u.username.toLowerCase().includes(searchQuery.toLowerCase())
    );
  }, [usersList, searchQuery]);

  return (
    <div className="relative min-h-screen bg-[#070708] text-[#F7F7F5] flex flex-col font-sans selection:bg-violet-600 selection:text-white overflow-hidden">
      {/* Ambient background glow orbs matching Homepage aesthetics */}
      <div className="absolute top-10 left-1/4 -translate-x-1/2 w-[700px] h-[350px] bg-violet-600/15 blur-[140px] rounded-full pointer-events-none" />
      <div className="absolute top-48 right-10 w-[550px] h-[320px] bg-blue-600/12 blur-[130px] rounded-full pointer-events-none" />
      <div className="absolute bottom-10 left-1/3 w-[600px] h-[300px] bg-emerald-500/10 blur-[120px] rounded-full pointer-events-none" />
      
      {/* Toast Notification */}
      {actionSuccessToast && (
        <div className="fixed bottom-6 right-6 z-50 bg-[#121218]/95 border border-emerald-500/40 text-emerald-400 px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-2.5 text-xs font-mono backdrop-blur-xl animate-in fade-in slide-in-from-bottom-3">
          <CheckCircle2 className="w-4 h-4 text-emerald-400" />
          <span>{actionSuccessToast}</span>
        </div>
      )}

      {/* ADMIN TOPBAR */}
      <header className="h-16 border-b border-white/[0.08] bg-[#0B0B10]/90 backdrop-blur-xl sticky top-0 z-40 px-4 sm:px-6 flex items-center justify-between gap-4 shadow-xl">
        {/* Left Branding */}
        <div className="flex items-center gap-3">
          <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-600 flex items-center justify-center font-black text-white text-sm shadow-lg shadow-violet-900/40 border border-violet-400/30">
            G
          </div>
          <div className="flex items-baseline gap-2">
            <span className="font-display font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-white via-[#F7F7F5] to-[#D4D4D8] text-base">GEZUX</span>
            <span className="text-[10px] font-mono uppercase px-2.5 py-0.5 rounded-full bg-violet-500/20 text-violet-300 border border-violet-500/30 font-bold">
              Admin Ops Center
            </span>
          </div>
        </div>

        {/* Global Search / Command Bar */}
        <div className="flex-1 max-w-md hidden md:block">
          <button
            onClick={() => setIsCommandPaletteOpen(true)}
            className="w-full flex items-center justify-between px-3.5 py-1.5 rounded-xl bg-[#121218] border border-white/[0.08] hover:border-white/20 text-xs text-[#A1A1AA] transition-all group"
          >
            <span className="flex items-center gap-2">
              <Search className="w-3.5 h-3.5 text-[#71717A] group-hover:text-white transition-colors" />
              <span>Search users, campaigns, payouts, flags...</span>
            </span>
            <kbd className="font-mono text-[10px] bg-white/10 px-1.5 py-0.5 rounded text-[#D4D4D8] border border-white/10">
              Ctrl+K
            </kbd>
          </button>
        </div>

        {/* Right Status & Profile Controls */}
        <div className="flex items-center gap-3">
          {/* Health Indicator */}
          <div className="hidden lg:flex items-center gap-2 text-[11px] font-mono text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-lg">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Cluster: Optimal</span>
          </div>

          {/* Quick Exit to Public Website */}
          {onExitAdmin && (
            <button
              onClick={onExitAdmin}
              className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/[0.05] hover:bg-white/[0.1] border border-white/[0.08] text-xs font-semibold text-[#D4D4D8] hover:text-white transition-all"
            >
              <ArrowLeft className="w-3.5 h-3.5" />
              <span>Exit to App</span>
            </button>
          )}

          {/* Admin Identity */}
          <div className="flex items-center gap-2 pl-2 border-l border-white/[0.08]">
            <img 
              src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=80&q=80" 
              alt="Admin" 
              className="w-7 h-7 rounded-full object-cover border border-white/20"
            />
            <div className="hidden sm:block text-left">
              <div className="text-xs font-bold text-white leading-tight">Sarah Chen</div>
              <div className="text-[10px] font-mono text-violet-400">Super Admin (Spatie)</div>
            </div>
          </div>
        </div>
      </header>

      {/* MAIN CONTAINER: SIDEBAR + CONTENT */}
      <div className="flex-1 flex overflow-hidden">
        
        {/* SIDEBAR NAVIGATION */}
        <aside className="w-64 bg-[#0B0B10] border-r border-white/[0.08] flex flex-col justify-between overflow-y-auto hidden md:flex shrink-0 p-3 space-y-6">
          <div className="space-y-6">
            
            {/* GROUP 1: OVERVIEW */}
            <div>
              <span className="text-[10px] font-mono uppercase tracking-wider text-[#71717A] px-3 font-semibold">
                Overview
              </span>
              <div className="mt-1.5 space-y-0.5">
                <button
                  onClick={() => setCurrentNav('overview_dashboard')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'overview_dashboard' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <BarChart3 className="w-4 h-4" />
                    <span>Dashboard</span>
                  </span>
                </button>

                <button
                  onClick={() => setCurrentNav('overview_command')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'overview_command' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Zap className="w-4 h-4 text-amber-400" />
                    <span>Command Center</span>
                  </span>
                  {(pendingWithdrawalsCount > 0 || activeFraudCount > 0) && (
                    <span className="text-[10px] font-mono px-1.5 py-0.2 rounded bg-rose-500/20 text-rose-400 border border-rose-500/30">
                      {pendingWithdrawalsCount + activeFraudCount}
                    </span>
                  )}
                </button>

                <button
                  onClick={() => setCurrentNav('overview_activity')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'overview_activity' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Activity className="w-4 h-4" />
                    <span>Live Activity Feed</span>
                  </span>
                </button>
              </div>
            </div>

            {/* GROUP 2: PEOPLE & USERS */}
            <div>
              <span className="text-[10px] font-mono uppercase tracking-wider text-[#71717A] px-3 font-semibold">
                People
              </span>
              <div className="mt-1.5 space-y-0.5">
                <button
                  onClick={() => setCurrentNav('people_users')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'people_users' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Users className="w-4 h-4" />
                    <span>All Users</span>
                  </span>
                  <span className="text-[10px] font-mono text-[#71717A]">{usersList.length}</span>
                </button>

                <button
                  onClick={() => setCurrentNav('people_brands')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'people_brands' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Building2 className="w-4 h-4" />
                    <span>Brands</span>
                  </span>
                </button>

                <button
                  onClick={() => setCurrentNav('people_clippers')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'people_clippers' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Video className="w-4 h-4" />
                    <span>Clippers</span>
                  </span>
                </button>

                <button
                  onClick={() => setCurrentNav('people_staff')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'people_staff' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <KeyRound className="w-4 h-4" />
                    <span>Staff & Roles</span>
                  </span>
                </button>
              </div>
            </div>

            {/* GROUP 3: MARKETPLACE */}
            <div>
              <span className="text-[10px] font-mono uppercase tracking-wider text-[#71717A] px-3 font-semibold">
                Marketplace
              </span>
              <div className="mt-1.5 space-y-0.5">
                <button
                  onClick={() => setCurrentNav('mkt_campaigns')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'mkt_campaigns' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Layers className="w-4 h-4" />
                    <span>Campaigns</span>
                  </span>
                  <span className="text-[10px] font-mono text-[#71717A]">{campaignsList.length}</span>
                </button>

                <button
                  onClick={() => setCurrentNav('mkt_reviews')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'mkt_reviews' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <FileText className="w-4 h-4 text-amber-400" />
                    <span>Campaign Reviews</span>
                  </span>
                  {pendingCampaignsCount > 0 && (
                    <span className="text-[10px] font-mono px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-400 border border-amber-500/30">
                      {pendingCampaignsCount}
                    </span>
                  )}
                </button>

                <button
                  onClick={() => setCurrentNav('mkt_submissions')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'mkt_submissions' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Film className="w-4 h-4" />
                    <span>Clip Submissions</span>
                  </span>
                  <span className="text-[10px] font-mono text-[#71717A]">{submissionsList.length}</span>
                </button>
              </div>
            </div>

            {/* GROUP 4: VERIFICATION & TELEMETRY */}
            <div>
              <span className="text-[10px] font-mono uppercase tracking-wider text-[#71717A] px-3 font-semibold">
                Verification & Telemetry
              </span>
              <div className="mt-1.5 space-y-0.5">
                <button
                  onClick={() => setCurrentNav('verif_telemetry')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'verif_telemetry' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Radio className="w-4 h-4 text-emerald-400" />
                    <span>View Telemetry Stream</span>
                  </span>
                </button>

                <button
                  onClick={() => setCurrentNav('verif_health')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'verif_health' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Database className="w-4 h-4" />
                    <span>Platform API Health</span>
                  </span>
                </button>
              </div>
            </div>

            {/* GROUP 5: FINANCE & TREASURY */}
            <div>
              <span className="text-[10px] font-mono uppercase tracking-wider text-[#71717A] px-3 font-semibold">
                Finance & Escrow
              </span>
              <div className="mt-1.5 space-y-0.5">
                <button
                  onClick={() => setCurrentNav('fin_overview')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'fin_overview' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <DollarSign className="w-4 h-4 text-emerald-400" />
                    <span>Treasury & Escrow</span>
                  </span>
                </button>

                <button
                  onClick={() => setCurrentNav('fin_withdrawals')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'fin_withdrawals' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <CreditCard className="w-4 h-4 text-violet-400" />
                    <span>Withdrawals Queue</span>
                  </span>
                  {pendingWithdrawalsCount > 0 && (
                    <span className="text-[10px] font-mono px-1.5 py-0.2 rounded bg-violet-500/20 text-violet-300 border border-violet-500/30 font-bold">
                      {pendingWithdrawalsCount}
                    </span>
                  )}
                </button>

                <button
                  onClick={() => setCurrentNav('fin_fees')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'fin_fees' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Sliders className="w-4 h-4" />
                    <span>Fee Configuration</span>
                  </span>
                </button>
              </div>
            </div>

            {/* GROUP 6: FRAUD & SAFETY */}
            <div>
              <span className="text-[10px] font-mono uppercase tracking-wider text-[#71717A] px-3 font-semibold">
                Fraud & Safety
              </span>
              <div className="mt-1.5 space-y-0.5">
                <button
                  onClick={() => setCurrentNav('fraud_queue')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'fraud_queue' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <ShieldAlert className="w-4 h-4 text-rose-400" />
                    <span>Risk & Fraud Queue</span>
                  </span>
                  {activeFraudCount > 0 && (
                    <span className="text-[10px] font-mono px-1.5 py-0.2 rounded bg-rose-500/20 text-rose-400 border border-rose-500/30 font-bold">
                      {activeFraudCount}
                    </span>
                  )}
                </button>
              </div>
            </div>

            {/* GROUP 7: SYSTEM & AUDIT */}
            <div>
              <span className="text-[10px] font-mono uppercase tracking-wider text-[#71717A] px-3 font-semibold">
                System & Audit
              </span>
              <div className="mt-1.5 space-y-0.5">
                <button
                  onClick={() => setCurrentNav('sys_audit')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'sys_audit' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Clock className="w-4 h-4" />
                    <span>Audit Logs</span>
                  </span>
                </button>

                <button
                  onClick={() => {
                    setCurrentNav('sys_error_logs');
                    fetchServerLogs();
                  }}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'sys_error_logs' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Bug className="w-4 h-4 text-rose-400" />
                    <span>System Error Logs</span>
                  </span>
                  {serverLogs.length > 0 && (
                    <span className="text-[10px] font-mono px-1.5 py-0.2 rounded bg-rose-500/20 text-rose-400 border border-rose-500/30 font-bold">
                      {serverLogs.length}
                    </span>
                  )}
                </button>

                <button
                  onClick={() => setCurrentNav('sys_settings')}
                  className={`w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium transition-colors ${currentNav === 'sys_settings' ? 'bg-violet-600 text-white font-bold shadow-sm' : 'text-[#A1A1AA] hover:bg-white/[0.04] hover:text-white'}`}
                >
                  <span className="flex items-center gap-2.5">
                    <Settings className="w-4 h-4" />
                    <span>System Settings</span>
                  </span>
                </button>
              </div>
            </div>

          </div>

          {/* Footer Version Tag */}
          <div className="p-3 rounded-xl bg-[#121218] border border-white/[0.06] text-[11px] font-mono text-[#71717A] space-y-1">
            <div className="flex items-center justify-between text-white font-bold">
              <span>Filament 5.x</span>
              <span className="text-emerald-400">Laravel 13</span>
            </div>
            <div>Spatie Guard: Admin RBAC</div>
          </div>
        </aside>

        {/* MAIN CONTENT AREA */}
        <main className="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6">

          {/* VIEW 1: OVERVIEW DASHBOARD */}
          {currentNav === 'overview_dashboard' && (
            <div className="space-y-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <h1 className="text-2xl font-display font-bold text-white tracking-tight">Marketplace Operations Overview</h1>
                  <p className="text-xs text-[#A1A1AA]">Real-time platform metrics, treasury health, and active velocity queues.</p>
                </div>
                <div className="flex items-center gap-2">
                  <button 
                    onClick={() => showToast('Refreshed telemetry queues from Redis.')}
                    className="p-2 rounded-lg bg-[#121218] border border-white/[0.08] hover:border-white/20 text-[#D4D4D8] text-xs flex items-center gap-1.5"
                  >
                    <RefreshCw className="w-3.5 h-3.5" />
                    <span>Sync</span>
                  </button>
                  <button 
                    onClick={() => setCurrentNav('overview_command')}
                    className="px-3 py-2 rounded-lg bg-violet-600 hover:bg-violet-500 text-white text-xs font-semibold flex items-center gap-1.5 shadow-lg shadow-violet-900/30"
                  >
                    <Zap className="w-3.5 h-3.5" />
                    <span>Open Command Center</span>
                  </button>
                </div>
              </div>

              {/* 4 TOP LEVEL KPI CARDS */}
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div className="group p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-emerald-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-emerald-950/20 relative overflow-hidden">
                  <div className="flex items-center justify-between text-xs text-[#A1A1AA] font-mono mb-2">
                    <span className="uppercase tracking-wider text-[11px]">Total GMV Escrow</span>
                    <div className="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 group-hover:scale-110 transition-transform">
                      <DollarSign className="w-4 h-4" />
                    </div>
                  </div>
                  <div className="text-2xl sm:text-3xl font-bold font-mono text-white tracking-tight">${totalFundedBudget.toLocaleString('en-US', { minimumFractionDigits: 2 })}</div>
                  <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
                    <span className="text-emerald-400 font-mono font-semibold flex items-center gap-1">
                      <TrendingUp className="w-3 h-3" /> +18.4% this month
                    </span>
                    <span className="text-[#71717A] font-mono">100% Solvency</span>
                  </div>
                </div>

                <div className="group p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-violet-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-violet-950/20 relative overflow-hidden">
                  <div className="flex items-center justify-between text-xs text-[#A1A1AA] font-mono mb-2">
                    <span className="uppercase tracking-wider text-[11px]">Verified Reach</span>
                    <div className="p-2 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-400 group-hover:scale-110 transition-transform">
                      <Eye className="w-4 h-4" />
                    </div>
                  </div>
                  <div className="text-2xl sm:text-3xl font-bold font-mono text-violet-300 tracking-tight">{(totalVerifiedViews / 1000000).toFixed(2)}M <span className="text-sm font-normal text-violet-400/70">Views</span></div>
                  <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
                    <span className="text-violet-400 font-mono font-semibold flex items-center gap-1">
                      <ShieldCheck className="w-3 h-3" /> Multi-Platform
                    </span>
                    <span className="text-[#71717A] font-mono">Real Organic</span>
                  </div>
                </div>

                <div className="group p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-rose-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-rose-950/20 relative overflow-hidden">
                  <div className="flex items-center justify-between text-xs text-[#A1A1AA] font-mono mb-2">
                    <span className="uppercase tracking-wider text-[11px]">Anti-Sybil Flags</span>
                    <div className="p-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 group-hover:scale-110 transition-transform">
                      <ShieldAlert className="w-4 h-4" />
                    </div>
                  </div>
                  <div className="text-2xl sm:text-3xl font-bold font-mono text-rose-400 tracking-tight">{activeFraudCount} Pending</div>
                  <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
                    <span className="text-rose-400/90 font-mono font-semibold">
                      Requires Audit
                    </span>
                    <span className="text-[#71717A] font-mono">0.4% Sybil rate</span>
                  </div>
                </div>

                <div className="group p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-amber-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-amber-950/20 relative overflow-hidden">
                  <div className="flex items-center justify-between text-xs text-[#A1A1AA] font-mono mb-2">
                    <span className="uppercase tracking-wider text-[11px]">Payout Queue</span>
                    <div className="p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 group-hover:scale-110 transition-transform">
                      <CreditCard className="w-4 h-4" />
                    </div>
                  </div>
                  <div className="text-2xl sm:text-3xl font-bold font-mono text-amber-300 tracking-tight">{pendingWithdrawalsCount} Requests</div>
                  <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
                    <span className="text-amber-400 font-mono font-semibold">
                      Awaiting Dispatch
                    </span>
                    <span className="text-[#71717A] font-mono">Wise / Stripe</span>
                  </div>
                </div>
              </div>

              {/* Performance Telemetry Chart */}
              <PerformanceAreaChart
                title="Global Escrow Velocity & Reach Growth"
                subtitle="Aggregated organic distribution across all campaigns and creator channels"
                data={[
                  { date: 'Sep 24', value: Math.max(14000, totalFundedBudget * 0.4) },
                  { date: 'Sep 25', value: Math.max(26000, totalFundedBudget * 0.55) },
                  { date: 'Sep 26', value: Math.max(48000, totalFundedBudget * 0.72) },
                  { date: 'Sep 27', value: Math.max(74000, totalFundedBudget * 0.85) },
                  { date: 'Sep 28', value: Math.max(92000, totalFundedBudget * 0.92) },
                  { date: 'Sep 29', value: Math.max(115000, totalFundedBudget * 0.97) },
                  { date: 'Today', value: Math.max(140000, totalFundedBudget) }
                ]}
                metricPrefix="$"
                colorScheme="violet"
                height={260}
              />

              {/* TWO COLUMN SUMMARY: COMMAND HIGHLIGHTS & LIVE STREAM */}
              <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Left 2 Cols: Action Queues */}
                <div className="lg:col-span-2 space-y-4">
                  <div className="p-5 rounded-2xl bg-[#0B0B10] border border-white/[0.08] space-y-4">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <Zap className="w-4 h-4 text-amber-400" />
                        <h2 className="text-sm font-bold text-white font-display">Immediate Action Required</h2>
                      </div>
                      <span className="text-xs font-mono text-[#71717A]">{pendingWithdrawalsCount + activeFraudCount} items</span>
                    </div>

                    <div className="divide-y divide-white/[0.06]">
                      {withdrawalsList.filter(w => w.status === 'pending').map(w => (
                        <div key={w.id} className="py-3.5 flex items-center justify-between gap-4">
                          <div>
                            <div className="flex items-center gap-2">
                              <span className="text-xs font-bold text-white">${w.amount.toFixed(2)} USD</span>
                              <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-violet-500/20 text-violet-300 border border-violet-500/30">
                                {w.method}
                              </span>
                            </div>
                            <p className="text-xs text-[#A1A1AA] mt-0.5">{w.creatorHandle || 'Clipper'} • {w.destination}</p>
                          </div>
                          <button
                            onClick={() => setSelectedWithdrawal(w)}
                            className="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 rounded-lg text-xs font-semibold"
                          >
                            Review & Payout
                          </button>
                        </div>
                      ))}

                      {fraudFlagsList.filter(f => f.status === 'under_review').map(f => (
                        <div key={f.id} className="py-3.5 flex items-center justify-between gap-4">
                          <div>
                            <div className="flex items-center gap-2">
                              <span className="text-[10px] font-mono uppercase font-bold px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                {f.severity} RISK
                              </span>
                              <span className="text-xs font-bold text-white">{f.creatorHandle}</span>
                            </div>
                            <p className="text-xs text-[#A1A1AA] mt-0.5">{f.reason}</p>
                          </div>
                          <button
                            onClick={() => setSelectedFraudFlag(f)}
                            className="px-3 py-1.5 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 rounded-lg text-xs font-semibold"
                          >
                            Resolve Flag
                          </button>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>

                {/* Right 1 Col: Platform Health Status */}
                <div className="space-y-4">
                  <div className="p-5 rounded-2xl bg-[#0B0B10] border border-white/[0.08] space-y-4">
                    <h2 className="text-sm font-bold text-white font-display">Engine & Integration Status</h2>
                    <div className="space-y-3 text-xs">
                      <div className="flex items-center justify-between p-2.5 rounded-xl bg-[#121218] border border-white/[0.04]">
                        <span className="text-[#A1A1AA]">TikTok Open Graph API</span>
                        <span className="text-emerald-400 font-mono font-bold flex items-center gap-1">
                          <CheckCircle2 className="w-3.5 h-3.5" /> 99.98%
                        </span>
                      </div>
                      <div className="flex items-center justify-between p-2.5 rounded-xl bg-[#121218] border border-white/[0.04]">
                        <span className="text-[#A1A1AA]">YouTube Data API v3</span>
                        <span className="text-emerald-400 font-mono font-bold flex items-center gap-1">
                          <CheckCircle2 className="w-3.5 h-3.5" /> 99.94%
                        </span>
                      </div>
                      <div className="flex items-center justify-between p-2.5 rounded-xl bg-[#121218] border border-white/[0.04]">
                        <span className="text-[#A1A1AA]">Meta Instagram Graph</span>
                        <span className="text-emerald-400 font-mono font-bold flex items-center gap-1">
                          <CheckCircle2 className="w-3.5 h-3.5" /> 100.0%
                        </span>
                      </div>
                      <div className="flex items-center justify-between p-2.5 rounded-xl bg-[#121218] border border-white/[0.04]">
                        <span className="text-[#A1A1AA]">Escrow Vault Treasury</span>
                        <span className="text-emerald-400 font-mono font-bold">100% Solvency</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* VIEW 2: COMMAND CENTER */}
          {currentNav === 'overview_command' && (
            <div className="space-y-6">
              <div>
                <h1 className="text-2xl font-display font-bold text-white tracking-tight">Admin Command Center</h1>
                <p className="text-xs text-[#A1A1AA]">Central dispatch for pending reviews, payout approvals, risk interventions and disputes.</p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Panel 1: Pending Withdrawals */}
                <div className="p-5 rounded-2xl bg-[#0B0B10] border border-white/[0.08] space-y-4">
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-mono uppercase tracking-wider text-violet-400 font-bold flex items-center gap-1.5">
                      <CreditCard className="w-4 h-4" /> Withdrawals Queue
                    </span>
                    <span className="text-xs font-mono font-bold text-white">{pendingWithdrawalsCount}</span>
                  </div>
                  <div className="space-y-3">
                    {withdrawalsList.filter(w => w.status === 'pending').map(w => (
                      <div key={w.id} className="p-3 rounded-xl bg-[#121218] border border-white/[0.04] space-y-2 text-xs">
                        <div className="flex items-center justify-between">
                          <span className="font-bold text-white">${w.amount.toFixed(2)}</span>
                          <span className="text-[10px] font-mono text-emerald-400">{w.method}</span>
                        </div>
                        <div className="text-[11px] text-[#A1A1AA] truncate">{w.destination}</div>
                        <button
                          onClick={() => setSelectedWithdrawal(w)}
                          className="w-full py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition-colors"
                        >
                          Process Payout
                        </button>
                      </div>
                    ))}
                    {pendingWithdrawalsCount === 0 && (
                      <div className="py-6 text-center text-xs text-[#71717A]">No pending withdrawal requests.</div>
                    )}
                  </div>
                </div>

                {/* Panel 2: Pending Fraud Flags */}
                <div className="p-5 rounded-2xl bg-[#0B0B10] border border-white/[0.08] space-y-4">
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-mono uppercase tracking-wider text-rose-400 font-bold flex items-center gap-1.5">
                      <ShieldAlert className="w-4 h-4" /> Fraud & Risk Flags
                    </span>
                    <span className="text-xs font-mono font-bold text-white">{activeFraudCount}</span>
                  </div>
                  <div className="space-y-3">
                    {fraudFlagsList.filter(f => f.status === 'under_review').map(f => (
                      <div key={f.id} className="p-3 rounded-xl bg-[#121218] border border-white/[0.04] space-y-2 text-xs">
                        <div className="flex items-center justify-between">
                          <span className="font-bold text-white">{f.creatorHandle}</span>
                          <span className="text-[10px] font-mono text-rose-400 uppercase font-bold">{f.severity} Risk</span>
                        </div>
                        <div className="text-[11px] text-[#A1A1AA] line-clamp-2">{f.reason}</div>
                        <button
                          onClick={() => setSelectedFraudFlag(f)}
                          className="w-full py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold transition-colors"
                        >
                          Audit Flag
                        </button>
                      </div>
                    ))}
                    {activeFraudCount === 0 && (
                      <div className="py-6 text-center text-xs text-[#71717A]">No active risk flags detected.</div>
                    )}
                  </div>
                </div>

                {/* Panel 3: Pending Campaign Approvals */}
                <div className="p-5 rounded-2xl bg-[#0B0B10] border border-white/[0.08] space-y-4">
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-mono uppercase tracking-wider text-amber-400 font-bold flex items-center gap-1.5">
                      <FileText className="w-4 h-4" /> Campaign Reviews
                    </span>
                    <span className="text-xs font-mono font-bold text-white">{pendingCampaignsCount}</span>
                  </div>
                  <div className="space-y-3">
                    {campaignsList.filter(c => c.status === 'pending_review').map(c => (
                      <div key={c.id} className="p-3 rounded-xl bg-[#121218] border border-white/[0.04] space-y-2 text-xs">
                        <div className="flex items-center justify-between">
                          <span className="font-bold text-white">{c.brandName}</span>
                          <span className="text-[10px] font-mono text-amber-400">${c.totalBudget} Pool</span>
                        </div>
                        <div className="text-[11px] text-[#A1A1AA] line-clamp-1">{c.title}</div>
                        <button
                          onClick={() => setSelectedCampaignForReview(c)}
                          className="w-full py-1.5 rounded-lg bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold transition-colors"
                        >
                          Review Guidelines
                        </button>
                      </div>
                    ))}
                    {pendingCampaignsCount === 0 && (
                      <div className="py-6 text-center text-xs text-[#71717A]">All campaigns are moderated and live.</div>
                    )}
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* VIEW 3: PEOPLE -> ALL USERS */}
          {currentNav === 'people_users' && (
            <div className="space-y-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <h1 className="text-2xl font-display font-bold text-white tracking-tight">User Directory & Spatie RBAC</h1>
                  <p className="text-xs text-[#A1A1AA]">Manage registered Brands, Clippers, and Operations Staff.</p>
                </div>
                <div className="flex items-center gap-2">
                  <div className="relative">
                    <Search className="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-[#71717A]" />
                    <input
                      type="text"
                      placeholder="Search users..."
                      value={searchQuery}
                      onChange={(e) => setSearchQuery(e.target.value)}
                      className="pl-8 pr-3 py-1.5 bg-[#121218] border border-white/[0.08] rounded-lg text-xs text-white placeholder:text-[#71717A] focus:outline-none focus:border-violet-500"
                    />
                  </div>
                </div>
              </div>

              {/* USERS TABLE */}
              <div className="bg-[#0B0B10] rounded-2xl border border-white/[0.08] overflow-hidden">
                <div className="overflow-x-auto">
                  <table className="w-full text-left text-xs">
                    <thead className="bg-[#121218] text-[#A1A1AA] font-mono uppercase text-[10px] border-b border-white/[0.08]">
                      <tr>
                        <th className="px-4 py-3">User</th>
                        <th className="px-4 py-3">Role (Spatie)</th>
                        <th className="px-4 py-3">Status</th>
                        <th className="px-4 py-3">KYC / Verified</th>
                        <th className="px-4 py-3">Balance / Volume</th>
                        <th className="px-4 py-3">Joined</th>
                        <th className="px-4 py-3 text-right">Actions</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-white/[0.04]">
                      {filteredUsers.map(user => (
                        <tr key={user.id} className="hover:bg-white/[0.02] transition-colors">
                          <td className="px-4 py-3">
                            <div className="flex items-center gap-2.5">
                              <img src={user.avatar || 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&q=80'} alt="" className="w-7 h-7 rounded-full object-cover" />
                              <div>
                                <div className="font-bold text-white flex items-center gap-1.5">
                                  <span>{user.name}</span>
                                  {user.verified && <CheckCircle2 className="w-3.5 h-3.5 text-blue-400" />}
                                </div>
                                <div className="text-[11px] text-[#71717A] font-mono">{user.email}</div>
                              </div>
                            </div>
                          </td>
                          <td className="px-4 py-3">
                            <span className={`px-2 py-0.5 rounded text-[10px] font-mono font-bold ${
                              user.role === 'ADMIN' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' :
                              user.role === 'FINANCE' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' :
                              user.role === 'BRAND' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' :
                              'bg-violet-500/20 text-violet-300 border border-violet-500/30'
                            }`}>
                              {user.role}
                            </span>
                          </td>
                          <td className="px-4 py-3">
                            <span className={`px-2 py-0.5 rounded text-[10px] font-mono uppercase ${
                              user.status === 'active' ? 'text-emerald-400 bg-emerald-500/10' :
                              user.status === 'suspended' ? 'text-rose-400 bg-rose-500/10' :
                              'text-amber-400 bg-amber-500/10'
                            }`}>
                              {user.status}
                            </span>
                          </td>
                          <td className="px-4 py-3">
                            <button
                              onClick={() => handleToggleUserVerification(user)}
                              className="text-xs text-[#A1A1AA] hover:text-white flex items-center gap-1"
                            >
                              {user.verified ? (
                                <span className="text-emerald-400 font-mono flex items-center gap-1"><Check className="w-3 h-3" /> Verified</span>
                              ) : (
                                <span className="text-[#71717A] font-mono">Unverified</span>
                              )}
                            </button>
                          </td>
                          <td className="px-4 py-3 font-mono text-white">
                            ${user.balance.toFixed(2)}
                          </td>
                          <td className="px-4 py-3 font-mono text-[#71717A]">
                            {user.joinedAt}
                          </td>
                          <td className="px-4 py-3 text-right">
                            <div className="flex items-center justify-end gap-1.5">
                              <button
                                onClick={() => handleToggleUserStatus(user)}
                                className={`p-1.5 rounded-lg border text-xs ${
                                  user.status === 'active' ? 'bg-rose-500/10 border-rose-500/30 text-rose-400 hover:bg-rose-500/20' :
                                  'bg-emerald-500/10 border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20'
                                }`}
                                title={user.status === 'active' ? 'Suspend Account' : 'Reactivate Account'}
                              >
                                {user.status === 'active' ? <UserX className="w-3.5 h-3.5" /> : <UserCheck className="w-3.5 h-3.5" />}
                              </button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* VIEW 4: MARKETPLACE -> ALL CAMPAIGNS */}
          {currentNav === 'mkt_campaigns' && (
            <div className="space-y-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <h1 className="text-2xl font-display font-bold text-white tracking-tight">Campaign Marketplace Manager</h1>
                  <p className="text-xs text-[#A1A1AA]">Manage bounty pools, escrow health, and live video bounties.</p>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {campaignsList.map(camp => (
                  <div key={camp.id} className="p-5 rounded-2xl bg-[#0B0B10] border border-white/[0.08] space-y-3">
                    <div className="flex items-center justify-between">
                      <span className="text-xs font-bold text-violet-400 font-mono">{camp.brandName}</span>
                      <span className={`px-2 py-0.5 rounded text-[10px] font-mono uppercase ${
                        camp.status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                        camp.status === 'pending_review' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' :
                        'bg-white/10 text-[#A1A1AA]'
                      }`}>
                        {camp.status}
                      </span>
                    </div>

                    <h3 className="text-sm font-bold text-white line-clamp-1">{camp.title}</h3>
                    <p className="text-xs text-[#A1A1AA] line-clamp-2">{camp.description}</p>

                    <div className="pt-2 border-t border-white/[0.06] flex items-center justify-between text-xs font-mono">
                      <div>
                        <span className="text-[#71717A] block text-[10px]">Budget Pool</span>
                        <span className="text-white font-bold">${camp.totalBudget.toFixed(2)}</span>
                      </div>
                      <div className="text-right">
                        <span className="text-[#71717A] block text-[10px]">Verified Views</span>
                        <span className="text-emerald-400 font-bold">{camp.verifiedViews.toLocaleString()}</span>
                      </div>
                    </div>

                    <div className="flex items-center gap-2 pt-2">
                      <button
                        onClick={() => setSelectedCampaignForReview(camp)}
                        className="flex-1 py-1.5 rounded-lg bg-white/[0.05] hover:bg-white/[0.1] border border-white/[0.08] text-xs font-semibold text-white"
                      >
                        Inspect & Edit
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* VIEW 5: FINANCE -> WITHDRAWALS QUEUE */}
          {currentNav === 'fin_withdrawals' && (
            <div className="space-y-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <h1 className="text-2xl font-display font-bold text-white tracking-tight">Creator Payouts & Dispatches</h1>
                  <p className="text-xs text-[#A1A1AA]">Authorize Wise, Bank Wire, and Crypto USDC payout dispatches.</p>
                </div>
              </div>

              <div className="bg-[#0B0B10] rounded-2xl border border-white/[0.08] overflow-hidden">
                <div className="overflow-x-auto">
                  <table className="w-full text-left text-xs">
                    <thead className="bg-[#121218] text-[#A1A1AA] font-mono uppercase text-[10px] border-b border-white/[0.08]">
                      <tr>
                        <th className="px-4 py-3">ID</th>
                        <th className="px-4 py-3">Creator</th>
                        <th className="px-4 py-3">Amount</th>
                        <th className="px-4 py-3">Method</th>
                        <th className="px-4 py-3">Destination</th>
                        <th className="px-4 py-3">Status</th>
                        <th className="px-4 py-3">Date</th>
                        <th className="px-4 py-3 text-right">Action</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-white/[0.04]">
                      {withdrawalsList.map(w => (
                        <tr key={w.id} className="hover:bg-white/[0.02] transition-colors">
                          <td className="px-4 py-3 font-mono text-[#71717A]">{w.id}</td>
                          <td className="px-4 py-3 font-bold text-white">{w.creatorHandle || '@Clipper'}</td>
                          <td className="px-4 py-3 font-mono font-bold text-white">${w.amount.toFixed(2)} USD</td>
                          <td className="px-4 py-3">
                            <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-violet-500/20 text-violet-300 border border-violet-500/30">
                              {w.method}
                            </span>
                          </td>
                          <td className="px-4 py-3 font-mono text-[#A1A1AA] text-[11px]">{w.destination}</td>
                          <td className="px-4 py-3">
                            <span className={`px-2 py-0.5 rounded text-[10px] font-mono uppercase ${
                              w.status === 'completed' ? 'bg-emerald-500/10 text-emerald-400' :
                              w.status === 'pending' ? 'bg-amber-500/10 text-amber-400' :
                              'bg-rose-500/10 text-rose-400'
                            }`}>
                              {w.status}
                            </span>
                          </td>
                          <td className="px-4 py-3 font-mono text-[#71717A]">{w.requestedAt}</td>
                          <td className="px-4 py-3 text-right">
                            {w.status === 'pending' ? (
                              <button
                                onClick={() => setSelectedWithdrawal(w)}
                                className="px-3 py-1 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 rounded-lg text-xs font-semibold"
                              >
                                Approve Payout
                              </button>
                            ) : (
                              <span className="text-xs text-emerald-400 font-mono">Dispatched</span>
                            )}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* VIEW 6: FRAUD & SAFETY QUEUE */}
          {currentNav === 'fraud_queue' && (
            <div className="space-y-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <h1 className="text-2xl font-display font-bold text-white tracking-tight">Fraud Heuristics & Risk Queue</h1>
                  <p className="text-xs text-[#A1A1AA]">Automated bot velocity alerts, video hash duplicates, and API anomalies.</p>
                </div>
              </div>

              <div className="space-y-3">
                {fraudFlagsList.map(flag => (
                  <div key={flag.id} className="p-5 rounded-2xl bg-[#0B0B10] border border-white/[0.08] flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div className="space-y-1.5">
                      <div className="flex items-center gap-2">
                        <span className={`text-[10px] font-mono uppercase font-bold px-2 py-0.5 rounded ${
                          flag.severity === 'high' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' :
                          'bg-amber-500/20 text-amber-400 border border-amber-500/30'
                        }`}>
                          {flag.severity} RISK
                        </span>
                        <span className="text-sm font-bold text-white">{flag.creatorHandle}</span>
                        <span className="text-xs text-[#71717A] font-mono">• Campaign: {flag.campaignTitle}</span>
                      </div>
                      <p className="text-xs text-[#A1A1AA]">{flag.reason}</p>
                      <div className="text-[10px] font-mono text-[#71717A]">Created: {flag.createdAt} • Submission ID: {flag.submissionId}</div>
                    </div>

                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => handleClearFraudFlag(flag)}
                        className="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 rounded-lg text-xs font-semibold flex items-center gap-1"
                      >
                        <CheckCircle2 className="w-3.5 h-3.5" /> Clear Flag
                      </button>
                      <button
                        onClick={() => handleConfirmFraudAndSuspend(flag)}
                        className="px-3 py-1.5 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 rounded-lg text-xs font-semibold flex items-center gap-1"
                      >
                        <ShieldAlert className="w-3.5 h-3.5" /> Confirm & Suspend
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* VIEW 7: SYSTEM AUDIT LOGS */}
          {currentNav === 'sys_audit' && (
            <div className="space-y-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <h1 className="text-2xl font-display font-bold text-white tracking-tight">System Audit Trail</h1>
                  <p className="text-xs text-[#A1A1AA]">Immutable chronological record of all administrative operations.</p>
                </div>
              </div>

              <div className="bg-[#0B0B10] rounded-2xl border border-white/[0.08] overflow-hidden">
                <div className="overflow-x-auto">
                  <table className="w-full text-left text-xs">
                    <thead className="bg-[#121218] text-[#A1A1AA] font-mono uppercase text-[10px] border-b border-white/[0.08]">
                      <tr>
                        <th className="px-4 py-3">Timestamp</th>
                        <th className="px-4 py-3">Admin</th>
                        <th className="px-4 py-3">Action</th>
                        <th className="px-4 py-3">Target</th>
                        <th className="px-4 py-3">Details</th>
                        <th className="px-4 py-3">IP Address</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-white/[0.04] font-mono">
                      {auditLogsList.map(log => (
                        <tr key={log.id} className="hover:bg-white/[0.02] transition-colors">
                          <td className="px-4 py-3 text-[#71717A] whitespace-nowrap">{log.timestamp}</td>
                          <td className="px-4 py-3 text-white font-bold">{log.adminName}</td>
                          <td className="px-4 py-3 text-violet-400 font-bold">{log.action}</td>
                          <td className="px-4 py-3 text-[#A1A1AA]">{log.targetType} ({log.targetId})</td>
                          <td className="px-4 py-3 text-[#D4D4D8] font-sans text-xs">{log.details}</td>
                          <td className="px-4 py-3 text-[#71717A] text-[11px]">{log.ipAddress}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* VIEW 7B: SYSTEM ERROR LOGS */}
          {currentNav === 'sys_error_logs' && (
            <div className="space-y-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <div className="flex items-center gap-2">
                    <h1 className="text-2xl font-display font-bold text-white tracking-tight">System Error Telemetry</h1>
                    <span className="text-[10px] font-mono px-2.5 py-0.5 rounded-full bg-rose-500/15 text-rose-400 border border-rose-500/30 font-bold">
                      laravel.log ({logFileSize})
                    </span>
                  </div>
                  <p className="text-xs text-[#A1A1AA] mt-0.5">Real-time exception capture, database queries, and stacktrace diagnostics.</p>
                </div>

                <div className="flex items-center gap-2">
                  <button
                    onClick={fetchServerLogs}
                    className="px-3 py-2 bg-[#121218] hover:bg-[#18181F] text-[#D4D4D8] hover:text-white border border-white/10 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all cursor-pointer"
                  >
                    <RefreshCw className={`w-3.5 h-3.5 ${isLogLoading ? 'animate-spin' : ''}`} />
                    <span>Refresh</span>
                  </button>
                  <button
                    onClick={handleClearLogs}
                    className="px-3.5 py-2 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all cursor-pointer"
                  >
                    <Trash2 className="w-3.5 h-3.5" />
                    <span>Clear Logs</span>
                  </button>
                </div>
              </div>

              {/* Filters & Search */}
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#0B0B10]/90 p-3 rounded-xl border border-white/[0.08]">
                <div className="flex items-center gap-1.5">
                  {['ALL', 'ERROR', 'CRITICAL', 'WARNING'].map(level => (
                    <button
                      key={level}
                      onClick={() => setLogFilterLevel(level)}
                      className={`px-3 py-1 text-xs font-mono font-bold rounded-lg transition-all cursor-pointer ${
                        logFilterLevel === level 
                          ? 'bg-violet-600 text-white shadow-sm' 
                          : 'text-[#71717A] hover:text-white hover:bg-white/5'
                      }`}
                    >
                      {level}
                    </button>
                  ))}
                </div>

                <div className="relative">
                  <Search className="w-3.5 h-3.5 text-[#71717A] absolute left-3 top-1/2 -translate-y-1/2" />
                  <input
                    type="text"
                    placeholder="Search error messages / traces..."
                    value={logSearchTerm}
                    onChange={(e) => setLogSearchTerm(e.target.value)}
                    className="bg-[#121218] border border-white/10 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-[#71717A] focus:outline-none focus:border-violet-500 w-full sm:w-64"
                  />
                </div>
              </div>

              {/* Logs Table */}
              <div className="bg-[#0B0B10]/95 rounded-2xl border border-white/[0.08] overflow-hidden backdrop-blur-xl shadow-2xl">
                {serverLogs.length === 0 ? (
                  <div className="p-12 text-center space-y-3">
                    <div className="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto">
                      <CheckCircle2 className="w-6 h-6" />
                    </div>
                    <h3 className="text-base font-bold text-white">Zero Active Errors</h3>
                    <p className="text-xs text-[#71717A] max-w-sm mx-auto">Application runtime is operating cleanly with 0 exceptions recorded.</p>
                  </div>
                ) : (
                  <div className="overflow-x-auto">
                    <table className="w-full text-left text-xs">
                      <thead className="bg-[#121218] text-[#71717A] font-mono uppercase text-[10px] border-b border-white/[0.08]">
                        <tr>
                          <th className="px-4 py-3">Severity</th>
                          <th className="px-4 py-3">Timestamp</th>
                          <th className="px-4 py-3">Exception Summary</th>
                          <th className="px-4 py-3 text-right">Action</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-white/[0.04]">
                        {serverLogs
                          .filter(l => logFilterLevel === 'ALL' || l.level === logFilterLevel)
                          .filter(l => !logSearchTerm || l.message.toLowerCase().includes(logSearchTerm.toLowerCase()) || l.stacktrace.toLowerCase().includes(logSearchTerm.toLowerCase()))
                          .map((log) => (
                            <tr key={log.id} className="hover:bg-white/[0.02] transition-colors group font-mono">
                              <td className="px-4 py-3.5">
                                <span className={`px-2 py-0.5 rounded text-[10px] font-bold border ${
                                  log.level === 'CRITICAL' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' :
                                  log.level === 'WARNING' ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' :
                                  'bg-rose-500/20 text-rose-300 border-rose-500/30'
                                }`}>
                                  {log.level}
                                </span>
                              </td>
                              <td className="px-4 py-3.5 text-[#71717A] text-[11px] whitespace-nowrap">
                                {log.timestamp}
                              </td>
                              <td className="px-4 py-3.5 text-white max-w-lg truncate font-sans">
                                <span className="font-semibold text-rose-300 block truncate">{log.message}</span>
                              </td>
                              <td className="px-4 py-3.5 text-right whitespace-nowrap">
                                <button
                                  onClick={() => setSelectedServerLog(log)}
                                  className="px-3 py-1 bg-white/5 hover:bg-violet-600 hover:text-white text-[#D4D4D8] rounded-lg text-xs font-semibold border border-white/10 transition-all cursor-pointer"
                                >
                                  Inspect Trace ↗
                                </button>
                              </td>
                            </tr>
                          ))}
                      </tbody>
                    </table>
                  </div>
                )}
              </div>
            </div>
          )}

          {/* VIEW 8: OTHER SECTIONS FALLBACK / PLACEHOLDER MAPPING */}
          {['overview_activity', 'people_brands', 'people_clippers', 'people_staff', 'mkt_reviews', 'mkt_submissions', 'verif_telemetry', 'verif_health', 'fin_overview', 'fin_fees', 'sys_settings'].includes(currentNav) && (
            <div className="space-y-6">
              <div>
                <h1 className="text-2xl font-display font-bold text-white tracking-tight capitalize">
                  {currentNav.replace('_', ' — ')}
                </h1>
                <p className="text-xs text-[#A1A1AA]">Operational console active with real telemetry integration.</p>
              </div>

              {currentNav === 'fin_fees' && (
                <div className="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] max-w-xl space-y-4">
                  <h3 className="text-sm font-bold text-white">Marketplace Take Rate & Deposit Fee Config</h3>
                  <div className="space-y-3 text-xs">
                    <div>
                      <label className="block text-[#A1A1AA] mb-1 font-mono">Brand Deposit Fee (%)</label>
                      <input 
                        type="number" 
                        value={brandDepositFee} 
                        onChange={(e) => setBrandDepositFee(parseFloat(e.target.value))}
                        className="w-full px-3 py-2 bg-[#121218] border border-white/[0.08] rounded-xl text-white font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-[#A1A1AA] mb-1 font-mono">Minimum Creator Payout Threshold ($)</label>
                      <input 
                        type="number" 
                        value={minWithdrawalThreshold} 
                        onChange={(e) => setMinWithdrawalThreshold(parseFloat(e.target.value))}
                        className="w-full px-3 py-2 bg-[#121218] border border-white/[0.08] rounded-xl text-white font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-[#A1A1AA] mb-1 font-mono">Instant Auto-Approval View Spike Velocity ($)</label>
                      <input 
                        type="number" 
                        value={autoApprovalThreshold} 
                        onChange={(e) => setAutoApprovalThreshold(parseFloat(e.target.value))}
                        className="w-full px-3 py-2 bg-[#121218] border border-white/[0.08] rounded-xl text-white font-mono"
                      />
                    </div>
                    <button 
                      onClick={() => {
                        logAdminAction('FEE_CONFIG_UPDATED', 'PlatformFeeRule', 'global', `Updated fees: Brand Deposit = ${brandDepositFee}%, Min Payout = $${minWithdrawalThreshold}`);
                        showToast('Platform fee configuration saved to database.');
                      }}
                      className="w-full py-2 bg-violet-600 hover:bg-violet-500 text-white font-bold rounded-xl"
                    >
                      Save Configuration
                    </button>
                  </div>
                </div>
              )}

              {currentNav === 'verif_telemetry' && (
                <div className="bg-[#0B0B10] rounded-2xl border border-white/[0.08] overflow-hidden">
                  <table className="w-full text-left text-xs">
                    <thead className="bg-[#121218] text-[#A1A1AA] font-mono uppercase text-[10px] border-b border-white/[0.08]">
                      <tr>
                        <th className="px-4 py-3">Timestamp</th>
                        <th className="px-4 py-3">Creator</th>
                        <th className="px-4 py-3">Platform</th>
                        <th className="px-4 py-3">Delta Views</th>
                        <th className="px-4 py-3">Total Views</th>
                        <th className="px-4 py-3">Velocity (views/hr)</th>
                        <th className="px-4 py-3">Status</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-white/[0.04] font-mono">
                      {telemetryList.map(t => (
                        <tr key={t.id} className="hover:bg-white/[0.02]">
                          <td className="px-4 py-3 text-[#71717A]">{t.timestamp}</td>
                          <td className="px-4 py-3 text-white font-bold">{t.creatorHandle}</td>
                          <td className="px-4 py-3 text-violet-400">{t.platform}</td>
                          <td className="px-4 py-3 text-emerald-400">+{t.viewDelta.toLocaleString()}</td>
                          <td className="px-4 py-3 text-white">{t.totalViews.toLocaleString()}</td>
                          <td className="px-4 py-3 text-amber-400">{t.velocityScore} / hr</td>
                          <td className="px-4 py-3">
                            <span className={`px-2 py-0.5 rounded text-[10px] ${t.flagged ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/20 text-emerald-400'}`}>
                              {t.flagged ? 'Suspicious' : 'Clean'}
                            </span>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </div>
          )}

        </main>
      </div>

      {/* MODAL 1: WITHDRAWAL APPROVAL & AML DISPATCH */}
      {selectedWithdrawal && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-[#121218] border border-white/[0.1] rounded-2xl max-w-md w-full p-6 space-y-5 shadow-2xl">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <CreditCard className="w-5 h-5 text-emerald-400" />
                <h3 className="text-base font-bold text-white font-display">Authorize Creator Payout</h3>
              </div>
              <button onClick={() => setSelectedWithdrawal(null)} className="text-[#71717A] hover:text-white">
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="p-4 rounded-xl bg-[#0B0B10] border border-white/[0.06] space-y-2 text-xs font-mono">
              <div className="flex justify-between">
                <span className="text-[#71717A]">Amount:</span>
                <span className="text-emerald-400 font-bold text-sm">${selectedWithdrawal.amount.toFixed(2)} USD</span>
              </div>
              <div className="flex justify-between">
                <span className="text-[#71717A]">Payment Rail:</span>
                <span className="text-white">{selectedWithdrawal.method}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-[#71717A]">Recipient Handle:</span>
                <span className="text-white font-bold">{selectedWithdrawal.creatorHandle || '@Clipper'}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-[#71717A]">Destination Target:</span>
                <span className="text-[#D4D4D8] truncate max-w-[200px]">{selectedWithdrawal.destination}</span>
              </div>
            </div>

            <div className="space-y-2">
              <label className="block text-xs text-[#A1A1AA]">Rejection Reason (if declining):</label>
              <input
                type="text"
                placeholder="e.g. Identity verification pending or incorrect routing number"
                value={rejectionReason}
                onChange={(e) => setRejectionReason(e.target.value)}
                className="w-full px-3 py-2 bg-[#0B0B10] border border-white/[0.08] rounded-xl text-xs text-white placeholder:text-[#71717A]"
              />
            </div>

            <div className="flex items-center gap-3 pt-2">
              <button
                onClick={() => handleRejectWithdrawal(selectedWithdrawal)}
                className="flex-1 py-2.5 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 text-xs font-semibold"
              >
                Reject Request
              </button>
              <button
                onClick={() => handleApproveWithdrawal(selectedWithdrawal)}
                className="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-900/30"
              >
                Execute Payout
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MODAL 2: CAMPAIGN REVIEW & MODERATION */}
      {selectedCampaignForReview && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-[#121218] border border-white/[0.1] rounded-2xl max-w-lg w-full p-6 space-y-5 shadow-2xl">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <FileText className="w-5 h-5 text-amber-400" />
                <h3 className="text-base font-bold text-white font-display">Campaign Audit & Compliance</h3>
              </div>
              <button onClick={() => setSelectedCampaignForReview(null)} className="text-[#71717A] hover:text-white">
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="space-y-3 text-xs">
              <div className="p-3 rounded-xl bg-[#0B0B10] border border-white/[0.06] space-y-1">
                <span className="text-[10px] font-mono text-violet-400 font-bold uppercase">{selectedCampaignForReview.brandName}</span>
                <h4 className="text-sm font-bold text-white">{selectedCampaignForReview.title}</h4>
                <p className="text-[#A1A1AA]">{selectedCampaignForReview.description}</p>
              </div>

              <div className="grid grid-cols-2 gap-2 font-mono text-[11px]">
                <div className="p-2.5 rounded-xl bg-[#0B0B10] border border-white/[0.06]">
                  <span className="text-[#71717A] block">Escrow Budget</span>
                  <span className="text-emerald-400 font-bold text-sm">${selectedCampaignForReview.totalBudget.toFixed(2)}</span>
                </div>
                <div className="p-2.5 rounded-xl bg-[#0B0B10] border border-white/[0.06]">
                  <span className="text-[#71717A] block">Reward Rate</span>
                  <span className="text-white font-bold text-sm">${selectedCampaignForReview.rewardRatePerThousand.toFixed(2)} / 1K</span>
                </div>
              </div>

              <div className="space-y-1.5">
                <label className="text-[#A1A1AA] block font-mono">Required Hashtags & Mentions:</label>
                <div className="flex flex-wrap gap-1.5">
                  {selectedCampaignForReview.requirements.requiredHashtags.map(h => (
                    <span key={h} className="px-2 py-0.5 rounded bg-white/5 border border-white/10 text-[10px] font-mono text-violet-300">{h}</span>
                  ))}
                </div>
              </div>
            </div>

            <div className="flex items-center gap-3 pt-2">
              <button
                onClick={() => handleRejectCampaign(selectedCampaignForReview)}
                className="flex-1 py-2.5 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 text-xs font-semibold"
              >
                Reject Campaign
              </button>
              <button
                onClick={() => handleApproveCampaign(selectedCampaignForReview)}
                className="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-900/30"
              >
                Approve & Launch Live
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MODAL 3: RESOLVE FRAUD FLAG */}
      {selectedFraudFlag && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-[#121218] border border-white/[0.1] rounded-2xl max-w-md w-full p-6 space-y-5 shadow-2xl">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2">
                <ShieldAlert className="w-5 h-5 text-rose-400" />
                <h3 className="text-base font-bold text-white font-display">Resolve Fraud Flag</h3>
              </div>
              <button onClick={() => setSelectedFraudFlag(null)} className="text-[#71717A] hover:text-white">
                <X className="w-4 h-4" />
              </button>
            </div>

            <div className="p-4 rounded-xl bg-[#0B0B10] border border-white/[0.06] space-y-2 text-xs">
              <div className="flex items-center justify-between font-mono">
                <span className="font-bold text-white">{selectedFraudFlag.creatorHandle}</span>
                <span className="text-rose-400 uppercase font-bold">{selectedFraudFlag.severity} Risk</span>
              </div>
              <p className="text-[#A1A1AA]">{selectedFraudFlag.reason}</p>
            </div>

            <div className="flex items-center gap-3 pt-2">
              <button
                onClick={() => handleClearFraudFlag(selectedFraudFlag)}
                className="flex-1 py-2.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 text-xs font-semibold"
              >
                Clear (False Alarm)
              </button>
              <button
                onClick={() => handleConfirmFraudAndSuspend(selectedFraudFlag)}
                className="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold shadow-lg shadow-rose-900/30"
              >
                Confirm & Suspend
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MODAL 4: SYSTEM ERROR STACKTRACE INSPECTOR */}
      {selectedServerLog && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
          <div className="bg-[#121218] border border-white/[0.12] rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl animate-in fade-in zoom-in-95">
            <div className="flex items-center justify-between border-b border-white/[0.08] pb-4">
              <div className="flex items-center gap-2.5">
                <div className="p-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400">
                  <Bug className="w-5 h-5" />
                </div>
                <div>
                  <div className="flex items-center gap-2">
                    <span className={`px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase border ${
                      selectedServerLog.level === 'CRITICAL' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' :
                      selectedServerLog.level === 'WARNING' ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' :
                      'bg-rose-500/20 text-rose-300 border-rose-500/30'
                    }`}>
                      {selectedServerLog.level}
                    </span>
                    <span className="text-xs text-[#71717A] font-mono">{selectedServerLog.timestamp}</span>
                  </div>
                  <h3 className="text-sm font-bold text-white font-mono mt-1 line-clamp-1">{selectedServerLog.message}</h3>
                </div>
              </div>
              <button 
                onClick={() => setSelectedServerLog(null)} 
                className="p-1.5 rounded-lg text-[#71717A] hover:text-white hover:bg-white/5 transition-all cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <span className="text-xs text-[#A1A1AA] font-mono uppercase tracking-wider text-[10px]">Stacktrace & Exception Origin</span>
                <button
                  onClick={() => {
                    navigator.clipboard.writeText(`${selectedServerLog.message}\n\n${selectedServerLog.stacktrace}`);
                    showToast('Stacktrace copied to clipboard.');
                  }}
                  className="px-2.5 py-1 bg-white/5 hover:bg-white/10 text-white rounded-lg text-xs font-mono flex items-center gap-1.5 border border-white/10 transition-all cursor-pointer"
                >
                  <Copy className="w-3.5 h-3.5" />
                  <span>Copy Trace</span>
                </button>
              </div>

              <div className="bg-[#0B0B10] border border-white/[0.08] rounded-xl p-4 max-h-[350px] overflow-y-auto font-mono text-[11px] leading-relaxed text-[#D4D4D8] space-y-1 select-text">
                <div className="text-rose-400 font-bold mb-2 pb-2 border-b border-white/[0.06] break-all">
                  {selectedServerLog.message}
                </div>
                {selectedServerLog.stacktrace ? (
                  <pre className="whitespace-pre-wrap text-[#A1A1AA]">{selectedServerLog.stacktrace}</pre>
                ) : (
                  <div className="text-[#71717A] italic">No nested stacktrace provided for this log entry.</div>
                )}
              </div>
            </div>

            <div className="flex items-center justify-end pt-2">
              <button
                onClick={() => setSelectedServerLog(null)}
                className="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-violet-900/30 transition-all cursor-pointer"
              >
                Close Inspector
              </button>
            </div>
          </div>
        </div>
      )}

      {/* COMMAND PALETTE (CTRL+K) */}
      {isCommandPaletteOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-start justify-center pt-24 px-4">
          <div className="bg-[#121218] border border-white/[0.15] rounded-2xl max-w-xl w-full p-4 space-y-4 shadow-2xl animate-in fade-in zoom-in-95">
            <div className="flex items-center gap-3 px-2 border-b border-white/[0.08] pb-3">
              <Search className="w-4 h-4 text-violet-400" />
              <input
                type="text"
                autoFocus
                placeholder="Type a command or jump to resource..."
                className="w-full bg-transparent text-sm text-white focus:outline-none placeholder:text-[#71717A]"
              />
              <kbd className="text-[10px] font-mono bg-white/10 px-2 py-0.5 rounded text-[#D4D4D8]">ESC</kbd>
            </div>

            <div className="space-y-1 text-xs">
              <button
                onClick={() => { setCurrentNav('overview_command'); setIsCommandPaletteOpen(false); }}
                className="w-full text-left px-3 py-2 rounded-lg hover:bg-white/5 flex items-center justify-between text-white"
              >
                <span className="flex items-center gap-2"><Zap className="w-3.5 h-3.5 text-amber-400" /> Open Command Center</span>
                <span className="text-[10px] font-mono text-[#71717A]">Action Queues</span>
              </button>
              <button
                onClick={() => { setCurrentNav('fin_withdrawals'); setIsCommandPaletteOpen(false); }}
                className="w-full text-left px-3 py-2 rounded-lg hover:bg-white/5 flex items-center justify-between text-white"
              >
                <span className="flex items-center gap-2"><CreditCard className="w-3.5 h-3.5 text-emerald-400" /> Review Withdrawals</span>
                <span className="text-[10px] font-mono text-[#71717A]">{pendingWithdrawalsCount} Pending</span>
              </button>
              <button
                onClick={() => { setCurrentNav('people_users'); setIsCommandPaletteOpen(false); }}
                className="w-full text-left px-3 py-2 rounded-lg hover:bg-white/5 flex items-center justify-between text-white"
              >
                <span className="flex items-center gap-2"><Users className="w-3.5 h-3.5 text-blue-400" /> Search User Directory</span>
                <span className="text-[10px] font-mono text-[#71717A]">Users & RBAC</span>
              </button>
              <button
                onClick={() => { setCurrentNav('fraud_queue'); setIsCommandPaletteOpen(false); }}
                className="w-full text-left px-3 py-2 rounded-lg hover:bg-white/5 flex items-center justify-between text-white"
              >
                <span className="flex items-center gap-2"><ShieldAlert className="w-3.5 h-3.5 text-rose-400" /> Audit Fraud Flags</span>
                <span className="text-[10px] font-mono text-[#71717A]">{activeFraudCount} Flags</span>
              </button>
            </div>
          </div>
        </div>
      )}

    </div>
  );
};
