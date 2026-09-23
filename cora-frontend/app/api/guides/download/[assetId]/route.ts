import { NextRequest, NextResponse } from 'next/server';
import { GUIDES_DATA } from '@/lib/guides-data';

interface RouteContext {
  params: Promise<{ assetId: string }>;
}

export async function generateStaticParams() {
  const assets = GUIDES_DATA.map((g) => g.downloadableAsset?.assetId).filter(Boolean) as string[];
  return assets.map((assetId) => ({ assetId }));
}

export async function GET(request: NextRequest, context: RouteContext) {
  const { assetId } = await context.params;

  // Locate the guide containing this asset
  const targetGuide = GUIDES_DATA.find((g) => g.downloadableAsset?.assetId === assetId);

  if (!targetGuide || !targetGuide.downloadableAsset) {
    return NextResponse.json(
      { error: 'Requested asset was not found.' },
      { status: 404 }
    );
  }

  const asset = targetGuide.downloadableAsset;

  // In production, return an official download payload with appropriate headers
  // For the pack, we provide a markdown/text/JSON SOP package or redirect to static asset
  const manifest = {
    title: asset.title,
    guide: targetGuide.title,
    assetId: asset.assetId,
    fileType: asset.fileType,
    publishedDate: targetGuide.publishedAt,
    highlights: asset.highlights,
    notice: 'Official Cora Operational Playbook Pack. For internal agency use.',
  };

  const manifestContent = JSON.stringify(manifest, null, 2);

  return new NextResponse(manifestContent, {
    status: 200,
    headers: {
      'Content-Type': 'application/json',
      'Content-Disposition': `attachment; filename="${asset.assetId}-pack.json"`,
      'Cache-Control': 'no-store, max-age=0',
    },
  });
}
