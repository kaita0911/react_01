import { useParams, useNavigate } from "react-router-dom";
import { useEffect, useState, useCallback, useMemo } from "react";
import Pagination from "@/pages/components/Pagination";
import { DndContext, closestCenter } from "@dnd-kit/core";
import { API_URL } from "@/config";
import {
  SortableContext,
  verticalListSortingStrategy,
  useSortable,
  arrayMove,
} from "@dnd-kit/sortable";

import { CSS } from "@dnd-kit/utilities";
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
export default function DynamicModule() {
  const { module } = useParams();
  const navigate = useNavigate();

  const [articles, setArticles] = useState([]);
  const [total, setTotal] = useState(0);
  const [page, setPage] = useState(1);
  const limit = 35;
  const totalPage = Math.ceil(total / limit);

  const [languages, setLanguages] = useState([]);
  const [activeTab, setActiveTab] = useState(null);
  const [editingId, setEditingId] = useState(null);
  const [editValue, setEditValue] = useState("");
  const [loading, setLoading] = useState(true);
  const [fields, setFields] = useState([]);
  ////update name
  const saveName = async (id) => {
    const fd = new FormData();

    fd.append("id", id);
    fd.append("languageid", activeTab);
    fd.append("name", editValue);

    const res = await fetch("/api/admin/articlelist.php?act=update_name", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      setArticles((prev) =>
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
  /* ================= LOAD COMPONENT ================= */

  useEffect(() => {
    const loadData = async () => {
      const resModule = await fetch(
        `/api/admin/component.php?act=comp&module=${module}`
      );
      const comp = await resModule.json();
      if (!comp.status) return;

      const compId = comp.data;

      const [fieldsRes, langRes] = await Promise.all([
        fetch(
          `/api/admin/component_fields.php?act=list&component=${compId}&target=article`
        ),
        fetch(`/api/admin/language.php?act=list`),
      ]);

      const fieldData = await fieldsRes.json();
      const langData = await langRes.json();
      if (fieldData.status) setFields(fieldData.data);
      if (langData.status) {
        const activeLang = langData.data.filter((l) => l.active == 1);
        setLanguages(activeLang);
        if (activeLang.length) setActiveTab(activeLang[0].id);
      }
      setLoading(false);
    };

    loadData();
  }, [module]);
  /* ================= FIELD SPLIT ================= */
  const fieldMap = useMemo(() => {
    const map = {};
    fields.forEach((f) => (map[f.name] = f));
    return map;
  }, [fields]);
  // ===== LOAD DATA =====
  const loadData = useCallback(() => {
    if (!module) return;

    fetch(
      `/api/admin/articlelist.php?act=list&module=${module}&page=${page}&limit=${limit}`
    )
      .then((res) => res.json())
      .then((result) => {
        // //console.log("API items:", result.data.length); // 👈 thêm dòng này

        if (result.status) {
          setArticles(result.data || []);
          setTotal(result.total || 0);
        }
      });
  }, [module, page, limit]);

  useEffect(() => {
    loadData();
  }, [loadData]);
  ////update image
  const handleImageUpload = async (id, file) => {
    const fd = new FormData();

    fd.append("id", id);
    fd.append("image", file);
    fd.append("module", module);
    const res = await fetch("/api/admin/articlelist.php?act=update_image", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      setArticles((prev) =>
        prev.map((item) =>
          item.id === id ? { ...item, img_thumb_vn: data.image } : item
        )
      );
    }
  };
  // ================= DELETE =================
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [deleteId, setDeleteId] = useState(null);
  const handleDelete = (id) => {
    setDeleteMode("single");
    setDeleteId(id);
    setShowDeleteModal(true);
  };

  // ===== XOÁ NHIỀU =====
  const [deleteMode, setDeleteMode] = useState(null);
  const [selectedIds, setSelectedIds] = useState([]);
  const handleDeleteMultiple = () => {
    if (selectedIds.length === 0) return;

    setDeleteMode("multi");
    setShowDeleteModal(true);
  };
  const toggleSelect = (id) => {
    setSelectedIds((prev) =>
      prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
    );
  };
  const toggleSelectAll = () => {
    if (selectedIds.length === (articles?.length || 0)) {
      setSelectedIds([]);
    } else {
      setSelectedIds((articles || []).map((item) => item.id));
    }
  };
  const confirmDelete = async () => {
    try {
      if (deleteMode === "single") {
        const fd = new FormData();
        fd.append("id", deleteId);

        const res = await fetch("/api/admin/articlelist.php?act=delete", {
          method: "POST",
          body: fd,
        });

        const result = await res.json();

        if (result.status) {
          if (articles.length === 1 && page > 1) {
            setPage(page - 1);
          } else {
            loadData();
          }
        }
      }

      if (deleteMode === "multi") {
        const fd = new FormData();

        selectedIds.forEach((id) => fd.append("ids[]", id));

        const res = await fetch("/api/admin/articlelist.php?act=delete_multi", {
          method: "POST",
          body: fd,
        });

        const result = await res.json();

        if (result.status) {
          setSelectedIds([]);
          loadData();
        }
      }

      setShowDeleteModal(false);
      setDeleteId(null);
      setDeleteMode(null);
    } catch (error) {
      console.error(error);
    }
  };
  if (loading) return <div>Loading...</div>;
  // ================= TOGGLE ACTIVE =================
  const handleToggle = async (id, column, currentValue) => {
    const newValue = Number(currentValue) === 1 ? 0 : 1;

    const formData = new FormData();
    formData.append("id", id);
    formData.append("table", "articlelist");
    formData.append("column", column);
    formData.append("value", newValue);

    try {
      const res = await fetch("/api/admin/update-active.php", {
        method: "POST",
        body: formData,
      });

      const result = await res.json();

      if (result.success) {
        setArticles((prev) =>
          prev.map((item) =>
            item.id === id ? { ...item, [column]: newValue } : item
          )
        );
      }
    } catch {
      alert("Lỗi server");
    }
  };
  ////XEM NHANH

  const getPreviewLink = (item) => {
    let slug = item.slug;

    if (typeof slug === "object") {
      slug = slug?.[activeTab] || Object.values(slug)[0];
    }

    if (!slug) return "#";
    return `${window.location.origin}/${slug}.html`;
  };

  // ===== SCROLL TOP =====
  const scrollTop = () => {
    const content = document.querySelector(".content");
    content?.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  };
  /////drag and drop
  const handleDragEnd = async (event) => {
    const { active, over } = event;

    if (!over || active.id === over.id) return;

    const oldIndex = articles.findIndex((m) => m.id === active.id);
    const newIndex = articles.findIndex((m) => m.id === over.id);

    const newMenus = arrayMove(articles, oldIndex, newIndex);
    const updated = newMenus.map((item, index) => ({
      ...item,
      num: total - ((page - 1) * limit + index),
    }));
    setArticles(updated);

    const formData = new FormData();

    updated.forEach((item) => {
      formData.append("id[]", item.id);
      formData.append("num[]", item.num);
    });

    await fetch("/api/admin/articlelist.php?act=reorder", {
      method: "POST",
      body: formData,
    });
  };
  return (
    <>
      <main>
        <div className="action-bar">
          <button
            className="c-btn btn-add"
            onClick={() => navigate(`/${module}/create`)}
          >
            <i className="fa-solid fa-circle-plus"></i> Thêm mới
          </button>
          <button
            className="c-btn btn-delete-multi"
            disabled={selectedIds.length === 0}
            onClick={handleDeleteMultiple}
          >
            <i className="fa-solid fa-trash-can"></i> Xoá đã chọn
            {selectedIds.length > 0 && `(${selectedIds.length})`}
          </button>
        </div>
      </main>
      <div className="page-editor">
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
        <DndContext
          collisionDetection={closestCenter}
          onDragEnd={handleDragEnd}
        >
          <table className="admin-table">
            <thead>
              <tr>
                <th className="col-id txt-center">
                  <input
                    className="check-del"
                    type="checkbox"
                    checked={
                      articles?.length > 0 &&
                      selectedIds.length === articles.length
                    }
                    onChange={toggleSelectAll}
                  />
                </th>
                <th className="col-order txt-center">Thứ tự</th>
                <th className="col-img txt-center">Hình</th>
                <th>Tên</th>
                {fieldMap.new && <th className="col-status txt-center">Mới</th>}
                {fieldMap.hot && <th className="col-status txt-center">Hot</th>}
                {fieldMap.mostview && (
                  <th className="col-status txt-center">Most View</th>
                )}
                <th className="col-status txt-center">Active</th>
                <th className="txt-center">Action</th>
              </tr>
            </thead>
            <SortableContext
              items={(articles || []).map((m) => m.id)}
              strategy={verticalListSortingStrategy}
            >
              <tbody>
                {articles?.length ? (
                  articles?.map((item) => (
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
                          <td className="txt-center">
                            {fieldMap.hinhanh && (
                              <label className="img-edit">
                                <input
                                  type="file"
                                  hidden
                                  onChange={(e) =>
                                    handleImageUpload(
                                      item.id,
                                      e.target.files[0]
                                    )
                                  }
                                />

                                {item.img_thumb_vn ? (
                                  <>
                                    <img
                                      className="img-thumbs"
                                      src={API_URL + item.img_thumb_vn}
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
                            )}
                          </td>
                          <td>
                            <div className="cat-name">
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
                          {fieldMap.new && (
                            <td className="txt-center">
                              <label className="switch">
                                <input
                                  type="checkbox"
                                  checked={Number(item.new) === 1}
                                  onChange={() =>
                                    handleToggle(item.id, "new", item.new)
                                  }
                                />
                                <span className="slider"></span>
                              </label>
                            </td>
                          )}
                          {fieldMap.hot && (
                            <td className="txt-center">
                              <label className="switch">
                                <input
                                  type="checkbox"
                                  checked={Number(item.hot) === 1}
                                  onChange={() =>
                                    handleToggle(item.id, "hot", item.hot)
                                  }
                                />
                                <span className="slider"></span>
                              </label>
                            </td>
                          )}
                          {fieldMap.mostview && (
                            <td className="txt-center">
                              <label className="switch">
                                <input
                                  type="checkbox"
                                  checked={Number(item.mostview) === 1}
                                  onChange={() =>
                                    handleToggle(
                                      item.id,
                                      "mostview",
                                      item.mostview
                                    )
                                  }
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
                                className="btn-view act"
                                onClick={() => {
                                  const url = getPreviewLink(item);
                                  console.log("PREVIEW URL:", url); // 👈 đặt ở đây
                                  window.open(url, "_blank");
                                }}
                              >
                                <i className="fa-solid fa-eye"></i>
                              </button>
                              <button
                                className="btn-edit act"
                                onClick={() =>
                                  navigate(`/${module}/edit/${item.id}`)
                                }
                              >
                                <i className="fa-solid fa-pen"></i>
                              </button>
                              <button
                                className="btn-delete act"
                                onClick={() => handleDelete(item.id)}
                              >
                                <i className="fa-solid fa-trash"></i>
                              </button>

                              <button
                                className="drag-handle act"
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
                  ))
                ) : (
                  <tr>
                    <td colSpan="3">Không có dữ liệu</td>
                  </tr>
                )}
              </tbody>
            </SortableContext>
          </table>
        </DndContext>

        <Pagination
          page={page}
          totalPage={totalPage}
          setPage={setPage}
          scrollTop={scrollTop}
        />
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
      </div>
    </>
  );
}
