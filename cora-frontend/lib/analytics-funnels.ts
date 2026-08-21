'use client';

/**
 * Cora Omnichannel Power-User Funnel & Conversion Tracker
 * Standardized across GA4, Google Tag Manager, Microsoft Clarity, and Meta Pixel.
 */

export interface FunnelEventParams {
  category?: string;
  label?: string;
  value?: number;
  currency?: string;
  plan?: string;
  billing?: 'monthly' | 'annual';
  industry?: string;
  tool_name?: string;
  scroll_percent?: number;
  cta_location?: string;
  time_seconds?: number;
  [key: string]: any;
}

/**
 * Helper to test if executing on a development / local environment.
 */
export function isLocalHost(): boolean {
  if (typeof window === 'undefined') return false;
  const h = window.location.hostname;
  return h === 'localhost' || h === '127.0.0.1' || h.endsWith('.local') || h === '';
}

/**
 * Dispatches an event to all connected analytics platforms simultaneously.
 */
export function dispatchEvent(eventName: string, params: FunnelEventParams = {}) {
  if (typeof window === 'undefined' || isLocalHost()) return;

  const enrichedParams = {
    ...params,
    page_path: window.location.pathname,
    page_title: document.title,
    timestamp: new Date().toISOString(),
  };

  // 1. Google Analytics 4 (GA4)
  if (typeof window.gtag === 'function') {
    window.gtag('event', eventName, enrichedParams);
  }

  // 2. Google Tag Manager (GTM DataLayer)
  if (Array.isArray(window.dataLayer)) {
    window.dataLayer.push({
      event: eventName,
      ...enrichedParams,
    });
  }

  // 3. Microsoft Clarity Custom Events & Smart Tags
  if (typeof window.clarity === 'function') {
    window.clarity('event', eventName);
    if (params.plan) {
      window.clarity('set', 'target_plan', params.plan);
    }
    if (params.industry) {
      window.clarity('set', 'target_industry', params.industry);
    }
    if (params.cta_location) {
      window.clarity('set', 'last_cta_clicked', params.cta_location);
    }
  }

  // 4. Meta Pixel (Facebook / Instagram Ads)
  if (typeof window.fbq === 'function') {
    switch (eventName) {
      case 'lead_form_submitted':
      case 'contact_submitted':
        window.fbq('track', 'Lead', enrichedParams);
        break;
      case 'signup_initiated':
      case 'start_trial_clicked':
        window.fbq('track', 'InitiateCheckout', {
          content_name: params.plan || 'Standard Plan',
          currency: 'INR',
          value: params.value || 299,
          ...enrichedParams,
        });
        break;
      case 'pricing_viewed':
      case 'plan_selected':
        window.fbq('track', 'ViewContent', {
          content_name: 'Pricing Plans',
          content_type: 'product_group',
          ...enrichedParams,
        });
        break;
      default:
        window.fbq('trackCustom', eventName, enrichedParams);
    }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// POWER-USER FUNNEL ACTION HELPERS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * 1. Top of Funnel: Industry Filter Clicked
 */
export function trackIndustrySelected(industryName: string) {
  dispatchEvent('industry_selected', {
    category: 'Engagement',
    industry: industryName,
    label: `Filtered to ${industryName}`,
  });
}

/**
 * 2. Top of Funnel: Hero Prompt Interacted
 */
export function trackPromptInteracted(promptText: string) {
  dispatchEvent('hero_prompt_interacted', {
    category: 'Discovery',
    label: promptText.slice(0, 50),
  });
}

/**
 * 3. Middle of Funnel: Pricing Plan Inspected / Toggled
 */
export function trackPlanSelected(planName: string, billing: 'monthly' | 'annual', price: number) {
  dispatchEvent('plan_selected', {
    category: 'Conversion Funnel',
    plan: planName,
    billing,
    value: price,
    currency: 'INR',
  });
}

/**
 * 4. Middle of Funnel: Tool Used (GST Calculator, Embed Builder)
 */
export function trackToolUsed(toolName: string, meta: Record<string, any> = {}) {
  dispatchEvent('tool_used', {
    category: 'Product Experience',
    tool_name: toolName,
    ...meta,
  });
}

/**
 * 5. Middle of Funnel: FAQ Question Expanded
 */
export function trackFaqExpanded(question: string) {
  dispatchEvent('faq_expanded', {
    category: 'Content',
    label: question.slice(0, 60),
  });
}

/**
 * 6. Bottom of Funnel: Sign-up / CTA Clicked (Appends stored UTMs)
 */
export function trackCtaClick(location: string, planName?: string) {
  dispatchEvent('signup_initiated', {
    category: 'Conversion',
    cta_location: location,
    plan: planName || 'free',
    label: `Clicked ${location}`,
  });
}

/**
 * 7. Bottom of Funnel: Lead Form Submitted
 */
export function trackLeadSubmission(formType: string, meta: Record<string, any> = {}) {
  dispatchEvent('lead_form_submitted', {
    category: 'Conversion',
    form_type: formType,
    ...meta,
  });
}
