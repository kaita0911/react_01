import { useParams } from "react-router-dom";
import { useEffect, useState } from "react";
import { API_URL } from "@/config";
import NewsDetail from "../pages/News/Detail";
import ProductDetail from "../pages/Product/Detail";
import P404 from "../pages/P404/";
function HtmlRouter() {
  const { slug } = useParams();
  const [data, setData] = useState(null);

  useEffect(() => {
    fetch(`${API_URL}/api/resolve.php?slug=${slug}`)
      .then((res) => res.json())
      .then((res) => {
        // console.log(res)
        setData(res);
      });
  }, [slug]);

  if (!data) return;

  switch (data.comp) {
    case 1: // Tin tức
      return <NewsDetail data={data} />;

    case 2: // Sản phẩm
      return <ProductDetail data={data} />;

    // case 3: // Dịch vụ
    //   return <ServiceDetail data={data} />

    // case 4: // Trang tĩnh
    //   return <PageDetail data={data} />

    default:
      return <P404 data={data} />;
  }
}

export default HtmlRouter;
