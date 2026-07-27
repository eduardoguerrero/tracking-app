import { createContext, useContext, useState } from 'react';
import translations from './translations';

const LocaleContext = createContext(null);

export function LocaleProvider({ children }) {
  const [locale, setLocale] = useState(() => localStorage.getItem('locale') || 'en');

  const t = (path) => {
    return path.split('.').reduce((obj, key) => obj?.[key], translations[locale]) ?? path;
  };

  const changeLocale = (lang) => {
    setLocale(lang);
    localStorage.setItem('locale', lang);
  };

  return (
    <LocaleContext.Provider value={{ locale, t, changeLocale }}>
      {children}
    </LocaleContext.Provider>
  );
}

export const useLocale = () => {
  const ctx = useContext(LocaleContext);
  if (!ctx) throw new Error('useLocale must be inside LocaleProvider');
  return ctx;
};
