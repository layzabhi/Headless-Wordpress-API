'use client';

import Link from 'next/link';
import { useState } from 'react';
import { useSiteSettings } from '../lib/hooks';

export default function Header() {
  const [isOpen, setIsOpen] = useState(false);
  const { settings } = useSiteSettings();

  const links = [
    { name: 'Home', href: '/' },
    { name: 'Projects', href: '/projects' },
  ];

  return (
    <header className="bg-white shadow-sm sticky top-0 z-50">
      <nav className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          <Link href="/" className="text-2xl font-bold text-gray-900 hover:text-blue-600 transition">
            {settings?.site_title || 'Portfolio'}
          </Link>

          {/* Desktop Nav */}
          <div className="hidden md:flex space-x-8">
            {links.map(link => (
              <Link
                key={link.href}
                href={link.href}
                className="text-gray-700 hover:text-blue-600 px-3 py-2 font-medium transition"
              >
                {link.name}
              </Link>
            ))}
          </div>

          {/* Mobile Toggle */}
          <button
            onClick={() => setIsOpen(!isOpen)}
            className="md:hidden p-2 rounded hover:bg-gray-100"
            aria-label="Menu"
          >
            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                d={isOpen ? "M6 18L18 6M6 6l12 12" : "M4 6h16M4 12h16M4 18h16"} />
            </svg>
          </button>
        </div>

        {/* Mobile Nav */}
        {isOpen && (
          <div className="md:hidden pb-4">
            {links.map(link => (
              <Link
                key={link.href}
                href={link.href}
                className="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded"
                onClick={() => setIsOpen(false)}
              >
                {link.name}
              </Link>
            ))}
          </div>
        )}
      </nav>
    </header>
  );
}