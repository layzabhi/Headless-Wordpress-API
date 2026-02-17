import useSWR from 'swr';
import { wordpressApi } from '../lib/wordpress';

export function useSiteSettings() {
  const { data, error, isLoading } = useSWR(
    '/settings',
    wordpressApi.getSiteSettings
  );

  return {
    settings: data,
    isLoading,
    isError: error,
  };
}