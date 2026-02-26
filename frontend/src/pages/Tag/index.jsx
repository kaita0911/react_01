import { useParams } from "react-router-dom";
import { useEffect, useState } from "react";
import { API_URL } from "@/config";
import { Link } from "react-router-dom";
function Tag() {
  const { slug } = useParams();
  const [items, setItems] = useState([]);

  useEffect(() => {
    fetch(`${API_URL}/api/tag.php?slug=${slug}`)
      .then((res) => res.json())
      .then((data) => setItems(data.articles || []));
  }, [slug]);

  return (
    <>
      <main>
        <div className="container">
          <div className="p-tags">
            <h1 className="ttl01">Tag: {slug}</h1>

            {items.length === 0 && <p>Không có bài viết</p>}
            <div className="p-products --mrg-top">
              {items.map((item) => (
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
                      <Link
                        className="product-item__ttl"
                        to={`/${item.slug}.html`}
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

                      {Number(item.priceold) > 0 && (
                        <span className="price-old">
                          {Number(item.priceold).toLocaleString("vi-VN")} đ
                        </span>
                      )}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </main>
    </>
  );
}

export default Tag;
