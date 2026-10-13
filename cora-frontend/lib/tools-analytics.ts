// ══════════════════════════════════════════════════════════════════════════
// CORA TOOLS ANALYTICS ENGINE (STRICT PRIVACY-FIRST SPECIFICATION)
// ══════════════════════════════════════════════════════════════════════════
// Rules:
// 1. NEVER track submitted domains, client names, emails, project revenue,
//    uploaded text, document contents, prompts, or raw results.
// 2. ONLY track generic metadata: tool_id, category, execution_mode,
//    result_band, completion_duration, and CTA interactions.
// ══════════════════════════════════════════════════════════════════════════

import { trackEvent } from '@/components/analytics/Analytics';

export type ToolEventName =
  | 'tool_viewed'
  | 'tool_started'
  | 'tool_completed'
  | 'tool_result_band'
  | 'tool_report_downloaded'
  | 'tool_related_opened'
  | 'tool_cora_cta_clicked'
  | 'tool_error';

export interface ToolAnalyticsPayload {
  tool_id: string;
  category?: string;
  execution_mode?: 'browser-only' | 'remote-audit' | 'ai-assisted';
  result_band?: string;
  step_completed?: number;
  duration_ms?: number;
  cta_target?: string;
  error_code?: string;
  [key: string]: unknown;
}

export function logToolEvent(eventName: ToolEventName, payload: ToolAnalyticsPayload): void {
  // Sanitize payload to guarantee no PII or sensitive values are transmitted
  const sanitized: Record<string, string | number | boolean> = {
    tool_id: payload.tool_id,
    category: payload.category || 'tools',
    event_timestamp: Date.now(),
  };

  if (payload.execution_mode) sanitized.execution_mode = payload.execution_mode;
  if (payload.result_band) sanitized.result_band = payload.result_band;
  if (typeof payload.step_completed === 'number') sanitized.step_completed = payload.step_completed;
  if (typeof payload.duration_ms === 'number') sanitized.duration_ms = Math.min(payload.duration_ms, 3600000);
  if (payload.cta_target) sanitized.cta_target = payload.cta_target;
  if (payload.error_code) sanitized.error_code = payload.error_code;

  // Track via global telemetry
  trackEvent(eventName, sanitized);

  // Development debugging log
  if (process.env.NODE_ENV === 'development') {
    console.debug(`[CoraToolAnalytics] ${eventName}`, sanitized);
  }
}

export function trackToolStarted(options: { tool_slug: string; category?: string }): void {
  logToolEvent('tool_started', {
    tool_id: options.tool_slug,
    category: options.category || 'tools',
  });
}

export function trackToolCompleted(options: { tool_slug: string; category?: string; calculation_mode?: string }): void {
  logToolEvent('tool_completed', {
    tool_id: options.tool_slug,
    category: options.category || 'tools',
    execution_mode: 'browser-only',
  });
}

export function trackToolResultBand(options: { tool_slug: string; band_name: string; primary_metric_value?: number }): void {
  logToolEvent('tool_result_band', {
    tool_id: options.tool_slug,
    category: 'tools',
    result_band: options.band_name,
  });
}

export function trackToolCoraCtaClicked(options: {
  tool_slug: string;
  cta_location: string;
  destination_url: string;
  button_text: string;
}): void {
  logToolEvent('tool_cora_cta_clicked', {
    tool_id: options.tool_slug,
    category: 'tools',
    cta_target: options.destination_url,
  });
}

export function trackToolReportDownloaded(options: { tool_slug: string; format: string }): void {
  logToolEvent('tool_report_downloaded', {
    tool_id: options.tool_slug,
    category: 'tools',
  });
}
