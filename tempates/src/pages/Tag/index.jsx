import { useParams, Link } from "react-router-dom";
import { useEffect, useState } from "react";
import { API_URL } from "@/config";
import { useLanguage } from "@/context/useLanguage";
import useLangPath from "@/utils/useLangPath";
import useLang from "@/context/useLang";
function Tag() {
  const { lang: urlLang } = useParams(); // lang từ URL
  const { defaultLang } = useLanguage();
  const lang = urlLang || defaultLang;
  const getLangPath = useLangPath();
  const { t } = useLang();
  const { slug } = useParams();
  const [items, setItems] = useState([]);

  useEffect(() => {
    fetch(`${API_URL}/api/tag.php?slug=${slug}&lang=${lang}`)
      .then((res) => res.json())
      .then((data) => setItems(data.articles || []));
  }, [slug, lang]);

  return (
    <>
      <main>
        <div className="container">
          <div className="p-tags">
            <h1 className="ttl01">Tag: {slug}</h1>

            {items.length === 0 && <p>{t.no_post}</p>}
            <div className="p-products --mrg-top">
              {items.map((item) => (
                <div className="product-item">
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
        </div>
      </main>
    </>
  );
}

export default Tag;
