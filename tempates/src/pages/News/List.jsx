import { useEffect, useState } from "react"
import { Link } from "react-router-dom"
import Seo from "@/components/Seo"
import { API_URL } from "@/config"
import Breadcrumb from "@/router/Breadcrumb"
import { getPages } from "@/utils/pagination"
import { useSearchParams } from "react-router-dom"
import "./News.scss"
function List() {

  const [news, setNews] = useState([])
  const [title, setTitle] = useState("") 
  const [pagination, setPagination] = useState(null)
  const [searchParams, setSearchParams] = useSearchParams()
  const page = Number(searchParams.get("page")) || 1
  useEffect(() => {
    fetch(`${API_URL}/api/news.php?act=list&page=${page}`)
      .then(res => res.json())
      .then(data => {
        setNews(data.items || [])
        setPagination(data.pagination)
      })
  }, [page])
 // ===== LẤY MENU → TITLE =====
  useEffect(() => {
    fetch(`${API_URL}/api/menu.php`)
      .then(res => res.json())
      .then(menu => {
        const item = menu.find(m => String(m.comp) === "1")
        if (item) setTitle(item.name_detail)
      })
  }, [])
  // ⭐ Lấy thông tin công ty
  const [info, setInfo] = useState(null)
    useEffect(() => {
      fetch(`${API_URL}/api/infos.php`)
        .then(res => res.json())
        .then(data => setInfo(data))
        
    }, [])
  return (
    <div className="container">
        <Breadcrumb comp="1" />
        {info && title && (
          <Seo
            title={title}
            description={info.seo?.desc}
            keywords={info.seo?.keyword}
          />
        )}
        <h1 className="ttl01">{title}</h1>
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
                  {item.short}
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

export default List
