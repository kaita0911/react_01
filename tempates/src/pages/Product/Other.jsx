import { Link } from "react-router-dom";
import { API_URL } from "@/config";
import useLang from "@/context/useLang";
import useLangPath from "@/utils/useLangPath";
function Other({ items }) {
  const getLangPath = useLangPath(); // gọi hook
  const { t } = useLang();
  if (!items || items.length === 0) return null;

  return (
    <div className="news-related">
      <h3>{t.product_related}</h3>

      <div className="p-products">
        {items.map((item) => (
          <div className="product-item" key={item.id}>
            <Link
              title={item.name}
              className="product-item__img"
              to={getLangPath(item.slug, ".html")}
            >
              <img src={`${API_URL}/${item.img_thumb_vn}`} alt={item.name} />
            </Link>

            <div className="product-item__meta">
              <h3>
                <Link
                  title={item.name}
                  className="product-item__ttl"
                  to={getLangPath(item.slug, ".html")}
                >
                  {item.name}
                </Link>
              </h3>

              <div className="product-price">
                <span className="price-current">
                  {Number(item.price) > 0
                    ? Number(item.price).toLocaleString("vi-VN") + " đ"
                    : "Liên hệ"}
                </span>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

export default Other;
