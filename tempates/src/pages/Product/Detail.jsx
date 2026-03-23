import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { useParams } from "react-router-dom";
import { API_URL } from "@/config";
import Seo from "@/components/Seo";
import Breadcrumb from "@/router/Breadcrumb";
import Other from "./Other";
import ProductGallery from "./ProductGallery";
import { useCart } from "@/context/CartContext";
import useLang from "@/context/useLang";
import useScrollToTop from "@/utils/useScrollToTop";
import { useLanguage } from "@/context/useLanguage";
import useLangPath from "@/utils/useLangPath";
function Detail() {
  const { slug, lang: urlLang } = useParams(); // lang từ URL
  const { defaultLang } = useLanguage();
  const lang = urlLang || defaultLang;
  const [news, setNews] = useState(null);
  const getLangPath = useLangPath(); // gọi hook
  const { t } = useLang();
  const { addToCart, buyNow } = useCart();
  const [qty, setQuantity] = useState(1);
  useScrollToTop(slug);
  useEffect(() => {
    fetch(`${API_URL}/api/products.php?act=detail&slug=${slug}&lang=${lang}`)
      .then((res) => res.json())
      .then((data) => {
        setNews(data);
      });
  }, [slug, lang]);

  if (!news) return;

  return (
    <main>
      <div className="container">
        <Breadcrumb comp="2" article={news} />
        <Seo
          title={news.name}
          description={news?.des}
          keywords={news?.keyword}
          image={news?.thumb && `${API_URL}/${news.thumb}`}
        />
        <div className="artseed-body">
          <div className="product-detail">
            <div className="product-detail__left">
              {news.images?.length > 0 ? (
                <ProductGallery
                  images={news.images.map((img) => `${API_URL}/${img.img_vn}`)}
                />
              ) : (
                <div className="main-img">
                  <img
                    src={`${API_URL}/${news.thumb}`}
                    alt={news.name}
                    className="img-scale"
                  />
                </div>
              )}
            </div>
            <div className="product-detail__right">
              <h1 className="ttl01">{news.name}</h1>
              <div className="product-price --detail">
                <span className="price-current">
                  {Number(news.price) > 0
                    ? Number(news.price).toLocaleString("vi-VN") + " đ"
                    : "Liên hệ"}
                </span>

                {Number(news.priceold) > 0 && (
                  <span className="price-old">
                    {Number(news.priceold).toLocaleString("vi-VN") + " đ"}
                  </span>
                )}
              </div>
              <div className="c-quality">
                <button
                  className="c-quality-btn minus"
                  onClick={() =>
                    setQuantity((prev) => (prev > 1 ? prev - 1 : 1))
                  }
                >
                  -
                </button>

                <input
                  type="number"
                  min="1"
                  value={qty}
                  onChange={(e) =>
                    setQuantity(Math.max(1, Number(e.target.value)))
                  }
                />

                <button
                  className="c-quality-btn plus"
                  onClick={() => setQuantity((prev) => prev + 1)}
                >
                  +
                </button>
              </div>

              <div className="product-detail__buttons">
                <button
                  className="btn-cart btn-add"
                  onClick={() =>
                    addToCart({
                      id: news.id,
                      title: news.name,
                      slug: news.slug,
                      price: Number(news.price),
                      image: news.thumb,
                      qty: qty,
                      lang: lang, // ✅ thêm dòng này
                    })
                  }
                >
                  🛒 {t.addtocart}
                </button>

                <button
                  className="btn-cart btn-buy"
                  onClick={() =>
                    buyNow({
                      id: news.id,
                      title: news.name,
                      slug: news.slug,
                      price: Number(news.price),
                      image: news.thumb,
                      qty: qty,
                    })
                  }
                >
                  ⚡ {t.buynow}
                </button>
              </div>
              <div
                className="product-detail-short"
                dangerouslySetInnerHTML={{ __html: news.short }}
              />
              <div className="tags">
                {news.tags?.map((tag) => (
                  <Link
                    key={tag.slug}
                    to={getLangPath(`tag/${tag.slug}`)}
                    className="tag-link"
                  >
                    {tag.name}
                  </Link>
                ))}
              </div>
            </div>
          </div>
          <div className="artseed-detail__ttl">{t.product_detail}</div>
          <div
            className="artseed-detail"
            dangerouslySetInnerHTML={{ __html: news.content }}
          />
        </div>
        <Other items={news.related} />
      </div>
    </main>
  );
}

export default Detail;
