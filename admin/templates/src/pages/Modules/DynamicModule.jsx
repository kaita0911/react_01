import { useParams, useNavigate } from "react-router-dom";
import { useEffect, useState, useCallback } from "react";
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

  // ===== LOAD DATA =====
  const loadData = useCallback(() => {
    if (!module) return;

    fetch(
      `/api/admin/articlelist.php?act=list&module=${module}&page=${page}&limit=${limit}`
    )
      .then((res) => res.json())
      .then((result) => {
        //console.log("API items:", result.data.length); // 👈 thêm dòng này

        if (result.status) {
          setArticles(result.data || []);
          setTotal(result.total || 0);
        }
      });
  }, [module, page, limit]);

  useEffect(() => {
    loadData();
  }, [loadData]);

  // reset page khi đổi module
  useEffect(() => {
    setPage(1);
  }, [module]);
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

  // ================= TOGGLE ACTIVE =================
  const handleToggle = async (id, column, currentValue) => {
    const newValue = currentValue == 1 ? 0 : 1;

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
      console.log(result);

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
  // const handleToggle = async (id, currentValue) => {
  //   const newValue = currentValue == 1 ? 0 : 1;

  //   const formData = new FormData();
  //   formData.append("id", id);
  //   formData.append("table", "articlelist");
  //   formData.append("column", "active");
  //   formData.append("value", newValue);

  //   try {
  //     const res = await fetch("/api/admin/update-active.php", {
  //       method: "POST",
  //       body: formData,
  //     });

  //     const result = await res.json();

  //     if (result.success) {
  //       setArticles((prev) =>
  //         prev.map((item) =>
  //           item.id === id ? { ...item, active: newValue } : item
  //         )
  //       );
  //     }
  //   } catch {
  //     alert("Lỗi server");
  //   }
  // };
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

    // cập nhật num theo thứ tự mới
    const updated = newMenus.map((item, index) => ({
      ...item,
      num: (page - 1) * limit + index + 1,
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
      <div className="module-page">
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
                <th className="col-status txt-center">Mới</th>
                <th className="col-status txt-center">Hot</th>
                <th className="col-status txt-center">Most View</th>
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
                            {item.img_thumb_vn && (
                              <img
                                className="img-thumbs"
                                src={API_URL + `/${item.img_thumb_vn}`}
                                alt={item.name}
                              />
                            )}
                          </td>
                          <td>{item.name}</td>
                          <td className="txt-center">
                            <label className="switch">
                              <input
                                type="checkbox"
                                checked={item.new == 1}
                                onChange={() =>
                                  handleToggle(item.id, "new", item.new)
                                }
                              />
                              <span className="slider"></span>
                            </label>
                          </td>
                          <td className="txt-center">
                            <label className="switch">
                              <input
                                type="checkbox"
                                checked={item.hot == 1}
                                onChange={() =>
                                  handleToggle(item.id, "hot", item.hot)
                                }
                              />
                              <span className="slider"></span>
                            </label>
                          </td>
                          <td className="txt-center">
                            <label className="switch">
                              <input
                                type="checkbox"
                                checked={item.mostview == 1}
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
