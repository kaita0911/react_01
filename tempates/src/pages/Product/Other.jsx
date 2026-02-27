import { Link } from "react-router-dom";
import { API_URL } from "@/config";

function Other({ items }) {
  if (!items || items.length === 0) return null;

  return (
    <div className="news-related">
      <h3>Sản phẩm liên quan</h3>

      <div className="p-products">
        {items.map((item) => (
          <div className="product-item" key={item.id}>
            <Link className="product-item__img" to={`/${item.unique_key}.html`}>
              <img src={`${API_URL}/${item.img_thumb_vn}`} alt={item.name} />
            </Link>

            <div className="product-item__meta">
              <h3>
                <Link
                  className="product-item__ttl"
                  to={`/${item.unique_key}.html`}
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
