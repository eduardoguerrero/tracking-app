import { useState } from 'react';
import { useTracking } from '../hooks/useTracking';
import { useLocale } from '../context/LocaleContext';
import SearchForm from '../components/SearchForm';
import LoadingSkeleton from '../components/LoadingSkeleton';
import ErrorMessage from '../components/ErrorMessage';
import PackageTimeline from '../components/PackageTimeline';

export default function TrackingPage() {
  const { t } = useLocale();
  const [inputValue, setInputValue] = useState('');
  const [trackingNumber, setTrackingNumber] = useState('');
  const { data, isLoading, isError, error, refetch } = useTracking(trackingNumber);

  const handleSearch = (value) => setTrackingNumber(value);

  const getErrorMessage = () => {
    if (!error) return '';
    const status = error.response?.status;
    if (status === 404) return t('tracking.packageNotFound');
    if (status === 401) return t('tracking.sessionExpired');
    return t('tracking.genericError');
  };

  const isNotFound = error?.response?.status === 404;

  return (
    <div className="px-4 py-8">
      <SearchForm
        value={inputValue}
        onChange={setInputValue}
        onSearch={handleSearch}
        isLoading={isLoading}
        footer={
          !inputValue.trim() ? (
            <div className="text-center text-gray-400 dark:text-gray-600 py-4">
              <svg className="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1}
                  d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
              </svg>
              <p className="text-sm">{t('tracking.emptySubtitle')}</p>
            </div>
          ) : isNotFound ? (
            <div className="bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-900 rounded-lg p-4 text-center">
              <svg className="w-6 h-6 text-blue-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p className="text-sm text-blue-700 dark:text-blue-400">{getErrorMessage()}</p>
              <button onClick={() => refetch()} className="mt-2 text-xs text-blue-600 dark:text-blue-300 hover:underline">
                {t('tracking.tryAgain')}
              </button>
            </div>
          ) : null
        }
      />

      {isLoading && <LoadingSkeleton />}

      {isError && !isNotFound && (
        <ErrorMessage message={getErrorMessage()} onRetry={() => refetch()} variant="error" />
      )}

      {data && !isLoading && <PackageTimeline data={data} />}
    </div>
  );
}
