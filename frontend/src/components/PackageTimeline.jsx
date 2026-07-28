import { useState } from 'react';
import { useLocale } from '../context/LocaleContext';
import TimelineItem from './TimelineItem';

const statusBadge = {
  Registered: 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
  'In Transit': 'bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300',
  'Out for Delivery': 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
  Delivered: 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-300',
  Cancelled: 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300',
};

export default function PackageTimeline({ data }) {
  const { t } = useLocale();
  const { package: pkg, tracking_history: history } = data;
  const [copied, setCopied] = useState(false);

  const handleCopy = async () => {
    try {
      await navigator.clipboard.writeText(pkg.tracking_number);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    } catch {
      // Fallback for older browsers
      const input = document.createElement('input');
      input.value = pkg.tracking_number;
      document.body.appendChild(input);
      input.select();
      document.execCommand('copy');
      document.body.removeChild(input);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  return (
    <div className="max-w-lg mx-auto mt-8 space-y-6">
      <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-5 shadow-sm">
        <div className="flex items-start justify-between gap-2">
          <h2 className="text-lg font-bold text-gray-800 dark:text-gray-100 break-all">{pkg.tracking_number}</h2>
          <button
            onClick={handleCopy}
            className="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
            title={copied ? t('tracking.copied') : t('tracking.copy')}
          >
            {copied ? (
              <svg className="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
            ) : (
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            )}
          </button>
        </div>
        <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">{pkg.description}</p>
        <div className="flex gap-4 mt-3 text-xs text-gray-500 dark:text-gray-500">
          {pkg.weight && <span>{t('tracking.weight')}: {pkg.weight} kg</span>}
          {pkg.recipient_name && <span>{t('tracking.to')}: {pkg.recipient_name}</span>}
        </div>
        <span className={`inline-block mt-3 px-2.5 py-0.5 rounded-full text-xs font-medium ${statusBadge[pkg.status] || 'bg-gray-100 text-gray-700'}`}>
          {pkg.status}
        </span>
        {(pkg.status === 'Delivered' || pkg.status === 'Cancelled') && (
          <p className="text-xs text-gray-400 dark:text-gray-500 mt-2">
            {t('tracking.finalStatus')} <span className="font-medium text-gray-600 dark:text-gray-300">{pkg.status}</span>
          </p>
        )}
      </div>

      <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
        <div className="px-5 pt-5 pb-2">
          <h3 className="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{t('tracking.history')}</h3>
        </div>
        <div className="relative ml-4 px-5 pb-5">
          {history.map((item, i) => (
            <TimelineItem key={item.id} item={item} isLast={i === history.length - 1} />
          ))}
        </div>
      </div>
    </div>
  );
}
