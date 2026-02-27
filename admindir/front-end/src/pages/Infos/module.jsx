import { useEffect, useState } from "react";

export default function General() {
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
    fetch("/api/component.php?act=list")
      .then((res) => res.json())
      .then((result) => {
        if (result.status) setData(result.data);
        else setModal({ type: "error", payload: result.message });
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
      const res = await fetch("/api/update-active.php", {
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
      const res = await fetch("/api/component.php?act=add", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) => [result.data, ...prev]);
        setForm({ do: "", detail_name: "", active: 1 });
        closeModal();
        //setModal({ type: "success", payload: "Thêm thành công" });
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
      const res = await fetch("/api/component.php?act=delete", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) => prev.filter((i) => i.id !== id));
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
      const res = await fetch("/api/component.php?act=delete_multiple", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ids: selectedIds }),
      });

      const result = await res.json();

      if (result.status) {
        setData((prev) => prev.filter((i) => !selectedIds.includes(i.id)));
        setSelectedIds([]);
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

  // ================= UI =================
  return (
    <main>
      {/* ===== Buttons ===== */}
      <div className="flex-buttons">
        <button className="btn-add" onClick={() => setModal({ type: "add" })}>
          <i className="fa-solid fa-circle-plus"></i> Thêm mới
        </button>

        <button
          className="btn-delete-multi"
          disabled={!selectedIds.length}
          onClick={handleDeleteMultiple}
        >
          <i className="fa-solid fa-trash-can"></i> Xoá đã chọn
        </button>
      </div>

      {/* ===== Table ===== */}
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
          </tr>
        </thead>

        <tbody>
          {data.map((item, index) => (
            <tr key={item.id}>
              <td className="col-id txt-center">
                <input
                  className="check-del"
                  type="checkbox"
                  checked={selectedIds.includes(item.id)}
                  onChange={() => toggleSelect(item.id)}
                />
              </td>

              <td className="col-order txt-center">{index + 1}</td>
              <td className="col-id txt-center">{item.id}</td>
              <td className="col-type">{item.do}</td>
              <td>{item.detail_name}</td>

              <td className="col-status txt-center">
                <div className="btn-actions">
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={item.active == 1}
                      onChange={() => handleToggle(item.id, item.active)}
                    />
                    <span className="slider"></span>
                  </label>
                  <p
                    className="btn-delete act"
                    onClick={() => handleDelete(item.id)}
                  >
                    <i className="fa-solid fa-trash-can"></i>
                  </p>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

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
                    Lưu
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
                    Xoá
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

            {/* SUCCESS */}
            {modal.type === "success" && (
              <>
                <h3>✅ Thành công</h3>
                <p>{modal.payload}</p>

                <div className="modal-actions">
                  <button className="btn-ok" onClick={closeModal}>
                    OK
                  </button>
                </div>
              </>
            )}

            {/* ERROR */}
            {modal.type === "error" && (
              <>
                <h3>❌ Lỗi</h3>
                <p>{modal.payload}</p>

                <div className="modal-actions">
                  <button className="btn-ok" onClick={closeModal}>
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
