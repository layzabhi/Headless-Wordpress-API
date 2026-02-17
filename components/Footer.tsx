'use client';

import Link from 'next/link';
import { useSiteSettings } from '../lib/hooks';

export default function Footer() {
  const { settings } = useSiteSettings();
  const year = new Date().getFullYear();

  return (
    <footer className="bg-gray-900 text-white mt-auto">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 className="text-xl font-bold mb-4">
              {settings?.site_title || 'Portfolio'}
            </h3>
            <p className="text-gray-400">
              {settings?.site_description || 'Modern headless WordPress portfolio'}
            </p>
          </div>

          <div>
            <h4 className="font-semibold mb-4">Quick Links</h4>
            <div className="space-y-2">
              <Link href="/" className="block text-gray-400 hover:text-white transition">
                Home
              </Link>
              <Link href="/projects" className="block text-gray-400 hover:text-white transition">
                Projects
              </Link>
            </div>
          </div>

          <div>
            <h4 className="font-semibold mb-4">Contact</h4>
            {settings?.contact_info?.email && (
              <a href={`mailto:${settings.contact_info.email}`} 
                className="block text-gray-400 hover:text-white transition mb-2">
                {settings.contact_info.email}
              </a>
            )}
          </div>
        </div>

        <div className="mt-8 pt-8 border-t border-gray-800 flex justify-between items-center">
          <p className="text-gray-400 text-sm">
            © {year} {settings?.site_title}. Built with WordPress & Next.js
          </p>

          {settings?.social_links && (
            <div className="flex space-x-4">
              {settings.social_links.linkedin && (
                <a href={settings.social_links.linkedin} 
                  target="_blank" rel="noopener noreferrer"
                  className="text-gray-400 hover:text-white transition"
                  aria-label="LinkedIn">
                  <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452z"/>
                  </svg>
                </a>
              )}
            </div>
          )}
        </div>
      </div>
    </footer>
  );
}