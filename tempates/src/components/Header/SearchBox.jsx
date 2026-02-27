import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { API_URL } from "@/config";

function SearchBox() {
  const [keyword, setKeyword] = useState("");
  const [suggest, setSuggest] = useState([]);
  const navigate = useNavigate();

  useEffect(() => {
    if (keyword.trim().length < 2) {
      setSuggest([]);
      return;
    }

    const timeout = setTimeout(() => {
      fetch(`${API_URL}/api/search-suggest.php?q=${keyword}`)
        .then((res) => res.json())
        .then((data) => setSuggest(data));
    }, 300); // debounce 300ms

    return () => clearTimeout(timeout);
  }, [keyword]);

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!keyword.trim()) return;
    navigate(`/search?q=${keyword}`);
    setSuggest([]);
  };

  return (
    <div className="search-box">
      <form onSubmit={handleSubmit}>
        <input
          type="text"
          value={keyword}
          onChange={(e) => setKeyword(e.target.value)}
          placeholder="Tìm sản phẩm..."
        />
        <i className="fa-solid fa-magnifying-glass"></i>
      </form>

      {suggest.length > 0 && (
        <div className="search-suggest">
          {suggest.map((item, index) => (
            <div
              key={index}
              onClick={() => {
                navigate(`${item.slug}.html`);
                setSuggest([]);
              }}
              className="suggest-item"
            >
              <div className="suggest-item__img">
                <img
                  className="img-scale"
                  src={`${API_URL}/${item.img_thumb_vn}`}
                  alt={item.name}
                />
              </div>
              <div className="suggest-item__meta">
                <div className="suggest-item__meta__name">{item.name}</div>
                <div className="suggest-item__meta__price">
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
      )}
    </div>
  );
}

export default SearchBox;
