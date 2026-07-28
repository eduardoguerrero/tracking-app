import { useQuery } from '@tanstack/react-query';
import client from '../api/client';

const fetchProfile = async () => {
  const { data } = await client.get('/auth/me');
  return data.data;
};

export function useProfile() {
  return useQuery({
    queryKey: ['profile'],
    queryFn: fetchProfile,
    staleTime: 5 * 60_000,
  });
}
