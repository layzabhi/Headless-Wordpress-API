/**
 * Custom React Hooks
 * 
 * Reusable hooks for data fetching with SWR
 * Provides caching, revalidation, and error handling
 */

import useSWR from 'swr';
import type { SiteSettings, Project } from './types';

const API_URL = 'https://localhost/projects/wp-json';
const fetcher = (url: string) => fetch(url).then(res => res.json());

export function useSiteSettings() {
  const { data, error, isLoading } = useSWR<SiteSettings>(
    `${API_URL}/headless/v1/settings`,
    fetcher
  );

  return {
    settings: data,
    isLoading,
    isError: error,
  };
}

export function useProjects() {
  const { data, error, isLoading } = useSWR<Project[]>(
    `${API_URL}/wp/v2/projects`,
    fetcher
  );

  return {
    projects: data || [],
    isLoading,
    isError: error,
  };
}