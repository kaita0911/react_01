import { useEffect, useState } from "react";
import Pagination from "@/pages/components/Pagination";
import "./Contact.scss";
export default function Contact() {
  const [data, setData] = useState([]);
  const [selectedIds, setSelectedIds] = useState([]);
  const [total, setTotal] = useState(0);
  const [page, setPage] = useState(1);
  // const [contacts, setContacts] = useState([]);
  const [tab, setTab] = useState("all");
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

  /* ================= DELETE MULTIPLE ================= */

  const toggleSelect = (id) => {
    setSelectedIds((prev) =>
      prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
    );
  };
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
  const formatDate = (date) => {
    const d = new Date(date);

    const time = d.toLocaleTimeString("vi-VN", {
      hour: "2-digit",
      minute: "2-digit",
    });

    const day = d.toLocaleDateString("vi-VN");

    return `${time} • ${day}`;
  };
  const totalAll = data.length;
  const totalUnread = data.filter((i) => Number(i.is_read) === 0).length;

  const filtered =
    tab === "unread" ? data.filter((i) => Number(i.is_read) === 0) : data;
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
      <div className="contact-tabs">
        <button
          className={tab === "all" ? "active" : ""}
          onClick={() => setTab("all")}
        >
          Tất cả ({totalAll})
        </button>

        <button
          className={tab === "unread" ? "active" : ""}
          onClick={() => setTab("unread")}
        >
          Chưa đọc ({totalUnread})
        </button>
      </div>
      <div className="contact-list">
        {filtered.map((item) => (
          <div
            key={item.id}
            className={`contact-row ${
              Number(item.is_read) === 0 ? "unread" : ""
            }`}
            onClick={() => handleView(item)}
          >
            <div className="contact-left">
              <input
                className="check-del"
                type="checkbox"
                checked={selectedIds.includes(item.id)}
                onChange={(e) => {
                  e.stopPropagation(); // chặn click lan lên
                  toggleSelect(item.id);
                }}
                onClick={(e) => e.stopPropagation()}
              />

              {Number(item.is_read) === 0 && <span className="dot"></span>}
            </div>

            <div className="contact-info">
              <div className="contact-name">{item.name}</div>
              <div className="contact-email">{item.email}</div>
            </div>

            <div className="contact-message">{item.message}</div>

            <div className="contact-date">{formatDate(item.dated)}</div>
          </div>
        ))}
      </div>
      <Pagination page={page} totalPage={totalPage} setPage={setPage} />
      {/* ================= MODAL ================= */}

      {modal.type && (
        <div className="modal" onClick={closeModal}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            {/* VIEW CONTACT */}
            {modal.type === "view" && (
              <div className="contact-view">
                <button className="modal-close" onClick={closeModal}>
                  <i className="fa-solid fa-xmark"></i>
                </button>
                <div className="contact-header">
                  <div className="contact-header-ttl">Chi tiết liên hệ</div>
                </div>
                <div className="contact-info-grid">
                  <div className="info-item">
                    <span className="label">Tên</span>
                    <span className="value">{modal.payload.name}</span>
                  </div>

                  <div className="info-item">
                    <span className="label">Email</span>
                    <span className="value">{modal.payload.email}</span>
                  </div>

                  <div className="info-item">
                    <span className="label">Phone</span>
                    <span className="value">{modal.payload.phone}</span>
                  </div>
                </div>
                <div className="contact-message-box">
                  <div className="message-title">Nội dung liên hệ</div>
                  <div className="message-content">{modal.payload.message}</div>
                </div>
                <span className="contact-time">
                  {formatDate(modal.payload.dated)}
                </span>
              </div>
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
