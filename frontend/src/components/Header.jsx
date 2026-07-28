import { useAuth } from '../context/AuthContext';
import { useTheme } from '../context/ThemeContext';
import { useLocale } from '../context/LocaleContext';
import ProfileMenu from './ProfileMenu';

export default function Header() {
  const { isAuthenticated } = useAuth();
  const { dark, toggle } = useTheme();
  const { t, locale, changeLocale } = useLocale();

  return (
    <header className="bg-emerald-700 dark:bg-gray-950 text-white py-4 px-6 flex items-center justify-between shadow-lg transition-colors">
      <div className="flex items-center gap-3">
        <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
        </svg>
        <h1 className="text-xl font-bold">{t('app.title')}</h1>
      </div>

      <div className="flex items-center gap-2">
        <button
          onClick={() => changeLocale(locale === 'en' ? 'es' : 'en')}
          className="text-xs bg-emerald-600 dark:bg-gray-800 hover:bg-emerald-500 dark:hover:bg-gray-700 px-2 py-1 rounded transition-colors"
        >
          {locale === 'en' ? 'ES' : 'EN'}
        </button>

        <button
          onClick={toggle}
          className="text-xs bg-emerald-600 dark:bg-gray-800 hover:bg-emerald-500 dark:hover:bg-gray-700 px-2 py-1 rounded transition-colors"
          title={dark ? t('header.light') : t('header.dark')}
        >
          {dark ? '\u2600\uFE0F' : '\uD83C\uDF19'}
        </button>

        {isAuthenticated && <ProfileMenu />}
      </div>
    </header>
  );
}
