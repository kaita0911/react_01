import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
export default function General() {
  const [config, setConfig] = useState({});
  const navigate = useNavigate();
  // ================= TOGGLE ACTIVE =================
  const handleToggle = async (id, currentValue) => {
    const newValue = currentValue == 1 ? 0 : 1;

    const formData = new FormData();
    formData.append("id", id);
    formData.append("table", "infos");
    formData.append("column", "active");
    formData.append("value", newValue);

    try {
      const res = await fetch("/api/admin/update-active.php", {
        method: "POST",
        body: formData,
      });

      const result = await res.json();

      if (result.success) {
        setConfig((prev) =>
          prev.map((item) =>
            item.id === id ? { ...item, active: newValue } : item
          )
        );
      } else {
        //setModal({ type: "error", payload: result.message });
      }
    } catch {
      //setModal({ type: "error", payload: "Lỗi server" });
    }
  };

  useEffect(() => {
    fetch("/api/admin/infos.php?act=list", {
      credentials: "include",
    })
      .then((res) => res.json())
      .then((result) => {
        if (result.status) {
          setConfig(result.data);
        }
      });
  }, []);

  if (!Object.keys(config).length) return <p>Đang tải...</p>;

  return (
    <main>
      <table className="admin-table">
        <thead>
          <tr>
            <th className="col-order txt-center">Thứ tự</th>
            <th className="col-order txt-center">ID</th>
            <th>Tên</th>
            <th className="col-status txt-center">Active</th>
            <th className="col-actions txt-center">Action</th>
          </tr>
        </thead>

        <tbody>
          {config.map((item, index) => (
            <tr key={item.id}>
              <td className="txt-center">{index + 1}</td>
              <td className="txt-center">{item.id}</td>
              <td>{item.name_vn}</td>
              <td className="txt-center col-status">
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
                    onClick={() => navigate(`/infos/edit/${item.id}`)}
                  >
                    <i className="fa-regular fa-pen-to-square"></i>
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </main>
  );
}
