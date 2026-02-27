import { useParams, useNavigate } from "react-router-dom";
import { useEffect, useState } from "react";

export default function ConfigEdit() {
  const { key } = useParams(); // lấy :key từ URL
  const navigate = useNavigate();

  const [value, setValue] = useState("");
  const [loading, setLoading] = useState(true);

  // ===== Load dữ liệu hiện tại =====
  useEffect(() => {
    fetch(`/api/infos.php?act=get&key=${key}`, {
      credentials: "include",
    })
      .then((res) => res.json())
      .then((result) => {
        if (result.status) {
          setValue(result.data);
        }
        setLoading(false);
      });
  }, [key]);

  // ===== Submit =====
  const handleSubmit = (e) => {
    e.preventDefault();

    fetch("/api/infos.php?act=update", {
      method: "POST",
      credentials: "include",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ key, value }),
    }).then(() => {
      alert("Đã cập nhật");
      navigate("/admin/Infos/general");
    });
  };

  if (loading) return <p>Đang tải...</p>;

  return (
    <div>
      <h2>Sửa cấu hình: {key}</h2>

      <form onSubmit={handleSubmit}>
        <textarea
          value={value}
          onChange={(e) => setValue(e.target.value)}
          rows={5}
          style={{ width: "100%" }}
        />

        <br />
        <br />

        <button type="submit">Lưu</button>
        <button
          type="button"
          onClick={() => navigate(-1)}
          style={{ marginLeft: 10 }}
        >
          Hủy
        </button>
      </form>
    </div>
  );
}
