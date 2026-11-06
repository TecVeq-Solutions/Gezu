import React from 'react';
import { X, ShieldCheck, FileText } from 'lucide-react';

interface LegalModalProps {
  type: 'terms' | 'privacy' | 'refund' | null;
  onClose: () => void;
}

export const LegalModal: React.FC<LegalModalProps> = ({ type, onClose }) => {
  if (!type) return null;

  const content = {
    terms: {
      title: 'Terms of Service',
      badge: 'Platform Rules',
      body: `1. Acceptance of Terms: By registering for or using GEZUX, you agree to comply with performance verification standards and campaign guidelines.
2. Campaign Rules: All clippers must adhere to campaign-specific rules, including aspect ratios (9:16 vertical), duration constraints, and required hashtags.
3. Verification & Earnings: Rewards are calculated solely from verified eligible views audited by GEZUX telemetry. Inorganic, bot, or spoofed view patterns result in immediate submission disqualification.
4. Independent Contractor Status: Clippers operate as independent video editors and are responsible for applicable local tax compliance.`
    },
    privacy: {
      title: 'Privacy Policy',
      badge: 'Data Security',
      body: `1. Data Collection: GEZUX collects channel usernames, submission URLs, and public video performance metrics to verify campaign performance.
2. No Private Credentials: GEZUX never stores or requests social account passwords.
3. Financial Records: Payout destinations and transaction logs are encrypted in accordance with strict treasury audit standards.
4. Third-Party Sharing: Telemetry data is shared with campaign owners exclusively for distribution verification.`
    },
    refund: {
      title: 'Campaign Fund & Refund Policy',
      badge: 'Treasury Protections',
      body: `1. Unused Campaign Budget: If a campaign reaches its expiration date before the target view ceiling is reached, remaining unallocated funds may be refunded or reallocated.
2. Verified Work: Funds already disbursed or allocated for verified eligible views submitted by clippers are non-refundable.
3. Dispute Resolution: If brand-safety criteria are violated by a submission, campaign owners may flag submissions for administrator audit within 72 hours.`
    }
  }[type];

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-200">
      <div className="relative w-full max-w-xl bg-[#0B0B10] rounded-2xl border border-white/15 p-6 sm:p-8 shadow-2xl overflow-y-auto max-h-[85vh]">
        
        <div className="flex items-center justify-between pb-4 border-b border-white/[0.08]">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-violet-400">
              <FileText className="w-4 h-4" />
            </div>
            <div>
              <span className="text-[10px] font-mono text-violet-400 uppercase font-bold">{content.badge}</span>
              <h3 className="text-lg font-bold text-white font-display">{content.title}</h3>
            </div>
          </div>
          <button onClick={onClose} className="p-1.5 text-[#A1A1AA] hover:text-white rounded-lg hover:bg-white/5">
            <X className="w-5 h-5" />
          </button>
        </div>

        <div className="mt-5 text-xs text-[#D4D4D8] whitespace-pre-line leading-relaxed space-y-3 font-sans">
          {content.body}
        </div>

        <div className="mt-6 pt-4 border-t border-white/[0.08] flex justify-end">
          <button
            onClick={onClose}
            className="px-5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg text-xs font-semibold"
          >
            Close Document
          </button>
        </div>

      </div>
    </div>
  );
};
