import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import Seo from "@/components/Seo";
import { API_URL } from "@/config";
import { Link } from "react-router-dom";
import Breadcrumb from "@/router/Breadcrumb";
import useLang from "@/context/useLang";
import useLangPath from "@/utils/useLangPath";
import useScrollToTop from "@/utils/useScrollToTop";
import { useLanguage } from "@/context/useLanguage";
function NewsDetail() {
  const getLangPath = useLangPath(); // gọi hook
  const { lang: urlLang } = useParams(); // lang từ URL
  // Lấy từ Context
  const { defaultLang } = useLanguage();
  // fallback lang nếu URL không có
  const lang = urlLang || defaultLang;
  const { slug } = useParams();
  const [news, setNews] = useState(null);
  const { t } = useLang();
  useScrollToTop(slug);
  useEffect(() => {
    fetch(`${API_URL}/api/news.php?act=detail&slug=${slug}&lang=${lang}`)
      .then((res) => res.json())
      .then(setNews);
  }, [slug, lang]);

  if (!news) return;

  return (
    <main>
      <div className="container">
        <Breadcrumb comp="1" article={news} />
        <Seo
          title={news.title}
          description={news?.des}
          keywords={news?.keyword}
          image={news?.img_thumb_vn && `${API_URL}/${news.img_thumb_vn}`}
        />
        <div className="artseed-body">
          <h1>{news.title}</h1>
          <div
            className="artseed-detail"
            dangerouslySetInnerHTML={{ __html: news.content }}
          />
        </div>

        {news.related?.length > 0 && (
          <div className="related-articles">
            <h2 className="ttl02">{t.related_news}</h2>
            <ul className="related-articles__lst">
              {news.related.map((item) => (
                <li key={item.id} className="related-item">
                  <h3>
                    <Link
                      title={item.name}
                      className="hover"
                      to={getLangPath(item.slug, ".html")}
                    >
                      {item.name}
                    </Link>
                  </h3>
                  - ({item.dated})
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>
    </main>
  );
}

export default NewsDetail;
