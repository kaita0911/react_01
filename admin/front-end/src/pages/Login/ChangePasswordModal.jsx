import { useState } from "react";
import { createPortal } from "react-dom";
//import { API_URL } from "@/config";

export default function ChangePasswordModal({ onClose }) {
  const [oldPass, setOldPass] = useState("");
  const [newPass, setNewPass] = useState("");
  const [msg, setMsg] = useState("");
  const [loading, setLoading] = useState(false);

  const handleChange = async () => {
    if (!oldPass || !newPass) {
      setMsg("Vui lòng nhập đầy đủ thông tin");
      return;
    }

    setLoading(true);

    try {
      const res = await fetch(`/api/admin/change-password.php`, {
        method: "POST",

        headers: {
          "Content-Type": "application/json",
        },
        credentials: "include", // ⭐ BẮT BUỘC
        body: JSON.stringify({
          old_password: oldPass,
          new_password: newPass,
        }),
      });

      if (!res.ok) {
        throw new Error("Server error: " + res.status);
      }

      const text = await res.text();

      let data;
      try {
        data = JSON.parse(text);
      } catch {
        console.error("Response không phải JSON:", text);
        throw new Error("Invalid JSON");
      }

      // ===== XỬ LÝ RESPONSE =====
      if (data.status) {
        setMsg("Đổi mật khẩu thành công!");
        setOldPass("");
        setNewPass("");

        setTimeout(onClose, 1200);
      } else {
        setMsg("Mật khẩu cũ không đúng!");
      }
    } catch (error) {
      console.error("API ERROR:", error);
      setMsg("❌ Không thể kết nối server");
    }

    setLoading(false);
  };

  return createPortal(
    <div className="modal" onClick={onClose}>
      <div className="modal-box" onClick={(e) => e.stopPropagation()}>
        <h3>Đổi mật khẩu</h3>

        <input
          type="password"
          placeholder="Mật khẩu cũ"
          value={oldPass}
          onChange={(e) => setOldPass(e.target.value)}
        />

        <input
          type="password"
          placeholder="Mật khẩu mới"
          value={newPass}
          onChange={(e) => setNewPass(e.target.value)}
        />

        {msg && <p className="message">{msg}</p>}

        <div className="modal-actions">
          <button
            className="btn-confirm"
            onClick={handleChange}
            disabled={loading}
          >
            {loading ? "Đang xử lý..." : "Xác nhận"}
          </button>

          <button className="btn-cancel" onClick={onClose}>
            Hủy
          </button>
        </div>
      </div>
    </div>,
    document.body
  );
}
