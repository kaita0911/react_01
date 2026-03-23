import { useSearchParams, useParams, Link } from "react-router-dom";
import { useEffect, useState } from "react";
import { API_URL } from "@/config";
import Seo from "@/components/Seo";
import { useLanguage } from "@/context/useLanguage";
import useLangPath from "@/utils/useLangPath";
import useLang from "@/context/useLang";
function Search() {
  const { lang: urlLang } = useParams(); // lang từ URL
  const { defaultLang } = useLanguage();
  const lang = urlLang || defaultLang;
  const getLangPath = useLangPath();
  const [searchParams] = useSearchParams();
  const keyword = searchParams.get("q");
  //console.log(lang);
  const [items, setItems] = useState([]);
  const { t } = useLang();
  useEffect(() => {
    if (!keyword) return;

    fetch(
      `${API_URL}/api/search.php?q=${encodeURIComponent(keyword)}&lang=${lang}`
    )
      .then((res) => res.json())
      .then((data) => setItems(data.items || []));
  }, [keyword, lang]);

  return (
    <>
      <Seo title="Tìm kiếm" />
      <main>
        <div className="container">
          <h1 className="ttl01">
            {t.search_kq}: {keyword}
          </h1>

          {items.length === 0 && <p>Không tìm thấy sản phẩm</p>}

          <div className="p-products">
            {items.map((item) => (
              <div key={item.id} className="product-item">
                <Link
                  className="product-item__img"
                  to={getLangPath(item.slug, ".html")}
                >
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
