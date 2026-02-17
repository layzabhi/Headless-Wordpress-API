/**
 * TypeScript Type Definitions
 * 
 * Type-safe interfaces for WordPress API responses
 * Ensures data consistency across the application
 */

export interface WPPost {
  id: number;
  title: { rendered: string };
  content: { rendered: string };
  excerpt: { rendered: string };
  slug: string;
  date: string;
  type: string;
  acf?: any;
  featured_image?: FeaturedImage;
  author_details?: Author;
}

export interface Project extends WPPost {
  acf?: {
    client?: string;
    year?: number;
    project_url?: string;
    featured?: boolean;
    technologies?: string[];
    gallery?: GalleryImage[];
    challenges?: string;
    solution?: string;
    results?: ProjectResult[];
  };
}

export interface TeamMember extends WPPost {
  acf?: {
    position: string;
    email?: string;
    social_links?: {
      linkedin?: string;
      github?: string;
    };
  };
}

export interface Testimonial extends WPPost {
  acf?: {
    author_name: string;
    company?: string;
    rating?: number;
  };
}

export interface FeaturedImage {
  thumbnail?: ImageSize;
  medium?: ImageSize;
  large?: ImageSize;
  full?: ImageSize;
}

export interface ImageSize {
  url: string;
  width: number;
  height: number;
}

export interface GalleryImage {
  id: number;
  url: string;
  alt: string;
  title: string;
}

export interface ProjectResult {
  metric: string;
  value: string;
}

export interface Author {
  id: number;
  name: string;
  avatar: string;
}

export interface SiteSettings {
  site_title: string;
  site_description: string;
  site_url: string;
  social_links?: {
    facebook?: string;
    linkedin?: string;
  };
  contact_info?: {
    email?: string;
    phone?: string;
  };
}