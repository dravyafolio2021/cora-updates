import { designSystemMarkdown } from '@/lib/design-system';
export const dynamic = 'force-static';
export function GET() {
  return new Response(designSystemMarkdown(), { headers: { 'Content-Type': 'text/plain; charset=utf-8' } });
}
