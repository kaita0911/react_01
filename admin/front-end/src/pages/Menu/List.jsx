import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { slugify } from "@/utils/slugify";
export default function Menu() {
  const [autoSlug, setAutoSlug] = useState(true);
  const [menus, setMenus] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editItem, setEditItem] = useState(null);
  const [linkType, setLinkType] = useState("module");
  const [modules, setModules] = useState([]);
  // 👉 language id (nếu có đa ngôn ngữ)
  const lang = 1;
  // ================= FETCH MENU =================
  useEffect(() => {
    const fetchMenu = async () => {
      try {
        const res = await fetch("/api/admin/menu.php?act=list");
        const result = await res.json();

        if (result.status) {
          setMenus(result.data);
        }
      } catch (err) {
        console.error("Menu error:", err);
      }

      setLoading(false);
    };

    fetchMenu();

    const fetchModules = async () => {
      try {
        const res = await fetch("/api/admin/component.php?act=list_active");
        const result = await res.json();

        if (result.status) {
          setModules(result.data);
        }
      } catch (err) {
        console.error("Module error:", err);
      }
    };

    fetchModules();
  }, []);
  // ================= TOGGLE ACTIVE =================
  const handleToggle = async (id, currentValue) => {
    const newValue = currentValue == 1 ? 0 : 1;

    const formData = new FormData();
    formData.append("id", id);
    formData.append("table", "menu");
    formData.append("column", "active");
    formData.append("value", newValue);

    try {
      const res = await fetch("/api/admin/update-active.php", {
        method: "POST",
        body: formData,
      });

      const result = await res.json();

      if (result.success) {
        setMenus((prev) =>
          prev.map((item) =>
            item.id === id ? { ...item, active: newValue } : item
          )
        );
      }
    } catch {
      alert("Lỗi server");
    }
  };
  // ================= OPEN EDIT =================
  const handleEdit = (item) => {
    setEditItem(item);

    setAutoSlug(true);

    setLinkType(item.link_out ? "external" : "module");

    setShowModal(true);
  };
  // ================= SAVE EDIT =================
  const handleSave = async () => {
    const formData = new FormData();

    formData.append("id", editItem.id);
    formData.append("comp", editItem.module_id || 0);
    formData.append("link_out", editItem.link_out || "");
    formData.append("has_sub", editItem.has_sub || 0);

    // 👇 dữ liệu đa ngôn ngữ
    const detail = editItem.details?.[lang] || {};

    formData.append("name", detail.name || "");
    formData.append("unique_key", detail.unique_key || "");
    formData.append("languageid", lang);

    const res = await fetch("/api/admin/menu.php?act=update", {
      method: "POST",
      body: formData,
    });

    const result = await res.json();

    if (result.status) {
      setMenus((prev) =>
        prev.map((item) =>
          item.id === editItem.id ? { ...item, ...editItem } : item
        )
      );

      setShowModal(false);
    } else {
      alert("Cập nhật thất bại");
    }
  };

  if (loading) return <div>Đang tải menu...</div>;

  return (
    <main>
      <table className="admin-table">
        <thead>
          <tr>
            <th className="col-order txt-center">Thứ tự</th>
            <th className="col-actions"> Link</th>
            <th>Tên</th>
            <th className="col-status txt-center">Active</th>
            <th className="col-action txt-center">Action</th>
          </tr>
        </thead>

        <tbody>
          {menus.map((menu) => {
            const detail = menu.details?.[lang];

            const name = detail?.name || "Menu";
            const link =
              menu.link_out || `${detail?.unique_key || "page-" + menu.id}`;

            return (
              <tr key={menu.id}>
                <td className="txt-center">{menu.id}</td>
                <td>{link}</td>
                <td>{name}</td>

                {/* ===== ACTIVE SWITCH ===== */}
                <td className="col-status txt-center">
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={menu.active == 1}
                      onChange={() => handleToggle(menu.id, menu.active)}
                    />
                    <span className="slider"></span>
                  </label>
                </td>

                {/* ===== ACTION ===== */}
                <td className="col-actions txt-center">
                  <div className="btn-actions">
                    <button
                      className="btn-edit act"
                      onClick={() => handleEdit({ ...menu, name })}
                    >
                      <i className="fa-regular fa-pen-to-square"></i>
                    </button>
                  </div>
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
      {showModal && (
        <div className="modal" onClick={() => setShowModal(false)}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            <h3>Chỉnh sửa Menu</h3>

            {/* ===== NAME ===== */}
            <label>Tên menu</label>
            <input
              type="text"
              value={editItem.details?.[lang]?.name || ""}
              onChange={(e) => {
                const newName = e.target.value;

                setEditItem((prev) => ({
                  ...prev,
                  details: {
                    ...prev.details,
                    [lang]: {
                      ...prev.details?.[lang],
                      name: newName,
                      unique_key: autoSlug
                        ? slugify(newName)
                        : prev.details?.[lang]?.unique_key,
                    },
                  },
                }));
              }}
            />

            {/* ===== SLUG = unique_key ===== */}
            <label>Slug</label>
            <input
              type="text"
              value={editItem.details?.[lang]?.unique_key || ""}
              onChange={(e) => {
                setAutoSlug(false); // 👈 user sửa tay

                setEditItem((prev) => ({
                  ...prev,
                  details: {
                    ...prev.details,
                    [lang]: {
                      ...prev.details?.[lang],
                      unique_key: e.target.value,
                    },
                  },
                }));
              }}
            />
            {/* ===== LINK TYPE ===== */}
            <label>Kiểu liên kết</label>
            <select
              value={linkType}
              onChange={(e) => {
                const type = e.target.value;
                setLinkType(type);

                setEditItem((prev) => ({
                  ...prev,
                  link_out: type === "external" ? "" : null,
                }));
              }}
            >
              <option value="module">Module</option>
              <option value="external">Link ngoài</option>
            </select>
            {/* ===== MODULE SELECT ===== */}
            {linkType === "module" && (
              <>
                <label>Chọn module</label>

                <select
                  value={editItem.comp || ""}
                  onChange={(e) =>
                    setEditItem({
                      ...editItem,
                      comp: e.target.value, // ✅ đúng field
                      link_out: "", // module thì bỏ link ngoài
                    })
                  }
                >
                  <option value="">-- Chọn module --</option>

                  {modules.map((m) => (
                    <option key={m.id} value={m.id}>
                      {m.detail_name}
                    </option>
                  ))}
                </select>
              </>
            )}
            {/* ===== EXTERNAL LINK ===== */}
            {linkType === "external" && (
              <>
                <label>Link ngoài</label>
                <input
                  type="text"
                  value={editItem.link_out || ""}
                  placeholder="https://..."
                  onChange={(e) =>
                    setEditItem({ ...editItem, link_out: e.target.value })
                  }
                />
              </>
            )}

            {/* ===== HAS CHILD ===== */}
            <div className="modal-active">
              <label className="switch">
                <input
                  type="checkbox"
                  checked={editItem?.has_sub == 1}
                  onChange={(e) =>
                    setEditItem({
                      ...editItem,
                      has_sub: e.target.checked ? 1 : 0,
                    })
                  }
                />

                <span className="slider"></span>
              </label>
              <span className="toggle-text">Có menu con</span>
            </div>

            {/* ===== ACTION ===== */}
            <div className="modal-actions">
              <button onClick={handleSave} className="btn-confirm">
                <i className="fa-regular fa-floppy-disk"></i> Lưu
              </button>

              <button
                onClick={() => setShowModal(false)}
                className="btn-cancel"
              >
                Huỷ
              </button>
            </div>
          </div>
        </div>
      )}
    </main>
  );
}
