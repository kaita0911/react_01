import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import Seo from "@/components/Seo";
import { API_URL } from "@/config";
import { Link } from "react-router-dom";
import { useCart } from "@/context/CartContext";
import { useWishlist } from "@/context/WishlistContext";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import "./Home.scss";
import useLang from "@/context/useLang";
import useLangPath from "@/utils/useLangPath";
import { useLanguage } from "@/context/useLanguage";
function Home() {
  const { lang: urlLang } = useParams(); // lang từ URL
  const { defaultLang } = useLanguage();
  const lang = urlLang || defaultLang;
  const getLangPath = useLangPath(); // gọi hook
  const { t } = useLang();
  const { addToCart } = useCart();
  const { wishlist, toggleWishlist } = useWishlist();
  const [quickItem, setQuickItem] = useState(null);
  const openQuickView = (item) => {
    setQuickItem(item);
  };

  const closeQuickView = () => {
    setQuickItem(null);
  };
  // ⭐ Lấy thông tin công ty
  const [info, setInfo] = useState(null);
  useEffect(() => {
    fetch(`${API_URL}/api/infos.php?lang=${lang}`)
      .then((res) => res.json())
      .then((data) => setInfo(data));
  }, [lang]);

  const [bannerTop, setBannerTop] = useState([]);
  const [bannerMid, setBannerMid] = useState([]);
  // 🔥 THÊM SETTINGS Ở ĐÂY
  const settings = {
    dots: true,
    arrows: false,
    infinite: true,
    speed: 500,
    autoplay: true,
    autoplaySpeed: 3000,
    slidesToShow: 1,
    slidesToScroll: 1,
  };
  //banner
  useEffect(() => {
    fetch(`${API_URL}/api/banner.php?comp=77`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          setBannerTop(data.data);
        }
      });
    fetch(`${API_URL}/api/banner.php?comp=74`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          setBannerMid(data.data);
        }
      });
  }, []);
  //product-hot
  const [products, setProducts] = useState([]);
  // ⭐ settings cho product hot
  const productSettings = {
    dots: false,
    arrows: true,
    infinite: true,
    speed: 500,
    autoplay: false,
    slidesToShow: 5, // hiển thị 4 sản phẩm
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: { slidesToShow: 3 },
      },
      {
        breakpoint: 768,
        settings: { slidesToShow: 2 },
      },
      {
        breakpoint: 480,
        settings: { slidesToShow: 2 },
      },
    ],
  };
  useEffect(() => {
    fetch(`${API_URL}/api/product_hot.php?lang=${lang}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          setProducts(data.data);
        }
      });
  }, [lang]);
  ///product-cate
  const [products_categories, setCategories] = useState([]);
  useEffect(() => {
    fetch(`${API_URL}/api/product_home_categories.php?lang=${lang}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success) setCategories(data.data);
      });
  }, [lang]);

  return (
    <>
      {info && (
        <Seo
          title={info.seo?.name_vn}
          description={info.seo?.desc}
          keywords={info.seo?.keyword}
        />
      )}
      <main>
        <div className="container">
          <div className="wrap-top">
            <Slider className="p-mv js-mv" {...settings}>
              {bannerTop.map((item) => (
                <div key={item.id} className="item">
                  <img
                    className="img-cover"
                    src={`${API_URL}/${item.img_thumb_vn}`}
                    alt={item.name}
                  />
                </div>
              ))}
            </Slider>

            <div className="col-bnr">
              {bannerMid.map((item) => (
                <div key={item.id} className="item">
                  <img
                    className="img-cover"
                    src={`${API_URL}/${item.img_thumb_vn}`}
                    alt={item.name}
                  />
                </div>
              ))}
            </div>
          </div>
          <div className="p-product">
            <div className="p-product-sale">
              <h2 className="ttl02 --sale">FLASH SALE - GIÁ SỐC MỖI NGÀY</h2>
              <Slider className="js-sale" {...productSettings}>
                {products.map((item) => {
                  const liked = wishlist.some((w) => w.id === item.id);

                  return (
                    <div key={item.id} className="product-item">
                      <Link
                        className="product-item__img"
                        to={getLangPath(item.slug, ".html")}
                        title={item.name}
                      >
                        <img
                          className="img-scale"
                          src={`${API_URL}/${item.img_thumb_vn}`}
                          alt={item.name}
                        />
                      </Link>

                      <div className="product-item__meta">
                        <h3>
                          <Link
                            className="product-item__ttl"
                            to={getLangPath(item.slug, ".html")}
                            title={item.name}
                          >
                            {item.name}
                          </Link>
                        </h3>

                        <div className="product-price">
                          <span className="price-current">
                            {Number(item.price) > 0
                              ? Number(item.price).toLocaleString("vi-VN") +
                                " đ"
                              : t.contact}
                          </span>

                          {Number(item.priceold) > 0 && (
                            <span className="price-old">
                              {Number(item.priceold).toLocaleString("vi-VN")} đ
                            </span>
                          )}
                        </div>

                        <div className="product-actions">
                          <div className="action-item">
                            <i
                              onClick={() =>
                                addToCart({
                                  id: item.id,
                                  title: item.name,
                                  slug: item.slug,
                                  price: Number(item.price),
                                  image: item.img_thumb_vn,
                                  qty: 1,
                                })
                              }
                              className="fa-solid fa-cart-shopping cart-icon"
                            ></i>
                            <span className="tooltip">{t.addtocart}</span>
                          </div>
                          <div className="action-item">
                            <i
                              onClick={() => openQuickView(item)}
                              className="fa-regular fa-eye view-icon"
                            ></i>
                            <span className="tooltip">{t.quickview}</span>
                          </div>
                          <div className="action-item">
                            <i
                              onClick={() => toggleWishlist(item)}
                              className={
                                liked
                                  ? "fa-solid fa-heart heart active"
                                  : "fa-regular fa-heart heart"
                              }
                            ></i>
                            <span className="tooltip">
                              {liked ? t.dislike : t.like}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </Slider>
            </div>
            {products_categories.map((cat) => (
              <div key={cat.id} className="home-category">
                <div className="home-category__head">
                  <h2 className="ttl02">{cat.name}</h2>

                  {cat.sub_categories?.length > 0 && (
                    <div className="cate-sub">
                      <ul>
                        {cat.sub_categories.map((sub) => (
                          <li key={sub.id}>
                            <Link to={`/${sub.slug}.html`}>{sub.name}</Link>
                          </li>
                        ))}
                      </ul>
                    </div>
                  )}
                </div>

                {/* Slider sản phẩm theo category */}
                <div className="p-product__wrap p-products">
                  {cat.products?.map((item) => {
                    const liked = wishlist.some((w) => w.id === item.id);
                    return (
                      <div key={item.id} className="product-item">
                        <Link
                          className="product-item__img"
                          to={getLangPath(item.slug, ".html")}
                          title={item.name}
                        >
                          <img
                            className="img-scale"
                            src={`${API_URL}/${item.img_thumb_vn}`}
                            alt={item.name}
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
                                ? Number(item.price).toLocaleString("vi-VN") +
                                  " đ"
                                : t.contact}
                            </span>

                            {Number(item.priceold) > 0 && (
                              <span className="price-old">
                                {Number(item.priceold).toLocaleString("vi-VN")}đ
                              </span>
                            )}
                          </div>
                          <div className="product-actions">
                            <div className="action-item">
                              <i
                                onClick={() =>
                                  addToCart({
                                    id: item.id,
                                    title: item.name,
                                    slug: item.slug,
                                    price: Number(item.price),
                                    image: item.img_thumb_vn,
                                    qty: 1,
                                  })
                                }
                                className="fa-solid fa-cart-shopping cart-icon"
                              ></i>
                              <span className="tooltip">{t.addtocart}</span>
                            </div>
                            <div className="action-item">
                              <i
                                onClick={() => openQuickView(item)}
                                className="fa-regular fa-eye view-icon"
                              ></i>
                              <span className="tooltip">{t.quickview}</span>
                            </div>
                            <div className="action-item">
                              <i
                                onClick={() => toggleWishlist(item)}
                                className={
                                  liked
                                    ? "fa-solid fa-heart heart active"
                                    : "fa-regular fa-heart heart"
                                }
                              ></i>
                              <span className="tooltip">
                                {liked ? t.dislike : t.like}
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>
            ))}
          </div>
        </div>
        {quickItem && (
          <div className="quickview-overlay" onClick={closeQuickView}>
            <div
              className="quickview-modal"
              onClick={(e) => e.stopPropagation()}
            >
              <button className="btn-close" onClick={closeQuickView}>
                ✕
              </button>
              <div className="quickview-modal-wrap">
                <div className="quickview-modal__img">
                  <img
                    className="img-scale"
                    src={`${API_URL}/${quickItem.img_thumb_vn}`}
                    alt={quickItem.name}
                  />
                </div>
                <div className="quickview-modal__meta">
                  <h3 className="quickview-modal__meta__ttl">
                    {quickItem.name}
                  </h3>
                  <div className="product-price">
                    <span className="price-current">
                      {Number(quickItem.price) > 0
                        ? Number(quickItem.price).toLocaleString("vi-VN") + " đ"
                        : t.contact}
                    </span>

                    {Number(quickItem.priceold) > 0 && (
                      <span className="price-old">
                        {Number(quickItem.priceold).toLocaleString("vi-VN")} đ
                      </span>
                    )}
                  </div>
                  <button
                    onClick={() => {
                      addToCart({
                        id: quickItem.id,
                        title: quickItem.name,
                        slug: quickItem.slug,
                        price: Number(quickItem.price),
                        image: quickItem.img_thumb_vn,
                        qty: 1,
                      });
                      closeQuickView();
                    }}
                  >
                    Thêm vào giỏ hàng
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}
      </main>
    </>
  );
}

export default Home;
