import { useEffect, useState } from "react";
import { Routes, Route, Navigate } from "react-router-dom";
import { LanguageProvider } from "@/context/LanguageProvider";
import Resolver from "./router/Resolver";
import HtmlRouter from "./router/HtmlRouter";

import Cart from "./pages/Cart/Cart";
import { CartProvider } from "@/context/CartProvider";
import CartToast from "@/context/CartToast";
import Checkout from "./pages/Cart/Checkout";
import Tag from "./pages/Tag";
import Search from "./pages/Search";
import Header from "./components/Header/Header";
import Footer from "./components/Footer/Footer";
import Home from "./pages/Home/Home";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import { WishlistProvider } from "@/context/WishlistContext";
import Wishlist from "./pages/Wishlist";
import { API_URL } from "@/config";

function App() {
  const [loading, setLoading] = useState(true);
  const [defaultLang, setDefaultLang] = useState(null);
  const [languages, setLanguages] = useState([]);
  const [singleLang, setSingleLang] = useState(false);

  useEffect(() => {
    const fetchLanguages = async () => {
      try {
        const res = await fetch(`${API_URL}/api/languages.php`);
        const data = await res.json();
        //console.log(data);
        const langs = data.languages || [];
        setLanguages(langs);

        // Lấy code ngôn ngữ mặc định do backend quyết định
        const defaultCode =
          data.default_language?.code || langs[0]?.code || "vi";
        setDefaultLang(defaultCode);

        // Nếu chỉ có 1 ngôn ngữ, đánh dấu singleLang
        setSingleLang(langs.length === 1);
      } catch (err) {
        console.error("Lỗi fetch languages:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchLanguages();
  }, []);

  if (loading || !defaultLang) return <div>Đang tải...</div>;

  return (
    <LanguageProvider
      languages={languages}
      singleLang={singleLang}
      defaultLang={defaultLang}
    >
      <WishlistProvider>
        <CartProvider>
          <Routes>
            {singleLang ? (
              // CHỈ 1 ngôn ngữ → không dùng /:lang
              <Route
                path="/*"
                element={
                  <>
                    <Header />
                    <Routes>
                      <Route index element={<Home />} />
                      <Route path="tag/:slug" element={<Tag />} />
                      <Route path="cart" element={<Cart />} />
                      <Route path="thanh-toan" element={<Checkout />} />
                      <Route path="search" element={<Search />} />
                      <Route path="wishlist" element={<Wishlist />} />
                      <Route path=":slug.html" element={<HtmlRouter />} />
                      <Route path=":slug" element={<Resolver />} />
                    </Routes>
                    <Footer />
                  </>
                }
              />
            ) : (
              // NHIỀU ngôn ngữ → dùng /:lang
              <>
                {/* redirect root "/" sang defaultLang */}
                <Route
                  path="/"
                  element={<Navigate to={`/${defaultLang}`} replace />}
                />
                <Route
                  path="/:lang/*"
                  element={
                    <>
                      <Header />
                      <Routes>
                        <Route index element={<Home />} />
                        <Route path="tag/:slug" element={<Tag />} />
                        <Route path="cart" element={<Cart />} />
                        <Route path="thanh-toan" element={<Checkout />} />
                        <Route path="search" element={<Search />} />
                        <Route path="wishlist" element={<Wishlist />} />
                        <Route path=":slug.html" element={<HtmlRouter />} />
                        <Route path=":slug" element={<Resolver />} />
                      </Routes>
                      <Footer />
                    </>
                  }
                />
              </>
            )}
          </Routes>

          <CartToast />
        </CartProvider>
      </WishlistProvider>
    </LanguageProvider>
  );
}

export default App;
