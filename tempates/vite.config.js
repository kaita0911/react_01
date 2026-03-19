import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path from "path";

export default defineConfig({
  base: "/", // dùng cho domain hoặc subdomain

  plugins: [react()], // ⭐ BẮT BUỘC cho React

  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
});
