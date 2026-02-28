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
  };
  useEffect(() => {
    let isMounted = true;

    const loadData = async () => {
      try {
        // ===== MENU =====
        const resMenu = await fetch("/api/admin/menu.php?act=list");
        const dataMenu = await resMenu.json();

        if (isMounted && dataMenu.status) {
          setMenus(dataMenu.data);
        }

        // ===== MODULE =====
        const resMod = await fetch("/api/admin/component.php?act=list_active");
        const dataMod = await resMod.json();

        if (isMounted && dataMod.status) {
          setModules(dataMod.data);
        }
      } catch (err) {
        console.error("Load error:", err);
      } finally {
        if (isMounted) setLoading(false);
      }
    };

    loadData();

    return () => {
      isMounted = false;
    };
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
    const isEdit = !!editItem.id;
    const formData = new FormData();

    if (isEdit) formData.append("id", editItem.id);

    formData.append("comp", editItem.comp || 0);
    formData.append("link_out", editItem.link_out || "");
    formData.append("has_sub", editItem.has_sub || 0);

    // 👇 dữ liệu đa ngôn ngữ
    const detail = editItem.details?.[lang] || {};

    formData.append("name", detail.name || "");
    formData.append("unique_key", detail.unique_key || "");
    formData.append("languageid", lang);

    const act = isEdit ? "update" : "add";

    const res = await fetch(`/api/admin/menu.php?act=${act}`, {
      method: "POST",
      body: formData,
    });

    const result = await res.json();

    if (result.status) {
      // setMenus((prev) =>
      //   prev.map((item) =>
      //     item.id === editItem.id ? { ...item, ...editItem } : item
      //   )
      // );
      await fetchMenu();

      setShowModal(false);
    } else {
      alert("Cập nhật thất bại");
    }
  };
  ////kéo thả
  const handleDragEnd = async (event) => {
    const { active, over } = event;

    if (!over || active.id === over.id) return;

    const oldIndex = menus.findIndex((m) => m.id === active.id);
    const newIndex = menus.findIndex((m) => m.id === over.id);

    const newMenus = arrayMove(menus, oldIndex, newIndex);

    // cập nhật num theo thứ tự mới
    const updated = newMenus.map((item, index) => ({
      ...item,
      num: index + 1,
    }));

    setMenus(updated);

    const formData = new FormData();

    updated.forEach((item) => {
      formData.append("id[]", item.id);
      formData.append("num[]", item.num);
    });

    await fetch("/api/admin/menu.php?act=reorder", {
      method: "POST",
      body: formData,
    });
  };
  ////Thêm mới
  const handleAddNew = () => {
    setAutoSlug(true);
    setLinkType("module");

    setEditItem({
      id: null,
      comp: "",
      link_out: "",
      has_sub: 0,
      details: {
        [lang]: {
          name: "",
          unique_key: "",
        },
      },
    });

    setShowModal(true);
  };
  ////xoÁ
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [deleteId, setDeleteId] = useState(null);
  const handleDelete = (id) => {
    setDeleteId(id);
    setShowDeleteModal(true);
  };
  const confirmDelete = async () => {
    const formData = new FormData();
    formData.append("id", deleteId);

    try {
      const res = await fetch("/api/admin/menu.php?act=delete", {
        method: "POST",
        body: formData,
      });

      const result = await res.json();

      if (result.status) {
        const updated = menus
          .filter((item) => item.id !== deleteId)
          .map((item, index) => ({
            ...item,
            num: index + 1,
          }));

        setMenus(updated);
        setShowDeleteModal(false);
        setDeleteId(null);
      } else {
        alert("Xoá thất bại");
      }
    } catch {
      alert("Lỗi server");
    }
  };
  ////Xoá nhiều
  const [selectedIds, setSelectedIds] = useState([]);
  const [showDeleteMultiModal, setShowDeleteMultiModal] = useState(false);
  const handleDeleteMultiple = () => {
    if (!selectedIds.length) return;
    setShowDeleteMultiModal(true);
  };
  const confirmDeleteMultiple = async () => {
    const formData = new FormData();

    selectedIds.forEach((id) => {
      formData.append("ids[]", id);
    });

    try {
      const res = await fetch("/api/admin/menu.php?act=delete_multiple", {
        method: "POST",
        body: formData,
      });

      const result = await res.json();

      if (result.status) {
        const updated = menus
          .filter((item) => !selectedIds.includes(item.id))
          .map((item, index) => ({
            ...item,
            num: index + 1,
          }));

        setMenus(updated);
        setSelectedIds([]);
        setShowDeleteMultiModal(false);
      } else {
        alert("Xoá thất bại");
      }
    } catch {
      alert("Lỗi server");
    }
  };

  ////xem nhanh
  const handlePreview = (menu) => {
    let url = "";

    // Nếu có link ngoài
    if (menu.link_out) {
      url = menu.link_out;
    } else {
      // Link nội bộ theo slug
      const detail = menu.details?.[lang];
      const slug = detail?.unique_key || menu.id;

      url = `/${slug}`;
    }

    window.open(url, "_blank");
  };

  if (loading) return <div>Đang tải menu...</div>;

  return (
    <main>
      {/* ===== Buttons ===== */}
      <div className="flex-buttons">
        <button className="c-btn btn-add" onClick={handleAddNew}>
          <i className="fa-solid fa-circle-plus"></i> Thêm mới
        </button>
        <button
          className="c-btn btn-delete-multi"
          disabled={!selectedIds.length}
          onClick={handleDeleteMultiple}
        >
          <i className="fa-solid fa-trash-can"></i> Xoá đã chọn
        </button>
      </div>
      <DndContext collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
        <table className="admin-table">
          <thead>
            <tr>
              <th className="col-id txt-center">
                <input
                  className="check-del"
                  type="checkbox"
                  checked={
                    menus.length > 0 &&
                    menus.every((m) => selectedIds.includes(m.id))
                  }
                  onChange={(e) => {
                    if (e.target.checked) {
                      setSelectedIds(menus.map((m) => m.id));
                    } else {
                      setSelectedIds([]);
                    }
                  }}
                />
              </th>
              <th className="col-order txt-center">Thứ tự</th>
              <th className="col-actions"> Link</th>
              <th>Tên</th>
              <th className="col-status txt-center">Active</th>
              <th className="col-action txt-center">Action</th>
            </tr>
          </thead>

          <SortableContext
            items={menus.map((m) => m.id)}
            strategy={verticalListSortingStrategy}
          >
            <tbody>
              {menus.map((menu) => {
                const detail = menu.details?.[lang];
                const name = detail?.name || "Menu";
                const link =
                  menu.link_out || `${detail?.unique_key || "page-" + menu.id}`;

                return (
                  <SortableRow key={menu.id} menu={menu}>
                    {({ attributes, listeners }) => (
                      <>
                        <td className="col-id txt-center">
                          <input
                            className="check-del"
                            type="checkbox"
                            checked={selectedIds.includes(menu.id)}
                            onChange={(e) => {
                              if (e.target.checked) {
                                setSelectedIds((prev) => [...prev, menu.id]);
                              } else {
                                setSelectedIds((prev) =>
                                  prev.filter((id) => id !== menu.id)
                                );
                              }
                            }}
                          />
                        </td>
                        <td className="txt-center">{menu.num}</td>

                        <td>{link}</td>
                        <td>{name}</td>

                        <td className="col-status txt-center">
                          <label className="switch">
                            <input
                              type="checkbox"
                              checked={menu.active == 1}
                              onChange={() =>
                                handleToggle(menu.id, menu.active)
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
                              title="Xem nhanh"
                            >
                              <i className="fa-regular fa-eye"></i>
                            </button>
                            <button
                              className="btn-edit act"
                              onClick={() => handleEdit({ ...menu, name })}
                            >
                              <i className="fa-regular fa-pen-to-square"></i>
                            </button>
                            <button
                              className="btn-delete act"
                              onClick={() => handleDelete(menu.id)}
                              title="Xoá"
                            >
                              <i className="fa-solid fa-trash"></i>
                            </button>
                            <div
                              className="drag-handle act"
                              {...attributes}
                              {...listeners}
                            >
                              <i className="fa-solid fa-up-down"></i>
                            </div>
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

      {showModal && (
        <div className="modal" onClick={() => setShowModal(false)}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            <h3>{editItem?.id ? "Chỉnh sửa Menu" : "Thêm mới Menu"}</h3>

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
      {showDeleteModal && (
        <div className="modal" onClick={() => setShowDeleteModal(false)}>
          <div
            className="modal-box delete-box"
            onClick={(e) => e.stopPropagation()}
          >
            <h3>Xác nhận xoá</h3>

            <p>Bạn có chắc muốn xoá menu này không?</p>

            <div className="modal-actions">
              <button className="btn-confirm" onClick={confirmDelete}>
                <i className="fa-solid fa-trash"></i> Xoá
              </button>

              <button
                className="btn-cancel"
                onClick={() => setShowDeleteModal(false)}
              >
                Huỷ
              </button>
            </div>
          </div>
        </div>
      )}
      {showDeleteMultiModal && (
        <div className="modal" onClick={() => setShowDeleteMultiModal(false)}>
          <div
            className="modal-box delete-box"
            onClick={(e) => e.stopPropagation()}
          >
            <h3>Xác nhận xoá</h3>

            <p>
              Bạn có chắc muốn xoá <b>{selectedIds.length}</b> menu đã chọn?
            </p>

            <div className="modal-actions">
              <button className="btn-confirm" onClick={confirmDeleteMultiple}>
                <i className="fa-solid fa-trash"></i> Xoá
              </button>

              <button
                className="btn-cancel"
                onClick={() => setShowDeleteMultiModal(false)}
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
