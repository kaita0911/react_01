import { Link } from "react-router-dom"
import { useEffect, useState } from "react"
import { API_URL } from "@/config"

function Breadcrumb({ article, comp }) {

  const [menu, setMenu] = useState([])

  useEffect(() => {
    fetch(`${API_URL}/api/menu.php`)
      .then(res => res.json())
      .then(setMenu)
  }, [])

  const targetComp = article?.comp || comp

  const menuItem = menu.find(
    m => String(m.comp) === String(targetComp)
  )

  return (
    <nav className="breadcrumb">

      <Link to="/">Trang chủ</Link>

      {/* ⭐ CHỈ render khi menuItem tồn tại */}
      {menuItem && (
        <>
          {" > "}
          <Link to={`/${menuItem.unique_key}/`}>
            {menuItem.name_detail}
          </Link>

          {/* CATEGORY PATH */}
          {article?.category_path
            ?.filter(c => c.slug !== menuItem.unique_key) // ⭐ bỏ trùng
            .map((c) => (
              <span key={c.id}>
                {" > "}
                <Link to={`/${c.slug}/`}>
                  {c.name}
                </Link>
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
  )
}

export default Breadcrumb
