import { useEffect, useState } from "react";
import { API_URL } from "@/config";
import { Link } from "react-router-dom";
import "./Footer.scss";

function Footer() {
  const [info, setInfo] = useState(null); //gọi thongtin tu footer // ⭐ Lấy thông tin công ty
  useEffect(() => {
    fetch(`${API_URL}/api/infos.php`)
      .then((res) => res.json())
      .then((data) => setInfo(data));
  }, []);
  const [consulting, setConsulting] = useState([]); //gọi thongtin tu footer // ⭐ Lấy thông tin công ty
  useEffect(() => {
    fetch(`${API_URL}/api/consulting.php`)
      .then((res) => res.json())
      .then((data) => setConsulting(data.articles));
  }, []);
  return (
    <footer className="p-footer">
      <div className="container">
        <div className="p-footer-wrap">
          <div className="p-footer-col">
            {info?.footer?.map((f, i) => (
              <div className="contact-info" key={i}>
                <h2 className="p-footer-col__ttl">{f.name}</h2>
                <p className="item">
                  <i className="fa-solid fa-location-dot"></i> {f.address}
                </p>
                <p className="item">
                  <i className="fa-solid fa-phone"></i> Hotline: {f.hotline}
                </p>
                <p className="item">
                  <i className="fa-solid fa-envelope"></i> Email: {f.email}
                </p>
              </div>
            ))}
          </div>
          <div className="p-footer-col --tt">
            <h2 className="p-footer-col__ttl">Thông tin hữu ích</h2>
            <div className="p-footer-lst">
              {consulting.map((item) => (
                <Link
                  key={item.id}
                  to={`/${item.slug}.html`}
                  className="services-item hover"
                >
                  <i className="fa-solid fa-caret-right"></i>
                  {item.name}
                </Link>
              ))}
            </div>
          </div>
        </div>
        <p className="copyright">© Bản quyền thuộc Tấn Phát Mobile</p>
      </div>
    </footer>
  );
}

export default Footer;
