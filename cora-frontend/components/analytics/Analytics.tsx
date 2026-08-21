'use client';

import React, { useEffect, useState } from 'react';
import Script from 'next/script';

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
 * Checks if the current execution is on a local development host.
 */
function isLocalHost(): boolean {
  if (typeof window === 'undefined') return false;
  const h = window.location.hostname;
  return h === 'localhost' || h === '127.0.0.1' || h.endsWith('.local') || h === '';
}

/**
 * Universal client-side event tracking helper for omnichannel analytics
 * Dispatches to GA4, GTM, Meta Pixel, Microsoft Clarity, and LinkedIn in parallel.
 * Automatically ignores development / localhost traffic.
 */
export function trackEvent(
  action: string,
  params: Record<string, any> = {}
) {
  if (typeof window === 'undefined' || isLocalHost()) return;

  // 1. Google Analytics 4 (GA4)
  if (typeof window.gtag === 'function') {
    window.gtag('event', action, params);
  }

  // 2. Google Tag Manager (GTM DataLayer)
  if (Array.isArray(window.dataLayer)) {
    window.dataLayer.push({
      event: action,
      ...params,
    });
  }

  // 3. Meta Pixel (Facebook / Instagram)
  if (typeof window.fbq === 'function') {
    if (action === 'lead_form_submitted' || action === 'contact_submitted') {
      window.fbq('track', 'Lead', params);
    } else if (action === 'start_trial_clicked' || action === 'signup_clicked') {
      window.fbq('track', 'InitiateCheckout', params);
    } else if (action === 'pricing_viewed') {
      window.fbq('track', 'ViewContent', { content_name: 'Pricing Plans', ...params });
    } else {
      window.fbq('trackCustom', action, params);
    }
  }

  // 4. Microsoft Clarity Custom Events
  if (typeof window.clarity === 'function') {
    window.clarity('event', action);
  }

  // 5. LinkedIn Insight Tag Conversion
  if (typeof window.lintrk === 'function' && params.conversionId) {
    window.lintrk('track', { conversion_id: params.conversionId });
  }
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

export function Analytics() {
  const [isProduction, setIsProduction] = useState(false);

  useEffect(() => {
    if (!isLocalHost()) {
      setIsProduction(true);
      captureAttribution();

      // Microsoft Clarity custom tagging on production
      if (typeof window.clarity === 'function') {
        window.clarity('set', 'platform', 'cora-marketing');
        window.clarity('set', 'environment', 'production');
      }
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
