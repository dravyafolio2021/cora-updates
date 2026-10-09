'use client';

/**
 * Universal Referral & Affiliate Tracking Engine for Cora
 * Handles multi-source detection (?ref, ?aff, ?invite, ?via, ?endorsely_referral, ?partner),
 * 30-day cookie persistence, LocalStorage, and background click tracking.
 */

export const REFERRAL_COOKIE_NAME = 'cora_referral_code';
export const LEGACY_REFERRAL_COOKIE_NAME = 'endorsely_referral';
export const REFERRAL_STORAGE_KEY = 'cora_ref_code';
export const REFERRAL_COOKIE_EXPIRY_DAYS = 30;
export const REFERRAL_DISCOUNT_PERCENTAGE = 10;

/**
 * Cookie helpers
 */
export function setCookie(name: string, value: string, days: number = REFERRAL_COOKIE_EXPIRY_DAYS): void {
  if (typeof document === 'undefined') return;
  try {
    const maxAge = days * 24 * 60 * 60;
    const isSecure = typeof window !== 'undefined' && window.location.protocol === 'https:';
    document.cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)}; Max-Age=${maxAge}; Path=/; SameSite=Lax${isSecure ? '; Secure' : ''}`;
  } catch (e) {
    // Silent failover
  }
}

export function getCookie(name: string): string | null {
  if (typeof document === 'undefined') return null;
  try {
    const nameEQ = encodeURIComponent(name) + '=';
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) === 0) {
        return decodeURIComponent(c.substring(nameEQ.length, c.length)).trim();
      }
    }
  } catch (e) {
    // Silent failover
  }
  return null;
}

/**
 * Safely extract referral code from URL query parameters.
 */
export function getReferralFromUrl(): string | null {
  if (typeof window === 'undefined') return null;
  try {
    const params = new URLSearchParams(window.location.search);
    const candidate = 
      params.get('ref') ||
      params.get('aff') ||
      params.get('invite') ||
      params.get('via') ||
      params.get('partner') ||
      params.get('endorsely_referral') ||
      params.get('referral') ||
      params.get('cora_ref');

    if (candidate && candidate.trim()) {
      return candidate.trim();
    }
  } catch (e) {}
  return null;
}

/**
 * Retrieve the active referral code across all persistence layers:
 * 1. Window global
 * 2. URL parameters
 * 3. Cookies (`cora_referral_code`, `endorsely_referral`)
 * 4. LocalStorage (`cora_ref_code`, `cora_referral_code`, `endorsely_referral`)
 */
export function getActiveReferralCode(): string {
  if (typeof window === 'undefined') return '';

  try {
    // 1. Direct window global
    if ((window as any).cora_referral && typeof (window as any).cora_referral === 'string') {
      return (window as any).cora_referral.trim();
    }
    if ((window as any).endorsely_referral && typeof (window as any).endorsely_referral === 'string') {
      return (window as any).endorsely_referral.trim();
    }

    // 2. URL query parameters (highest priority for fresh clicks)
    const urlRef = getReferralFromUrl();
    if (urlRef) {
      persistReferralCode(urlRef);
      return urlRef;
    }

    // 3. Cookies
    const cookieRef = getCookie(REFERRAL_COOKIE_NAME) || getCookie(LEGACY_REFERRAL_COOKIE_NAME);
    if (cookieRef && cookieRef.trim()) {
      return cookieRef.trim();
    }

    // 4. LocalStorage
    const storedRef = 
      localStorage.getItem(REFERRAL_STORAGE_KEY) ||
      localStorage.getItem('cora_referral_code') ||
      localStorage.getItem('endorsely_referral');
    if (storedRef && storedRef.trim()) {
      return storedRef.trim();
    }
  } catch (e) {}

  return '';
}

/**
 * Persist referral code across cookies, localStorage, and window global.
 */
export function persistReferralCode(code: string): void {
  if (!code || typeof window === 'undefined') return;
  const cleanCode = code.trim();
  if (!cleanCode) return;

  try {
    // Set Cookies (30 days)
    setCookie(REFERRAL_COOKIE_NAME, cleanCode, REFERRAL_COOKIE_EXPIRY_DAYS);
    setCookie(LEGACY_REFERRAL_COOKIE_NAME, cleanCode, REFERRAL_COOKIE_EXPIRY_DAYS);

    // Set LocalStorage
    localStorage.setItem(REFERRAL_STORAGE_KEY, cleanCode);
    localStorage.setItem('cora_referral_code', cleanCode);
    localStorage.setItem('endorsely_referral', cleanCode);

    // Set Window globals
    (window as any).cora_referral = cleanCode;
    (window as any).endorsely_referral = cleanCode;
  } catch (e) {}
}

/**
 * Trigger background tracking ping to Next.js API / WP Affiliate Engine.
 */
export async function trackReferralClick(code: string): Promise<boolean> {
  if (!code || typeof window === 'undefined') return false;
  const cleanCode = code.trim();
  if (!cleanCode) return false;

  const sessionKey = `cora_ref_tracked_${cleanCode}`;
  try {
    if (sessionStorage.getItem(sessionKey)) {
      // Already tracked in this browser session
      return true;
    }

    sessionStorage.setItem(sessionKey, '1');

    const payload = {
      referralCode: cleanCode,
      sourceUrl: window.location.href,
      landingPage: window.location.pathname,
      referrer: document.referrer || '',
      userAgent: navigator.userAgent || '',
      timestamp: new Date().toISOString()
    };

    // Use sendBeacon if available for non-blocking ping
    if (navigator.sendBeacon) {
      const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' });
      navigator.sendBeacon('/api/referral/track', blob);
      return true;
    }

    // Fetch fallback
    fetch('/api/referral/track', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
      keepalive: true
    }).catch(() => {});

    return true;
  } catch (e) {
    return false;
  }
}

/**
 * Universal init hook to detect, persist, and track referrals on page mount.
 */
export function initReferralTracking(): { code: string | null; isTracked: boolean } {
  if (typeof window === 'undefined') return { code: null, isTracked: false };

  const urlRef = getReferralFromUrl();
  if (urlRef) {
    persistReferralCode(urlRef);
    trackReferralClick(urlRef);
    return { code: urlRef, isTracked: true };
  }

  const activeCode = getActiveReferralCode();
  if (activeCode) {
    return { code: activeCode, isTracked: true };
  }

  return { code: null, isTracked: false };
}
