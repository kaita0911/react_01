import { useEffect, useState } from "react";
import { useParams, Link, useNavigate } from "react-router-dom";
import { API_URL } from "@/config";
import { DndContext, closestCenter } from "@dnd-kit/core";
import {
  SortableContext,
  verticalListSortingStrategy,
  useSortable,
  arrayMove,
} from "@dnd-kit/sortable";

import { CSS } from "@dnd-kit/utilities";

/* ================= FLATTEN ================= */

const flatten = (list, level = 0, parent = 0) => {
  let arr = [];

  list.forEach((item) => {
    arr.push({
      ...item,
      level,
      parent_id: parent,
    });

    if (item.children?.length) {
      arr = arr.concat(flatten(item.children, level + 1, item.id));
    }
  });

  return arr;
};
/* ================= ROW ================= */

function SortableRow({ item, children }) {
  const { attributes, listeners, setNodeRef, transform, transition } =
    useSortable({ id: item.id });

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

/* ================= PAGE ================= */

export default function CategoryList() {
  const { module } = useParams();
  const navigate = useNavigate();

  const [deleteId, setDeleteId] = useState(null);
  const [selectedIds, setSelectedIds] = useState([]);
  const [deleteMode, setDeleteMode] = useState(null);
  const [showDeleteModal, setShowDeleteModal] = useState(false);

  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [fields, setFields] = useState([]);

  const [languages, setLanguages] = useState([]);
  const [activeTab, setActiveTab] = useState(null);

  const [editingId, setEditingId] = useState(null);
  const [editValue, setEditValue] = useState("");
  ////update name
  const saveName = async (id) => {
    const fd = new FormData();

    fd.append("id", id);
    fd.append("languageid", activeTab);
    fd.append("name", editValue);

    const res = await fetch("/api/admin/category.php?act=update_name", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      setRows((prev) =>
        prev.map((item) =>
          item.id === id
            ? {
                ...item,
                names: {
                  ...item.names,
                  [activeTab]: editValue,
                },
              }
            : item
        )
      );
    }

    setEditingId(null);
  };
  ////upload image
  const handleImageUpload = async (id, file) => {
    const fd = new FormData();

    fd.append("id", id);
    fd.append("image", file);

    const res = await fetch("/api/admin/category.php?act=update_image", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      setRows((prev) =>
        prev.map((item) =>
          item.id === id ? { ...item, img_vn: data.image } : item
        )
      );
    }
  };
  /////
  useEffect(() => {
    const loadData = async () => {
      const resModule = await fetch(
        `/api/admin/component.php?act=comp&module=${module}`
      );
      const comp = await resModule.json();

      if (!comp.status) return;

      const compId = comp.data;

      const [fieldsRes, catRes, langRes] = await Promise.all([
        fetch(
          `/api/admin/component_fields.php?act=list&component=${compId}&target=category`
        ),
        fetch(`/api/admin/category.php?act=list&comp=${compId}`),
        fetch(`/api/admin/language.php?act=list`),
      ]);

      const fieldData = await fieldsRes.json();
      const catData = await catRes.json();
      const langData = await langRes.json();

      if (fieldData.status) setFields(fieldData.data);
      if (catData.status) setRows(flatten(catData.data));
      if (langData.status) {
        const activeLang = langData.data.filter((l) => l.active == 1);
        setLanguages(activeLang);
        if (activeLang.length) setActiveTab(activeLang[0].id);
      }
      setLoading(false);
    };

    loadData();
  }, [module]);
  /* ================= RENDER fields ================= */
  const getField = (name) => fields.some((f) => f.name === name);

  const rebuildTree = (list) =>
    list.map((item, index) => {
      let parent_id = 0;

      for (let i = index - 1; i >= 0; i--) {
        if (list[i].level < item.level) {
          parent_id = list[i].id;
          break;
        }
      }

      return { ...item, parent_id, num: index + 1 };
    });

  const saveTree = async (list) => {
    const fd = new FormData();

    list.forEach((r) => {
      fd.append("id[]", r.id);
      fd.append("parent_id[]", r.parent_id);
      fd.append("num[]", r.num);
    });

    await fetch("/api/admin/category.php?act=reorder_tree", {
      method: "POST",
      body: fd,
    });
  };
  /* ================= DELETE ================= */

  const handleDelete = async () => {
    const fd = new FormData();
    fd.append("id", deleteId);

    const res = await fetch("/api/admin/category.php?act=delete", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      setRows((prev) => prev.filter((r) => r.id !== deleteId));
    }

    setDeleteId(null);
  };
  // ===== XOÁ NHIỀU =====
  const toggleSelect = (id) => {
    setSelectedIds((prev) =>
      prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
    );
  };
  const toggleSelectAll = () => {
    if (selectedIds.length === rows.length) {
      setSelectedIds([]);
    } else {
      setSelectedIds(rows.map((r) => r.id));
    }
  };
  const handleDeleteMultiple = async () => {
    if (selectedIds.length === 0) return;

    const fd = new FormData();

    selectedIds.forEach((id) => {
      fd.append("ids[]", id);
    });

    const res = await fetch("/api/admin/category.php?act=delete_multiple", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      setRows((prev) => prev.filter((r) => !selectedIds.includes(r.id)));
      setSelectedIds([]);
    }

    setShowDeleteModal(false);
  };
  const confirmDelete = () => {
    if (deleteMode === "single") {
      handleDelete();
    } else if (deleteMode === "multiple") {
      handleDeleteMultiple();
    }
  };
  /* ================= DRAG ================= */

  const handleDragEnd = async (event) => {
    const { active, over } = event;

    if (!over || active.id === over.id) return;

    const oldIndex = rows.findIndex((r) => r.id === active.id);
    const newIndex = rows.findIndex((r) => r.id === over.id);

    const moved = arrayMove(rows, oldIndex, newIndex);

    const updated = rebuildTree(moved);

    setRows(updated);

    saveTree(updated);
  };

  /* ================= INDENT RIGHT ================= */

  const indentRight = (id) => {
    const index = rows.findIndex((r) => r.id === id);

    if (index <= 0) return;

    const newRows = [...rows];

    newRows[index].level++;

    const updated = rebuildTree(newRows);

    setRows(updated);
    saveTree(updated);
  };

  /* ================= INDENT LEFT ================= */

  const indentLeft = (id) => {
    const index = rows.findIndex((r) => r.id === id);

    const newRows = [...rows];

    newRows[index].level = Math.max(0, newRows[index].level - 1);

    const updated = rebuildTree(newRows);

    setRows(updated);
    saveTree(updated);
  };

  /* ================= LOADING ================= */

  if (loading) return <div>Loading...</div>;
  // ================= TOGGLE ACTIVE =================
  const handleToggle = async (id, column, currentValue) => {
    const newValue = currentValue == 1 ? 0 : 1;

    const formData = new FormData();
    formData.append("id", id);
    formData.append("table", "categories");
    formData.append("column", column);
    formData.append("value", newValue);

    try {
      const res = await fetch("/api/admin/update-active.php", {
        method: "POST",
        body: formData,
      });

      const result = await res.json();
      // console.log(result);

      if (result.success) {
        setRows((prev) =>
          prev.map((item) =>
            item.id === id ? { ...item, [column]: newValue } : item
          )
        );
      }
    } catch {
      alert("Lỗi server");
    }
  };

  /* ================= RENDER ================= */

  return (
    <main className="page-editor">
      <div className="action-bar">
        <Link to={`/${module}/category/create`} className="c-btn btn-add">
          <i className="fa-solid fa-circle-plus"></i> Thêm mới
        </Link>
        <button
          className="c-btn btn-delete-multi"
          disabled={selectedIds.length === 0}
          onClick={() => {
            setDeleteMode("multiple");
            setShowDeleteModal(true);
          }}
        >
          <i className="fa-solid fa-trash-can"></i> Xoá đã chọn
          {selectedIds.length > 0 && `(${selectedIds.length})`}
        </button>
      </div>
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
      <DndContext collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
        <table className="admin-table">
          <thead>
            <tr>
              <th className="col-id txt-center">
                <input
                  className="check-del"
                  type="checkbox"
                  checked={
                    rows?.length > 0 && selectedIds.length === rows.length
                  }
                  onChange={toggleSelectAll}
                />
              </th>
              <th className="col-order txt-center">Thứ tự</th>
              {getField("hinhdanhmuc") && (
                <th className="col-order txt-center">Hình</th>
              )}

              <th>Tên</th>
              {getField("active_cate_home") && (
                <th className="col-action txt-center">Hiện trang chủ</th>
              )}
              <th className="col-status txt-center">Active</th>
              <th className="txt-center">Action</th>
            </tr>
          </thead>

          <SortableContext
            items={rows.map((r) => r.id)}
            strategy={verticalListSortingStrategy}
          >
            <tbody>
              {rows.map((item) => (
                <SortableRow key={item.id} item={item}>
                  {({ attributes, listeners }) => (
                    <>
                      <td className="txt-center">
                        <input
                          className="check-del"
                          type="checkbox"
                          checked={selectedIds.includes(item.id)}
                          onChange={() => toggleSelect(item.id)}
                        />
                      </td>
                      <td className="txt-center">{item.num}</td>
                      {getField("hinhdanhmuc") && (
                        <td className="txt-center">
                          <label className="img-edit">
                            <input
                              type="file"
                              hidden
                              onChange={(e) =>
                                handleImageUpload(item.id, e.target.files[0])
                              }
                            />

                            {item.img_vn ? (
                              <>
                                <img
                                  className="img-thumbs"
                                  src={API_URL + item.img_vn}
                                  alt=""
                                />
                                <div className="img-overlay">
                                  <i className="fa-solid fa-camera"></i>
                                </div>
                              </>
                            ) : (
                              <div className="img-placeholder">
                                <i className="fa-solid fa-camera"></i>
                              </div>
                            )}
                          </label>
                        </td>
                      )}
                      <td>
                        <div
                          className="cat-name"
                          style={{ paddingLeft: item.level * 20 }}
                        >
                          {"-- ".repeat(item.level)}
                          {editingId === item.id ? (
                            <input
                              className="inline-input"
                              value={editValue}
                              autoFocus
                              onChange={(e) => setEditValue(e.target.value)}
                              onBlur={() => saveName(item.id)}
                              onKeyDown={(e) => {
                                if (e.key === "Enter") saveName(item.id);
                              }}
                            />
                          ) : (
                            <span
                              className="inline-text"
                              onClick={() => {
                                setEditingId(item.id);
                                setEditValue(item.names?.[activeTab] || "");
                              }}
                            >
                              {item.names?.[activeTab] || "-"}
                            </span>
                          )}
                        </div>
                      </td>
                      {getField("active_cate_home") && (
                        <td className="txt-center">
                          <label className="switch">
                            <input
                              type="checkbox"
                              checked={item.home == 1}
                              onChange={() => {
                                console.log("home:", item.home);
                                handleToggle(item.id, "home", item.home);
                              }}
                            />
                            <span className="slider"></span>
                          </label>
                        </td>
                      )}
                      <td className="txt-center">
                        <label className="switch">
                          <input
                            type="checkbox"
                            checked={item.active == 1}
                            onChange={() =>
                              handleToggle(item.id, "active", item.active)
                            }
                          />
                          <span className="slider"></span>
                        </label>
                      </td>
                      <td className="col-actions txt-center">
                        <div className="btn-actions">
                          <button
                            className="act btn-edit"
                            onClick={() =>
                              navigate(`/${module}/category/edit/${item.id}`)
                            }
                          >
                            <i className="fa-solid fa-pen"></i>
                          </button>

                          <button
                            className="act btn-delete"
                            onClick={() => {
                              setDeleteId(item.id);
                              setDeleteMode("single");
                              setShowDeleteModal(true);
                            }}
                          >
                            <i className="fa-solid fa-trash"></i>
                          </button>
                          <button
                            className="act drag-handle"
                            onClick={() => indentLeft(item.id)}
                          >
                            <i className="fa-solid fa-outdent"></i>
                          </button>

                          <button
                            className="act drag-handle"
                            onClick={() => indentRight(item.id)}
                          >
                            <i className="fa-solid fa-indent"></i>
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
              ))}
            </tbody>
          </SortableContext>
        </table>
      </DndContext>
      {deleteId && (
        <div className="modal">
          <div className="modal-box">
            <h3>Xác nhận xoá</h3>
            <p>Bạn có chắc muốn xóa?</p>
            <div className="modal-actions">
              <button className="btn-confirm" onClick={handleDelete}>
                <i className="fa-solid fa-trash"></i> Xoá
              </button>
              <button className="btn-cancel" onClick={() => setDeleteId(null)}>
                Hủy
              </button>
            </div>
          </div>
        </div>
      )}
      {showDeleteModal && (
        <div className="modal">
          <div className="modal-box delete-box">
            <h3>Xác nhận xoá</h3>

            <p>
              {deleteMode === "single"
                ? "Bạn có chắc muốn xoá dữ liệu này?"
                : `Bạn có chắc muốn xoá ${selectedIds.length} dòng đã chọn?`}
            </p>

            <div className="modal-actions">
              <button className="btn-confirm" onClick={confirmDelete}>
                Xoá
              </button>

              <button
                className="btn-cancel"
                onClick={() => {
                  setShowDeleteModal(false);
                  setDeleteId(null);
                  setDeleteMode(null);
                }}
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
