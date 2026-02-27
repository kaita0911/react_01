import { Link } from "react-router-dom";
import { useWishlist } from "@/context/WishlistContext";
import { API_URL } from "@/config";
import Seo from "@/components/Seo";
// import "./Wishlist.scss";

function Wishlist() {
  const { wishlist, toggleWishlist } = useWishlist();

  return (
    <>
      <Seo title="Sản phẩm yêu thích" />
      <main>
        <div className="container">
          <h1 className="ttl01">Sản phẩm yêu thích</h1>

          {wishlist.length === 0 ? (
            <p>Chưa có sản phẩm nào trong danh sách ❤️</p>
          ) : (
            <div className="p-products">
              {wishlist.map((item) => (
                <div key={item.id} className="product-item">
                  <Link className="product-item__img" to={`/${item.slug}.html`}>
                    <img
                      src={`${API_URL}/${item.img_thumb_vn}`}
                      alt={item.name}
                      className="img-scale"
                    />
                  </Link>

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

                  <button
                    className="remove"
                    onClick={() => toggleWishlist(item)}
                  >
                    ❌ Bỏ yêu thích
                  </button>
                </div>
              ))}
            </div>
          )}
        </div>
      </main>
    </>
  );
}

export default Wishlist;
