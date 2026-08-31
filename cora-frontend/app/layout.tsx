import type { Metadata } from 'next';
import { Inter, Inter_Tight, Caveat, JetBrains_Mono } from 'next/font/google';
import Script from 'next/script';
import '@/styles/globals.css';
import { Navbar } from '@/components/layout/Navbar';
import { Footer } from '@/components/layout/Footer';
import { ToastProvider } from '@/components/ui/Toast';
import { Analytics } from '@/components/analytics/Analytics';
import { SmoothScrollProvider } from '@/components/providers/SmoothScrollProvider';
import { MadeInCoraBadge } from '@/components/ui/MadeInCoraBadge';
import { generatePlatformSchemas } from '@/lib/seo-schema';

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-sans',
  display: 'swap',
  preload: true,
});

const interTight = Inter_Tight({
  subsets: ['latin'],
  variable: '--font-display',
  display: 'swap',
  preload: true,
});

const caveat = Caveat({
  subsets: ['latin'],
  variable: '--font-scribble',
  display: 'swap',
});

const jetbrainsMono = JetBrains_Mono({
  subsets: ['latin'],
  variable: '--font-mono',
  display: 'swap',
});

export const metadata: Metadata = {
  title: 'Cora — The AI Co-Founder for Operations, Revenue & Automation',
  description: 'Meet Cora (Co-founder for Operations, Revenue & Automation). One AI co-founder to run your proposals, SHA-256 contracts, 18% GST invoices, crew dispatch, and client delivery on autopilot.',
  keywords: [
    'Cora',
    'HeyCora',
    'AI co-founder for service businesses',
    'operations revenue automation',
    'commercial photography CRM',
    'creative agency operating system',
    '18% GST invoice generator',
    'SHA-256 digital signatures India',
    'HoneyBook alternative',
    'Studio Ninja alternative',
    'DocuSign alternative',
    'professional services AI OS'
  ],
  metadataBase: new URL('https://heycora.in'),
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      'max-video-preview': -1,
      'max-image-preview': 'large',
      'max-snippet': -1,
    },
  },
  verification: {
    google: process.env.NEXT_PUBLIC_GSC_VERIFICATION || 'cora-google-search-console-verification',
  },
  icons: {
    icon: '/favicon.png',
    apple: '/apple-touch-icon.png',
  },
  openGraph: {
    title: 'Cora — The AI Co-Founder for Operations, Revenue & Automation',
    description: 'Meet Cora (Co-founder for Operations, Revenue & Automation). Run your proposals, SHA-256 contracts, 18% GST invoices, and client vaults on autopilot.',
    url: 'https://heycora.in',
    siteName: 'Cora',
    locale: 'en_US',
    images: [
      {
        url: '/og-image.png',
        width: 1200,
        height: 630,
        alt: 'Cora — The AI Co-Founder for Operations, Revenue & Automation',
      },
    ],
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Cora — The AI Co-Founder for Operations, Revenue & Automation',
    description: 'Meet Cora (Co-founder for Operations, Revenue & Automation). Run your proposals, SHA-256 contracts, 18% GST invoices, and client vaults on autopilot.',
    images: ['/og-image.png'],
    creator: '@dravyafolio',
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const schemas = generatePlatformSchemas();

  return (
    <html
      lang="en"
      suppressHydrationWarning
      className={`${inter.variable} ${interTight.variable} ${caveat.variable} ${jetbrainsMono.variable}`}
    >
      <head>
        {/* Performance Resource Hints for Faster LCP */}
        <link rel="preconnect" href="https://checkout.razorpay.com" />
        <link rel="dns-prefetch" href="https://checkout.razorpay.com" />
        
        {/* Preload Hero Visual Assets */}
        <link rel="preload" as="image" href="/images/cora_hero_landscape.jpg" fetchPriority="high" />
        <link rel="preload" as="image" href="/images/cora_3d_ai_badge.png" fetchPriority="high" />

        {/* AI Discovery Meta Hints */}
        <link rel="alternate" type="text/plain" href="https://heycora.in/llms.txt" title="LLMs.txt" />
        <meta name="ai-content-declaration" content="human-directed-autonomous-platform" />

        <script
          dangerouslySetInnerHTML={{
            __html: `
              (function() {
                try {
                  // Suppress third-party Chrome extension runtime errors from triggering Next.js dev overlay
                  if (typeof window !== 'undefined') {
                    window.addEventListener('error', function(event) {
                      if (event && (
                        (event.filename && event.filename.indexOf('chrome-extension://') !== -1) ||
                        (event.message && (event.message.indexOf('M_ID') !== -1 || event.message.indexOf('chrome-extension') !== -1))
                      )) {
                        event.stopImmediatePropagation();
                        event.preventDefault();
                        return true;
                      }
                    }, true);

                    window.addEventListener('unhandledrejection', function(event) {
                      if (event && event.reason && event.reason.stack && event.reason.stack.indexOf('chrome-extension://') !== -1) {
                        event.stopImmediatePropagation();
                        event.preventDefault();
                      }
                    }, true);
                  }

                  // Guard against browser extension attribute pollution
                  if (typeof Element !== 'undefined') {
                    var origSetAttr = Element.prototype.setAttribute;
                    Element.prototype.setAttribute = function(name, val) {
                      if (typeof name === 'string' && (name === 'bis_skin_checked' || name.indexOf('bis_') === 0)) {
                        return;
                      }
                      return origSetAttr.apply(this, arguments);
                    };
                    var origSetAttrNode = Element.prototype.setAttributeNode;
                    if (origSetAttrNode) {
                      Element.prototype.setAttributeNode = function(attr) {
                        if (attr && attr.name && (attr.name === 'bis_skin_checked' || attr.name.indexOf('bis_') === 0)) {
                          return null;
                        }
                        return origSetAttrNode.apply(this, arguments);
                      };
                    }
                  }
                  if (typeof MutationObserver !== 'undefined') {
                    var observer = new MutationObserver(function(mutations) {
                      for (var i = 0; i < mutations.length; i++) {
                        var m = mutations[i];
                        if (m.type === 'attributes' && m.attributeName && m.attributeName.indexOf('bis_') === 0) {
                          m.target.removeAttribute(m.attributeName);
                        }
                      }
                    });
                    if (document.documentElement) {
                      observer.observe(document.documentElement, {
                        attributes: true,
                        subtree: true,
                        attributeFilter: ['bis_skin_checked', 'bis_frame_id', 'bis_register']
                      });
                    }
                  }
                } catch (e) {}
              })();
            `,
          }}
        />
        <script
          id="platform-json-ld"
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(schemas) }}
        />
      </head>
      <body suppressHydrationWarning className="font-sans antialiased text-zinc-950 bg-white selection:bg-zinc-950 selection:text-white">
        <Script src="https://checkout.razorpay.com/v1/checkout.js" strategy="lazyOnload" />
        <SmoothScrollProvider>
          <ToastProvider>
            <Analytics />
            <div className="bg-ambient" />
            <Navbar />
            {children}
            <Footer />
            <MadeInCoraBadge />
          </ToastProvider>
        </SmoothScrollProvider>
      </body>
    </html>
  );
}
