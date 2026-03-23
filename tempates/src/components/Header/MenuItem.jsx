import { NavLink } from "react-router-dom";

function MenuItem({ item, lang, singleLang }) {
  // 🔥 build URL đúng cho cả 2 mode
  const url = singleLang ? `/${item.slug}/` : `/${lang}/${item.slug}/`;

  const hasSub = item.categories && item.categories.length > 0;

  return (
    <li className={hasSub ? "has-sub" : ""}>
      <NavLink
        to={url}
        className={({ isActive }) => "menu-item" + (isActive ? " active" : "")}
      >
        {item.name}
      </NavLink>

      {hasSub && (
        <ul className="submenu">
          {item.categories.map((child) => (
            <MenuItem
              key={child.id}
              item={child}
              lang={lang}
              singleLang={singleLang} // 🔥 BẮT BUỘC truyền xuống
            />
          ))}
        </ul>
      )}
    </li>
  );
}

export default MenuItem;
