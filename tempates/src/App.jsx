// import { useState } from 'react'
// import reactLogo from './assets/react.svg'
// import viteLogo from '/vite.svg'
// import './App.css'

import Resolver from "./router/Resolver";
import HtmlRouter from "./router/HtmlRouter";
import { Routes, Route } from "react-router-dom";
import Cart from "./pages/Cart/Cart";
import { CartProvider } from "@/context/CartContext";
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

function App() {
  return (
    <>
      <WishlistProvider>
        <CartProvider>
          <Header />
          <Routes>
            <Route path="/" element={<Home />} />
            {/* ✅ Route cụ thể trước */}
            <Route path="/tag/:slug" element={<Tag />} />
            <Route path="/cart/" element={<Cart />} />
            <Route path="/thanh-toan/" element={<Checkout />} />
            <Route path="/search" element={<Search />} />
            <Route path="/wishlist" element={<Wishlist />} />;
            {/* ⚠️ Route tổng quát để cuối */}
            <Route path="/:slug.html" element={<HtmlRouter />} />
            <Route path="/:slug" element={<Resolver />} />
          </Routes>
          <Footer />
          <CartToast />
        </CartProvider>
      </WishlistProvider>
    </>
  );
}

export default App;
