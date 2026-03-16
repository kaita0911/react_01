import { Link, useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";
//import { API_URL } from "@/config";
import ChangePasswordModal from "../Login/ChangePasswordModal";

export default function Header() {
  const [newContact, setNewContact] = useState(0);
  const navigate = useNavigate();
  const userName = localStorage.getItem("admin_name") || "Admin";
  const [showChange, setShowChange] = useState(false);

  useEffect(() => {
    const checkContact = () => {
      fetch("/api/admin/contact.php?act=count_new")
        .then((res) => res.json())
        .then((data) => {
          if (data.status) {
            setNewContact(data.total);
          }
        });
    };

    checkContact();

    const interval = setInterval(checkContact, 5000);

    return () => clearInterval(interval);
  }, []);
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
          {newContact > 0 && (
            <Link to={`/contact`} className="contact">
              <span className="badge-contact">Liên hệ mới {newContact}</span>
            </Link>
          )}
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
