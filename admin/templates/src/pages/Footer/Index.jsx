import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";

export default function Footer() {
  const [footer, setFooter] = useState([]);
  const [loading, setLoading] = useState(true);

  const navigate = useNavigate();

  // ================= GET FOOTER =================
  const fetchFooter = async () => {
    setLoading(true);

    try {
      const res = await fetch(`/api/admin/footer.php?act=list`, {
        credentials: "include",
      });

      const result = await res.json();

      if (result.success) {
        setFooter(result.data);
      } else {
        setFooter([]);
      }
    } catch (error) {
      console.error("Fetch footer error:", error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchFooter();
  }, []);

  // ================= TOGGLE ACTIVE =================
  const handleToggle = async (id, currentValue) => {
    const newValue = currentValue == 1 ? 0 : 1;

    const formData = new FormData();
    formData.append("id", id);
    formData.append("table", "footer");
    formData.append("column", "active");
    formData.append("value", newValue);

    try {
      const res = await fetch("/api/admin/update-active.php", {
        method: "POST",
        body: formData,
        credentials: "include",
      });

      const result = await res.json();

      if (result.success) {
        setFooter((prev) =>
          prev.map((item) =>
            item.id === id ? { ...item, active: newValue } : item
          )
        );
      }
    } catch (error) {
      console.error("Toggle error:", error);
    }
  };
  // ================= DELETE =================
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [deleteId, setDeleteId] = useState(null);
  const handleDelete = (id) => {
    setDeleteId(id);
    setShowDeleteModal(true);
  };
  const confirmDelete = async () => {
    if (!deleteId) return;

    try {
      const res = await fetch(
        `/api/admin/footer.php?act=delete&id=${deleteId}`,
        {
          method: "GET",
          credentials: "include",
        }
      );

      const result = await res.json();

      if (result.success) {
        setFooter((prev) => prev.filter((item) => item.id !== deleteId));
      }

      setShowDeleteModal(false);
      setDeleteId(null);
    } catch (error) {
      console.error("Delete error:", error);
    }
  };
  if (loading) return <p>Đang tải...</p>;

  return (
    <main>
      <div className="action-bar">
        <button
          className="c-btn btn-add"
          onClick={() => navigate(`/footer/edit/new`)}
        >
          <i className="fa-solid fa-circle-plus"></i> Thêm mới
        </button>
      </div>

      <table className="admin-table footer">
        <thead>
          <tr>
            <th className="txt-center">Thứ tự</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Hotline</th>
            <th className="txt-center">Active</th>
            <th className="txt-center">Action</th>
          </tr>
        </thead>

        <tbody>
          {footer.length === 0 && (
            <tr>
              <td colSpan="6" className="txt-center">
                Chưa có dữ liệu
              </td>
            </tr>
          )}

          {footer.map((item, index) => (
            <tr key={item.id}>
              <td className="txt-center">{index + 1}</td>

              <td>{item.name}</td>
              <td>{item.email}</td>
              <td>{item.hotline}</td>

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

              <td className="txt-center col-actions">
                <div className="btn-actions">
                  <button
                    className="btn-edit act"
                    onClick={() => navigate(`/footer/edit/${item.id}`)}
                  >
                    <i className="fa-regular fa-pen-to-square"></i>
                  </button>
                  <button
                    className="btn-delete act"
                    onClick={() => {
                      handleDelete(item.id);
                    }}
                  >
                    <i className="fa-solid fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
      {showDeleteModal && (
        <div className="modal">
          <div className="modal-box delete-box">
            <h3>Xác nhận xoá</h3>
            <p>Bạn có chắc muốn xoá dữ liệu này?</p>

            <div className="modal-actions">
              <button className="btn-confirm" onClick={confirmDelete}>
                Xoá
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
    </main>
  );
}
