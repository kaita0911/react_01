import { Link, NavLink } from "react-router-dom";
import { useState, useEffect } from "react";

export default function Sidebar({ modules }) {
  const [openMenu, setOpenMenu] = useState(null);
  const [open, setOpen] = useState(false);
  const [infos, setInfos] = useState(null); // 👈 thêm
  const toggleMenu = (id) => {
    setOpenMenu(openMenu === id ? null : id);
  };
  const userName = localStorage.getItem("admin_name") || "Admin";
  const isAdmin = userName === "admin";
  useEffect(() => {
    fetch("/api/admin/infos.php?act=list") // sửa lại đúng API của bạn
      .then((res) => res.json())
      .then((res) => {
        if (res.status) {
          setInfos(res.data);
        }
      });
  }, []);
  return (
    <aside className="sidebar">
      <h2 className="logo">ADMIN</h2>

      <nav>
        {modules.map((item) => {
          const hasCategory = Number(item.nhomcon) === 1;
          const isOpen = openMenu === item.id;

          return (
            <div key={item.id} className="menu-parent">
              {!hasCategory ? (
                /* MODULE KHÔNG CÓ CATEGORY */
                <NavLink className="menu-title" to={`/${item.do}`}>
                  {item.detail_name}
                </NavLink>
              ) : (
                <>
                  {/* MODULE CÓ CATEGORY */}
                  <div
                    className="menu-title menusub"
                    onClick={() => toggleMenu(item.id)}
                  >
                    {item.detail_name}
                    <span className={`arrow ${isOpen ? "open" : ""}`}>▾</span>
                  </div>

                  {isOpen && (
                    <div className="submenu">
                      <NavLink to={`/${item.do}`} end>
                        Danh sách
                      </NavLink>
                      <NavLink to={`/${item.do}/category`}>Danh mục</NavLink>
                    </div>
                  )}
                </>
              )}
            </div>
          );
        })}
        {infos?.find((item) => item.id == 12)?.active == 1 && (
          <Link to="/cart">Đơn hàng</Link>
        )}
        <Link to="/contact">Liên hệ</Link>
        {/* THÔNG TIN WEBSITE */}
        <div className="menu-parent">
          <div className="menu-title" onClick={() => setOpen(!open)}>
            Thông tin website
            <span className={`arrow ${open ? "open" : ""}`}>▾</span>
          </div>

          {open && (
            <div className="submenu">
              {!isAdmin && <Link to="/language">Ngôn ngữ</Link>}
              {!isAdmin && <Link to="/component">Component</Link>}
              {!isAdmin && <Link to="/fields">Fields</Link>}
              <Link to="/menu">Menu trên</Link>
              <Link to="/footer">Footer</Link>
              <Link to="/infos">Cấu hình</Link>
            </div>
          )}
        </div>
        <Link to="/dashboard">Dashboard</Link>
      </nav>
    </aside>
  );
}
