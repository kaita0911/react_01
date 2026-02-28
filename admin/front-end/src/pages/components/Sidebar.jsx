import { Link } from "react-router-dom";
import { useState } from "react";
export default function Sidebar() {
  const [open, setOpen] = useState(false);
  return (
    <aside className="sidebar">
      <h2 className="logo">ADMIN</h2>

      <nav>
        <Link to="/">Dashboard</Link>
        <Link to="/products">Sản phẩm</Link>
        <Link to="/orders">Đơn hàng</Link>
        <div className="menu-parent">
          <div className="menu-title" onClick={() => setOpen(!open)}>
            Thông tin website ▾
          </div>

          {/* MENU CON */}
          {open && (
            <div className="submenu">
              {/* <Link to="/site/general">Cấu hình chung</Link>
              <Link to="/site/banner">Banner</Link>
              <Link to="/site/contact">Liên hệ</Link> */}
              <Link to="/modules">Module</Link>
              <Link to="/menu">Menu trên</Link>
              <Link to="/infos/list">Cấu hình</Link>
            </div>
          )}
        </div>
      </nav>
    </aside>
  );
}
