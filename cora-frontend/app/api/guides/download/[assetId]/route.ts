import { NextRequest, NextResponse } from 'next/server';
import { GUIDES_DATA } from '@/lib/guides-data';
import { getGuideAssetPayload } from '@/lib/guide-assets';

interface RouteContext {
  params: Promise<{ assetId: string }>;
}

export async function generateStaticParams() {
  const assets = GUIDES_DATA.map((g) => g.downloadableAsset?.assetId).filter(Boolean) as string[];
  return assets.map((assetId) => ({ assetId }));
}

export async function GET(request: NextRequest, context: RouteContext) {
  const { assetId } = await context.params;

  const targetGuide = GUIDES_DATA.find((g) => g.downloadableAsset?.assetId === assetId);
  const payload = getGuideAssetPayload(assetId);

  if (!targetGuide || !targetGuide.downloadableAsset || !payload) {
    return NextResponse.json(
      { error: 'Requested asset was not found.' },
      { status: 404 }
    );
  }

  return new NextResponse(payload.content, {
    status: 200,
    headers: {
      'Content-Type': payload.contentType,
      'Content-Disposition': `attachment; filename="${payload.filename}"`,
      'Cache-Control': 'private, no-store, max-age=0',
      'X-Robots-Tag': 'noindex, nofollow, noarchive',
    },
  });
}
