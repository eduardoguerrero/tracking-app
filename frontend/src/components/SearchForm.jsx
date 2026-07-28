import { useLocale } from '../context/LocaleContext';

export default function SearchForm({ value, onChange, onSearch, isLoading, footer }) {
  const { t } = useLocale();

  const handleSubmit = (e) => {
    e.preventDefault();
    const trimmed = value.trim();
    if (trimmed) {
      onSearch(trimmed);
    }
  };

  const handleChange = (e) => {
    onChange(e.target.value);
  };

  return (
    <div className="max-w-lg mx-auto">
      <div className="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm">
        <div className="px-5 pt-5 pb-2">
          <h2 className="text-lg font-semibold text-gray-800 dark:text-gray-100">{t('tracking.heading')}</h2>
        </div>
        <form onSubmit={handleSubmit} className="flex gap-3 px-5 pb-4">
          <input
            type="text"
            value={value}
            onChange={handleChange}
            placeholder={t('tracking.inputPlaceholder')}
            className="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg text-sm
                       bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                       disabled:opacity-50"
            disabled={isLoading}
          />
          <button
            type="submit"
            disabled={isLoading || !value.trim()}
            className="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg
                       hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed
                       transition-colors text-sm whitespace-nowrap"
          >
            {isLoading ? t('tracking.searching') : t('tracking.track')}
          </button>
        </form>
        {footer && (
          <div className="px-5 pb-5">
            {footer}
          </div>
        )}
      </div>
    </div>
  );
}
