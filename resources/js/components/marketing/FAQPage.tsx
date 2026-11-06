import React, { useState } from 'react';
import { ChevronDown, HelpCircle, ArrowRight } from 'lucide-react';

interface FAQPageProps {
  onExploreCampaigns: () => void;
  onLaunchCampaign: () => void;
}

export const FAQPage: React.FC<FAQPageProps> = ({
  onExploreCampaigns,
  onLaunchCampaign
}) => {
  const [openIdx, setOpenIdx] = useState<number | null>(0);

  const faqs = [
    {
      q: 'How does GEZUX verify eligible views?',
      a: 'GEZUX connects to social platform analytics telemetry and applies automated heuristic auditing to ensure views originate from organic user engagement and match the target country demographics defined by the campaign.'
    },
    {
      q: 'How do creators get paid?',
      a: 'Once your submitted clip is verified, your creator wallet is credited based on the campaign reward rate (e.g. $1.00 per 1,000 eligible views). You can request instant payout withdrawals via Wise, Direct Bank Transfer, USDC Crypto, or PayPal.'
    },
    {
      q: 'Can brands set custom rules for their clips?',
      a: 'Yes. When launching a campaign in the Brand Studio, you can specify mandatory hashtags, brand mentions, allowed clip durations (15–60s), 9:16 aspect ratios, target country multipliers, and prohibited sensitive topics.'
    },
    {
      q: 'What platforms are supported?',
      a: 'GEZUX supports short-form video distribution across TikTok, YouTube Shorts, Instagram Reels, Facebook Reels, and X (Twitter).'
    },
    {
      q: 'Are there any platform fees?',
      a: 'GEZUX charges zero upfront fees to creators. Brands fund their campaign pool with transparent pricing, and all reward rates shown on the bounty board are paid out directly to verified clippers.'
    }
  ];

  return (
    <div className="pt-32 pb-24 min-h-screen bg-gray-50 dark:bg-[#070708]">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        {/* Header */}
        <div className="text-center space-y-3">
          <span className="text-xs font-mono font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400">
            Frequently Asked Questions
          </span>
          <h1 className="text-3xl sm:text-5xl font-display font-extrabold text-gray-900 dark:text-white tracking-tight">
            Everything You Need to Know
          </h1>
          <p className="text-sm text-gray-600 dark:text-[#A1A1AA]">
            Clear answers about performance clipping, view auditing, and payout distribution.
          </p>
        </div>

        {/* FAQ Accordion List */}
        <div className="space-y-3">
          {faqs.map((faq, i) => {
            const isOpen = openIdx === i;
            return (
              <div 
                key={i}
                className="rounded-2xl bg-white dark:bg-[#0B0B10] border border-gray-200 dark:border-white/[0.08] shadow-sm dark:shadow-md overflow-hidden transition-all"
              >
                <button
                  onClick={() => setOpenIdx(isOpen ? null : i)}
                  className="w-full p-5 text-left flex items-center justify-between gap-4 font-semibold text-sm text-gray-900 dark:text-white hover:text-violet-600 dark:hover:text-violet-300 transition-colors cursor-pointer"
                >
                  <span>{faq.q}</span>
                  <ChevronDown className={`w-4 h-4 text-gray-500 dark:text-[#71717A] shrink-0 transition-transform duration-200 ${isOpen ? 'rotate-180 text-violet-600 dark:text-violet-400' : ''}`} />
                </button>
                {isOpen && (
                  <div className="px-5 pb-5 text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed border-t border-gray-100 dark:border-white/[0.04] pt-3">
                    {faq.a}
                  </div>
                )}
              </div>
            );
          })}
        </div>

        {/* Bottom CTA */}
        <div className="p-8 rounded-2xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 text-center space-y-4 shadow-sm dark:shadow-md">
          <h3 className="text-lg font-bold text-gray-900 dark:text-white">Still have questions?</h3>
          <p className="text-xs text-gray-600 dark:text-[#A1A1AA]">Ready to scale your long-form video or start earning as an editor?</p>
          <div className="flex justify-center gap-3">
            <button
              onClick={onExploreCampaigns}
              className="px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-xs font-semibold rounded-xl cursor-pointer"
            >
              Start Clipping
            </button>
            <button
              onClick={onLaunchCampaign}
              className="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 text-gray-900 dark:text-white text-xs font-semibold rounded-xl border border-gray-200 dark:border-white/10 cursor-pointer"
            >
              Launch Campaign
            </button>
          </div>
        </div>

      </div>
    </div>
  );
};
