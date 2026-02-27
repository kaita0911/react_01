import { useSearchParams } from "react-router-dom";
import { useEffect, useState } from "react";
import { API_URL } from "@/config";
import { Link } from "react-router-dom";
import Seo from "@/components/Seo";
function Search() {
  const [searchParams] = useSearchParams();
  const keyword = searchParams.get("q");

  const [items, setItems] = useState([]);

  useEffect(() => {
    if (!keyword) return;

    fetch(`${API_URL}/api/search.php?q=${encodeURIComponent(keyword)}`)
      .then((res) => res.json())
      .then((data) => setItems(data.items || []));
  }, [keyword]);

  return (
    <>
      <Seo title="Tìm kiếm" />
      <main>
        <div className="container">
          <h1 className="ttl01">Kết quả tìm kiếm: {keyword}</h1>

          {items.length === 0 && <p>Không tìm thấy sản phẩm</p>}

          <div className="p-products">
            {items.map((item) => (
              <div key={item.id} className="product-item">
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
      </main>
    </>
  );
}

export default Search;
