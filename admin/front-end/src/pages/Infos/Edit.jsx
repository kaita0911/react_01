import { useParams, useNavigate } from "react-router-dom";
import { useEffect, useState } from "react";
import UploadImage from "@/pages/components/UploadImage";

export default function InfoEdit() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [data, setData] = useState(null);
  const [form, setForm] = useState({});
  const [fileMap, setFileMap] = useState({});
  const [loading, setLoading] = useState(false);
  const [toast, setToast] = useState(null);

  // ===== LOAD DATA =====
  useEffect(() => {
    const fetchData = async () => {
      const res = await fetch(`/api/admin/infos.php?act=detail&id=${id}`, {
        credentials: "include",
      });

      const result = await res.json();

      if (result.status) {
        setData(result.data);
        setForm(result.data); // ⭐ lưu toàn bộ field
      }
    };

    fetchData();
  }, [id]);

  // ===== FIELD THEO ID =====
  const fieldById = {
    1: ["img_thumb_vn"], // logo
    2: ["domain"],
    6: ["plain_text_vn"],
    5: ["domain", "phone"],
    7: ["facebook", "youtube", "tiktok", "linkedin", "instagram", "printest"],
    8: ["content_vn"],
    15: ["name_vn", "keyword", "desc"],
    17: ["plain_text_vn"],
    18: ["plain_text_vn"],
  };

  const fields = fieldById[id] || [];
  const labelMap = {
    5: {
      domain: "Hotline",
      phone: "Zalo",
    },
    8: {
      content_vn: "Nội dung",
    },
    15: {
      name_vn: "Title",
      desc: "Meta description",
    },
    17: {
      plain_text_vn: "Nội dung",
    },
    18: {
      plain_text_vn: "Nội dung",
    },
  };
  // ===== SAVE =====
  const handleSave = async () => {
    setLoading(true);

    const formData = new FormData();
    formData.append("id", id);

    // append text fields
    fields.forEach((key) => {
      formData.append(key, form[key] || "");
    });

    // append image fields
    Object.keys(fileMap).forEach((key) => {
      formData.append(key, fileMap[key]);
    });

    const res = await fetch("/api/admin/infos.php?act=update", {
      method: "POST",
      body: formData,
      credentials: "include",
    });

    const result = await res.json();
    setLoading(false);

    if (result.status) {
      setToast({ type: "success", message: "Lưu thành công !" });
      setTimeout(() => navigate(-1), 600);
    } else {
      setToast({ type: "error", message: "Lỗi khi lưu ❌" });
    }
  };

  if (!data) return <p>Đang tải...</p>;

  return (
    <main>
      <div className="c-edit">
        <h2 className="edit-title">{data.name_vn}</h2>

        {fields.map((key) => (
          <>
            <label className="form-label">{labelMap[id]?.[key] || key}</label>
            <div className={`form-group --cuz-${id}`} key={key}>
              {/* IMAGE FIELD */}
              {key.includes("img") ? (
                <UploadImage
                  currentImage={form[key]}
                  onChange={(selectedFile) =>
                    setFileMap({ ...fileMap, [key]: selectedFile })
                  }
                />
              ) : Number(id) === 17 || Number(id) === 18 ? (
                // 👉 Nếu id = 17 thì dùng textarea
                <textarea
                  value={form[key] || ""}
                  onChange={(e) => setForm({ ...form, [key]: e.target.value })}
                  className="form-textarea"
                />
              ) : (
                <input
                  value={form[key] || ""}
                  onChange={(e) => setForm({ ...form, [key]: e.target.value })}
                  className="form-input"
                />
              )}
            </div>
          </>
        ))}

        <div className="flex-buttons">
          <button
            className="c-btn btn-save"
            onClick={handleSave}
            disabled={loading}
          >
            <i className="fa-regular fa-floppy-disk"></i>{" "}
            {loading ? "Đang lưu..." : "Lưu"}
          </button>

          <button className="c-btn btn-cancel" onClick={() => navigate(-1)}>
            Hủy
          </button>
        </div>
      </div>

      {toast && <div className={`toast ${toast.type}`}>{toast.message}</div>}
    </main>
  );
}
