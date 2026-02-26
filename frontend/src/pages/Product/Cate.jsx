import { useEffect, useState } from "react";
import { API_URL } from "@/config";
import Seo from "@/components/Seo";
import Breadcrumb from "@/router/Breadcrumb";
import { getPages } from "@/utils/pagination";
import { useSearchParams } from "react-router-dom";
import ProductItem from "./ProductItem";
import "./Product.scss";
function Cate({ data }) {
  const [news, setNews] = useState([]);
  const [pagination, setPagination] = useState(null);
  const [searchParams, setSearchParams] = useSearchParams();
  const page = Number(searchParams.get("page")) || 1;
  const [categoryPath, setCategoryPath] = useState([]);
  const [category, setCategory] = useState(null);
  useEffect(() => {
    if (!data?.id) return;
    fetch(`${API_URL}/api/products.php?act=sub&cate_id=${data.id}&page=${page}`)
      .then((res) => res.json())
      .then((data) => {
        setNews(data.items || []);
        setPagination(data.pagination);
        setCategoryPath(data.category_path || []);
        setCategory(data.category || null);
      });
  }, [data?.id, page]);

  return (
    <div className="container">
      <Breadcrumb comp="2" article={{ ...data, category_path: categoryPath }} />
      <Seo
        title={category?.name}
        description={category?.des}
        keywords={category?.keyword}
        image={category?.img_vn && `${API_URL}/${category.img_vn}`}
      />
      <h1 className="ttl01">{category?.name}</h1>
      <div className="p-products --mrg-top">
        {news.map((item) => (
          <ProductItem key={item.id} item={item} />
        ))}
      </div>
      {pagination && pagination.totalPages > 1 && (
        <div className="pagination">
          {getPages(page, pagination.totalPages).map((p, i) =>
            p === "..." ? (
              <span key={i} className="dots">
                ...
              </span>
            ) : (
              <button
                key={i}
                className={p === page ? "active" : ""}
                onClick={() => {
                  setSearchParams({ page: p });
                  window.scrollTo({ top: 0, behavior: "smooth" });
                }}
              >
                {p}
              </button>
            )
          )}
        </div>
      )}
    </div>
  );
}

export default Cate;
