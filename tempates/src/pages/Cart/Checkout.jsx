import { useCart } from "@/context/CartContext";
import { API_URL } from "@/config";
import Seo from "@/components/Seo";
import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { useRef } from "react";
import "./Cart.scss";

function Checkout() {
  const { cart, totalPrice, totalQty, clearCart } = useCart();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const [showSuccess, setShowSuccess] = useState(false);
  const [provinces, setProvinces] = useState([]);
  const [districts, setDistricts] = useState([]);
  const [wards, setWards] = useState([]);
  //focus input
  const nameRef = useRef(null);
  const phoneRef = useRef(null);
  const addressRef = useRef(null);
  const provinceRef = useRef(null);
  const districtRef = useRef(null);
  const wardRef = useRef(null);
  const [form, setForm] = useState({
    province: "",
    district: "",
    ward: "",
    name: "",
    phone: "",
    email: "",
    address: "",
    note: "",
    payment: "",
  });
  useEffect(() => {
    fetch("https://provinces.open-api.vn/api/p/")
      .then((res) => res.json())
      .then((data) => setProvinces(data));
  }, []);
  const handleProvince = (code) => {
    setForm((prev) => ({
      ...prev,
      province: code,
      district: "",
      ward: "",
    }));

    fetch(`https://provinces.open-api.vn/api/p/${code}?depth=2`)
      .then((res) => res.json())
      .then((data) => setDistricts(data.districts));

    setWards([]);
  };
  const handleDistrict = (code) => {
    setForm((prev) => ({
      ...prev,
      district: code,
      ward: "",
    }));

    fetch(`https://provinces.open-api.vn/api/d/${code}?depth=2`)
      .then((res) => res.json())
      .then((data) => setWards(data.wards));
  };
  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const validate = () => {
    const newErrors = {};
    const phoneRegex = /^(03|05|07|08|09)\d{8}$/;
    if (!form.name) {
      newErrors.name = "Vui lòng nhập họ tên";
      nameRef.current?.focus();
    } else if (!form.phone) {
      newErrors.phone = "Vui lòng nhập số điện thoại";
      phoneRef.current?.focus();
    } else if (!phoneRegex.test(form.phone)) {
      newErrors.phone = "Số điện thoại không hợp lệ";
      phoneRef.current?.focus();
    } else if (!form.address) {
      newErrors.address = "Vui lòng nhập địa chỉ";
      addressRef.current?.focus();
    } else if (!form.province) {
      newErrors.province = "Chọn tỉnh/thành";
      provinceRef.current?.focus();
    } else if (!form.district) {
      newErrors.district = "Chọn quận/huyện";
      districtRef.current?.focus();
    } else if (!form.ward) {
      newErrors.ward = "Chọn phường/xã";
      wardRef.current?.focus();
    } else if (!form.payment) {
      newErrors.payment = "Vui lòng chọn phương thức thanh toán";
    }
    setErrors(newErrors);

    return Object.keys(newErrors).length === 0;
  };
  const handleOrder = async () => {
    if (!validate()) return;

    const orderData = {
      customer: form,
      products: cart,
      total: totalPrice,
    };

    try {
      setLoading(true); // ⭐ bật loading
      const res = await fetch(`${API_URL}/api/order.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(orderData),
      });

      const data = await res.json();
      setLoading(false);
      if (data.success) {
        clearCart();
        setShowSuccess(true);
        setTimeout(() => {
          setShowSuccess(false);
          navigate("/");
        }, 3000);
      } else {
        alert("Có lỗi xảy ra!");
      }
    } catch (err) {
      console.error(err);
      alert("Không kết nối được server!");
    }
  };

  return (
    <>
      <Seo title="Thanh toán" />
      <main>
        <div className="container">
          <h1 className="ttl-cart">Thanh toán</h1>

          <div className="cart-box">
            {/* ===== LEFT — FORM ===== */}
            <div className="cart-box-left --flex">
              <div className="cart-pay cart-bd">
                <div className="cart-pay-ttl">
                  <i className="fa-solid fa-user"></i> Thông tin khách hàng
                </div>
                <div className="input-group">
                  <input
                    ref={nameRef}
                    type="text"
                    name="name"
                    value={form.name}
                    onChange={handleChange}
                    placeholder="Nguyễn Văn A"
                  />
                  {errors.name && <p className="error-text">{errors.name}</p>}
                </div>

                <div className="input-group">
                  <input
                    ref={phoneRef}
                    type="tel"
                    name="phone"
                    value={form.phone}
                    onChange={(e) => {
                      let value = e.target.value;
                      value = value.replace(/\D/g, "");
                      value = value.slice(0, 10);
                      setForm((prev) => ({ ...prev, phone: value }));
                    }}
                    placeholder="Điện thoại"
                  />
                  {errors.phone && <p className="error-text">{errors.phone}</p>}
                </div>

                {/* <div className="input-group">
              <textarea
                name="email"
                value={form.email}
                onChange={handleChange}
              />
            </div> */}

                <div className="input-group">
                  <input
                    ref={addressRef}
                    type="text"
                    name="address"
                    value={form.address}
                    onChange={handleChange}
                    placeholder="Địa chỉ"
                  />
                  {errors.address && (
                    <p className="error-text">{errors.address}</p>
                  )}
                </div>

                <div className="input-group">
                  <textarea
                    name="note"
                    value={form.note}
                    onChange={handleChange}
                    placeholder="Nội dung"
                  />
                </div>
                <div className="input-address">
                  <div className="select-address">
                    <select
                      ref={provinceRef}
                      id="city"
                      value={form.province}
                      onChange={(e) => handleProvince(e.target.value)}
                    >
                      <option value="">Chọn Tỉnh / Thành</option>
                      {provinces.map((p) => (
                        <option key={p.code} value={p.code}>
                          {p.name}
                        </option>
                      ))}
                    </select>
                    {errors.province && (
                      <p className="error-text">{errors.province}</p>
                    )}
                  </div>
                  <div className="select-address">
                    <select
                      ref={districtRef}
                      id="district"
                      value={form.district}
                      onChange={(e) => handleDistrict(e.target.value)}
                      disabled={!form.province}
                    >
                      <option value="">Chọn Quận / Huyện</option>
                      {districts.map((d) => (
                        <option key={d.code} value={d.code}>
                          {d.name}
                        </option>
                      ))}
                    </select>
                    {errors.district && (
                      <p className="error-text">{errors.district}</p>
                    )}
                  </div>
                  <div className="select-address">
                    <select
                      ref={wardRef}
                      id="wards"
                      value={form.ward}
                      onChange={(e) =>
                        setForm((prev) => ({ ...prev, ward: e.target.value }))
                      }
                      disabled={!form.district}
                    >
                      <option value="">Chọn Phường / Xã</option>
                      {wards.map((w) => (
                        <option key={w.code} value={w.code}>
                          {w.name}
                        </option>
                      ))}
                    </select>
                    {errors.ward && <p className="error-text">{errors.ward}</p>}
                  </div>
                </div>
                <div className="payment-method">
                  <div className="cart-pay-ttl">
                    <i className="fa-solid fa-credit-card"></i> Phương thức
                    thanh toán
                  </div>
                  <label className="payment-option">
                    <input
                      type="radio"
                      checked={form.payment === "cod"}
                      onChange={() =>
                        setForm((prev) => ({
                          ...prev,
                          payment: "cod",
                        }))
                      }
                    />
                    Thanh toán khi nhận hàng (COD)
                  </label>

                  <label className="payment-option">
                    <input
                      type="radio"
                      checked={form.payment === "bank"}
                      onChange={() =>
                        setForm((prev) => ({
                          ...prev,
                          payment: "bank",
                        }))
                      }
                    />
                    Chuyển khoản ngân hàng
                  </label>

                  {errors.payment && (
                    <p className="error-text">{errors.payment}</p>
                  )}
                </div>
              </div>
              <div className="cart-bd cart-pay">
                <div className="cart-pay-ttl">
                  <i className="fa-solid fa-cart-shopping"></i> Đơn hàng của bạn
                </div>
                {cart.map((item) => (
                  <div key={item.id} className="cart-item">
                    <div className="cart-item__img">
                      <img src={`${API_URL}/${item.image}`} alt={item.title} />
                    </div>
                    <div className="cart-item__meta">
                      <div className="cart-item__ttl">{item.title}</div>
                      <div className="cart-item__more">
                        <div className="cart-item__quality">X {item.qty}</div>
                        <div className="cart-item__price">
                          <span className="price-current">
                            {item.price.toLocaleString("vi-VN")} đ
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* ===== RIGHT — ORDER SUMMARY ===== */}
            <div className="cart-box-right cart-bd">
              <div className="cart-pay-ttl">
                <i className="fa-solid fa-cart-shopping"></i> Chi tiết đơn hàng
              </div>
              <div className="cart-summary">
                <div className="cart-summary__row">
                  <label>Tổng tiền</label>
                  <span className="cart-summary-total">
                    {totalPrice.toLocaleString("vi-VN")} đ
                  </span>
                </div>
                {/* <div className="cart-summary__row">
                <label>Giảm giá</label>
                <span className="cart-summary-sale"> 0₫</span>
              </div> */}
                <div className="cart-summary__row">
                  <label>Số lượng</label>
                  <span className="cart-summary-quality">{totalQty}</span>
                </div>
              </div>
              {/* <div className="checkout-summary">
            <div>
              <span>Tổng số lượng</span>
              <span>{totalQty}</span>
            </div>

            <div className="total">
              <span>Thành tiền</span>
              <strong>{totalPrice.toLocaleString("vi-VN")} đ</strong>
            </div>
          </div> */}
              <div className="cart-pay">
                <label>Thành tiền</label>
                <strong className="cart-pay-total">
                  {totalPrice.toLocaleString("vi-VN")} đ
                </strong>
              </div>

              <button className="cart-btn" onClick={handleOrder}>
                Đặt hàng <i className="fa-solid fa-arrow-right"></i>
              </button>
            </div>
          </div>
        </div>
        {loading && <span className="loading"></span>}
        {showSuccess && (
          <div
            className="popup-overlay"
            onClick={() => {
              setShowSuccess(false);
              navigate("/");
            }}
          >
            <div className="popup" onClick={(e) => e.stopPropagation()}>
              <div className="popup-head">
                <span className="popup-head__ttl">Thông báo</span>
                <button
                  className="popup-close"
                  onClick={() => {
                    setShowSuccess(false);
                    navigate("/");
                  }}
                >
                  ×
                </button>
              </div>
              <div className="popup-content">
                <div className="popup-icon">✓</div>
                <h3>Đặt hàng thành công!</h3>
                <p>Cảm ơn bạn đã mua hàng</p>
              </div>
            </div>
          </div>
        )}
      </main>
    </>
  );
}

export default Checkout;
