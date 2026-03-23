import { useCart } from "@/context/CartContext";
import { Link } from "react-router-dom";
import { API_URL } from "@/config";
import { i18n } from "@/utils/i18n";
// import useLang from "@/context/useLang";
//import { useLanguage } from "@/context/useLanguage";
import { useLanguage } from "@/context/useLanguage";
function CartToast() {
  //const { lang: urlLang } = useParams(); // lang từ URL
  //const { defaultLang } = useLanguage();
  //const lang = urlLang || defaultLang;
  const { singleLang } = useLanguage();
  const { toast, setToast } = useCart(); // ⭐ thêm setToast
  // const { t } = useLang();
  const lang = toast?.lang;
  const tLang = i18n[lang] || i18n.vi;
  //const tLang = i18n[lang] || i18n.vi;
  if (!toast) return null;
  console.log(lang);
  return (
    <div className="cart-toast">
      <div className="cart-toast-ttl">
        {tLang.addedcart}
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
                : tLang.contact}
            </span>
          </div>
        </div>
      </div>
      <Link
        className="btn-view-cart"
        to={singleLang ? "/cart" : `/${lang}/cart`}
      >
        {tLang.viewcart}
      </Link>
    </div>
  );
}

export default CartToast;
