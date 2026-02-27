import { useCart } from "@/context/CartContext";
import { API_URL } from "@/config";
import { Link } from "react-router-dom";

function CartToast() {
  const { toast, setToast } = useCart(); // ⭐ thêm setToast

  if (!toast) return null;

  return (
    <div className="cart-toast">
      <div className="cart-toast-ttl">
        Đã thêm vào giỏ hàng
        <button className="cart-toast-close" onClick={() => setToast(null)}>
          ×
        </button>
      </div>
      <div className="cart-toast-item">
        <div className="cart-toast-item__img">
          <img
            className="img-scale"
            src={`${API_URL}/${toast.image}`}
            alt={toast.title}
          />
        </div>
        <div className="cart-toast-item__info">
          <h3 className="cart-toast-item__info__ttl">{toast.title}</h3>
          <div className="cart-toast-item__info__qty">X {toast.qty}</div>
          <div className="cart-toast-item__info__price">
            <span className="price-current">
              {Number(toast.price) > 0
                ? Number(toast.price).toLocaleString("vi-VN") + " đ"
                : "Liên hệ"}
            </span>
          </div>
        </div>
      </div>
      <Link className="btn-view-cart" to="/cart/">
        Xem giỏ hàng
      </Link>
    </div>
  );
}

export default CartToast;
