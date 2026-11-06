import React, { useState } from 'react';
import { Campaign, Platform, ClipSubmission } from '../../types';
import { X, Video, Link, ShieldCheck, CheckCircle2, AlertCircle, Sparkles } from 'lucide-react';

interface SubmitClipModalProps {
  isOpen: boolean;
  onClose: () => void;
  campaigns: Campaign[];
  selectedCampaign?: Campaign | null;
  onSubmit: (newSubmission: ClipSubmission) => void;
}

export const SubmitClipModal: React.FC<SubmitClipModalProps> = ({
  isOpen,
  onClose,
  campaigns,
  selectedCampaign,
  onSubmit
}) => {
  const [campaignId, setCampaignId] = useState<string>(selectedCampaign?.id || campaigns[0]?.id || '');
  const [platform, setPlatform] = useState<Platform>('TikTok');
  const [postUrl, setPostUrl] = useState<string>('');
  const [clipTitle, setClipTitle] = useState<string>('');
  const [agreedToRules, setAgreedToRules] = useState<boolean>(false);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false);
  const [submittedSuccess, setSubmittedSuccess] = useState<boolean>(false);

  if (!isOpen) return null;

  const currentCamp = campaigns.find(c => c.id === campaignId) || campaigns[0];

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!postUrl || !agreedToRules) return;

    setIsSubmitting(true);
    
    setTimeout(() => {
      const newSub: ClipSubmission = {
        id: `sub-${Date.now().toString().slice(-4)}`,
        campaignId: currentCamp.id,
        campaignTitle: currentCamp.title,
        brandName: currentCamp.brandName,
        creatorHandle: '@AlexClips',
        creatorAvatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=120&q=80',
        platform,
        postUrl,
        externalPostId: `post_${Math.floor(Math.random() * 9000000 + 1000000)}`,
        submittedAt: new Date().toISOString().replace('T', ' ').slice(0, 16),
        status: 'under_review',
        observedViews: 1200,
        eligibleViews: 1150,
        estimatedReward: (1150 / 1000) * currentCamp.rewardRatePerThousand,
        fraudRiskScore: 2
      };

      onSubmit(newSub);
      setIsSubmitting(false);
      setSubmittedSuccess(true);
      
      setTimeout(() => {
        setSubmittedSuccess(false);
        setPostUrl('');
        setClipTitle('');
        onClose();
      }, 1600);
    }, 1000);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-200">
      <div className="relative w-full max-w-lg bg-[#0B0B10] rounded-2xl border border-white/15 p-6 sm:p-8 shadow-2xl overflow-hidden">
        
        {/* Glow */}
        <div className="absolute top-0 right-0 w-48 h-48 bg-violet-600/15 blur-[80px] rounded-full pointer-events-none" />

        {/* Header */}
        <div className="flex items-center justify-between pb-4 border-b border-white/[0.08]">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded-lg bg-violet-950/80 border border-violet-500/30 flex items-center justify-center text-violet-400">
              <Video className="w-4 h-4" />
            </div>
            <div>
              <h3 className="text-base font-display font-bold text-white">
                Submit Published Clip
              </h3>
              <p className="text-xs text-[#A1A1AA]">
                Register your social post for automated view verification
              </p>
            </div>
          </div>
          <button 
            onClick={onClose}
            className="p-1.5 text-[#A1A1AA] hover:text-white rounded-lg hover:bg-white/5 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {submittedSuccess ? (
          <div className="py-12 text-center space-y-3">
            <div className="w-14 h-14 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center mx-auto animate-bounce">
              <CheckCircle2 className="w-8 h-8" />
            </div>
            <h4 className="text-lg font-bold text-white">Submission Registered!</h4>
            <p className="text-xs text-[#A1A1AA] max-w-xs mx-auto">
              Status: <span className="text-emerald-400 font-mono font-bold">UNDER VERIFICATION</span>. Realtime view telemetry initialized.
            </p>
          </div>
        ) : (
          <form onSubmit={handleSubmit} className="mt-6 space-y-4">
            
            {/* Target Campaign */}
            <div>
              <label className="text-xs text-[#D4D4D8] font-mono block mb-1">
                Target Campaign:
              </label>
              <select
                value={campaignId}
                onChange={(e) => setCampaignId(e.target.value)}
                className="w-full bg-[#121218] border border-white/10 rounded-lg p-2.5 text-xs text-white focus:outline-none focus:border-violet-500"
              >
                {campaigns.map(c => (
                  <option key={c.id} value={c.id} className="bg-[#121218] text-white">
                    {c.title} — {c.brandName} (${c.rewardRatePerThousand}/1K)
                  </option>
                ))}
              </select>
            </div>

            {/* Platform Selection */}
            <div>
              <label className="text-xs text-[#D4D4D8] font-mono block mb-1">
                Distribution Platform:
              </label>
              <div className="grid grid-cols-4 gap-2">
                {(['TikTok', 'Instagram', 'YouTube Shorts', 'X'] as Platform[]).map((p) => (
                  <button
                    key={p}
                    type="button"
                    onClick={() => setPlatform(p)}
                    className={`py-2 text-xs font-mono rounded-lg border transition-all ${platform === p ? 'bg-violet-600 border-violet-400 text-white font-bold' : 'bg-[#121218] border-white/10 text-[#A1A1AA] hover:text-white'}`}
                  >
                    {p}
                  </button>
                ))}
              </div>
            </div>

            {/* Published Video URL */}
            <div>
              <label className="text-xs text-[#D4D4D8] font-mono block mb-1">
                Published Video Link (Public URL):
              </label>
              <div className="relative">
                <input
                  type="url"
                  required
                  placeholder="e.g. https://www.tiktok.com/@AlexClips/video/..."
                  value={postUrl}
                  onChange={(e) => setPostUrl(e.target.value)}
                  className="w-full bg-[#121218] border border-white/10 rounded-lg pl-9 pr-3 py-2.5 text-xs text-white focus:outline-none focus:border-violet-500 font-mono"
                />
                <Link className="w-4 h-4 text-[#71717A] absolute left-3 top-3" />
              </div>
            </div>

            {/* Connected Account indicator */}
            <div className="p-3 rounded-lg bg-white/[0.03] border border-white/[0.06] flex items-center justify-between text-xs">
              <span className="text-[#A1A1AA]">Connected Creator Channel:</span>
              <span className="font-mono font-bold text-violet-400 flex items-center gap-1">
                <CheckCircle2 className="w-3.5 h-3.5 text-emerald-400" />
                @AlexClips (Verified)
              </span>
            </div>

            {/* Requirements check confirmation */}
            <div className="pt-2">
              <label className="flex items-start gap-2.5 cursor-pointer text-xs text-[#A1A1AA] select-none">
                <input
                  type="checkbox"
                  required
                  checked={agreedToRules}
                  onChange={(e) => setAgreedToRules(e.target.checked)}
                  className="mt-0.5 accent-violet-500 rounded"
                />
                <span>
                  I confirm this clip follows campaign requirements (9:16 vertical, required hashtags: <code className="text-violet-300 font-mono">{currentCamp.requirements.requiredHashtags.join(' ')}</code>, original edits).
                </span>
              </label>
            </div>

            {/* Action buttons */}
            <div className="pt-4 border-t border-white/[0.08] flex items-center justify-end gap-3">
              <button
                type="button"
                onClick={onClose}
                className="px-4 py-2 text-xs font-medium text-[#A1A1AA] hover:text-white"
              >
                Cancel
              </button>
              <button
                type="submit"
                disabled={isSubmitting || !postUrl || !agreedToRules}
                className="px-6 py-2 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-500 disabled:opacity-50 rounded-lg shadow-lg shadow-violet-800/30 transition-all flex items-center gap-1.5 cursor-pointer"
              >
                {isSubmitting ? (
                  <span>Verifying Link...</span>
                ) : (
                  <>
                    <ShieldCheck className="w-3.5 h-3.5" />
                    Submit for Verification
                  </>
                )}
              </button>
            </div>

          </form>
        )}

      </div>
    </div>
  );
};
