import { NextRequest, NextResponse } from 'next/server';
import { revalidatePath, revalidateTag } from 'next/cache';

/**
 * On-Demand Next.js ISR Route Revalidation Webhook
 *
 * Triggered automatically by Cora Growth Workspace whenever content is published or updated.
 */
export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const { secret, path, tags } = body;

    const authHeader = request.headers.get('authorization');
    const headerSecret = request.headers.get('x-cora-revalidate-secret');
    const bearerToken = authHeader?.replace(/^Bearer\s+/i, '');

    const expectedSecret = process.env.CORA_GROWTH_REVALIDATE_SECRET || process.env.REVALIDATE_SECRET || 'cora_revalidate_secret_staging_2026';

    // Verify secret token
    const providedSecret = secret || bearerToken || headerSecret;
    if (expectedSecret && providedSecret !== expectedSecret) {
      return NextResponse.json(
        { success: false, message: 'Invalid revalidation secret.' },
        { status: 401 }
      );
    }

    const revalidatedPaths: string[] = [];
    const revalidatedTags: string[] = [];

    // Revalidate specific path
    if (path && typeof path === 'string') {
      revalidatePath(path);
      revalidatedPaths.push(path);
    }

    // Always revalidate index routes when content changes
    revalidatePath('/blog');
    revalidatePath('/guides');
    revalidatedPaths.push('/blog', '/guides');

    // Revalidate cache tags if provided
    if (Array.isArray(tags)) {
      for (const tag of tags) {
        if (typeof tag === 'string') {
          try {
            (revalidateTag as any)(tag);
            revalidatedTags.push(tag);
          } catch (e) {
            // Ignore tag revalidation errors if unsupported
          }
        }
      }
    }

    return NextResponse.json({
      success: true,
      revalidated: true,
      paths: revalidatedPaths,
      tags: revalidatedTags,
      timestamp: new Date().toISOString(),
    });
  } catch (err: any) {
    return NextResponse.json(
      { success: false, message: err.message || 'Error executing revalidation.' },
      { status: 500 }
    );
  }
}
