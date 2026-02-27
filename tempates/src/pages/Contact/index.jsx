import { useEffect, useState } from "react";
import Seo from "@/components/Seo";
import { API_URL } from "@/config";
import Breadcrumb from "@/router/Breadcrumb";
import "./Contact.scss";

function Contact({ data }) {
  const [form, setForm] = useState({
    name: "",
    phone: "",
    email: "",
    address: "",
    message: "",
  });

  const [info, setInfo] = useState(null); //gọi thongtin tu footer
  const [showPopup, setShowPopup] = useState(false);
  const [loading, setLoading] = useState(false);

  // ⭐ Lấy thông tin công ty
  useEffect(() => {
    fetch(`${API_URL}/api/infos.php`)
      .then((res) => res.json())
      .then((data) => setInfo(data));
  }, []);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setLoading(true); // ⭐ bật loading
    fetch(`${API_URL}/api/contact.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    })
      .then((res) => res.json())
      .then((res) => {
        setLoading(false); // ⭐ tắt loading
        if (res.success) {
          setShowPopup(true);
          setForm({ name: "", phone: "", email: "", address: "", message: "" });

          // tự tắt sau 3s
          setTimeout(() => setShowPopup(false), 2000);
        } else {
          alert("Gửi thất bại!");
        }
      });
  };

  return (
    <main>
      <div className="container">
        <Breadcrumb comp="4" article={data} />
        {info && (
          <Seo
            title={info.seo?.name_vn}
            description={info.seo?.desc}
            keywords={info.seo?.keyword}
          />
        )}
        <div className="p-contact">
          <div className="p-contact__meta">
            <h1 className="ttl01">Liên hệ</h1>

            {info?.footer?.map((f, i) => (
              <div className="contact-info" key={i}>
                <p>
                  <b>Địa chỉ:</b> {f.address}
                </p>
                <p>
                  <b>Điện thoại:</b> {f.hotline}
                </p>
                <p>
                  <b>Email:</b> {f.email}
                </p>
                <div
                  className="map"
                  dangerouslySetInnerHTML={{ __html: f.map }}
                />
              </div>
            ))}
          </div>
          {/* //<p>showPopup: {String(showPopup)}</p> */}
          <form className="contact-form" onSubmit={handleSubmit}>
            <input
              name="name"
              placeholder="Họ tên"
              value={form.name}
              onChange={handleChange}
              required
            />

            <input
              name="phone"
              placeholder="Số điện thoại"
              value={form.phone}
              onChange={handleChange}
              required
            />

            <input
              name="email"
              type="email"
              placeholder="Email"
              value={form.email}
              onChange={handleChange}
            />
            <input
              name="address"
              placeholder="Địa chỉ"
              value={form.address}
              onChange={handleChange}
            />
            <textarea
              name="message"
              placeholder="Nội dung"
              value={form.message}
              onChange={handleChange}
              required
            />
            <button type="submit">
              Gửi liên hệ <i className="fa-regular fa-paper-plane"></i>
            </button>
          </form>
          {status && <p className="form-status">{status}</p>}
        </div>
      </div>
      {/* <span className="loader"></span> */}
      {loading && <span className="loading"></span>}
      {showPopup && (
        <div className="popup-modal">
          <div className="popup-modal-content">
            <div className="popup-icon">✅</div>
            <p>Gửi liên hệ thành công!</p>
          </div>
        </div>
      )}
    </main>
  );
}

export default Contact;
