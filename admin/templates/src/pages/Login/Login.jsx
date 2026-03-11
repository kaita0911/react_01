import { useState } from "react";
import { useNavigate } from "react-router-dom";
//import { API_URL } from "@/config";
import "./Login.scss";

export default function Login() {
  const navigate = useNavigate();

  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const handleLogin = async () => {
    const res = await fetch(`/api/admin/login.php`, {
      method: "POST",

      headers: {
        "Content-Type": "application/json",
      },
      credentials: "include", // ⭐ CỰC KỲ QUAN TRỌNG
      body: JSON.stringify({
        username,
        password,
      }),
    });

    const data = await res.json();
    console.log(data);
    if (data.status) {
      localStorage.setItem("admin_token", "logged");
      console.log("Đã lưu token");
      navigate("/", { replace: true });
    } else {
      setError("Sai tài khoản hoặc mật khẩu !");
    }
  };
  return (
    <>
      <div className="login-page">
        <div className="login-card">
          <h2>Admin Login</h2>

          <input
            placeholder="Username"
            value={username}
            onChange={(e) => setUsername(e.target.value)}
          />

          <input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
          {error && <p className="login-error">{error}</p>}

          <button onClick={handleLogin}>Login</button>
          <p className="forgot-link" onClick={() => navigate("/forgot")}>
            Quên mật khẩu?
          </p>
        </div>
      </div>
    </>
  );
}
