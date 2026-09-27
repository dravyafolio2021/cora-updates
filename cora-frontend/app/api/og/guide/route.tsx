import { ImageResponse } from 'next/og';
import { NextRequest } from 'next/server';

export const dynamic = 'force-static';

export async function GET(request: NextRequest) {
  let title = 'The Agency Client Onboarding Playbook';
  let category = 'Agency Operations';
  let chapter: string | null = null;
  let readTime = '28 min read';
  let customImage: string | null = null;

  try {
    if (request && request.url) {
      const url = new URL(request.url);
      title = url.searchParams.get('title') || title;
      category = url.searchParams.get('category') || category;
      chapter = url.searchParams.get('chapter');
      readTime = url.searchParams.get('readTime') || readTime;
      customImage = url.searchParams.get('customImage');
    }
  } catch (e) {
    // Static build time fallback
  }

  try {
    // If a custom image override URL is provided, redirect directly to it
    if (customImage && (customImage.startsWith('http') || customImage.startsWith('/'))) {
      const origin = request?.nextUrl?.origin || 'https://heycora.in';
      const redirectUrl = customImage.startsWith('http')
        ? customImage
        : `${origin}${customImage}`;
      return Response.redirect(redirectUrl, 302);
    }

    return new ImageResponse(
      (
        <div
          style={{
            height: '100%',
            width: '100%',
            display: 'flex',
            flexDirection: 'column',
            justifyContent: 'space-between',
            backgroundColor: '#09090b',
            backgroundImage:
              'radial-gradient(circle at 15% 20%, rgba(59, 130, 246, 0.15) 0%, transparent 40%), radial-gradient(circle at 85% 80%, rgba(16, 185, 129, 0.15) 0%, transparent 40%)',
            padding: '60px 80px',
            fontFamily: 'sans-serif',
            color: '#ffffff',
          }}
        >
          {/* Header Row */}
          <div
            style={{
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'space-between',
              width: '100%',
            }}
          >
            {/* Cora Logo & Tag */}
            <div style={{ display: 'flex', alignItems: 'center', gap: '14px' }}>
              <div
                style={{
                  width: '40px',
                  height: '40px',
                  borderRadius: '12px',
                  backgroundColor: '#ffffff',
                  color: '#09090b',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  fontSize: '22px',
                  fontWeight: '900',
                }}
              >
                C
              </div>
              <span
                style={{
                  fontSize: '24px',
                  fontWeight: '800',
                  letterSpacing: '-0.02em',
                  color: '#ffffff',
                }}
              >
                CORA <span style={{ color: '#a1a1aa', fontWeight: '400', fontSize: '20px', marginLeft: '6px' }}>PLAYBOOKS</span>
              </span>
            </div>

            {/* Category Pill */}
            <div
              style={{
                display: 'flex',
                alignItems: 'center',
                padding: '8px 18px',
                borderRadius: '9999px',
                backgroundColor: 'rgba(255, 255, 255, 0.10)',
                border: '1px solid rgba(255, 255, 255, 0.18)',
                fontSize: '14px',
                fontWeight: '700',
                textTransform: 'uppercase',
                letterSpacing: '0.06em',
                color: '#e4e4e7',
              }}
            >
              {category}
            </div>
          </div>

          {/* Main Content Area */}
          <div
            style={{
              display: 'flex',
              flexDirection: 'column',
              gap: '16px',
              maxWidth: '1000px',
            }}
          >
            {chapter && (
              <div
                style={{
                  display: 'flex',
                  alignItems: 'center',
                  fontSize: '18px',
                  fontWeight: '700',
                  color: '#34d399',
                  letterSpacing: '0.04em',
                  textTransform: 'uppercase',
                }}
              >
                {chapter}
              </div>
            )}

            <div
              style={{
                fontSize: title.length > 55 ? '46px' : '54px',
                fontWeight: '900',
                lineHeight: 1.15,
                letterSpacing: '-0.03em',
                color: '#ffffff',
              }}
            >
              {title}
            </div>
          </div>

          {/* Footer Metadata */}
          <div
            style={{
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'space-between',
              width: '100%',
              paddingTop: '24px',
              borderTop: '1px solid rgba(255, 255, 255, 0.12)',
            }}
          >
            <div style={{ display: 'flex', alignItems: 'center', gap: '24px' }}>
              <span style={{ fontSize: '16px', color: '#a1a1aa', fontWeight: '500' }}>
                Digital Book &amp; SOP Pack
              </span>
              <span style={{ fontSize: '16px', color: '#71717a' }}>•</span>
              <span style={{ fontSize: '16px', color: '#34d399', fontWeight: '600' }}>
                Free Resource
              </span>
              <span style={{ fontSize: '16px', color: '#71717a' }}>•</span>
              <span style={{ fontSize: '16px', color: '#a1a1aa' }}>
                {readTime}
              </span>
            </div>

            <div style={{ fontSize: '15px', color: '#a1a1aa', fontFamily: 'monospace' }}>
              heycora.in/guides
            </div>
          </div>
        </div>
      ),
      {
        width: 1200,
        height: 630,
      }
    );
  } catch (e: any) {
    console.error('OG generation error:', e);
    return new Response('Failed to generate image', { status: 500 });
  }
}
