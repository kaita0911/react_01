import { Link } from "react-router-dom";
import { API_URL } from "@/config";

function ProductItem({ item }) {
  return (
    <div className="product-item">
      <Link className="product-item__img" to={`/${item.slug}.html`}>
        <img
          src={`${API_URL}/${item.img_thumb_vn}`}
          alt={item.name}
          className="img-scale"
        />
      </Link>

      <div className="product-item__meta">
        <h3>
          <Link className="product-item__ttl" to={`/${item.slug}.html`}>
            {item.name}
          </Link>
        </h3>

        <div className="product-price">
          <span className="price-current">
            {Number(item.price) > 0
              ? Number(item.price).toLocaleString("vi-VN") + " đ"
              : "Liên hệ"}
          </span>

          {Number(item.priceold) > 0 && (
            <span className="price-old">
              {Number(item.priceold).toLocaleString("vi-VN")} đ
            </span>
          )}
        </div>
      </div>
    </div>
  );
}

export default ProductItem;
