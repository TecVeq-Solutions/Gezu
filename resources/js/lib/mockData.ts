import { Campaign, ClipSubmission, SocialAccount, Wallet, WithdrawalRequest, FraudFlag } from '../types';

export const INITIAL_CAMPAIGNS: Campaign[] = [
  {
    id: 'camp-1',
    title: 'The Future of AI — Podcast #42',
    slug: 'future-labs-podcast-42',
    brandName: 'Future Labs',
    category: 'Technology & AI',
    description: 'We sat down with leading neural network researchers to discuss autonomous robotics, reasoning models, and agent architectures. Extract 30–60s impactful thought-provoking moments with clean subtitles.',
    sourceVideoUrl: 'https://youtube.com/watch?v=sample-future-labs-42',
    sourceDuration: '1h 14m',
    thumbnail: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80',
    totalBudget: 5000,
    remainingBudget: 1257.19,
    rewardRatePerThousand: 1.00,
    currency: 'USD',
    targetViews: 5000000,
    verifiedViews: 3742810,
    status: 'active',
    platforms: ['TikTok', 'Instagram', 'YouTube Shorts'],
    countries: [
      { countryCode: 'US', countryName: 'United States', rewardMultiplier: 1.0, targetViews: 3000000 },
      { countryCode: 'GB', countryName: 'United Kingdom', rewardMultiplier: 0.95, targetViews: 1000000 },
      { countryCode: 'CA', countryName: 'Canada', rewardMultiplier: 0.90, targetViews: 1000000 },
    ],
    requirements: {
      aspectRatio: '9:16 Vertical',
      duration: '15–60 Seconds',
      requiredHashtags: ['#FutureLabs', '#AITechnology', '#TechPodcast', '#GEZUX'],
      requiredMentions: ['@FutureLabsTech'],
      prohibitedContent: ['Misleading titles', 'AI voice cloning over real speaker', 'Unauthorized music'],
      brandMentionRequired: true
    },
    deadline: '2026-10-25',
    daysRemaining: 18,
    totalSubmissions: 84,
    createdAt: '2026-09-15'
  },
  {
    id: 'camp-2',
    title: 'Northstar Keynote: Next-Gen GPU Architecture',
    slug: 'northstar-gpu-keynote',
    brandName: 'Northstar Media',
    category: 'Hardware & Tech',
    description: 'Highlight the explosive silicon benchmark reveals and energy efficiency comparisons from our annual keynote. Looking for high-energy fast cuts with caption animation.',
    sourceVideoUrl: 'https://youtube.com/watch?v=sample-northstar-keynote',
    sourceDuration: '52m 10s',
    thumbnail: 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80',
    totalBudget: 3500,
    remainingBudget: 1556.00,
    rewardRatePerThousand: 1.20,
    currency: 'USD',
    targetViews: 2900000,
    verifiedViews: 1620000,
    status: 'active',
    platforms: ['TikTok', 'YouTube Shorts', 'X'],
    countries: [
      { countryCode: 'US', countryName: 'United States', rewardMultiplier: 1.0, targetViews: 1800000 },
      { countryCode: 'DE', countryName: 'Germany', rewardMultiplier: 0.95, targetViews: 600000 },
      { countryCode: 'AU', countryName: 'Australia', rewardMultiplier: 0.90, targetViews: 500000 },
    ],
    requirements: {
      aspectRatio: '9:16 Vertical',
      duration: '20–45 Seconds',
      requiredHashtags: ['#NorthstarSilicon', '#GPUWars', '#TechNews'],
      requiredMentions: ['@NorthstarMedia'],
      prohibitedContent: ['Unverified benchmark numbers', 'Low-res re-uploads'],
      brandMentionRequired: true
    },
    deadline: '2026-10-30',
    daysRemaining: 23,
    totalSubmissions: 52,
    createdAt: '2026-09-18'
  },
  {
    id: 'camp-3',
    title: 'Building in Public — Founder Series Ep 12',
    slug: 'founder-series-ep-12',
    brandName: 'The Founder Podcast',
    category: 'Business & Startups',
    description: 'How a bootstrapped founder scaled to $10M ARR in 24 months without VC funding. Emphasize the hard lessons, failure anecdotes, and tactical pricing strategies.',
    sourceVideoUrl: 'https://youtube.com/watch?v=sample-founder-series',
    sourceDuration: '1h 02m',
    thumbnail: 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?auto=format&fit=crop&w=800&q=80',
    totalBudget: 2000,
    remainingBudget: 344.00,
    rewardRatePerThousand: 0.90,
    currency: 'USD',
    targetViews: 2200000,
    verifiedViews: 1840000,
    status: 'active',
    platforms: ['Instagram', 'TikTok', 'YouTube Shorts'],
    countries: [
      { countryCode: 'US', countryName: 'United States', rewardMultiplier: 1.0, targetViews: 1200000 },
      { countryCode: 'GB', countryName: 'United Kingdom', rewardMultiplier: 0.95, targetViews: 600000 },
      { countryCode: 'AE', countryName: 'UAE', rewardMultiplier: 1.1, targetViews: 400000 },
    ],
    requirements: {
      aspectRatio: '9:16 Vertical',
      duration: '30–60 Seconds',
      requiredHashtags: ['#StartupHustle', '#SaaSFounder', '#BusinessTips'],
      requiredMentions: ['@FounderPod'],
      prohibitedContent: ['Get rich quick claims', 'Sensationalized clickbait'],
      brandMentionRequired: true
    },
    deadline: '2026-10-12',
    daysRemaining: 6,
    totalSubmissions: 67,
    createdAt: '2026-09-10'
  },
  {
    id: 'camp-4',
    title: 'Vertex Studio — Realtime Physics Engine Demo',
    slug: 'vertex-studio-physics',
    brandName: 'Vertex Studios',
    category: 'Gaming & VFX',
    description: 'Next-gen water, destruction, and cloth simulation showcases. Visual hooks must occur within the first 2 seconds. Excellent clips can earn performance bonus multipliers.',
    sourceVideoUrl: 'https://youtube.com/watch?v=sample-vertex-studio',
    sourceDuration: '38m 45s',
    thumbnail: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=800&q=80',
    totalBudget: 4000,
    remainingBudget: 2922.00,
    rewardRatePerThousand: 1.10,
    currency: 'USD',
    targetViews: 3600000,
    verifiedViews: 980000,
    status: 'active',
    platforms: ['TikTok', 'YouTube Shorts', 'X'],
    countries: [
      { countryCode: 'US', countryName: 'United States', rewardMultiplier: 1.0, targetViews: 2000000 },
      { countryCode: 'CA', countryName: 'Canada', rewardMultiplier: 0.90, targetViews: 800000 },
      { countryCode: 'DE', countryName: 'Germany', rewardMultiplier: 0.95, targetViews: 800000 },
    ],
    requirements: {
      aspectRatio: '9:16 Vertical',
      duration: '15–30 Seconds',
      requiredHashtags: ['#VFX', '#GameDev', '#UnrealEngine', '#PhysicsSim'],
      requiredMentions: ['@VertexEngine'],
      prohibitedContent: ['Watermarked third-party clips', 'Static footage without edits'],
      brandMentionRequired: false
    },
    deadline: '2026-11-05',
    daysRemaining: 38,
    totalSubmissions: 39,
    createdAt: '2026-09-22'
  }
];

export const INITIAL_SUBMISSIONS: ClipSubmission[] = [
  {
    id: 'sub-101',
    campaignId: 'camp-1',
    campaignTitle: 'The Future of AI — Podcast #42',
    brandName: 'Future Labs',
    creatorHandle: '@AlexClips',
    creatorAvatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80',
    platform: 'TikTok',
    postUrl: 'https://www.tiktok.com/@AlexClips/video/7419284192',
    externalPostId: '7419284192',
    submittedAt: '2026-09-26 14:30',
    status: 'verified',
    observedViews: 132400,
    eligibleViews: 128000,
    estimatedReward: 128.00,
    verifiedReward: 128.00,
    fraudRiskScore: 4
  },
  {
    id: 'sub-102',
    campaignId: 'camp-1',
    campaignTitle: 'The Future of AI — Podcast #42',
    brandName: 'Future Labs',
    creatorHandle: '@MediaForge',
    creatorAvatar: 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=120&q=80',
    platform: 'Instagram',
    postUrl: 'https://www.instagram.com/reel/C-x82910fa',
    externalPostId: 'C-x82910fa',
    submittedAt: '2026-09-27 09:15',
    status: 'verified',
    observedViews: 86500,
    eligibleViews: 84000,
    estimatedReward: 84.00,
    verifiedReward: 84.00,
    fraudRiskScore: 8
  },
  {
    id: 'sub-103',
    campaignId: 'camp-2',
    campaignTitle: 'Northstar Keynote: Next-Gen GPU Architecture',
    brandName: 'Northstar Media',
    creatorHandle: '@ViralCut',
    creatorAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80',
    platform: 'YouTube Shorts',
    postUrl: 'https://youtube.com/shorts/n8v2Kld991',
    externalPostId: 'n8v2Kld991',
    submittedAt: '2026-09-27 18:40',
    status: 'under_review',
    observedViews: 64200,
    eligibleViews: 62000,
    estimatedReward: 74.40,
    fraudRiskScore: 12
  },
  {
    id: 'sub-104',
    campaignId: 'camp-3',
    campaignTitle: 'Building in Public — Founder Series Ep 12',
    brandName: 'The Founder Podcast',
    creatorHandle: '@FrameTheory',
    creatorAvatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80',
    platform: 'TikTok',
    postUrl: 'https://www.tiktok.com/@FrameTheory/video/7421199401',
    externalPostId: '7421199401',
    submittedAt: '2026-09-28 08:20',
    status: 'under_review',
    observedViews: 19500,
    eligibleViews: 19000,
    estimatedReward: 17.10,
    fraudRiskScore: 6
  }
];

export const INITIAL_SOCIAL_ACCOUNTS: SocialAccount[] = [
  {
    id: 'soc-1',
    platform: 'TikTok',
    username: '@AlexClips',
    displayName: 'Alex Rivers | Tech Cuts',
    avatarUrl: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80',
    verified: true,
    connectedAt: '2026-08-12',
    followersCount: 248000
  },
  {
    id: 'soc-2',
    platform: 'YouTube Shorts',
    username: '@AlexClipsOfficial',
    displayName: 'Alex Clips (Shorts)',
    verified: true,
    connectedAt: '2026-08-20',
    followersCount: 112000
  },
  {
    id: 'soc-3',
    platform: 'Instagram',
    username: '@alex.edits.pro',
    displayName: 'Alex Edits',
    verified: false,
    connectedAt: '2026-09-01',
    followersCount: 45000
  },
  {
    id: 'soc-4',
    platform: 'X',
    username: '@alex_edits',
    displayName: 'Alex R.',
    verified: true,
    connectedAt: '2026-09-14',
    followersCount: 18400
  }
];

export const INITIAL_WALLET: Wallet = {
  availableBalance: 1420.50,
  pendingBalance: 340.00,
  totalWithdrawn: 4890.00,
  currency: 'USD',
  transactions: [
    {
      id: 'tx-101',
      type: 'campaign_reward',
      amount: 128.00,
      currency: 'USD',
      status: 'completed',
      description: 'Verified Views Reward — Podcast #42 Clip #1',
      date: '2026-09-26 18:00',
      referenceId: 'sub-101'
    },
    {
      id: 'tx-102',
      type: 'campaign_reward',
      amount: 84.00,
      currency: 'USD',
      status: 'completed',
      description: 'Verified Views Reward — Podcast #42 Clip #2',
      date: '2026-09-27 12:30',
      referenceId: 'sub-102'
    },
    {
      id: 'tx-103',
      type: 'withdrawal',
      amount: -500.00,
      currency: 'USD',
      status: 'completed',
      description: 'Payout Transfer via Wise (to ****4821)',
      date: '2026-09-20 10:14',
      referenceId: 'wd-501'
    },
    {
      id: 'tx-104',
      type: 'campaign_reward',
      amount: 215.50,
      currency: 'USD',
      status: 'completed',
      description: 'Verified Views Reward — AI Hardware Showcase',
      date: '2026-09-15 16:45',
      referenceId: 'sub-088'
    }
  ]
};

export const INITIAL_WITHDRAWALS: WithdrawalRequest[] = [
  {
    id: 'wd-503',
    amount: 1250.00,
    currency: 'USD',
    method: 'Crypto (USDC)',
    destination: '0x71C...B849 (Polygon PoS)',
    status: 'pending',
    requestedAt: '2026-09-28 14:15',
    creatorHandle: '@ViralClipper_Sam',
    creatorEmail: 'sam.clipping@gmail.com'
  },
  {
    id: 'wd-504',
    amount: 480.00,
    currency: 'USD',
    method: 'Wise',
    destination: 'elena.rostova@transfer.me (EUR IBAN)',
    status: 'pending',
    requestedAt: '2026-09-28 11:02',
    creatorHandle: '@ElenaShorts',
    creatorEmail: 'elena.rostova@gmail.com'
  },
  {
    id: 'wd-505',
    amount: 820.00,
    currency: 'USD',
    method: 'Bank Transfer',
    destination: 'HSBC UK (Sort: 40-12-34, Acct: 88419201)',
    status: 'pending',
    requestedAt: '2026-09-28 08:30',
    creatorHandle: '@ZenithClips',
    creatorEmail: 'zenith@zenithmedia.co'
  },
  {
    id: 'wd-501',
    amount: 500.00,
    currency: 'USD',
    method: 'Wise',
    destination: 'alex.rivers@email.com (USD Account)',
    status: 'completed',
    requestedAt: '2026-09-20 09:30',
    creatorHandle: '@AlexRivers',
    creatorEmail: 'alex.rivers@email.com'
  },
  {
    id: 'wd-502',
    amount: 350.00,
    currency: 'USD',
    method: 'Bank Transfer',
    destination: 'Chase Bank (Acct Ending in 8841)',
    status: 'completed',
    requestedAt: '2026-09-02 11:20',
    creatorHandle: '@TechPulse',
    creatorEmail: 'pulse@gmail.com'
  }
];

export const INITIAL_FRAUD_FLAGS: FraudFlag[] = [
  {
    id: 'ff-01',
    submissionId: 'sub-092',
    creatorHandle: '@SpamBot_99',
    campaignTitle: 'Future Labs Podcast #42',
    reason: 'Abnormal velocity: 18,400 views added in 90 seconds from datacenter IP blocks (OVH/Hetzner)',
    severity: 'high',
    status: 'under_review',
    createdAt: '2026-09-28 07:14'
  },
  {
    id: 'ff-02',
    submissionId: 'sub-083',
    creatorHandle: '@FastClipz',
    campaignTitle: 'Northstar Keynote: Next-Gen GPU',
    reason: 'Duplicate video hash: Match score 99.4% with previously submitted clip sub-041',
    severity: 'medium',
    status: 'under_review',
    createdAt: '2026-09-27 15:40'
  },
  {
    id: 'ff-03',
    submissionId: 'sub-071',
    creatorHandle: '@GhostReach',
    campaignTitle: 'AI Trading Bot Suite',
    reason: 'Platform API mismatch: TikTok view counter locked while external embed counter spiked',
    severity: 'high',
    status: 'under_review',
    createdAt: '2026-09-26 19:22'
  }
];

export const INITIAL_USERS: import('../types').UserRecord[] = [
  {
    id: 'usr-001',
    name: 'Marcus Vance',
    email: 'marcus@futurelabs.io',
    username: 'futurelabs_admin',
    role: 'BRAND',
    status: 'active',
    verified: true,
    balance: 14200.00,
    totalSpentOrEarned: 48500.00,
    joinedAt: '2026-08-10',
    avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&q=80',
    country: 'United States',
    campaignCount: 4,
    submissionsCount: 0
  },
  {
    id: 'usr-002',
    name: 'Samuel Rivera',
    email: 'sam.clipping@gmail.com',
    username: 'ViralClipper_Sam',
    role: 'CLIPPER',
    status: 'active',
    verified: true,
    balance: 1845.50,
    totalSpentOrEarned: 8920.00,
    joinedAt: '2026-08-14',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&q=80',
    country: 'United States',
    campaignCount: 0,
    submissionsCount: 42
  },
  {
    id: 'usr-003',
    name: 'Elena Rostova',
    email: 'elena.rostova@gmail.com',
    username: 'ElenaShorts',
    role: 'CLIPPER',
    status: 'active',
    verified: true,
    balance: 620.00,
    totalSpentOrEarned: 4310.00,
    joinedAt: '2026-08-22',
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80',
    country: 'Germany',
    campaignCount: 0,
    submissionsCount: 29
  },
  {
    id: 'usr-004',
    name: 'Devon Cross',
    email: 'devon@northstar.media',
    username: 'northstar_team',
    role: 'BRAND',
    status: 'active',
    verified: true,
    balance: 8500.00,
    totalSpentOrEarned: 22000.00,
    joinedAt: '2026-08-25',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=120&q=80',
    country: 'United Kingdom',
    campaignCount: 3,
    submissionsCount: 0
  },
  {
    id: 'usr-005',
    name: 'Sarah Chen (Admin)',
    email: 'sarah.admin@gezux.io',
    username: 'sarah_superadmin',
    role: 'ADMIN',
    status: 'active',
    verified: true,
    balance: 0,
    totalSpentOrEarned: 0,
    joinedAt: '2026-08-01',
    avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=120&q=80',
    country: 'Canada'
  },
  {
    id: 'usr-006',
    name: 'Tariq Al-Mansoor',
    email: 'tariq.finance@gezux.io',
    username: 'tariq_finance',
    role: 'FINANCE',
    status: 'active',
    verified: true,
    balance: 0,
    totalSpentOrEarned: 0,
    joinedAt: '2026-08-05',
    avatar: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=120&q=80',
    country: 'United Arab Emirates'
  },
  {
    id: 'usr-007',
    name: 'Spam Bot Syndicate',
    email: 'botfarm99@burner.cc',
    username: 'SpamBot_99',
    role: 'CLIPPER',
    status: 'suspended',
    verified: false,
    balance: 0.00,
    totalSpentOrEarned: 0.00,
    joinedAt: '2026-09-27',
    avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80',
    country: 'Unknown',
    campaignCount: 0,
    submissionsCount: 8
  }
];

export const INITIAL_AUDIT_LOGS: import('../types').AuditLogEntry[] = [
  {
    id: 'aud-901',
    adminName: 'Sarah Chen',
    adminEmail: 'sarah.admin@gezux.io',
    role: 'Super Admin',
    action: 'DISBURSE_PAYOUT_BATCH',
    targetType: 'WithdrawalRequest',
    targetId: 'wd-501',
    details: 'Approved $500.00 payout via Wise for creator @AlexRivers following anti-fraud clear',
    timestamp: '2026-09-28 15:10:22',
    ipAddress: '192.0.2.45'
  },
  {
    id: 'aud-902',
    adminName: 'Tariq Al-Mansoor',
    adminEmail: 'tariq.finance@gezux.io',
    role: 'Finance Manager',
    action: 'ESCROW_FUNDING_APPROVED',
    targetType: 'Campaign',
    targetId: 'camp-1',
    details: 'Verified $5,000.00 Stripe deposit for Future Labs Podcast campaign escrow',
    timestamp: '2026-09-28 12:44:09',
    ipAddress: '198.51.100.12'
  },
  {
    id: 'aud-903',
    adminName: 'Sarah Chen',
    adminEmail: 'sarah.admin@gezux.io',
    role: 'Super Admin',
    action: 'ACCOUNT_SUSPENDED',
    targetType: 'User',
    targetId: 'usr-007',
    details: 'Automated suspension trigger: Bot velocity detected on 3 consecutive submissions',
    timestamp: '2026-09-28 07:18:30',
    ipAddress: 'SYSTEM_BOT_GUARD'
  },
  {
    id: 'aud-904',
    adminName: 'Sarah Chen',
    adminEmail: 'sarah.admin@gezux.io',
    role: 'Super Admin',
    action: 'FEE_RULE_UPDATED',
    targetType: 'PlatformRule',
    targetId: 'fee_global',
    details: 'Updated Brand deposit fee from 4.0% to 5.0% with zero take rate on creator side',
    timestamp: '2026-09-27 18:00:11',
    ipAddress: '192.0.2.45'
  }
];

export const INITIAL_TELEMETRY_RECORDS: import('../types').TelemetryRecord[] = [
  {
    id: 'tel-101',
    submissionId: 'sub-101',
    creatorHandle: '@ViralClipper_Sam',
    campaignTitle: 'Future Labs Podcast #42',
    platform: 'TikTok',
    viewDelta: 42100,
    totalViews: 482900,
    velocityScore: 1420,
    flagged: false,
    timestamp: '2026-09-28 16:30:00'
  },
  {
    id: 'tel-102',
    submissionId: 'sub-092',
    creatorHandle: '@SpamBot_99',
    campaignTitle: 'Future Labs Podcast #42',
    platform: 'YouTube Shorts',
    viewDelta: 18400,
    totalViews: 19100,
    velocityScore: 9200,
    flagged: true,
    timestamp: '2026-09-28 07:14:00'
  },
  {
    id: 'tel-103',
    submissionId: 'sub-102',
    creatorHandle: '@ElenaShorts',
    campaignTitle: 'Future Labs Podcast #42',
    platform: 'Instagram',
    viewDelta: 12500,
    totalViews: 84000,
    velocityScore: 850,
    flagged: false,
    timestamp: '2026-09-28 15:45:00'
  },
  {
    id: 'tel-104',
    submissionId: 'sub-103',
    creatorHandle: '@ZenithClips',
    campaignTitle: 'Northstar Keynote: Next-Gen GPU',
    platform: 'YouTube Shorts',
    viewDelta: 54000,
    totalViews: 620000,
    velocityScore: 2100,
    flagged: false,
    timestamp: '2026-09-28 14:10:00'
  }
];

