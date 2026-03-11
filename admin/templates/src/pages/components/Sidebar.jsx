import { Link } from "react-router-dom";
import { NavLink } from "react-router-dom";
import { useState } from "react";
export default function Sidebar({ modules }) {
  ////gọi module ra đây
  const [open, setOpen] = useState(false);
  return (
    <aside className="sidebar">
      <h2 className="logo">ADMIN</h2>

      <nav>
        {/* render module */}
        {modules.map((item) => (
          <NavLink key={item.id} to={`/${item.do}`}>
            {item.detail_name}
          </NavLink>
        ))}
        <div className="menu-parent">
          <div className="menu-title" onClick={() => setOpen(!open)}>
            Thông tin website ▾
          </div>

          {/* MENU CON */}
          {open && (
            <div className="submenu">
              <Link to="/language">Ngôn ngữ</Link>
              <Link to="/component">Component</Link>
              <Link to="/menu">Menu trên</Link>
              <Link to="/footer">Footer</Link>
              <Link to="/infos">Cấu hình</Link>
            </div>
          )}
        </div>
      </nav>
    </aside>
  );
}
