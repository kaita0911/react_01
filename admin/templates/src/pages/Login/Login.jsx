import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";

//import { API_URL } from "@/config";
import "./Login.scss";

export default function Login() {
  const navigate = useNavigate();

  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  // ⭐ Nếu đã login, mở /login thì auto redirect
  useEffect(() => {
    const token = localStorage.getItem("admin_token");
    if (token) navigate("/dashboard", { replace: true });
  }, [navigate]);

  const handleLogin = async () => {
    try {
      const res = await fetch(`/api/admin/login.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "include",
        body: JSON.stringify({ username, password }),
      });

      const data = await res.json();

      if (data.status) {
        // Lưu token và tên user
        localStorage.setItem("admin_token", data.data.token || "true");
        localStorage.setItem("admin_name", data.data.username);

        // Redirect trực tiếp sang dashboard
        navigate("/dashboard", { replace: true });
      } else {
        setError("Sai tài khoản hoặc mật khẩu !");
      }
    } catch (err) {
      console.error(err);
      setError("Có lỗi xảy ra, vui lòng thử lại !");
    }
  };
  return (
    <>
      <div className="login-page">
        <div className="login-card">
          <h2>Admin Login</h2>
          <form
            onSubmit={(e) => {
              e.preventDefault(); // tránh reload page
              handleLogin();
            }}
          >
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
            <button type="submit">Login</button>
          </form>
          <p className="forgot-link" onClick={() => navigate("/forgot")}>
            Quên mật khẩu?
          </p>
        </div>
      </div>
    </>
  );
}
