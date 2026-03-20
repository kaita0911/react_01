import { useEffect, useState } from "react";
import { useNavigate, useParams, Link } from "react-router-dom";
import { API_URL } from "@/config";
import "./Header.scss";
import MenuItem from "./MenuItem";
import { useCart } from "@/context/CartContext";
import { useWishlist } from "@/context/WishlistContext";

function Header() {
  const [menu, setMenu] = useState([]);
  const [infos, setInfos] = useState({});
  const [keyword, setKeyword] = useState("");
  const [languages, setLanguages] = useState([]);
  const { totalQty } = useCart();
  const { totalWishlist } = useWishlist();

  const navigate = useNavigate();
  const { lang } = useParams(); // 🔥 lấy lang từ URL
  const currentLang = lang || "vi";
  const changeLang = (newLang) => {
    const path = window.location.pathname;
    const segments = path.split("/").filter(Boolean);

    // nếu chưa có lang (trường hợp hiếm)
    if (segments.length === 0) {
      navigate(`/${newLang}`);
      return;
    }

    // thay lang ở segment đầu
    segments[0] = newLang;

    navigate("/" + segments.join("/"));
  };
  useEffect(() => {
    fetch(`${API_URL}/api/languages.php`)
      .then((res) => res.json())
      .then((data) => setLanguages(data));
  }, []);
  // 👉 MENU theo lang
  useEffect(() => {
    fetch(`${API_URL}/api/menu.php?lang=${currentLang}`)
      .then((res) => res.json())
      .then((data) => setMenu(data))
      .catch((err) => console.error(err));
  }, [currentLang]);

  // 👉 INFOS theo lang
  useEffect(() => {
    fetch(`${API_URL}/api/infos.php?lang=${currentLang}`)
      .then((res) => res.json())
      .then((data) => setInfos(data));
  }, [currentLang]);

  // 👉 SEARCH
  const handleSearch = (e) => {
    e.preventDefault();
    if (!keyword.trim()) return;

    navigate(`/${currentLang}/search?q=${encodeURIComponent(keyword)}`);
  };

  return (
    <header className="p-header">
      <div className="container">
        <div className="p-header-wrap">
          {/* LOGO */}
          <Link className="logo" to={`/${currentLang}`}>
            {infos.logoHome && (
              <img
                src={`${API_URL}/${infos.logoHome.img_thumb_vn}`}
                alt={infos.logoHome.name_vn}
              />
            )}
          </Link>

          {/* MENU */}
          <div className="menu menu_mb" id="mobile-menu">
            <nav className="menutop">
              <ul>
                {menu.map((item) => (
                  <MenuItem key={item.id} item={item} lang={currentLang} />
                ))}
              </ul>
            </nav>
          </div>

          {/* SEARCH */}
          <div className="box-search">
            <form onSubmit={handleSearch} className="search-form">
              <input
                type="text"
                placeholder={
                  lang === "vi" ? "Tìm sản phẩm..." : "Search products..."
                }
                value={keyword}
                onChange={(e) => setKeyword(e.target.value)}
              />

              <button type="submit" className="btn-search">
                <i className="fa-solid fa-magnifying-glass"></i>
              </button>
            </form>
          </div>
          <div className="lang-switch">
            {languages.map((l) => (
              <button
                key={l.code}
                onClick={() => changeLang(l.code)}
                className={lang === l.code ? "active" : ""}
              >
                {l.name}
              </button>
            ))}
          </div>
          {/* WISHLIST */}
          <Link to={`/${currentLang}/wishlist`} className="header-wishlist">
            ❤️
            {totalWishlist > 0 && (
              <span className="badge">{totalWishlist}</span>
            )}
          </Link>

          {/* CART */}
          <Link to={`/${currentLang}/cart`} className="cart-icon">
            🛒
            {totalQty > 0 && <span className="badge">{totalQty}</span>}
          </Link>
        </div>
      </div>
    </header>
  );
}

export default Header;
