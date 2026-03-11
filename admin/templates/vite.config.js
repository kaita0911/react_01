import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path from "path";

export default defineConfig({
  base: "/",

  plugins: [react()],

  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },

  server: {
    proxy: {
      "/api": {
        target: "http://react_01.local/",
        changeOrigin: true,
      },
      "/ckfinder": {
        target: "http://react_01.local",
        changeOrigin: true,
      },
      "/picture": {
        target: "http://react_01.local",
        changeOrigin: true,
      },
    },
  },
});
