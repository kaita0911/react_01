import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";

export default function Edit() {
  const navigate = useNavigate();
  const { id } = useParams();

  const [form, setForm] = useState({});
  const [loading, setLoading] = useState(true);

  // ===== danh sách checkbox =====
  const attrs = [
    { name: "hinhanh", label: "Hình ảnh" },
    { name: "short", label: "Mô tả vắn tắt" },
    { name: "des", label: "Mô tả chi tiết" },
    { name: "metatag", label: "Meta tag" },
    { name: "nhieuhinh", label: "Nhiều hình" },
    { name: "masp", label: "Mã sản phẩm" },
    { name: "price", label: "Có giá" },
    { name: "priceold", label: "Giá cũ" },
    { name: "mausac", label: "Màu sắc" },
    { name: "kichthuoc", label: "Kích thước" },
    { name: "voucher", label: "Voucher" },
    { name: "phiship", label: "Phí ship" },
    { name: "new", label: "Mới" },
    { name: "hot", label: "Nổi bật" },
    { name: "mostview", label: "Xem nhiều" },
    { name: "viewed", label: "Đã xem" },
    { name: "active", label: "Show" },
    { name: "link_out", label: "Link ngoài" },
    { name: "attribute", label: "Thuộc tính" },
  ];

  const categoryAttrs = [
    { name: "nhomcon", label: "Danh mục" },
    { name: "danhmuchome", label: "Hiện trang chủ" },
    { name: "hinhdanhmuc", label: "Hình danh mục" },
    { name: "motadanhmuc", label: "Mô tả danh mục" },
    { name: "brand", label: "Thương hiệu" },
  ];

  const moduleAttrs = [
    { name: "hinhmodule", label: "Hình ảnh" },
    { name: "motamodule", label: "Mô tả chung" },
  ];

  const allCheckbox = [
    ...attrs.map((a) => a.name),
    ...categoryAttrs.map((a) => a.name),
    ...moduleAttrs.map((a) => a.name),
  ];

  // ===== load data =====
  useEffect(() => {
    fetch(`/api/admin/component.php?act=detail&id=${id}`)
      .then((res) => res.json())
      .then((result) => {
        if (result.status) {
          const data = { ...result.data };

          allCheckbox.forEach((name) => {
            data[name] = Number(data[name]) || 0;
          });

          setForm(data);
        }

        setLoading(false);
      });
  }, [id]);

  // ===== change input =====
  const handleChange = (name, value) => {
    setForm((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  // ===== checkbox =====
  const handleCheckbox = (name) => {
    setForm((prev) => ({
      ...prev,
      [name]: prev[name] === 1 ? 0 : 1,
    }));
  };

  // ===== submit =====
  const handleSubmit = async (e) => {
    e.preventDefault();

    const fd = new FormData();
    fd.append("id", id);

    Object.keys(form).forEach((k) => {
      fd.append(k, form[k]);
    });

    const res = await fetch("/api/admin/component.php?act=update", {
      method: "POST",
      body: fd,
    });

    const result = await res.json();

    console.log(result);

    if (result.status) {
      navigate("/component");
    }
  };
  if (loading) return <p>Loading...</p>;

  return (
    <div className="component-edit">
      <form onSubmit={handleSubmit}>
        {/* ACTION */}
        <div className="action-bar">
          <button type="submit" className="c-btn btn-save">
            <i className="fa fa-save"></i> Lưu
          </button>

          <button
            type="button"
            className="c-btn btn-cancel"
            onClick={() => navigate("/component")}
          >
            Quay lại
          </button>
        </div>

        {/* BASIC */}
        <fieldset>
          <legend>Thông tin cơ bản</legend>

          <div className="item">
            <div className="title">Tiêu đề</div>
            <input
              className="input-text"
              value={form.name || ""}
              onChange={(e) => handleChange("name", e.target.value)}
            />
          </div>

          <div className="item">
            <div className="title">TYPE</div>
            <input
              className="input-text"
              value={form.do || ""}
              onChange={(e) => handleChange("do", e.target.value)}
            />
          </div>

          <div className="item">
            <div className="title">Phân trang</div>
            <input
              className="input-text"
              value={form.phantrang || ""}
              onChange={(e) => handleChange("phantrang", e.target.value)}
            />
          </div>
        </fieldset>

        {/* ATTR */}
        <fieldset>
          <legend>Thuộc tính riêng</legend>

          <div className="box-feature">
            {attrs.map((attr) => (
              <label key={attr.name} className="feature-item">
                <span>
                  <small>{attr.name}</small>
                  {attr.label}
                </span>

                <input
                  type="checkbox"
                  checked={form[attr.name] === 1}
                  onChange={() => handleCheckbox(attr.name)}
                />
              </label>
            ))}
          </div>
        </fieldset>

        {/* CATEGORY */}
        <fieldset>
          <legend>Thuộc tính DANH MỤC</legend>

          <div className="box-feature">
            {categoryAttrs.map((attr) => (
              <label key={attr.name} className="feature-item">
                <span>
                  <small>{attr.name}</small>
                  {attr.label}
                </span>

                <input
                  type="checkbox"
                  checked={form[attr.name] === 1}
                  onChange={() => handleCheckbox(attr.name)}
                />
              </label>
            ))}
          </div>
        </fieldset>

        {/* MODULE */}
        <fieldset>
          <legend>Thuộc tính module</legend>

          <div className="box-feature">
            {moduleAttrs.map((attr) => (
              <label key={attr.name} className="feature-item">
                <span>
                  <small>{attr.name}</small>
                  {attr.label}
                </span>

                <input
                  type="checkbox"
                  checked={form[attr.name] === 1}
                  onChange={() => handleCheckbox(attr.name)}
                />
              </label>
            ))}
          </div>
        </fieldset>
      </form>
    </div>
  );
}
