import { useNavigate } from "react-router-dom";
import { useState } from "react";
//import { API_URL } from "@/config";
import ChangePasswordModal from "../Login/ChangePasswordModal";

export default function Header() {
  const navigate = useNavigate();
  const userName = localStorage.getItem("admin_name") || "Admin";
  const [showChange, setShowChange] = useState(false);

  const handleLogout = async () => {
    await fetch(`/api/admin/logout.php`, {
      method: "POST",
      credentials: "include", // ⭐ BẮT BUỘC
    });

    localStorage.removeItem("admin_token");
    localStorage.removeItem("admin_name");

    navigate("/login", { replace: true });
  };
  // Format ngày Việt Nam
  const today = new Date().toLocaleDateString("vi-VN", {
    weekday: "long",
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });

  return (
    <>
      <header className="header">
        <div className="header-left">
          <h3>Dashboard</h3>
        </div>
        <div className="header-right">
          <div className="greeting">
            Xin chào, <b>{userName}</b>
            <div className="date">{today}</div>
          </div>

          <div className="user-box">
            <div
              onClick={() => {
                setShowChange(true);
              }}
            >
              <i className="fa-solid fa-key"></i>
              Đổi mật khẩu
            </div>

            <div onClick={handleLogout}>
              <i className="fa-solid fa-right-from-bracket"></i>Đăng xuất
            </div>
          </div>
        </div>
      </header>

      {showChange && (
        <ChangePasswordModal onClose={() => setShowChange(false)} />
      )}
    </>
  );
}
