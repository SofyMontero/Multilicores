import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  // Build se despliega en Hostinger como /sistema/app/
  base: '/sistema/app/',
  server: {
    port: 5173,
    proxy: {
      '/sistema/api': {
        target: 'https://multilicoreschapinero.com',
        changeOrigin: true,
        secure: true,
      },
      '/sistema/assets': {
        target: 'https://multilicoreschapinero.com',
        changeOrigin: true,
        secure: true,
      },
    },
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true,
  },
})
