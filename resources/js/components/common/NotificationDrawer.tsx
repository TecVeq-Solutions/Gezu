import React from 'react';
import { Bell, CheckCircle2, ShieldAlert, Sparkles, DollarSign, X } from 'lucide-react';

interface NotificationDrawerProps {
  isOpen: boolean;
  onClose: () => void;
}

export const NotificationDrawer: React.FC<NotificationDrawerProps> = ({
  isOpen,
  onClose
}) => {
  if (!isOpen) return null;

  const notifications = [
    {
      id: 'notif-1',
      title: 'Eligible Views Verified',
      desc: '128,000 views audited for Podcast #42 clip. +$128.00 credited to available balance.',
      time: '25m ago',
      type: 'reward',
      icon: DollarSign,
      color: 'text-emerald-400 bg-emerald-500/20'
    },
    {
      id: 'notif-2',
      title: 'Campaign Reached 74.8%',
      desc: 'The Future of AI Podcast #42 campaign has achieved 3,742,810 views.',
      time: '2h ago',
      type: 'campaign',
      icon: Sparkles,
      color: 'text-violet-400 bg-violet-500/20'
    },
    {
      id: 'notif-3',
      title: 'Payout Dispatched',
      desc: '$500.00 USD transfer completed via Wise (Transfer #WD-501).',
      time: '1d ago',
      type: 'payout',
      icon: CheckCircle2,
      color: 'text-blue-400 bg-blue-500/20'
    }
  ];

  return (
    <div className="fixed inset-0 z-50 flex items-start justify-end p-4 sm:p-6 bg-black/60 backdrop-blur-sm animate-in fade-in duration-150">
      <div className="w-full max-w-sm bg-[#0B0B10] rounded-2xl border border-white/15 p-5 shadow-2xl space-y-4 animate-in slide-in-from-right-6 duration-200">
        
        <div className="flex items-center justify-between pb-3 border-b border-white/[0.08]">
          <div className="flex items-center gap-2">
            <Bell className="w-4 h-4 text-violet-400" />
            <h3 className="text-sm font-bold text-white font-display">Notification Center</h3>
          </div>
          <button onClick={onClose} className="p-1 text-[#A1A1AA] hover:text-white">
            <X className="w-4 h-4" />
          </button>
        </div>

        <div className="divide-y divide-white/[0.04] space-y-2">
          {notifications.map(n => {
            const Icon = n.icon;
            return (
              <div key={n.id} className="pt-2 pb-1 flex items-start gap-3 text-xs">
                <div className={`w-8 h-8 rounded-lg ${n.color} flex items-center justify-center shrink-0 mt-0.5`}>
                  <Icon className="w-4 h-4" />
                </div>
                <div className="flex-1 space-y-0.5">
                  <div className="flex items-center justify-between">
                    <strong className="text-white font-semibold">{n.title}</strong>
                    <span className="text-[10px] text-[#71717A] font-mono">{n.time}</span>
                  </div>
                  <p className="text-[11px] text-[#A1A1AA] leading-normal">{n.desc}</p>
                </div>
              </div>
            );
          })}
        </div>

        <div className="pt-2 border-t border-white/[0.06] text-center">
          <button
            onClick={onClose}
            className="text-[11px] font-mono text-violet-400 hover:text-violet-300 font-semibold"
          >
            Mark All as Read
          </button>
        </div>

      </div>
    </div>
  );
};
