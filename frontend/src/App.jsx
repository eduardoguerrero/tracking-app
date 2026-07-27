import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { AuthProvider, useAuth } from './context/AuthContext';
import { ThemeProvider } from './context/ThemeContext';
import { LocaleProvider } from './context/LocaleContext';
import Header from './components/Header';
import LoginPage from './pages/LoginPage';
import TrackingPage from './pages/TrackingPage';

const queryClient = new QueryClient({
  defaultOptions: {
    queries: { refetchOnWindowFocus: false },
  },
});

function RequireAuth({ children }) {
  const { isAuthenticated } = useAuth();
  return isAuthenticated ? children : <Navigate to="/" replace />;
}

function AppRoutes() {
  const { isAuthenticated } = useAuth();

  return (
    <Routes>
      <Route path="/" element={
        isAuthenticated ? <Navigate to="/tracking" replace /> : <LoginPage />
      } />
      <Route path="/tracking" element={
        <RequireAuth><TrackingPage /></RequireAuth>
      } />
    </Routes>
  );
}

export default function App() {
  return (
    <ThemeProvider>
      <LocaleProvider>
        <QueryClientProvider client={queryClient}>
          <AuthProvider>
            <BrowserRouter>
              <div className="min-h-screen bg-gray-50 dark:bg-gray-950 transition-colors">
                <Header />
                <AppRoutes />
              </div>
            </BrowserRouter>
          </AuthProvider>
        </QueryClientProvider>
      </LocaleProvider>
    </ThemeProvider>
  );
}
