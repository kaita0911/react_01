
import { NavLink } from "react-router-dom"

function MenuItem({ item }) {
  const url = `/${item.unique_key}/`
  const hasSub = item.categories && item.categories.length > 0
  return (
    <li className={hasSub ? "has-sub" : ""}>
      <NavLink to={url} className={({ isActive }) => "menu-item" + (isActive ? " active" : "")}>
        {item.name_detail}
      </NavLink>
      {item.categories && item.categories.length > 0 && (
        <ul className="submenu">
          {item.categories.map(child => (
            <MenuItem key={child.id} item={child} />
          ))}
        </ul>
      )}
    </li>
  )
}

export default MenuItem
