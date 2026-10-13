import { designSystemCss } from '@/lib/design-system';
export const dynamic = 'force-static';
export function GET() {
  return new Response(designSystemCss(), { headers: { 'Content-Type': 'text/css; charset=utf-8' } });
}
