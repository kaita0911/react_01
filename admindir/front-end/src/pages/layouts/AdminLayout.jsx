// import { useState } from "react";
// import { useNavigate } from "react-router-dom";
//import { API_URL } from "@/config";
import Sidebar from "../components/Sidebar";
import { Outlet } from "react-router-dom";
import Header from "../components/Header";
export default function AdminLayout() {
  return (
    <div className="admin-layout">
      <Sidebar />

      <div className="main">
        <Header />

        <div className="content">
          <Outlet />
        </div>
      </div>
    </div>
  );
}
