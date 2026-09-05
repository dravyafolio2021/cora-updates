import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Profile Photo Maker - Professional Avatars & Headshots Free | Cora',
  description: 'Turn casual headshots into clean executive profile photos. Add circular framing, custom studio backdrops, and accent ring borders. 100% private in-browser tool.',
  keywords: [
    'profile photo maker',
    'avatar maker',
    'linkedin profile photo editor',
    'circle profile picture',
    'headshot backdrop editor',
    'round avatar maker',
    'profile ring border',
    'executive headshot maker',
    'cora profile photo tool'
  ],
  alternates: {
    canonical: 'https://heycora.in/tools/profile-photo-maker',
  },
  openGraph: {
    title: 'Profile Photo Maker - Professional Avatars & Headshots Free | Cora',
    description: 'Turn casual headshots into clean executive profile photos with circular framing, studio backdrops, and accent rings. 100% in-browser privacy.',
    url: 'https://heycora.in/tools/profile-photo-maker',
    siteName: 'Cora',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Profile Photo Maker - Professional Avatars & Headshots Free | Cora',
    description: 'Turn casual headshots into clean executive profile photos with circular framing, studio backdrops, and accent rings.',
  },
};

export default function ProfilePhotoMakerLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}
