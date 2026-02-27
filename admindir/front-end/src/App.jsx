import { Routes, Route, Navigate } from "react-router-dom";
import Login from "./pages/Login/Login";
import ForgotPassword from "./pages/Login/ForgotPassword";

import Dashboard from "./pages/Dashboard/Dashboard";
import Configs from "./pages/Infos/general";
import Module from "./pages/Infos/module";
import ConfigEdit from "./pages/Infos/ConfigEdit";

import AdminLayout from "./pages/layouts/AdminLayout";

function App() {
  const isLogin = !!localStorage.getItem("admin_token");

  return (
    <Routes>
      {/* ===== AUTH ===== */}
      <Route path="/admin/login" element={<Login />} />
      <Route path="/admin/forgot" element={<ForgotPassword />} />

      {/* ===== ADMIN ===== */}
      <Route
        path="/admin"
        element={
          isLogin ? <AdminLayout /> : <Navigate to="/admin/login" replace />
        }
      >
        <Route index element={<Dashboard />} />

        <Route path="/admin/Infos/general" element={<Configs />} />
        <Route path="/admin/Infos/general/edit/:key" element={<ConfigEdit />} />
        <Route path="/admin/Infos/module" element={<Module />} />
      </Route>
    </Routes>
  );
}

export default App;
