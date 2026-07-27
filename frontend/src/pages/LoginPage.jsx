import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useLocale } from '../context/LocaleContext';

export default function LoginPage() {
  const { login } = useAuth();
  const navigate = useNavigate();
  const { t } = useLocale();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [serverError, setServerError] = useState('');
  const [fieldErrors, setFieldErrors] = useState({});
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setServerError('');
    setFieldErrors({});
    setLoading(true);
    try {
      await login(email, password);
      navigate('/tracking');
    } catch (err) {
      const data = err.response?.data;
      if (data?.errors && typeof data.errors === 'object') {
        setFieldErrors(
          Object.fromEntries(
            Object.entries(data.errors).map(([key, msgs]) => [key, Array.isArray(msgs) ? msgs[0] : msgs])
          )
        );
        setServerError(data.message || t('login.validationError'));
      } else if (err.response?.status === 401) {
        setServerError(t('login.invalidCredentials'));
      } else {
        setServerError(t('login.genericError'));
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gray-50 dark:bg-gray-950 px-4 transition-colors">
      <div className="w-full max-w-sm">
        <div className="text-center mb-8">
          <h2 className="text-2xl font-bold text-gray-800 dark:text-gray-100">{t('login.title')}</h2>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-2">{t('login.subtitle')}</p>
        </div>

        <form onSubmit={handleSubmit} className="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 space-y-4">
          {serverError && !Object.keys(fieldErrors).length && (
            <div className="bg-red-50 dark:bg-red-950 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-400 text-sm rounded-lg p-3">{serverError}</div>
          )}

          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{t('login.email')}</label>
            <input
              type="email"
              value={email}
              onChange={(e) => { setEmail(e.target.value); setFieldErrors((f) => ({ ...f, email: '' })); }}
              className={`w-full px-3 py-2 border rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-transparent ${
                fieldErrors.email ? 'border-red-300 ring-red-200 focus:ring-red-500' : 'border-gray-300 dark:border-gray-700 focus:ring-blue-500'
              }`}
              placeholder={t('login.emailPlaceholder')}
              required
            />
            {fieldErrors.email && <p className="text-xs text-red-600 dark:text-red-400 mt-1">{fieldErrors.email}</p>}
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{t('login.password')}</label>
            <input
              type="password"
              value={password}
              onChange={(e) => { setPassword(e.target.value); setFieldErrors((f) => ({ ...f, password: '' })); }}
              className={`w-full px-3 py-2 border rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:border-transparent ${
                fieldErrors.password ? 'border-red-300 ring-red-200 focus:ring-red-500' : 'border-gray-300 dark:border-gray-700 focus:ring-blue-500'
              }`}
              placeholder={t('login.passwordPlaceholder')}
              required
            />
            {fieldErrors.password && <p className="text-xs text-red-600 dark:text-red-400 mt-1">{fieldErrors.password}</p>}
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full py-2.5 bg-blue-600 text-white font-medium rounded-lg
                       hover:bg-blue-700 disabled:opacity-50 transition-colors text-sm"
          >
            {loading ? t('login.loading') : t('login.submit')}
          </button>
        </form>
      </div>
    </div>
  );
}
