import { createContext, useContext, useEffect, useState } from "react";

const CartContext = createContext();

export const useCart = () => useContext(CartContext);

export const CartProvider = ({ children }) => {
  const [toast, setToast] = useState(null);
  const [cart, setCart] = useState(() => {
    const saved = localStorage.getItem("cart");
    return saved ? JSON.parse(saved) : [];
  });

  useEffect(() => {
    localStorage.setItem("cart", JSON.stringify(cart));
  }, [cart]);

  // Thêm sản phẩm
  const addToCart = (product) => {
    setCart((prev) => {
      const exist = prev.find((p) => p.id === product.id);
      const qtyToAdd = product.qty || 1;
      if (exist) {
        return prev.map((p) =>
          p.id === product.id ? { ...p, qty: p.qty + qtyToAdd } : p
        );
      }

      return [...prev, { ...product, qty: qtyToAdd }];
    });
    // ⭐ bật toast
    setToast({
      title: product.title,
      price: product.price,
      image: product.image,
      qty: product.qty || 1,
    });

    // ⭐ tự tắt sau 3 giây
    setTimeout(() => {
      setToast(null);
    }, 3000);
  };

  // Mua ngay
  const buyNow = (product) => {
    addToCart(product);
    window.location.href = "/cart/";
  };

  // Tăng giảm số lượng
  const updateQty = (id, qty) => {
    if (qty < 1) return;
    setCart((prev) => prev.map((p) => (p.id === id ? { ...p, qty } : p)));
  };

  // Xóa sản phẩm
  const removeItem = (id) => {
    setCart((prev) => prev.filter((p) => p.id !== id));
  };

  const clearCart = () => setCart([]);

  const totalPrice = cart.reduce((sum, p) => sum + p.price * p.qty, 0);
  const totalQty = cart.reduce((sum, p) => sum + p.qty, 0);

  return (
    <CartContext.Provider
      value={{
        cart,
        addToCart,
        toast,
        setToast,
        buyNow,
        updateQty,
        removeItem,
        clearCart,
        totalPrice,
        totalQty,
      }}
    >
      {children}
    </CartContext.Provider>
  );
};
