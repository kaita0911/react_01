import { Link } from "react-router-dom";
import { useEffect, useState } from "react";
import { API_URL } from "@/config";
import useLang from "@/context/useLang";
function Breadcrumb({ article, comp }) {
  const [menu, setMenu] = useState([]);
  const { t, lang } = useLang();
  useEffect(() => {
    fetch(`${API_URL}/api/menu.php?lang=${lang}`)
      .then((res) => res.json())
      .then(setMenu);
  }, [lang]);

  const targetComp = article?.comp || comp;

  const menuItem = menu.find((m) => String(m.comp) === String(targetComp));

  return (
    <nav className="breadcrumb">
      <Link to="/">{t.home}</Link>

      {/* ⭐ CHỈ render khi menuItem tồn tại */}
      {menuItem && (
        <>
          {" > "}
          <Link to={`/${lang}/${menuItem.slug}/`}>{menuItem.name}</Link>

          {/* CATEGORY PATH */}
          {article?.category_path
            ?.filter((c) => c.slug !== menuItem.slug) // ⭐ bỏ trùng
            .map((c) => (
              <span key={c.id}>
                {" > "}
                <Link to={`/${lang}/${c.slug}/`}>{c.name}</Link>
              </span>
            ))}

          {/* TITLE */}
          {article?.title && !article?.category_path?.length && (
            <>
              {" > "}
              <span>{article.title}</span>
            </>
          )}
        </>
      )}
    </nav>
  );
}

export default Breadcrumb;
