import { useParams } from "react-router-dom";
import { useEffect, useState } from "react";
import { API_URL } from "@/config";
import About from "../pages/About";
import Product from "../pages/Product/List";
import ProductCate from "../pages/Product/Cate";
import News from "../pages/News/List";
import NewsCate from "../pages/News/Cate";
import Contact from "../pages/Contact/";
import P404 from "../pages/P404/";

function Resolver() {
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
    case 3:
      return <About data={data} />;
    case 23:
      return <Contact data={data} />;

    case 2:
      if (data.type === "category") {
        return <ProductCate data={data} />;
      }
      return <Product data={data} />;

    case 1:
      if (data.type === "category") {
        return <NewsCate data={data} />;
      }
      return <News data={data} />;

    default:
      return <P404 data={data} />;
  }
}

export default Resolver;
