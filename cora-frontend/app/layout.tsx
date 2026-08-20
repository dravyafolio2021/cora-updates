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
});

const interTight = Inter_Tight({
  subsets: ['latin'],
  variable: '--font-display',
  display: 'swap',
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
  title: 'Cora — The AI workspace built for ambitious founders',
  description: 'Cora orchestrates your client pipelines, revenue workflows, and specialized AI agents in one hyper-focused command center.',
  metadataBase: new URL('https://heycora.in'),
  alternates: {
    canonical: 'https://heycora.in',
  },
  verification: {
    google: process.env.NEXT_PUBLIC_GSC_VERIFICATION || 'cora-google-search-console-verification',
  },
  icons: {
    icon: '/favicon.png',
    apple: '/apple-touch-icon.png',
  },
  openGraph: {
    title: 'Cora — The AI workspace built for ambitious founders',
    description: 'Cora orchestrates your client pipelines, revenue workflows, and specialized AI agents in one hyper-focused command center.',
    url: 'https://heycora.in',
    siteName: 'Cora',
    images: [
      {
        url: '/og-image.png',
        width: 1200,
        height: 630,
        alt: 'Cora — The AI workspace built for ambitious founders',
      },
    ],
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Cora — The AI workspace built for ambitious founders',
    description: 'Cora orchestrates your client pipelines, revenue workflows, and specialized AI agents in one hyper-focused command center.',
    images: ['/og-image.png'],
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
        <script
          dangerouslySetInnerHTML={{
            __html: `
              (function() {
                try {
                  // Guard against browser extension attribute pollution (e.g. Bitdefender bis_skin_checked)
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
      <body suppressHydrationWarning className="font-sans antialiased text-zinc-950 bg-white">
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
