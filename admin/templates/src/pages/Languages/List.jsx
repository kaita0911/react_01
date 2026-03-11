import { useEffect, useState } from "react";

export default function Language() {
  const [data, setData] = useState([]);
  const [selectedIds, setSelectedIds] = useState([]);

  const [modal, setModal] = useState({
    type: null,
    payload: null,
  });

  const closeModal = () => setModal({ type: null, payload: null });

  // ================= FETCH =================
  const fetchData = async () => {
    try {
      const res = await fetch("/api/admin/language.php?act=list");
      const result = await res.json();

      if (result.status) setData(result.data);
    } catch {
      setModal({ type: "error", payload: "Không tải được dữ liệu" });
    }
  };
  useEffect(() => {
    const loadData = async () => {
      await fetchData();
    };

    loadData();
  }, []);

  // ================= TOGGLE ACTIVE =================
  const handleToggle = async (id, currentValue) => {
    const newValue = currentValue === 1 ? 0 : 1;

    const formData = new FormData();
    formData.append("id", id);
    formData.append("table", "language");
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
      } else {
        setModal({ type: "error", payload: result.message });
      }
    } catch {
      setModal({ type: "error", payload: "Lỗi server" });
    }
  };

  // ================= ADD =================
  const [form, setForm] = useState({
    name: "",
    code: "",
    active: 1,
  });

  const handleAdd = async () => {
    const fd = new FormData();
    fd.append("name", form.name);
    fd.append("code", form.code);
    fd.append("active", form.active);

    try {
      const res = await fetch("/api/admin/language.php?act=add", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        fetchData(); // reload list
        //setData((prev) => [result.data, ...prev]);
        setForm({ name: "", code: "", active: 1 });
        closeModal();
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
      name: item.name,
      code: item.code,
      active: item.active,
    });

    setModal({ type: "edit" });
  };

  const handleUpdate = async () => {
    const fd = new FormData();

    fd.append("id", form.id);
    fd.append("name", form.name);
    fd.append("code", form.code);
    fd.append("active", form.active);

    try {
      const res = await fetch("/api/admin/language.php?act=update", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) =>
          prev.map((item) =>
            item.id === form.id ? { ...item, ...form } : item
          )
        );

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
      const res = await fetch("/api/admin/language.php?act=delete", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) => prev.filter((i) => i.id !== id));
        closeModal();
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
      const res = await fetch("/api/admin/language.php?act=delete_multiple", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ids: selectedIds }),
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) => prev.filter((i) => !selectedIds.includes(i.id)));
        setSelectedIds([]);
        closeModal();
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

  const toggleSelectAll = (e) => {
    if (!data.length) return;

    setSelectedIds(e.target.checked ? data.map((i) => i.id) : []);
  };
  //////is_default/////
  const handleSetDefault = async (id) => {
    const fd = new FormData();
    fd.append("id", id);

    try {
      const res = await fetch("/api/admin/language.php?act=set_default", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        fetchData();
      } else {
        setModal({ type: "error", payload: result.message });
      }
    } catch {
      setModal({ type: "error", payload: "Lỗi server" });
    }
  };
  // ================= UI =================
  return (
    <main>
      <div className="action-bar">
        <button
          className="c-btn btn-add"
          onClick={() => setModal({ type: "add" })}
        >
          <i className="fa-solid fa-circle-plus"></i> Thêm ngôn ngữ
        </button>

        <button
          className="c-btn btn-delete-multi"
          disabled={!selectedIds.length}
          onClick={handleDeleteMultiple}
        >
          <i className="fa-solid fa-trash-can"></i> Xoá đã chọn
        </button>
      </div>

      <table className="admin-table">
        <thead>
          <tr>
            <th className="col-id txt-center">
              <input
                className="check-del"
                type="checkbox"
                onChange={toggleSelectAll}
              />
            </th>
            <th className="col-order txt-center">Thứ tự</th>
            <th className="col-id txt-center">ID</th>
            <th className="col-type">Code</th>
            <th>Tên ngôn ngữ</th>
            <th className="col-action txt-center">Mặc định</th>
            <th className="col-status txt-center">Active</th>
            <th className="col-action txt-center">Action</th>
          </tr>
        </thead>

        <tbody>
          {data.map((item, index) => (
            <tr key={item.id}>
              <td className="txt-center">
                <input
                  className="check-del"
                  type="checkbox"
                  checked={selectedIds.includes(item.id)}
                  onChange={() => toggleSelect(item.id)}
                />
              </td>

              <td className="txt-center">{index + 1}</td>
              <td className="txt-center">{item.id}</td>
              <td>{item.code}</td>
              <td>{item.name}</td>
              <td className="txt-center">
                <label className="switch">
                  <input
                    type="checkbox"
                    checked={item.is_default == 1}
                    onChange={() => handleSetDefault(item.id)}
                  />
                  <span className="slider"></span>
                </label>
              </td>
              <td className="txt-center">
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
                    onClick={() => handleEdit(item)}
                  >
                    <i className="fa-regular fa-pen-to-square"></i>
                  </button>
                  <button
                    type="button"
                    className={`btn-delete act ${
                      Number(item.is_default) === 1 ? "disabled" : ""
                    }`}
                    onClick={() => handleDelete(item.id)}
                    disabled={Number(item.is_default) === 1}
                    title="Delete language"
                  >
                    <i className="fa-solid fa-trash-can"></i>
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* ===== MODAL ===== */}
      {modal.type && (
        <div className="modal" onClick={closeModal}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            {/* ADD */}
            {modal.type === "add" && (
              <>
                <h3>Thêm ngôn ngữ</h3>

                <input
                  placeholder="Code (vi, en...)"
                  value={form.code}
                  onChange={(e) => setForm({ ...form, code: e.target.value })}
                />

                <input
                  placeholder="Tên ngôn ngữ"
                  value={form.name}
                  onChange={(e) => setForm({ ...form, name: e.target.value })}
                />

                <select
                  value={form.active}
                  onChange={(e) => setForm({ ...form, active: e.target.value })}
                >
                  <option value="1">Hoạt động</option>
                  <option value="0">Tắt</option>
                </select>

                <div className="modal-actions">
                  <button className="btn-confirm" onClick={handleAdd}>
                    Lưu
                  </button>
                  <button className="btn-cancel" onClick={closeModal}>
                    Hủy
                  </button>
                </div>
              </>
            )}

            {/* EDIT */}
            {modal.type === "edit" && (
              <>
                <h3>Chỉnh sửa ngôn ngữ</h3>

                <input
                  value={form.code}
                  onChange={(e) => setForm({ ...form, code: e.target.value })}
                />

                <input
                  value={form.name}
                  onChange={(e) => setForm({ ...form, name: e.target.value })}
                />

                <select
                  value={form.active}
                  onChange={(e) =>
                    setForm({ ...form, active: Number(e.target.value) })
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

            {/* DELETE */}
            {modal.type === "confirmDelete" && (
              <>
                <h3>Xác nhận xoá</h3>
                <p>Bạn có chắc muốn xoá?</p>

                <div className="modal-actions">
                  <button className="btn-confirm" onClick={confirmDelete}>
                    Xoá
                  </button>
                  <button className="btn-cancel" onClick={closeModal}>
                    Huỷ
                  </button>
                </div>
              </>
            )}

            {/* DELETE MULTI */}
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
            {modal.type === "error" && (
              <>
                <h3>Thông báo lỗi</h3>

                <p>{modal.payload}</p>

                <div className="modal-actions">
                  <button className="btn-confirm" onClick={closeModal}>
                    OK
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
