import { NextResponse } from 'next/server';
import { SEED_PROJECTS } from '@/lib/projects-data';

export const revalidate = 60;

export async function GET() {
  try {
    const wpBase = process.env.WORDPRESS_API_URL || 'http://cora.local';
    const wpRes = await fetch(`${wpBase}/wp-json/cora-api/v1/projects`, {
      headers: {
        'Accept': 'application/json'
      },
      next: { revalidate: 60 }
    });

    if (wpRes.ok) {
      const data = await wpRes.json();
      if (Array.isArray(data) && data.length > 0) {
        return NextResponse.json(data);
      }
    }
  } catch (error) {
    // Gracefully handle local WP offline or unreachable
  }

  // Return seed case studies
  return NextResponse.json(SEED_PROJECTS);
}
