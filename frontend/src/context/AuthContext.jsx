import { createContext, useContext, useState, useCallback } from 'react';
import client from '../api/client';
import { getToken, setToken, removeToken } from '../utils/storage';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [token, setTokenState] = useState(() => getToken());

  const login = useCallback(async (email, password) => {
    const { data } = await client.post('/auth/login', { email, password });
    const accessToken = data.data.access_token;
    setToken(accessToken);
    setTokenState(accessToken);
  }, []);

  const logout = useCallback(() => {
    removeToken();
    setTokenState(null);
  }, []);

  return (
    <AuthContext.Provider value={{ token, isAuthenticated: !!token, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be inside AuthProvider');
  return ctx;
};
