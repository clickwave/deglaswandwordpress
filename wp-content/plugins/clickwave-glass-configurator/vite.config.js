import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: './assets/js',
    rollupOptions: {
      output: {
        entryFileNames: 'configurator.js',
        assetFileNames: 'configurator.[ext]'
      }
    }
  }
})
