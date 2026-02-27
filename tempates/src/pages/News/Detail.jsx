import { useEffect, useState } from "react"
import { useParams } from "react-router-dom"
import Seo from "@/components/Seo"
import { API_URL } from "@/config"
import { Link } from "react-router-dom"
import Breadcrumb from "@/router/Breadcrumb"
function NewsDetail() {

  const { slug } = useParams()
  const [news, setNews] = useState(null)

  useEffect(() => {
    fetch(`${API_URL}/api/news.php?act=detail&slug=${slug}`)
      .then(res => res.json())
      .then(setNews)
  }, [slug])
  if (!news) return

  return (
    <main>
        <div className="container">
           <Breadcrumb comp="1" article={news}/>
           <Seo
              title={news.title}
              description={news?.des}
              keywords={news?.keyword}
              image={news?.img_thumb_vn && `${API_URL}/${news.img_thumb_vn}`}
            />
            <div className="artseed-body">
                <h1>{news.title}</h1>
                <div className="artseed-detail" dangerouslySetInnerHTML={{ __html: news.content }}/>
            </div>
           
            {news.related?.length > 0 && (
            <div className="related-articles">
                <h2 className="ttl02">Tin liên quan</h2>
                <ul className="related-articles__lst">
                {news.related.map(item => (
                    <li key={item.id} className="related-item">
                      <h3>
                        <Link className="hover" to={`/${item.unique_key}.html`}>
                            {item.title}
                        </Link>
                      </h3> - ({item.dated})
                    </li>
                ))}
                </ul>
            </div>
            )}
            </div>
    </main>
   
  )
}

export default NewsDetail
