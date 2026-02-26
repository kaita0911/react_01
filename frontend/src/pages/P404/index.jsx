import { Link } from "react-router-dom";
import img_404 from "@/assets/images/404.svg";
import Seo from "@/components/Seo";
import "./NotFound.scss";

function NotFound() {
  return (
    <>
      <Seo title="Trang không tồn tại" />
      <main>
        <div className="container">
          <div className="notfound">
            <img src={img_404} alt="404" />
            <h2>Trang này không tồn tại</h2>
            <p>Vui lòng kiểm tra đường dẫn của bạn hoặc quay về trang chủ.</p>
            <Link to="/" className="btn-home">
              ← Quay về trang chủ
            </Link>
          </div>
        </div>
      </main>
    </>
  );
}

export default NotFound;
