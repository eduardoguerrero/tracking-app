import { useLocale } from '../context/LocaleContext';
import TimelineItem from './TimelineItem';

export default function PackageTimeline({ data }) {
  const { t } = useLocale();
  const { package: pkg, tracking_history: history } = data;

  const statusColors = {
    Registered: 'blue',
    'In Transit': 'orange',
    'Out for Delivery': 'amber',
    Delivered: 'green',
    Cancelled: 'red',
  };

  return (
    <div className="max-w-lg mx-auto mt-8">
      <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-5 mb-6 shadow-sm">
        <h2 className="text-lg font-bold text-gray-800 dark:text-gray-100">{pkg.tracking_number}</h2>
        <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">{pkg.description}</p>
        <div className="flex gap-4 mt-3 text-xs text-gray-500 dark:text-gray-500">
          {pkg.weight && <span>{t('tracking.weight')}: {pkg.weight} kg</span>}
          {pkg.recipient_name && <span>{t('tracking.to')}: {pkg.recipient_name}</span>}
        </div>
        <span className={`inline-block mt-3 px-2.5 py-0.5 rounded-full text-xs font-medium
          bg-${statusColors[pkg.status] || 'gray'}-100 dark:bg-${statusColors[pkg.status] || 'gray'}-900
          text-${statusColors[pkg.status] || 'gray'}-700 dark:text-${statusColors[pkg.status] || 'gray'}-300`}>
          {pkg.status}
        </span>
      </div>

      <h3 className="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">{t('tracking.history')}</h3>
      <div className="relative ml-4">
        {history.map((item, i) => (
          <TimelineItem key={item.id} item={item} isLast={i === history.length - 1} />
        ))}
      </div>
    </div>
  );
}
