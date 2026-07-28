import { useQuery } from '@tanstack/react-query';
import client from '../api/client';

const fetchPackage = async (trackingNumber) => {
  const { data } = await client.get(`/packages/${trackingNumber}`);
  return data.data;
};

export function useTracking(trackingNumber) {
  return useQuery({
    queryKey: ['package', trackingNumber],
    queryFn: () => fetchPackage(trackingNumber),
    enabled: !!trackingNumber,
    staleTime: 30_000,
    retry: (failureCount, error) => {
      if (error.response?.status === 404) return false;
      return failureCount < 2;
    },
  });
}
