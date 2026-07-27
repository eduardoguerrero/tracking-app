import { useLocale } from '../context/LocaleContext';

export default function EmptyState() {
  const { t } = useLocale();

  return (
    <div className="max-w-lg mx-auto mt-16 text-center text-gray-400 dark:text-gray-600">
      <svg className="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1}
          d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
      </svg>
      <p className="text-lg font-medium dark:text-gray-400">{t('tracking.emptyTitle')}</p>
      <p className="text-sm mt-1">{t('tracking.emptySubtitle')}</p>
    </div>
  );
}
