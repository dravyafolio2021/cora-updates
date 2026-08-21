'use client';

import React, { useEffect, useState } from 'react';
import Script from 'next/script';
import { isLocalHost, dispatchEvent } from '@/lib/analytics-funnels';

// Environment variable keys for Omnichannel Analytics
const GA_ID = process.env.NEXT_PUBLIC_GA_ID || 'G-5BW9VLMM7F';
const GTM_ID = process.env.NEXT_PUBLIC_GTM_ID || '';
const CLARITY_ID = process.env.NEXT_PUBLIC_CLARITY_ID || 'y5wmfas17o';
const META_PIXEL_ID = process.env.NEXT_PUBLIC_META_PIXEL_ID || '';
const LINKEDIN_PARTNER_ID = process.env.NEXT_PUBLIC_LINKEDIN_PARTNER_ID || '';

declare global {
  interface Window {
    gtag?: (...args: any[]) => void;
    dataLayer?: any[];
    clarity?: (...args: any[]) => void;
    fbq?: (...args: any[]) => void;
    _fbq?: any;
    lintrk?: (...args: any[]) => void;
  }
}

/**
 * Universal client-side event tracking helper for omnichannel analytics
 */
export function trackEvent(
  action: string,
  params: Record<string, any> = {}
) {
  dispatchEvent(action, params);
}

/**
 * Captures first-touch and last-touch UTM parameters & ad click IDs
 */
function captureAttribution() {
  if (typeof window === 'undefined' || isLocalHost()) return;
  try {
    const urlParams = new URLSearchParams(window.location.search);
    const utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'];
    const captured: Record<string, string> = {};

    utmKeys.forEach((key) => {
      const val = urlParams.get(key);
      if (val) {
        captured[key] = val;
      }
    });

    if (Object.keys(captured).length > 0) {
      localStorage.setItem('cora_last_touch_utm', JSON.stringify({
        ...captured,
        timestamp: new Date().toISOString(),
        referrer: document.referrer,
      }));

      if (!localStorage.getItem('cora_first_touch_utm')) {
        localStorage.setItem('cora_first_touch_utm', JSON.stringify({
          ...captured,
          timestamp: new Date().toISOString(),
          referrer: document.referrer,
        }));
      }
    }
  } catch (e) {
    // Non-blocking error
  }
}

/**
 * Sets up automated scroll depth milestones (25%, 50%, 75%, 90%)
 */
function setupScrollTracking() {
  if (typeof window === 'undefined' || isLocalHost()) return () => {};

  const milestones = [25, 50, 75, 90];
  const reached = new Set<number>();

  const handleScroll = () => {
    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    if (docHeight <= 0) return;

    const scrollPercent = Math.round((scrollTop / docHeight) * 100);

    milestones.forEach((threshold) => {
      if (scrollPercent >= threshold && !reached.has(threshold)) {
        reached.add(threshold);
        dispatchEvent('scroll_depth', {
          scroll_percent: threshold,
          label: `Scrolled ${threshold}%`,
        });
        if (typeof window.clarity === 'function') {
          window.clarity('set', 'max_scroll', `${threshold}%`);
        }
      }
    });
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  return () => window.removeEventListener('scroll', handleScroll);
}

/**
 * Sets up engaged time-on-page milestones (30s, 60s, 120s)
 */
function setupEngagementTimers() {
  if (typeof window === 'undefined' || isLocalHost()) return () => {};

  const timers: NodeJS.Timeout[] = [];
  const intervals = [30, 60, 120];

  intervals.forEach((sec) => {
    const t = setTimeout(() => {
      dispatchEvent('engaged_time', {
        time_seconds: sec,
        label: `Engaged for ${sec}s`,
      });
      if (typeof window.clarity === 'function') {
        window.clarity('set', 'engaged_time', `${sec}s`);
      }
    }, sec * 1000);
    timers.push(t);
  });

  return () => timers.forEach(clearTimeout);
}

export function Analytics() {
  const [isProduction, setIsProduction] = useState(false);

  useEffect(() => {
    if (!isLocalHost()) {
      setIsProduction(true);
      captureAttribution();

      const cleanupScroll = setupScrollTracking();
      const cleanupTimers = setupEngagementTimers();

      // Microsoft Clarity smart tags & user intent classification
      if (typeof window.clarity === 'function') {
        window.clarity('set', 'platform', 'cora-marketing');
        window.clarity('set', 'environment', 'production');
        
        // Tag traffic source
        try {
          const rawUtm = localStorage.getItem('cora_first_touch_utm');
          if (rawUtm) {
            const parsed = JSON.parse(rawUtm);
            if (parsed.utm_source) {
              window.clarity('set', 'utm_source', parsed.utm_source);
            }
            if (parsed.utm_campaign) {
              window.clarity('set', 'utm_campaign', parsed.utm_campaign);
            }
          }
        } catch (e) {}
      }

      return () => {
        cleanupScroll();
        cleanupTimers();
      };
    }
  }, []);

  // Suppress all analytics & screen recordings on localhost
  if (!isProduction) {
    return null;
  }

  return (
    <>
      {/* ── 1. Google Analytics 4 (GA4) ─────────────────────────── */}
      {GA_ID && (
        <>
          <Script
            src={`https://www.googletagmanager.com/gtag/js?id=${GA_ID}`}
            strategy="afterInteractive"
          />
          <Script id="google-analytics" strategy="afterInteractive">
            {`
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              gtag('config', '${GA_ID}', {
                page_path: window.location.pathname,
                send_page_view: true,
                cookie_flags: 'SameSite=None;Secure'
              });
            `}
          </Script>
        </>
      )}

      {/* ── 2. Google Tag Manager (GTM) ─────────────────────────── */}
      {GTM_ID && (
        <Script id="google-tag-manager" strategy="afterInteractive">
          {`
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','${GTM_ID}');
          `}
        </Script>
      )}

      {/* ── 3. Meta (Facebook / Instagram) Pixel ────────────────── */}
      {META_PIXEL_ID && (
        <Script id="meta-pixel" strategy="afterInteractive">
          {`
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '${META_PIXEL_ID}');
            fbq('track', 'PageView');
          `}
        </Script>
      )}

      {/* ── 4. Microsoft Clarity (Heatmaps & Session Recordings) ── */}
      {CLARITY_ID && (
        <Script id="microsoft-clarity" strategy="afterInteractive">
          {`
            (function(c,l,a,r,i,t,y){
              c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
              t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
              y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "${CLARITY_ID}");
          `}
        </Script>
      )}

      {/* ── 5. LinkedIn Insight Tag ──────────────────────────────── */}
      {LINKEDIN_PARTNER_ID && (
        <Script id="linkedin-insight" strategy="afterInteractive">
          {`
            _linkedin_partner_id = "${LINKEDIN_PARTNER_ID}";
            window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
            window._linkedin_data_partner_ids.push(_linkedin_partner_id);
            (function(l) {
              if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
              window.lintrk.q=[]}
              var s = document.getElementsByTagName("script")[0];
              var b = document.createElement("script");
              b.type = "text/javascript";b.async = true;
              b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
              s.parentNode.insertBefore(b, s);
            })(window.lintrk);
          `}
        </Script>
      )}
    </>
  );
}
