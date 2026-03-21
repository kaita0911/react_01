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
  //timkiemnangcao
  //const [suggests, setSuggests] = useState([]);

  const [languages, setLanguages] = useState([]);
  const { totalQty } = useCart();
  const { totalWishlist } = useWishlist();

  const navigate = useNavigate();
  const { lang } = useParams(); // 🔥 lấy lang từ URL

  const changeLang = async (newLang) => {
    const path = window.location.pathname;
    const segments = path.split("/").filter(Boolean);

    // / → home
    if (segments.length === 0 || segments.length === 1) {
      navigate(`/${newLang}`);
      return;
    }

    // slug cuối luôn là segments[segments.length - 1]
    let slug = segments[segments.length - 1];
    let ext = "";

    if (slug.endsWith(".html")) {
      slug = slug.replace(".html", "");
      ext = ".html";
    }

    try {
      const res = await fetch(
        `${API_URL}/api/resolve.php?slug=${slug}&lang=${newLang}`
      );
      const data = await res.json();

      if (data.slug) {
        navigate(`/${newLang}/${data.slug}${ext}`);
      } else {
        navigate(`/${newLang}`);
      }
    } catch {
      navigate(`/${newLang}`);
    }
  };
  useEffect(() => {
    fetch(`${API_URL}/api/languages.php`)
      .then((res) => res.json())
      .then((data) => {
        console.log("Languages API:", data);
        setLanguages(data.languages || []);
      })
      .catch((err) => console.error(err));
  }, []);
  // 👉 MENU theo lang
  useEffect(() => {
    fetch(`${API_URL}/api/menu.php?lang=${lang}`)
      .then((res) => res.json())
      .then((data) => setMenu(data))
      .catch((err) => console.error(err));
  }, [lang]);

  // 👉 INFOS theo lang
  useEffect(() => {
    fetch(`${API_URL}/api/infos.php?lang=${lang}`)
      .then((res) => res.json())
      .then((data) => setInfos(data));
  }, [lang]);

  // 👉 SEARCH
  const handleSearch = (e) => {
    e.preventDefault();
    if (!keyword.trim()) return;

    navigate(`/${lang}/search?q=${encodeURIComponent(keyword)}`);
  };
  // 👉 SEARCH nângcao
  // useEffect(() => {
  //   if (!keyword.trim()) {
  //     const timeout = setTimeout(() => setSuggests([]), 0);
  //     return () => clearTimeout(timeout);
  //   }

  //   const timeout = setTimeout(() => {
  //     fetch(
  //       `${API_URL}/api/search-suggest.php?q=${encodeURIComponent(
  //         keyword
  //       )}&lang=${lang}`
  //     )
  //       .then((res) => res.json())
  //       .then((data) => setSuggests(data))
  //       .catch((err) => console.error(err));
  //   }, 300); // debounce 300ms

  //   return () => clearTimeout(timeout); // clear timeout nếu keyword thay đổi
  // }, [keyword, lang]);
  return (
    <header className="p-header">
      <div className="container">
        <div className="p-header-wrap">
          {/* LOGO */}
          <Link className="logo" to={`/${lang}`}>
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
                  <MenuItem key={item.id} item={item} lang={lang} />
                ))}
              </ul>
            </nav>
          </div>
          {/* SEARCH tim kiếm đơn giản */}
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
          {/* SEARCH nâng cao */}
          {/* <div className="box-search" style={{ position: "relative" }}>
            <form onSubmit={handleSearch}>
              <input
                type="text"
                value={keyword}
                onChange={(e) => setKeyword(e.target.value)}
                placeholder="Tìm sản phẩm..."
              />
              <button type="submit">Tìm</button>
            </form>

            {suggests.length > 0 && (
              <ul
                className="suggest-list"
                style={{
                  position: "absolute",
                  top: "100%",
                  left: 0,
                  right: 0,
                  background: "#fff",
                  border: "1px solid #ccc",
                  zIndex: 999,
                  listStyle: "none",
                  margin: 0,
                  padding: 0,
                }}
              >
                {suggests.map((item) => (
                  <li
                    key={item.id}
                    onClick={() => navigate(`/${lang}/product/${item.slug}`)}
                    style={{ padding: "8px", cursor: "pointer" }}
                  >
                    {item.name} {item.price ? `- ${item.price}₫` : ""}
                  </li>
                ))}
              </ul>
            )}
          </div> */}
          {languages.length > 1 && (
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
          )}

          {/* WISHLIST */}
          <Link to={`/${lang}/wishlist`} className="header-wishlist">
            ❤️
            {totalWishlist > 0 && (
              <span className="badge">{totalWishlist}</span>
            )}
          </Link>
          {/* CART */}
          <Link to={`/${lang}/cart`} className="cart-icon">
            🛒
            {totalQty > 0 && <span className="badge">{totalQty}</span>}
          </Link>
        </div>
      </div>
    </header>
  );
}

export default Header;
