export type UserRole = 'GUEST' | 'CLIPPER' | 'BRAND' | 'ADMIN';

export type Platform = 'TikTok' | 'Instagram' | 'YouTube Shorts' | 'Facebook' | 'X';

export type CampaignStatus = 'draft' | 'pending_review' | 'active' | 'paused' | 'completed' | 'cancelled';

export type SubmissionStatus = 'pending' | 'under_review' | 'verified' | 'rejected' | 'flagged';

export interface CampaignCountry {
  countryCode: string;
  countryName: string;
  rewardMultiplier: number;
  targetViews: number;
}

export interface Campaign {
  id: string;
  title: string;
  slug: string;
  brandName: string;
  brandLogo?: string;
  category: string;
  description: string;
  sourceVideoUrl: string;
  sourceDuration: string;
  thumbnail: string;
  totalBudget: number;
  remainingBudget: number;
  rewardRatePerThousand: number; // e.g. $1.00 / 1K views
  currency: string;
  targetViews: number;
  verifiedViews: number;
  status: CampaignStatus;
  platforms: Platform[];
  countries: CampaignCountry[];
  requirements: {
    aspectRatio: string;
    duration: string;
    requiredHashtags: string[];
    requiredMentions: string[];
    prohibitedContent: string[];
    brandMentionRequired: boolean;
  };
  deadline: string;
  daysRemaining: number;
  totalSubmissions: number;
  createdAt: string;
}

export interface ClipSubmission {
  id: string;
  campaignId: string;
  campaignTitle: string;
  brandName: string;
  creatorHandle: string;
  creatorAvatar: string;
  platform: Platform;
  postUrl: string;
  externalPostId: string;
  submittedAt: string;
  status: SubmissionStatus;
  observedViews: number;
  eligibleViews: number;
  estimatedReward: number;
  verifiedReward?: number;
  rejectionReason?: string;
  fraudRiskScore?: number; // 0-100
}

export interface SocialAccount {
  id: string;
  platform: Platform;
  username: string;
  displayName: string;
  avatarUrl?: string;
  verified: boolean;
  connectedAt: string;
  followersCount: number;
}

export interface WalletTransaction {
  id: string;
  type: 'campaign_reward' | 'withdrawal' | 'campaign_funding' | 'platform_fee' | 'adjustment';
  amount: number;
  currency: string;
  status: 'pending' | 'completed' | 'failed';
  description: string;
  date: string;
  referenceId?: string;
}

export interface Wallet {
  availableBalance: number;
  pendingBalance: number;
  totalWithdrawn: number;
  currency: string;
  transactions: WalletTransaction[];
}

export interface WithdrawalRequest {
  id: string;
  amount: number;
  currency: string;
  method: 'Bank Transfer' | 'Wise' | 'PayPal' | 'Crypto (USDC)';
  destination: string;
  status: 'pending' | 'processing' | 'completed' | 'rejected';
  requestedAt: string;
  creatorHandle?: string;
  creatorEmail?: string;
}

export interface FraudFlag {
  id: string;
  submissionId: string;
  creatorHandle: string;
  campaignTitle: string;
  reason: string;
  severity: 'low' | 'medium' | 'high';
  status: 'under_review' | 'confirmed' | 'cleared';
  createdAt: string;
}

export interface UserRecord {
  id: string;
  name: string;
  email: string;
  username: string;
  role: 'BRAND' | 'CLIPPER' | 'ADMIN' | 'MODERATOR' | 'FINANCE';
  status: 'active' | 'suspended' | 'pending_verification' | 'banned';
  verified: boolean;
  balance: number;
  totalSpentOrEarned: number;
  joinedAt: string;
  avatar?: string;
  country?: string;
  campaignCount?: number;
  submissionsCount?: number;
}

export interface AuditLogEntry {
  id: string;
  adminName: string;
  adminEmail: string;
  role: string;
  action: string;
  targetType: string;
  targetId: string;
  details: string;
  timestamp: string;
  ipAddress: string;
}

export interface TelemetryRecord {
  id: string;
  submissionId: string;
  creatorHandle: string;
  campaignTitle: string;
  platform: Platform;
  viewDelta: number;
  totalViews: number;
  velocityScore: number; // views / hour
  flagged: boolean;
  timestamp: string;
}
