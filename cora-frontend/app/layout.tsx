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
  title: 'Cora — The Transformative AI Co-Founder for Service Businesses',
  description: 'Native AI infrastructure bridging your customer-facing Website & 24/7 AI Concierge with your admin-facing Workspace for contracts, 18% GST billing, and scheduling.',
  keywords: [
    'AI co-founder for creative studios',
    'customer facing AI concierge',
    'admin workspace operating system',
    'commercial photography CRM',
    'film production software',
    '18% GST invoice generator',
    'SHA-256 digital signatures India',
    'HoneyBook alternative',
    'Studio Ninja alternative',
    'DocuSign alternative',
    'wedding photography call sheets'
  ],
  metadataBase: new URL('https://heycora.in'),
  alternates: {
    canonical: 'https://heycora.in',
  },
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
    title: 'Cora — The Transformative AI Co-Founder for Service Businesses',
    description: 'Native AI infrastructure bridging your customer-facing Website & 24/7 AI Concierge with your admin-facing Workspace for contracts, 18% GST billing, and scheduling.',
    url: 'https://heycora.in',
    siteName: 'Cora Studio OS',
    locale: 'en_US',
    images: [
      {
        url: '/og-image.png',
        width: 1200,
        height: 630,
        alt: 'Cora — AI Co-Founder for Service Businesses',
      },
    ],
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Cora — The Transformative AI Co-Founder for Service Businesses',
    description: 'Native AI infrastructure bridging your customer-facing Website & 24/7 AI Concierge with your admin-facing Workspace for contracts, 18% GST billing, and scheduling.',
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
      </head>
      <body suppressHydrationWarning className="font-sans antialiased text-zinc-950 bg-white selection:bg-zinc-950 selection:text-white">
        <Script
          id="json-ld"
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(schemas) }}
        />
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
