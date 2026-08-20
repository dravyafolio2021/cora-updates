'use client';

import React, { useEffect } from 'react';
import Script from 'next/script';

const GA_ID = process.env.NEXT_PUBLIC_GA_ID || '';
const GTM_ID = process.env.NEXT_PUBLIC_GTM_ID || '';
const CLARITY_ID = process.env.NEXT_PUBLIC_CLARITY_ID || '';

declare global {
  interface Window {
    gtag?: (...args: any[]) => void;
    dataLayer?: any[];
    clarity?: (...args: any[]) => void;
  }
}

/**
 * Universal client-side event tracking helper for omnichannel analytics
 */
export function trackEvent(
  action: string,
  params: Record<string, any> = {}
) {
  if (typeof window === 'undefined') return;

  // 1. Google Analytics 4
  if (typeof window.gtag === 'function') {
    window.gtag('event', action, params);
  }

  // 2. Google Tag Manager DataLayer
  if (Array.isArray(window.dataLayer)) {
    window.dataLayer.push({
      event: action,
      ...params,
    });
  }

  // 3. Microsoft Clarity Custom Events
  if (typeof window.clarity === 'function') {
    window.clarity('event', action);
  }
}

export function Analytics() {
  useEffect(() => {
    // Initial Clarity custom tags if active
    if (typeof window.clarity === 'function') {
      window.clarity('set', 'platform', 'cora-marketing');
      window.clarity('set', 'environment', process.env.NODE_ENV || 'production');
    }
  }, []);

  return (
    <>
      {/* ── Google Analytics 4 (GA4) ─────────────────────────── */}
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

      {/* ── Google Tag Manager (GTM) ─────────────────────────── */}
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

      {/* ── Microsoft Clarity (Heatmaps & Recordings) ────────── */}
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
    </>
  );
}
