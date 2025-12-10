// vite.config.mjs
import tailwind from '@tailwindcss/vite'
import path from 'path'
import { defineConfig } from 'vite'

export default defineConfig({
  plugins: [tailwind()],
  base: '',                   // relative Pfade für WP
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    emptyOutDir: true,
    manifest: true,           // <-- wichtig!
    rollupOptions: {
      input: {
        app: path.resolve(__dirname, 'assets/js/app.js'),
      },
    },
  },
})
