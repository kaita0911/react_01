import { Routes, Route, Navigate, Outlet } from "react-router-dom";
import Login from "./pages/Login/Login";
import ForgotPassword from "./pages/Login/ForgotPassword";

import Dashboard from "./pages/Dashboard/Dashboard";
import Configs from "./pages/Infos/Index";
import Component from "./pages/Component/List";
import ComponentEdit from "./pages/Component/Edit";
import Field from "./pages/Component/ListFields";
import Menu from "./pages/Menu/List";
import Footer from "./pages/Footer/Index";
import FooterEdit from "./pages/Footer/Edit";
import InfoEdit from "./pages/Infos/Edit";
import Language from "./pages/Languages/List";
import Contact from "./pages/Contact/List";
import Cart from "./pages/Cart/List";
import AdminLayout from "./pages/layouts/AdminLayout";
import DynamicModule from "./pages/Modules/DynamicModule";
import Category from "./pages/Category/List";
import CategoryCreate from "./pages/Category/Create";
import CategoryEdit from "./pages/Category/Edit";
import Create from "./pages/Modules/Create";
import Edit from "./pages/Modules/Edit";
import ProtectedRoute from "./pages/components/ProtectedRoute";
// ===== WRAPPER RESET MODULE =====
function DynamicModuleWrapper() {
  return <Outlet />;
}

function App() {
  return (
    <Routes>
      {/* Root */}
      <Route path="/" element={<Navigate to="/dashboard" replace />} />

      {/* ===== AUTH ===== */}
      <Route path="/login" element={<Login />} />
      <Route path="/forgot" element={<ForgotPassword />} />

      {/* ===== ADMIN ===== */}
      <Route
        element={
          <ProtectedRoute>
            <AdminLayout />
          </ProtectedRoute>
        }
      >
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/infos" element={<Configs />} />
        <Route path="/infos/edit/:id" element={<InfoEdit />} />
        <Route path="/component" element={<Component />} />
        <Route path="/component/edit/:component" element={<ComponentEdit />} />
        <Route path="/fields" element={<Field />} />
        <Route path="/menu" element={<Menu />} />
        <Route path="/footer" element={<Footer />} />
        <Route path="/footer/edit/:id" element={<FooterEdit />} />
        <Route path="/language" element={<Language />} />
        <Route path="/contact" element={<Contact />} />
        <Route path="/cart" element={<Cart />} />
        {/* ===== DYNAMIC MODULE ===== */}

        <Route path="/:module" element={<DynamicModuleWrapper />}>
          <Route path="category" element={<Category />} />
          <Route index element={<DynamicModule />} />
          <Route path="category/create" element={<CategoryCreate />} />
          <Route path="category/edit/:id" element={<CategoryEdit />} />
          <Route path="create" element={<Create />} />
          <Route path="edit/:id" element={<Edit />} />
        </Route>
      </Route>
    </Routes>
  );
}

export default App;
