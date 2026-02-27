import { useState } from "react";
//import { API_URL } from "@/config";
import { useNavigate } from "react-router-dom";
export default function ForgotPassword() {
  const [email, setEmail] = useState("");
  const [msg, setMsg] = useState("");
  const navigate = useNavigate();
  const handleSubmit = async () => {
    const res = await fetch(`/api/api/forgot.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email }),
    });

    const data = await res.json();

    if (data.status) {
      setMsg("Mật khẩu mới đã được gửi");
    } else {
      setMsg("Email không tồn tại");
    }
  };

  return (
    <div className="login-page">
      <div className="login-card">
        <h2>Quên mật khẩu</h2>
        <input
          placeholder="Nhập email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />
        {msg && <p className="login-error">{msg}</p>}
        <button onClick={handleSubmit}>Lấy lại mật khẩu</button>{" "}
        <p className="forgot-link" onClick={() => navigate("/login")}>
          ← Quay lại đăng nhập
        </p>
      </div>
    </div>
  );
}
