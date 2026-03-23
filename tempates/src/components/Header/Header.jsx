import { useEffect, useState } from "react";
import { useNavigate, useParams, Link } from "react-router-dom";
import { API_URL } from "@/config";
import "./Header.scss";
import MenuItem from "./MenuItem";
import { useCart } from "@/context/CartContext";
import { useWishlist } from "@/context/WishlistContext";
import { useLanguage } from "@/context/useLanguage";

function Header() {
  const [menu, setMenu] = useState([]);
  const [infos, setInfos] = useState({});
  const [keyword, setKeyword] = useState("");

  const { totalQty } = useCart();
  const { totalWishlist } = useWishlist();

  const navigate = useNavigate();

  // Lấy từ Context

  const { lang: urlLang } = useParams(); // lang từ URL

  // Lấy từ Context
  const { singleLang, languages, defaultLang } = useLanguage();

  // fallback lang nếu URL không có
  const lang = urlLang || defaultLang;

  // useEffect(() => {
  //   console.log("Languages loaded:", languages.code);
  // }, [lang]);
  // Change language
  const changeLang = async (newLang) => {
    const path = window.location.pathname;
    const segments = path.split("/").filter(Boolean);

    if (segments.length === 0 || segments.length === 1) {
      navigate(`/${newLang}`);
      return;
    }

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

      if (data.slug) navigate(`/${newLang}/${data.slug}${ext}`);
      else navigate(`/${newLang}`);
    } catch {
      navigate(`/${newLang}`);
    }
  };

  // Fetch menu theo lang
  useEffect(() => {
    fetch(`${API_URL}/api/menu.php?lang=${lang}`)
      .then((res) => res.json())
      .then((data) => setMenu(data))
      .catch(console.error);
  }, [lang]);

  // Fetch info theo lang
  useEffect(() => {
    fetch(`${API_URL}/api/infos.php?lang=${lang}`)
      .then((res) => res.json())
      .then((data) => setInfos(data))
      .catch(console.error);
  }, [lang]);

  // Search
  const handleSearch = (e) => {
    e.preventDefault();
    if (!keyword.trim()) return;

    const q = encodeURIComponent(keyword);

    if (singleLang) {
      navigate(`/search?q=${q}`);
    } else {
      navigate(`/${lang}/search?q=${q}`);
    }
  };

  return (
    <header className="p-header">
      <div className="container">
        <div className="p-header-wrap">
          {/* LOGO */}
          <Link className="logo" to={singleLang ? "/" : `/${lang}`}>
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
                  <MenuItem
                    key={item.id}
                    item={item}
                    lang={lang}
                    singleLang={singleLang}
                  />
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

          {/* LANGUAGE SWITCH */}
          {!singleLang && languages.length > 1 && (
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
          <Link
            to={singleLang ? "/wishlist/" : `/${lang}/wishlist/`}
            className="header-wishlist"
          >
            ❤️
            {totalWishlist > 0 && (
              <span className="badge">{totalWishlist}</span>
            )}
          </Link>

          {/* CART */}
          <Link
            to={singleLang ? "/cart/" : `/${lang}/cart/`}
            className="cart-icon"
          >
            🛒 {totalQty > 0 && <span className="badge">{totalQty}</span>}
          </Link>
        </div>
      </div>
    </header>
  );
}

export default Header;
