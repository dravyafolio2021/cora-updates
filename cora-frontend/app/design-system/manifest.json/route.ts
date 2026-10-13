import { designSystem } from '@/lib/design-system';
export const dynamic = 'force-static';
export function GET() {
  return Response.json(designSystem);
}
