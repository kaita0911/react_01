import { useCart } from "@/context/CartContext";
import { useState } from "react";
import Seo from "@/components/Seo";
import { API_URL } from "@/config";
import { Link } from "react-router-dom";

import "./Cart.scss";
function Cart() {
  const { cart, updateQty, removeItem, clearCart, totalPrice, totalQty } =
    useCart();

  if (cart.length === 0) return <h2>Giỏ hàng trống</h2>;
  const [confirmId, setConfirmId] = useState(null);
  return (
    <>
      <Seo title="Giỏ hàng" />
      <main>
        <div className="container">
          <h1 className="ttl-cart">Giỏ hàng</h1>
          <div className="cart-box">
            <div className="cart-box-left cart-bd">
              {cart.map((item) => (
                <div key={item.id} className="cart-item">
                  <div className="cart-item__img">
                    <img
                      className="img-scale"
                      src={`${API_URL}/${item.image}`}
                      alt={item.title}
                    />
                  </div>

                  <div className="cart-item__meta">
                    <div className="cart-item__meta__head">
                      <h3>
                        <Link
                          className="product-item__ttl"
                          to={`/${item.slug}.html`}
                          alt={item.title}
                        >
                          {item.title}
                        </Link>
                      </h3>
                      <button
                        className="cart-item__del"
                        onClick={() => setConfirmId(item.id)}
                      >
                        <i className="fa-solid fa-trash-can"></i>
                      </button>
                    </div>
                    <div className="cart-item__more">
                      <div className="c-quality --view">
                        <button
                          className="btn-qty decrease"
                          onClick={() => updateQty(item.id, item.qty - 1)}
                        >
                          −
                        </button>

                        <span className="input-qty">{item.qty}</span>

                        <button
                          className="btn-qty increase"
                          onClick={() => updateQty(item.id, item.qty + 1)}
                        >
                          +
                        </button>
                      </div>
                      <div className="cart-item__price">
                        <span className="price-current">
                          {item.price.toLocaleString("vi-VN")} đ
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
            <div className="cart-box-right cart-bd">
              <div className="cart-box__ttl">Chi tiết đơn hàng</div>
              <div className="cart-summary">
                <div className="cart-summary__row">
                  <label>Tổng tiền</label>
                  <span className="cart-summary-total">
                    {totalPrice.toLocaleString("vi-VN")} đ
                  </span>
                </div>
                {/* <div className="cart-summary__row">
                  <label>Giảm giá</label>
                  <span className="cart-summary-sale"> 0₫</span>
                </div> */}
                <div className="cart-summary__row">
                  <label>Số lượng</label>
                  <span className="cart-summary-quality">{totalQty}</span>
                </div>
              </div>
              <div className="cart-pay">
                <label>Thành tiền</label>
                <strong className="cart-pay-total">
                  {totalPrice.toLocaleString("vi-VN")} đ
                </strong>
              </div>
              <Link to="/thanh-toan/" className="cart-btn">
                Thanh toán <i className="fa-solid fa-arrow-right"></i>
              </Link>
            </div>
          </div>
        </div>
        {confirmId && (
          <div className="popup-overlay" onClick={() => setConfirmId(null)}>
            <div className="popup" onClick={(e) => e.stopPropagation()}>
              <div className="popup-head">
                <span className="popup-head__ttl">Xoá sản phẩm</span>
                <button
                  className="popup-close"
                  onClick={() => setConfirmId(null)}
                >
                  ×
                </button>
              </div>
              <div className="popup-content">
                <div className="icon-trash">
                  <i className="fa-solid fa-trash-can"></i>
                </div>
                <h3>Bạn có chắc muốn xoá sản phẩm này không?</h3>
              </div>

              <div className="popup-actions">
                <button
                  className="btn-cancel"
                  onClick={() => setConfirmId(null)}
                >
                  Không
                </button>

                <button
                  className="btn-delete"
                  onClick={() => {
                    removeItem(confirmId);
                    setConfirmId(null);
                  }}
                >
                  Xoá
                </button>
              </div>
            </div>
          </div>
        )}
      </main>
    </>
  );
}

export default Cart;
