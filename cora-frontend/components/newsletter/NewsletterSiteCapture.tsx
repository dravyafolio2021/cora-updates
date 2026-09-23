'use client';

import { usePathname } from 'next/navigation';
import { NewsletterCapture } from './NewsletterCapture';

export function NewsletterSiteCapture() {
  const pathname = usePathname() || '/';

  if (
    pathname.startsWith('/docs') ||
    pathname === '/terms' ||
    pathname === '/privacy' ||
    pathname === '/refund-policy' ||
    pathname === '/security' ||
    pathname === '/sla' ||
    pathname === '/contact'
  ) {
    return null;
  }

  let source = 'footer';
  let eyebrow = 'CORA OPERATOR BRIEF';
  let title = 'One useful business system every week.';
  let description = 'Practical workflows, templates and operating ideas for running a calmer, more profitable service business.';

  if (pathname.startsWith('/articles/')) {
    source = 'article';
    title = 'Turn useful reading into a better operating system.';
    description = 'One practical operator note every week: client systems, scope, delivery, retainers and AI workflows you can actually use.';
  } else if (pathname.startsWith('/tools/')) {
    source = 'tool';
    title = 'Get the next useful agency tool before everyone else.';
    description = 'We send practical templates, generators and operating systems built to save real client-service work—not filler newsletters.';
  } else if (pathname === '/agency-management-software-india/' || pathname.startsWith('/use-cases/marketing-seo')) {
    source = 'agency';
    title = 'A weekly operating brief for agency owners.';
    description = 'Client onboarding, scope control, retainers, reporting, delivery systems and AI workflows—one useful idea at a time.';
  } else if (pathname.startsWith('/partners/agencies')) {
    source = 'partner';
    eyebrow = 'FOR AGENCY OWNERS';
    title = 'Not ready to partner yet? Stay in the loop.';
    description = 'Get practical agency systems and partner updates. Use the ideas first; explore Cora when the timing makes sense.';
  }

  return (
    <div className="w-full bg-white px-4 pb-4 pt-10 sm:px-6 sm:pb-6 sm:pt-14">
      <div className="mx-auto max-w-[1240px]">
        <NewsletterCapture
          source={source}
          eyebrow={eyebrow}
          title={title}
          description={description}
        />
      </div>
    </div>
  );
}
