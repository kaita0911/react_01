// import { defineConfig } from "vite";
// import react from "@vitejs/plugin-react";
// import path from "path";

// export default defineConfig({
//   plugins: [react()],

//   resolve: {
//     alias: {
//       "@": path.resolve(__dirname, "./src"), // 👈 thêm dòng này
//     },
//   },
// });
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

export default defineConfig({
  base: "/", // 👈 THÊM DÒNG NÀY

  plugins: [react()],

  server: {
    proxy: {
      "/api": {
        target: "http://react_01.local/admindir",
        changeOrigin: true,
      },
    },
  },
});
