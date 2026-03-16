import { useEffect, useState } from "react";
import Pagination from "@/pages/components/Pagination";
export default function Contact() {
  const [data, setData] = useState([]);
  const [selectedIds, setSelectedIds] = useState([]);
  const [total, setTotal] = useState(0);
  const [page, setPage] = useState(1);
  const limit = 25;
  const totalPage = Math.ceil(total / limit);
  const [modal, setModal] = useState({
    type: null,
    payload: null,
  });

  const closeModal = () => setModal({ type: null, payload: null });

  /* ================= FETCH ================= */

  useEffect(() => {
    fetch(`/api/admin/contact.php?act=list&page=${page}&limit=${limit}`)
      .then((res) => res.json())
      .then((result) => {
        if (result.status) {
          setData(result.data);
          setTotal(result.total || 0);
        } else setModal({ type: "error", payload: result.message });
      })
      .catch(() => setModal({ type: "error", payload: "Lỗi tải dữ liệu" }));
  }, [page, limit]);

  /* ================= DELETE 1 ================= */

  const handleDelete = (id) => {
    setModal({ type: "confirmDelete", payload: id });
  };

  const confirmDelete = async () => {
    const id = modal.payload;

    const fd = new FormData();
    fd.append("id", id);

    try {
      const res = await fetch("/api/admin/contact.php?act=delete", {
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

  /* ================= DELETE MULTIPLE ================= */

  const handleDeleteMultiple = () => {
    setModal({ type: "confirmDeleteMulti" });
  };

  const confirmDeleteMultiple = async () => {
    try {
      const res = await fetch("/api/admin/contact.php?act=delete_multiple", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
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

  /* ================= SELECT ================= */

  const toggleSelect = (id) => {
    setSelectedIds((prev) =>
      prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
    );
  };

  const toggleSelectAll = (e) => {
    if (e.target.checked) {
      setSelectedIds(data.map((i) => i.id));
    } else {
      setSelectedIds([]);
    }
  };

  /* ================= VIEW ================= */

  const handleView = async (item) => {
    const fd = new FormData();
    fd.append("id", item.id);

    await fetch("/api/admin/contact.php?act=mark_read", {
      method: "POST",
      body: fd,
    });

    setData((prev) =>
      prev.map((i) => (i.id === item.id ? { ...i, is_read: 1 } : i))
    );

    setModal({ type: "view", payload: item });
  };

  /* ================= UI ================= */

  return (
    <main>
      <div className="action-bar">
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
            <th>Tên</th>
            <th>Email</th>
            <th>Phone</th>
            <th className="txt-center">Action</th>
          </tr>
        </thead>

        <tbody>
          {data.map((item, index) => (
            <tr
              key={item.id}
              className={Number(item.is_read) === 0 ? "contact-unread" : ""}
            >
              <td className="txt-center">
                <input
                  className="check-del"
                  type="checkbox"
                  checked={selectedIds.includes(item.id)}
                  onChange={() => toggleSelect(item.id)}
                />
              </td>

              <td className="txt-center">{index + 1}</td>
              <td>{item.name}</td>
              <td>{item.email}</td>
              <td>{item.phone}</td>

              <td className="col-actions txt-center">
                <div className="btn-actions">
                  <button
                    className="btn-view act"
                    onClick={() => handleView(item)}
                  >
                    <i className="fa-solid fa-eye"></i>
                  </button>

                  <button
                    className="btn-delete act"
                    onClick={() => handleDelete(item.id)}
                  >
                    <i className="fa-solid fa-trash-can"></i>
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
      <Pagination page={page} totalPage={totalPage} setPage={setPage} />
      {/* ================= MODAL ================= */}

      {modal.type && (
        <div className="modal" onClick={closeModal}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            {/* VIEW CONTACT */}
            {modal.type === "view" && (
              <>
                <h3>Chi tiết liên hệ</h3>

                <p>
                  <b>Tên:</b> {modal.payload.name}
                </p>

                <p>
                  <b>Email:</b> {modal.payload.email}
                </p>

                <p>
                  <b>Phone:</b> {modal.payload.phone}
                </p>

                <p>
                  <b>Nội dung:</b>
                </p>

                <div className="contact-message">{modal.payload.message}</div>

                <div className="modal-actions">
                  <button className="btn-cancel" onClick={closeModal}>
                    Đóng
                  </button>
                </div>
              </>
            )}

            {/* DELETE */}
            {modal.type === "confirmDelete" && (
              <>
                <h3>Xác nhận xoá</h3>
                <p>Bạn có chắc muốn xoá contact này?</p>

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

            {/* DELETE MULTI */}
            {modal.type === "confirmDeleteMulti" && (
              <>
                <h3>Xác nhận xoá</h3>
                <p>Xoá các contact đã chọn?</p>

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
          </div>
        </div>
      )}
    </main>
  );
}
