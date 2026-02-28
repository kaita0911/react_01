import { Routes, Route, Navigate } from "react-router-dom";
import Login from "./pages/Login/Login";
import ForgotPassword from "./pages/Login/ForgotPassword";

import Dashboard from "./pages/Dashboard/Dashboard";
import Configs from "./pages/Infos/List";
import Module from "./pages/Modules/List";
import Menu from "./pages/Menu/List";
import InfoEdit from "./pages/Infos/Edit";

import AdminLayout from "./pages/layouts/AdminLayout";

function App() {
  const isLogin = !!localStorage.getItem("admin_token");

  return (
    <Routes>
      {/* ===== AUTH ===== */}
      <Route path="/login" element={<Login />} />
      <Route path="/forgot" element={<ForgotPassword />} />

      {/* ===== ADMIN ===== */}
      <Route
        path=""
        element={isLogin ? <AdminLayout /> : <Navigate to="/login" replace />}
      >
        <Route index element={<Dashboard />} />
        <Route path="/infos/list" element={<Configs />} />
        <Route path="/infos/edit/:id" element={<InfoEdit />} />
        <Route path="/modules" element={<Module />} />
        <Route path="/menu" element={<Menu />} />
      </Route>
    </Routes>
  );
}

export default App;
