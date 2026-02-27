import { useEffect, useState } from "react";
import Seo from "@/components/Seo";
//import { Link } from "react-router-dom"
import { API_URL } from "@/config";
import { fixContent } from "@/utils/fixContent";
import Breadcrumb from "@/router/Breadcrumb";
//import "./News.scss"

function About() {
  const [news, setNews] = useState([]);

  useEffect(() => {
    fetch(`${API_URL}/api/about.php?act=list`)
      .then((res) => res.json())
      .then((data) => setNews(data.items?.slice(0, 1) || []));
  }, []);

  return (
    <>
      <main>
        <div className="container">
          <div className="artseed-body">
            <Breadcrumb comp="3" /> {/* 👈 đặt ở đây */}
            {news.map((item) => (
              <div className="assets-detail" key={item.id}>
                <Seo
                  title={item.name}
                  description={item.des}
                  keywords={item.keyword}
                  image={
                    item?.img_thumb_vn && `${API_URL}/${item.img_thumb_vn}`
                  }
                />
                <h1>{item.name}</h1>
                <p>
                  {item.des}, {item.keyword}
                </p>
                <div
                  className="artseed-detail"
                  dangerouslySetInnerHTML={{
                    __html: fixContent(item.content),
                  }}
                />
              </div>
            ))}
          </div>
        </div>
      </main>
    </>
  );
}

export default About;
