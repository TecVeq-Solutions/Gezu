import React from 'react';
import { ArrowRight, Video, Sparkles } from 'lucide-react';
import { Vortex } from './Vortex';
import { StarfieldButton } from '../common/StarfieldButton';

interface FinalCTAProps {
  onLaunchCampaign: () => void;
  onStartClipping: () => void;
}

export const FinalCTA: React.FC<FinalCTAProps> = ({
  onLaunchCampaign,
  onStartClipping
}) => {
  return (
    <section className="py-28 lg:py-36 relative bg-white dark:bg-[#070708] overflow-hidden">
      
      {/* 3D WebGL Vortex / Tornado in Final CTA Background */}
      <Vortex
        topRadius={400}
        waistRadius={60}
        bottomRadius={1200}
        twist={2.8}
        zoom={68}
        speed={9}
        direction="left"
        lineOptions={{ count: 180, color: "#8B5CF6", glow: 9 }}
        dotOptions={{ count: 4500, size: 24, color: "#DDD6FE", glow: 9, flicker: 10 }}
        cometOptions={{ count: 10, speed: 6, color: "#60A5FA", glow: 8, tail: 20, delay: 7, collide: 5 }}
        repel={true}
        repelOptions={{ radius: 60, strength: 16 }}
        className="opacity-25 dark:opacity-55"
      />

      {/* Background radial ambient lights */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[350px] bg-violet-600/20 blur-[150px] rounded-full pointer-events-none" />
      <div className="absolute top-1/2 left-1/3 w-[350px] h-[220px] bg-blue-600/15 blur-[120px] rounded-full pointer-events-none" />

      <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        
        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-100 dark:bg-white/[0.04] border border-violet-200 dark:border-white/10 mb-6 text-xs font-mono text-violet-700 dark:text-violet-300">
          <Sparkles className="w-3.5 h-3.5" />
          Scale Organic Reach
        </div>

        <h2 className="text-4xl sm:text-6xl font-display font-extrabold text-gray-900 dark:text-[#F7F7F5] tracking-tight leading-tight">
          One Video Can Go <br />
          <span className="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 via-gray-900 to-cyan-600 dark:from-violet-400 dark:via-white dark:to-cyan-300">
            Much Further.
          </span>
        </h2>

        <p className="mt-6 text-base sm:text-lg text-gray-600 dark:text-[#A1A1AA] max-w-2xl mx-auto leading-relaxed">
          Launch a campaign and put your content in the hands of creators around the world. Or monetize your video cutting skills with verified bounties.
        </p>

        {/* Dual CTAs */}
        <div className="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
          <StarfieldButton
            size="lg"
            variant="primary"
            onClick={onLaunchCampaign}
            icon={<ArrowRight className="w-4 h-4" />}
            iconPosition="right"
            className="w-full sm:w-auto"
          >
            Launch a Campaign
          </StarfieldButton>
          <StarfieldButton
            size="lg"
            variant="secondary"
            onClick={onStartClipping}
            icon={<Video className="w-4 h-4 text-violet-500 dark:text-violet-400" />}
            iconPosition="left"
            className="w-full sm:w-auto"
          >
            Start Clipping
          </StarfieldButton>
        </div>

      </div>
    </section>
  );
};
