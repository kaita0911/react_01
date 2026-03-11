import { useEffect, useState } from "react";
import { useOutletContext } from "react-router-dom";
import { useNavigate } from "react-router-dom";
import { DndContext, closestCenter } from "@dnd-kit/core";
import { CSS } from "@dnd-kit/utilities";
import {
  SortableContext,
  verticalListSortingStrategy,
  useSortable,
  arrayMove,
} from "@dnd-kit/sortable";

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

export default function General() {
  const navigate = useNavigate();
  const { loadModules } = useOutletContext(); ////loadmodule
  ////kéo thả
  const handleDragEnd = async (event) => {
    const { active, over } = event;

    if (!over || active.id === over.id) return;

    const oldIndex = data.findIndex((i) => i.id === active.id);
    const newIndex = data.findIndex((i) => i.id === over.id);

    const newMenus = arrayMove(data, oldIndex, newIndex);

    // cập nhật num theo thứ tự mới
    const updated = newMenus.map((item, index) => ({
      ...item,
      num: index + 1,
    }));

    setData(updated);

    const formData = new FormData();

    updated.forEach((item) => {
      formData.append("id[]", item.id);
      formData.append("num[]", item.num);
    });

    await fetch("/api/admin/component.php?act=reorder", {
      method: "POST",
      body: formData,
    });
    loadModules(); // ⭐ thêm dòng này
  };
  const [data, setData] = useState([]);
  const [selectedIds, setSelectedIds] = useState([]);

  // ⭐ Modal system duy nhất
  const [modal, setModal] = useState({
    type: null, // add | confirmDelete | confirmDeleteMulti | success | error
    payload: null,
  });

  const closeModal = () => setModal({ type: null, payload: null });

  // ================= FETCH =================
  useEffect(() => {
    fetch("/api/admin/component.php?act=list")
      .then((res) => res.json())
      .then((result) => {
        if (result.status) {
          const sorted = result.data.sort((a, b) => a.num - b.num);
          setData(sorted);
        } else setModal({ type: "error", payload: result.message });
      })
      .catch(() => setModal({ type: "error", payload: "Lỗi tải dữ liệu" }));
  }, []);

  // ================= TOGGLE ACTIVE =================
  const handleToggle = async (id, currentValue) => {
    const newValue = currentValue == 1 ? 0 : 1;

    const formData = new FormData();
    formData.append("id", id);
    formData.append("table", "component");
    formData.append("column", "active");
    formData.append("value", newValue);

    try {
      const res = await fetch("/api/admin/update-active.php", {
        method: "POST",
        body: formData,
      });

      const result = await res.json();

      if (result.success) {
        setData((prev) =>
          prev.map((item) =>
            item.id === id ? { ...item, active: newValue } : item
          )
        );
        loadModules(); // ⭐ reload sidebar
      } else {
        setModal({ type: "error", payload: result.message });
      }
    } catch {
      setModal({ type: "error", payload: "Lỗi server" });
    }
  };

  // ================= ADD =================
  const [form, setForm] = useState({
    do: "",
    detail_name: "",
    active: 1,
  });

  const handleAdd = async () => {
    const fd = new FormData();
    fd.append("do", form.do);
    fd.append("detail_name", form.detail_name);
    fd.append("active", form.active);

    try {
      const res = await fetch("/api/admin/component.php?act=add", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) => [result.data, ...prev]);
        setForm({ do: "", detail_name: "", active: 1 });
        loadModules(); // ⭐ cập nhật sidebar
        closeModal();
        //setModal({ type: "success", payload: "Thêm thành công" });
      } else {
        setModal({ type: "error", payload: result.message });
      }
    } catch {
      setModal({ type: "error", payload: "Lỗi server" });
    }
  };
  // ================= EDIT =================
  const handleEdit = (item) => {
    setForm({
      id: item.id,
      do: item.do,
      detail_name: item.detail_name,
      active: item.active,
    });

    setModal({ type: "edit" });
  };
  const handleUpdate = async () => {
    const fd = new FormData();

    fd.append("id", form.id);
    fd.append("do", form.do);
    fd.append("detail_name", form.detail_name);
    fd.append("active", form.active);

    try {
      const res = await fetch("/api/admin/component.php?act=update", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) =>
          prev.map((item) =>
            item.id === form.id
              ? {
                  ...item,
                  do: form.do,
                  detail_name: form.detail_name,
                  active: form.active,
                }
              : item
          )
        );
        loadModules(); // ⭐ reload sidebar
        closeModal();
      } else {
        setModal({ type: "error", payload: result.message });
      }
    } catch {
      setModal({ type: "error", payload: "Lỗi server" });
    }
  };
  // ================= DELETE 1 =================
  const handleDelete = (id) => setModal({ type: "confirmDelete", payload: id });

  const confirmDelete = async () => {
    const id = modal.payload;

    const fd = new FormData();
    fd.append("id", id);

    try {
      const res = await fetch("/api/admin/component.php?act=delete", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) => prev.filter((i) => i.id !== id));
        loadModules(); // ⭐ reload sidebar
        closeModal();
        //setModal({ type: "success", payload: "Đã xoá" });
      } else {
        setModal({ type: "error", payload: result.message });
      }
    } catch {
      setModal({ type: "error", payload: "Lỗi server" });
    }
  };

  // ================= DELETE MULTI =================
  const handleDeleteMultiple = () => setModal({ type: "confirmDeleteMulti" });

  const confirmDeleteMultiple = async () => {
    try {
      const res = await fetch("/api/admin/component.php?act=delete_multiple", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ids: selectedIds }),
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) => prev.filter((i) => !selectedIds.includes(i.id)));
        setSelectedIds([]);
        loadModules(); // ⭐ reload sidebar
        closeModal();
        //setModal({ type: "success", payload: "Đã xoá" });
      } else {
        setModal({ type: "error", payload: result.message });
      }
    } catch {
      setModal({ type: "error", payload: "Lỗi server" });
    }
  };

  // ================= SELECT =================
  const toggleSelect = (id) =>
    setSelectedIds((prev) =>
      prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
    );

  const toggleSelectAll = (e) =>
    e.target.checked
      ? setSelectedIds(data.map((i) => i.id))
      : setSelectedIds([]);
  // ================= edites =================
  const handleEditDetail = (id) => {
    navigate(`/component/edit/${id}`);
  };
  // ================= UI =================
  return (
    <main>
      {/* ===== Buttons ===== */}
      <div className="action-bar">
        <button
          className="c-btn btn-add"
          onClick={() => setModal({ type: "add" })}
        >
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
              <th className="txt-center">
                <input
                  className="check-del"
                  type="checkbox"
                  onChange={toggleSelectAll}
                />
              </th>
              <th className="col-order txt-center">Thứ tự</th>
              <th className="col-id txt-center">ID</th>
              <th className="col-type">Type</th>
              <th>Tên</th>
              <th className="col-status txt-center">Active</th>
              <th className="col-action txt-center">Action</th>
            </tr>
          </thead>
          <SortableContext
            items={data.map((m) => m.id)}
            strategy={verticalListSortingStrategy}
          >
            <tbody>
              {data.map((item) => (
                <SortableRow key={item.id} menu={item}>
                  {({ attributes, listeners }) => (
                    <>
                      <td className="col-id txt-center">
                        <input
                          className="check-del"
                          type="checkbox"
                          checked={selectedIds.includes(item.id)}
                          onChange={() => toggleSelect(item.id)}
                        />
                      </td>

                      <td className="col-order txt-center">{item.num}</td>
                      <td className="col-id txt-center">{item.id}</td>
                      <td className="col-type">{item.do}</td>
                      <td>{item.detail_name}</td>

                      <td className="col-status txt-center">
                        <label className="switch">
                          <input
                            type="checkbox"
                            checked={item.active == 1}
                            onChange={() => handleToggle(item.id, item.active)}
                          />
                          <span className="slider"></span>
                        </label>
                      </td>

                      <td className="col-actions txt-center">
                        <div className="btn-actions">
                          <button
                            className="btn-edit act"
                            onClick={() => handleEditDetail(item.id)}
                          >
                            <i className="fa-solid fa-pen"></i>
                          </button>
                          {/* <button
                            className="btn-edit act"
                            onClick={() => handleEdit(item)}
                          >
                            <i className="fa-regular fa-pen-to-square"></i>
                          </button> */}

                          <button
                            className="btn-delete act"
                            onClick={() => handleDelete(item.id)}
                          >
                            <i className="fa-solid fa-trash-can"></i>
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
              ))}
            </tbody>
          </SortableContext>
        </table>
      </DndContext>

      {/* ===== MODAL SYSTEM ===== */}
      {modal.type && (
        <div className="modal" onClick={closeModal}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            {/* ADD */}
            {modal.type === "add" && (
              <>
                <h3>Thêm Component</h3>

                <input
                  placeholder="Type"
                  value={form.do}
                  onChange={(e) => setForm({ ...form, do: e.target.value })}
                />

                <input
                  placeholder="Tên"
                  value={form.detail_name}
                  onChange={(e) =>
                    setForm({
                      ...form,
                      detail_name: e.target.value,
                    })
                  }
                />

                <select
                  value={form.active}
                  onChange={(e) =>
                    setForm({
                      ...form,
                      active: e.target.value,
                    })
                  }
                >
                  <option value="1">Hoạt động</option>
                  <option value="0">Tắt</option>
                </select>

                <div className="modal-actions">
                  <button className="btn-confirm" onClick={handleAdd}>
                    <i className="fa-regular fa-floppy-disk"></i> Lưu
                  </button>
                  <button className="btn-cancel" onClick={closeModal}>
                    Hủy
                  </button>
                </div>
              </>
            )}

            {/* CONFIRM DELETE 1 */}
            {modal.type === "confirmDelete" && (
              <>
                <h3>Xác nhận xoá</h3>
                <p>Bạn có chắc muốn xoá?</p>

                <div className="modal-actions">
                  <button className="btn-confirm" onClick={confirmDelete}>
                    <i className="fa-solid fa-trash"></i> Xoá
                  </button>
                  <button className="btn-cancel" onClick={closeModal}>
                    Huỷ
                  </button>
                </div>
              </>
            )}

            {/* CONFIRM DELETE MULTI */}
            {modal.type === "confirmDeleteMulti" && (
              <>
                <h3>Xác nhận xoá</h3>
                <p>Xoá các mục đã chọn?</p>

                <div className="modal-actions">
                  <button
                    className="btn-confirm"
                    onClick={confirmDeleteMultiple}
                  >
                    Xoá
                  </button>
                  <button className="btn-cancel" onClick={closeModal}>
                    Huỷ
                  </button>
                </div>
              </>
            )}
            {modal.type === "edit" && (
              <>
                <h3>Chỉnh sửa Component</h3>

                <input
                  placeholder="Type"
                  value={form.do}
                  onChange={(e) => setForm({ ...form, do: e.target.value })}
                />

                <input
                  placeholder="Tên"
                  value={form.detail_name}
                  onChange={(e) =>
                    setForm({
                      ...form,
                      detail_name: e.target.value,
                    })
                  }
                />

                <select
                  value={form.active}
                  onChange={(e) =>
                    setForm({
                      ...form,
                      active: e.target.value,
                    })
                  }
                >
                  <option value="1">Hoạt động</option>
                  <option value="0">Tắt</option>
                </select>

                <div className="modal-actions">
                  <button className="btn-confirm" onClick={handleUpdate}>
                    Cập nhật
                  </button>
                  <button className="btn-cancel" onClick={closeModal}>
                    Hủy
                  </button>
                </div>
              </>
            )}
          </div>
        </div>
      )}
    </main>
  );
}
