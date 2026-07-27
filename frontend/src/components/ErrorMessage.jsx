import { useLocale } from '../context/LocaleContext';

const styles = {
  error: {
    bg: 'bg-red-50 dark:bg-red-950',
    border: 'border-red-200 dark:border-red-900',
    icon: 'text-red-400',
    text: 'text-red-700 dark:text-red-400',
    btn: 'bg-red-500 hover:bg-red-600',
  },
  info: {
    bg: 'bg-blue-50 dark:bg-blue-950',
    border: 'border-blue-200 dark:border-blue-900',
    icon: 'text-blue-400',
    text: 'text-blue-700 dark:text-blue-400',
    btn: 'bg-blue-500 hover:bg-blue-600',
  },
};

export default function ErrorMessage({ message, onRetry, variant = 'error' }) {
  const { t } = useLocale();
  const s = styles[variant] || styles.error;

  return (
    <div className={`max-w-lg mx-auto mt-8 ${s.bg} ${s.border} border rounded-lg p-6 text-center`}>
      <svg className={`w-10 h-10 ${s.icon} mx-auto mb-3`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {variant === 'info' ? (
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        ) : (
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        )}
      </svg>
      <p className={`${s.text} text-sm mb-4`}>{message}</p>
      {onRetry && (
        <button onClick={onRetry} className={`text-sm ${s.btn} text-white px-4 py-2 rounded-lg transition-colors`}>
          {t('tracking.tryAgain')}
        </button>
      )}
    </div>
  );
}
