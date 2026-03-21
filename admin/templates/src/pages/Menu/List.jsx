import { useEffect, useState } from "react";
import { slugify } from "@/utils/slugify";
import { DndContext, closestCenter } from "@dnd-kit/core";
import {
  SortableContext,
  verticalListSortingStrategy,
  useSortable,
  arrayMove,
} from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";

function SortableRow({ menu, children }) {
  const { attributes, listeners, setNodeRef, transform, transition } =
    useSortable({ id: menu.id });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
  };

  return (
    <tr ref={setNodeRef} style={style}>
      {children({ attributes, listeners })}
    </tr>
  );
}

export default function Menu() {
  const [menus, setMenus] = useState([]);
  const [modules, setModules] = useState([]);
  const [languages, setLanguages] = useState([]);
  const [activeTab, setActiveTab] = useState(null);

  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editItem, setEditItem] = useState(null);
  const [autoSlug, setAutoSlug] = useState(true);
  const [linkType, setLinkType] = useState("module");

  const [selectedIds, setSelectedIds] = useState([]);
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [showDeleteMultiModal, setShowDeleteMultiModal] = useState(false);
  const [deleteId, setDeleteId] = useState(null);

  // ================= FETCH DATA =================
  const fetchData = async () => {
    try {
      const [menuRes, modRes, langRes] = await Promise.all([
        fetch("/api/admin/menu.php?act=list").then((r) => r.json()),
        fetch("/api/admin/component.php?act=list_active").then((r) => r.json()),
        fetch("/api/admin/language.php?act=list").then((r) => r.json()),
      ]);

      if (menuRes.status) setMenus(menuRes.data);
      if (modRes.status) setModules(modRes.data);
      if (langRes.status) {
        const activeLang = langRes.data.filter((l) => l.active == 1);
        setLanguages(activeLang);
        if (activeLang.length) setActiveTab(activeLang[0].id);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
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
          prev.map((m) => (m.id === id ? { ...m, active: newValue } : m))
        );
      }
    } catch {
      alert("Lỗi server");
    }
  };

  // ================= ADD / EDIT =================
  const openEditModal = (menu = null) => {
    setAutoSlug(true);
    setLinkType(menu?.link_out ? "external" : "module");

    setEditItem(
      menu || {
        id: null,
        comp: "",
        link_out: "",
        has_sub: 0,
        details: {
          [activeTab]: { name: "", slug: "" },
        },
      }
    );

    setShowModal(true);
  };

  const handleSave = async () => {
    if (!editItem) return;

    const isEdit = !!editItem.id;
    const formData = new FormData();

    if (isEdit) formData.append("id", editItem.id);

    const detail = editItem.details?.[activeTab] || {};
    formData.append("name", detail.name || "");
    formData.append("slug", detail.slug || "");
    formData.append("languageid", activeTab);
    formData.append("comp", editItem.comp || 0);
    formData.append("link_out", editItem.link_out || "");
    formData.append("has_sub", editItem.has_sub || 0);

    const res = await fetch(
      `/api/admin/menu.php?act=${isEdit ? "update" : "add"}`,
      {
        method: "POST",
        body: formData,
      }
    );

    const result = await res.json();
    if (result.status) {
      await fetchData();
      setShowModal(false);
    } else {
      alert("Cập nhật thất bại");
    }
  };

  // ================= DELETE =================
  const handleDelete = (id) => {
    setDeleteId(id);
    setShowDeleteModal(true);
  };
  const confirmDelete = async () => {
    if (!deleteId) return;
    const formData = new FormData();
    formData.append("id", deleteId);
    const res = await fetch("/api/admin/menu.php?act=delete", {
      method: "POST",
      body: formData,
    });
    const result = await res.json();
    if (result.status) {
      setMenus((prev) => prev.filter((m) => m.id !== deleteId));
      setDeleteId(null);
      setShowDeleteModal(false);
    } else alert("Xoá thất bại");
  };

  const handleDeleteMultiple = () => setShowDeleteMultiModal(true);
  const confirmDeleteMultiple = async () => {
    const formData = new FormData();
    selectedIds.forEach((id) => formData.append("ids[]", id));
    const res = await fetch("/api/admin/menu.php?act=delete_multiple", {
      method: "POST",
      body: formData,
    });
    const result = await res.json();
    if (result.status) {
      setMenus((prev) => prev.filter((m) => !selectedIds.includes(m.id)));
      setSelectedIds([]);
      setShowDeleteMultiModal(false);
    } else alert("Xoá thất bại");
  };

  // ================= DRAG & DROP =================
  const handleDragEnd = async (event) => {
    const { active, over } = event;
    if (!over || active.id === over.id) return;

    const oldIndex = menus.findIndex((m) => m.id === active.id);
    const newIndex = menus.findIndex((m) => m.id === over.id);
    const newMenus = arrayMove(menus, oldIndex, newIndex).map((m, idx) => ({
      ...m,
      num: idx + 1,
    }));
    setMenus(newMenus);

    const formData = new FormData();
    newMenus.forEach((m) => {
      formData.append("id[]", m.id);
      formData.append("num[]", m.num);
    });

    await fetch("/api/admin/menu.php?act=reorder", {
      method: "POST",
      body: formData,
    });
  };

  // ================= PREVIEW =================
  const handlePreview = (menu) => {
    let url =
      menu.link_out ||
      `/${menu.details?.[activeTab]?.slug || "page-" + menu.id}`;
    window.open(url, "_blank");
  };

  if (loading) return <div>Đang tải menu...</div>;

  return (
    <main className="page-editor">
      {/* ===== Buttons ===== */}
      <div className="action-bar">
        <button className="c-btn btn-add" onClick={() => openEditModal()}>
          <i className="fa-solid fa-circle-plus"></i> Thêm mới
        </button>
        <button
          className="c-btn btn-delete-multi"
          disabled={!selectedIds.length}
          onClick={handleDeleteMultiple}
        >
          Xoá đã chọn ({selectedIds.length})
        </button>
      </div>

      {/* ===== Language Tabs ===== */}
      {languages.length > 1 && (
        <div className="lang-tabs-header list">
          {languages.map((lang) => (
            <button
              key={lang.id}
              className={`lang-btn ${activeTab === lang.id ? "active" : ""}`}
              onClick={() => setActiveTab(lang.id)}
            >
              {lang.name}
            </button>
          ))}
        </div>
      )}

      {/* ===== Menu Table ===== */}
      <DndContext collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
        <table className="admin-table">
          <thead>
            <tr>
              <th className="col-id txt-center">
                <input
                  className="check-del"
                  type="checkbox"
                  checked={
                    menus.length &&
                    menus.every((m) => selectedIds.includes(m.id))
                  }
                  onChange={(e) =>
                    setSelectedIds(
                      e.target.checked ? menus.map((m) => m.id) : []
                    )
                  }
                />
              </th>
              <th className="col-order txt-center">STT</th>
              <th>Tên</th>
              <th>Link</th>
              <th className="txt-center">Active</th>
              <th className="txt-center">Actions</th>
            </tr>
          </thead>

          <SortableContext
            items={menus.map((m) => m.id)}
            strategy={verticalListSortingStrategy}
          >
            <tbody>
              {menus.map((menu) => {
                const detail = menu.details?.[activeTab];
                return (
                  <SortableRow key={menu.id} menu={menu}>
                    {({ attributes, listeners }) => (
                      <>
                        <td className="txt-center">
                          <input
                            className="check-del"
                            type="checkbox"
                            checked={selectedIds.includes(menu.id)}
                            onChange={(e) => {
                              if (e.target.checked)
                                setSelectedIds((prev) => [...prev, menu.id]);
                              else
                                setSelectedIds((prev) =>
                                  prev.filter((id) => id !== menu.id)
                                );
                            }}
                          />
                        </td>
                        <td className="txt-center">{menu.num}</td>
                        <td>{detail?.name || "Menu"}</td>
                        <td>{menu.link_out || detail?.slug || ""}</td>
                        <td className="txt-center">
                          <label className="switch">
                            <input
                              type="checkbox"
                              checked={menu.active == 1}
                              onChange={() =>
                                handleToggle(menu.id, "active", menu.active)
                              }
                            />
                            <span className="slider"></span>
                          </label>
                        </td>
                        <td className="col-actions txt-center">
                          <div className="btn-actions">
                            <button
                              className="btn-view act"
                              onClick={() => handlePreview(menu)}
                            >
                              <i className="fa-solid fa-eye"></i>
                            </button>
                            <button
                              className="act btn-edit"
                              onClick={() => openEditModal(menu)}
                            >
                              <i className="fa-solid fa-pen"></i>
                            </button>
                            <button
                              className="act btn-delete"
                              onClick={() => handleDelete(menu.id)}
                            >
                              <i className="fa-solid fa-trash"></i>
                            </button>
                            <button
                              className="act drag-handle"
                              {...attributes}
                              {...listeners}
                            >
                              <i className="fa-solid fa-up-down"></i>
                            </button>
                          </div>
                        </td>
                      </>
                    )}
                  </SortableRow>
                );
              })}
            </tbody>
          </SortableContext>
        </table>
      </DndContext>

      {/* ===== MODAL ADD/EDIT ===== */}
      {showModal && (
        <div className="modal" onClick={() => setShowModal(false)}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            <h3>{editItem?.id ? "Chỉnh sửa Menu" : "Thêm Menu"}</h3>

            <label>Tên</label>
            <input
              type="text"
              value={editItem.details?.[activeTab]?.name || ""}
              onChange={(e) =>
                setEditItem((prev) => ({
                  ...prev,
                  details: {
                    ...prev.details,
                    [activeTab]: {
                      ...prev.details?.[activeTab],
                      name: e.target.value,
                      slug: autoSlug
                        ? slugify(e.target.value)
                        : prev.details?.[activeTab]?.slug,
                    },
                  },
                }))
              }
            />

            <label>Slug</label>
            <input
              type="text"
              value={editItem.details?.[activeTab]?.slug || ""}
              onChange={(e) => {
                setAutoSlug(false);
                setEditItem((prev) => ({
                  ...prev,
                  details: {
                    ...prev.details,
                    [activeTab]: {
                      ...prev.details?.[activeTab],
                      slug: e.target.value,
                    },
                  },
                }));
              }}
            />

            <label>Kiểu liên kết</label>
            <select
              value={linkType}
              onChange={(e) => setLinkType(e.target.value)}
            >
              <option value="module">Module</option>
              <option value="external">Link ngoài</option>
            </select>

            {linkType === "module" && (
              <>
                <label>Chọn module</label>
                <select
                  value={editItem.comp || ""}
                  onChange={(e) =>
                    setEditItem({
                      ...editItem,
                      comp: e.target.value,
                      link_out: "",
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

            {linkType === "external" && (
              <>
                <label>Link ngoài</label>
                <input
                  type="text"
                  value={editItem.link_out || ""}
                  onChange={(e) =>
                    setEditItem({ ...editItem, link_out: e.target.value })
                  }
                />
              </>
            )}

            <div className="modal-active">
              <label className="switch">
                <input
                  type="checkbox"
                  checked={editItem.has_sub == 1}
                  onChange={(e) =>
                    setEditItem({
                      ...editItem,
                      has_sub: e.target.checked ? 1 : 0,
                    })
                  }
                />
                <span className="slider"></span>
              </label>
              <span>Có menu con</span>
            </div>

            <div className="modal-actions">
              <button className="btn-confirm" onClick={handleSave}>
                <i class="fa-regular fa-floppy-disk"></i> Lưu
              </button>
              <button
                className="btn-cancel"
                onClick={() => setShowModal(false)}
              >
                Huỷ
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ===== DELETE MODAL ===== */}
      {showDeleteModal && (
        <div className="modal" onClick={() => setShowDeleteModal(false)}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            <h3>Xác nhận xoá</h3>
            <p>Bạn có chắc muốn xoá menu này không?</p>
            <div>
              <button onClick={confirmDelete}>Xoá</button>
              <button onClick={() => setShowDeleteModal(false)}>Huỷ</button>
            </div>
          </div>
        </div>
      )}

      {/* ===== DELETE MULTI MODAL ===== */}
      {showDeleteMultiModal && (
        <div className="modal" onClick={() => setShowDeleteMultiModal(false)}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            <h3>Xác nhận xoá</h3>
            <p>Bạn có chắc muốn xoá {selectedIds.length} menu đã chọn?</p>
            <div>
              <button onClick={confirmDeleteMultiple}>Xoá</button>
              <button onClick={() => setShowDeleteMultiModal(false)}>
                Huỷ
              </button>
            </div>
          </div>
        </div>
      )}
    </main>
  );
}
