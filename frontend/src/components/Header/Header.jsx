import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { API_URL } from "@/config";
import "./Header.scss";
import { Link } from "react-router-dom";
import MenuItem from "./MenuItem";
import { useCart } from "@/context/CartContext";
//import SearchBox from "./SearchBox";
import { useWishlist } from "@/context/WishlistContext";
function Header() {
  const [menu, setMenu] = useState([]);
  const { totalQty } = useCart();
  useEffect(() => {
    fetch(`${API_URL}/api/menu.php`)
      .then((res) => res.json())
      .then((data) => setMenu(data))
      .catch((err) => console.error(err));
  }, []);

  const [infos, setInfos] = useState({});

  useEffect(() => {
    fetch(`${API_URL}/api/infos.php`)
      .then((res) => res.json())
      .then((data) => setInfos(data));
  }, []);
  ///search
  const [keyword, setKeyword] = useState("");
  const navigate = useNavigate();
  const handleSearch = (e) => {
    e.preventDefault();

    if (!keyword.trim()) return;

    navigate(`/search?q=${encodeURIComponent(keyword)}`);
  };

  ////wishlist
  const { totalWishlist } = useWishlist();
  return (
    <header className="p-header">
      <div className="container">
        <div className="p-header-wrap">
          <Link className="logo" to="/">
            {infos.logoHome && (
              <img
                src={`${API_URL}/${infos.logoHome.img_thumb_vn}`}
                alt={infos.logoHome.name_vn}
              />
            )}
          </Link>
          <div className="menu menu_mb" id="mobile-menu">
            <nav className="menutop">
              <ul>
                {menu.map((item) => (
                  <MenuItem key={item.id} item={item} />
                ))}
              </ul>
            </nav>
          </div>
          <div className="box-search">
            <form onSubmit={handleSearch} className="search-form">
              <input
                type="text"
                placeholder="Tìm sản phẩm..."
                value={keyword}
                onChange={(e) => setKeyword(e.target.value)}
              />

              <button type="submit" className="btn-search">
                <i className="fa-solid fa-magnifying-glass"></i>
              </button>
            </form>

            {/* Tìm kiếm nâng cao <SearchBox /> */}
          </div>
          <Link to="/wishlist" className="header-wishlist">
            ❤️
            {totalWishlist > 0 && (
              <span className="badge">{totalWishlist}</span>
            )}
          </Link>

          <Link to="/cart/" className="cart-icon">
            🛒
            {totalQty > 0 && <span className="badge">{totalQty}</span>}
          </Link>
        </div>
      </div>
    </header>
  );
}

export default Header;
