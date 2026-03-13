import { Link, NavLink } from "react-router-dom";
import { useState } from "react";

export default function Sidebar({ modules }) {
  const [openMenu, setOpenMenu] = useState(null);
  const [open, setOpen] = useState(false);

  const toggleMenu = (id) => {
    setOpenMenu(openMenu === id ? null : id);
  };

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

        {/* THÔNG TIN WEBSITE */}
        <div className="menu-parent">
          <div className="menu-title" onClick={() => setOpen(!open)}>
            Thông tin website
            <span className={`arrow ${open ? "open" : ""}`}>▾</span>
          </div>

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
