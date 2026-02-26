import { useEffect, useState } from "react"
import { Link } from "react-router-dom"
import Seo from "@/components/Seo"
import { API_URL } from "@/config"
import Breadcrumb from "@/router/Breadcrumb"
import { getPages } from "@/utils/pagination"
import { useSearchParams } from "react-router-dom"
import "./News.scss"
function Cate({data}) {

  const [news, setNews] = useState([])
  const [pagination, setPagination] = useState(null)
  const [searchParams, setSearchParams] = useSearchParams()
  const page = Number(searchParams.get("page")) || 1
  const [categoryPath, setCategoryPath] = useState([])
  const [category, setCategory] = useState(null)
  useEffect(() => {
    if (!data?.id) return
    fetch(`${API_URL}/api/news.php?act=sub&cate_id=${data.id}&page=${page}`)
      .then(res => res.json())
      .then(data => {
        setNews(data.items || [])
        setPagination(data.pagination)
        setCategoryPath(data.category_path || [])
        setCategory(data.category || null)
      })
  }, [data?.id,page])


  return (
    <div className="container">
        <Breadcrumb comp="1" article={{...data,category_path: categoryPath}}/>
        <Seo
          title={category?.name}
          description={category?.des}
          keywords={category?.keyword}
          image={category?.img_vn && `${API_URL}/${category.img_vn}`}
        />
        <h1 className="ttl01">{category?.name}</h1>
            <div className="f-articles">
              {news.map(item => (
                  <div className="news-item" key={item.id}>
                    <Link className="news-item__img" to={`/${item.unique_key}.html`}>
                        {item.name}
                        <img src={`${API_URL}/${item.img_thumb_vn}`}
                        alt={item.name}
                        />
                    </Link>
                    <div className="news-item__meta">
                        <h3>
                        <Link className="news-item__ttl" to={`/${item.unique_key}.html`}>
                            {item.title}
                        </Link>
                        </h3>
                    
                        <p className="news-date">{item.dated}</p>
                        <div className="news-item__short">
                        {item.short_desc}
                        </div>

                    </div>
                  </div>
              ))}
            </div>
            {pagination && pagination.totalPages > 1 && (
                <div className="pagination">
                  {getPages(page, pagination.totalPages).map((p, i) =>
                    p === "..." ? (
                      <span key={i} className="dots">...</span>
                    ) : (
                      <button
                        key={i}
                        className={p === page ? "active" : ""}
                        onClick={() => {
                          setSearchParams({ page: p })
                          window.scrollTo({ top: 0, behavior: "smooth" })
                        }}
                      >
                        {p}
                      </button>
                    )
                  )}
                </div>
              )}
    </div>
  )
}

export default Cate
