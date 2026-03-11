import { Routes, Route, Navigate } from "react-router-dom";
import Login from "./pages/Login/Login";
import ForgotPassword from "./pages/Login/ForgotPassword";

import Dashboard from "./pages/Dashboard/Dashboard";
import Configs from "./pages/Infos/Index";
import Component from "./pages/Component/List";
import ComponentEdit from "./pages/Component/Edit";
import Menu from "./pages/Menu/List";
import Footer from "./pages/Footer/Index";
import FooterEdit from "./pages/Footer/Edit";
import InfoEdit from "./pages/Infos/Edit";
import Language from "./pages/Languages/List";

import AdminLayout from "./pages/layouts/AdminLayout";
import DynamicModule from "./pages/Modules/DynamicModule";
import Create from "./pages/Modules/Create";
import Edit from "./pages/Modules/Edit";
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
        <Route path="/infos" element={<Configs />} />
        <Route path="/infos/edit/:id" element={<InfoEdit />} />
        <Route path="/component" element={<Component />} />
        <Route path="/component/edit/:id" element={<ComponentEdit />} />
        <Route path="/menu" element={<Menu />} />
        <Route path="/footer" element={<Footer />} />
        <Route path="/footer/edit/:id" element={<FooterEdit />} />
        <Route path="/language" element={<Language />} />
        <Route path="/:module" element={<DynamicModule />} />
        <Route path="/:module/create" element={<Create />} />
        <Route path="/:module/edit/:id" element={<Edit />} />
      </Route>
    </Routes>
  );
}

export default App;
