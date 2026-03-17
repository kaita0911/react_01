// //import { API_URL } from "@/config";
// import AdminLayout from "../layouts/AdminLayout";
// import "./Main.scss";
// export default function Dashboard() {
//   const userName = localStorage.getItem("admin_name") || "Admin";
//   return <h1>Dashboard</h1>;
// }
import { useEffect, useState } from "react";
import "./Main.scss";
import {
  BarChart,
  Bar,
  XAxis,
  Tooltip,
  ResponsiveContainer,
  CartesianGrid,
} from "recharts";
const formatMoney = (v) => {
  return Number(v || 0).toLocaleString("vi-VN") + " đ";
};

const CustomTooltip = ({ active, payload }) => {
  if (active && payload && payload.length) {
    return (
      <div className="tooltip">
        <b>{payload[0].payload.name}</b>
        <p>{formatMoney(payload[0].value)}</p>
      </div>
    );
  }
  return null;
};
export default function Dashboard() {
  const [orders, setOrders] = useState([]);
  const [contacts, setContacts] = useState([]);
  const [showDetail, setShowDetail] = useState(false);
  const [orderDetail, setOrderDetail] = useState(null);
  const userName = localStorage.getItem("admin_name") || "Admin";
  const [stats, setStats] = useState({
    orders: 0,
    orders_today: 0,
    revenue: 0,
    revenue_today: 0,
    contacts: 0,
    products: 0,
    pending: 0,
    shipping: 0,
    done: 0,
    visitors: 0, // ✅ thêm
    visitors_today: 0, // ✅ thêm
  });
  const chartData = (stats.revenue_month || []).map((v, i) => ({
    name: `T${i + 1}`,
    revenue: v,
  }));

  useEffect(() => {
    fetch("/api/visit.php");
  }, []);
  useEffect(() => {
    fetch("/api/admin/orders.php?act=latest", {
      credentials: "include",
    })
      .then((res) => res.json())
      .then((res) => {
        if (res.status) {
          setOrders(res.data);
        }
      });
  }, []);
  const getStatusText = (s) => {
    switch (Number(s)) {
      case 0:
        return "Đang chờ";
      case 1:
        return "Đang giao";
      case 2:
        return "Hoàn thành";
      default:
        return "Không rõ";
    }
  };

  const getStatusClass = (s) => {
    switch (Number(s)) {
      case 0:
        return "pending";
      case 1:
        return "shipping";
      case 2:
        return "done";
      default:
        return "";
    }
  };
  ///Xem chi tiết đơn
  const viewDetail = (id) => {
    fetch(`/api/admin/orders.php?act=detail&id=${id}`, {
      credentials: "include",
    })
      .then((res) => res.json())
      .then((res) => {
        if (res.status) {
          setOrderDetail(res.data);
          setShowDetail(true);
        }
      });
  };
  const formatDate = (date) => {
    const d = new Date(date);

    const time = d.toLocaleTimeString("vi-VN", {
      hour: "2-digit",
      minute: "2-digit",
    });

    const day = d.toLocaleDateString("vi-VN");

    return `${time} • ${day}`;
  };
  const fullAddress = [
    orderDetail?.order?.address,
    orderDetail?.order?.phuongxa,
    orderDetail?.order?.quanhuyen,
    orderDetail?.order?.thanhpho,
  ]
    .filter(Boolean)
    .join(", ");

  const totalItems = orderDetail?.items?.reduce((sum, i) => {
    return sum + Number(i.tamtinh);
  }, 0);
  useEffect(() => {
    fetch("/api/admin/dashboard.php", {
      credentials: "include",
    })
      .then((res) => res.json())
      .then((res) => {
        console.log("DASHBOARD API:", res); // 👈 thêm dòng này
        if (res.status) {
          setStats(res.data);
        }
      });
  }, []);
  useEffect(() => {
    fetch("/api/admin/contact.php?act=latest", {
      credentials: "include",
    })
      .then((res) => res.json())
      .then((res) => {
        if (res.status) {
          setContacts(res.data);
        }
      });
  }, []);
  return (
    <>
      {" "}
      <div className="dashboard">
        {/* GREETING */}
        <div className="dashboard-header">
          <h2>👋 Xin chào, {userName}!</h2>
          <p>Chúc bạn một ngày làm việc hiệu quả 🚀</p>
        </div>

        {/* CARDS */}
        <div className="cards">
          <div className="card blue">
            <i className="fa-solid fa-cart-shopping"></i>
            <h4>Đơn hàng</h4>
            <h2>{stats.orders}</h2>
            <span>Hôm nay: {stats.orders_today}</span>
          </div>

          <div className="card green">
            <i className="fa-solid fa-sack-dollar"></i>
            <h4>Doanh thu</h4>
            <h2>{stats.revenue.toLocaleString()} đ</h2>
            <span>Tháng này: {stats.revenue_today.toLocaleString()} đ</span>
          </div>

          <div className="card orange">
            <i className="fa-solid fa-envelope"></i>
            <h4>Liên hệ mới</h4>
            <h2>{stats.contacts}</h2>
            <span>Chưa xử lý</span>
          </div>

          <div className="card purple">
            <i className="fa-solid fa-box"></i>
            <h4>Sản phẩm</h4>
            <h2>{stats.products}</h2>
            <span>Đang bán</span>
          </div>
        </div>

        {/* CONTENT */}
        <div className="dashboard-content">
          {/* CHART */}
          <div className="chart-box">
            <h3 className="ttl03">📊 Doanh thu theo tháng</h3>

            <ResponsiveContainer width="100%" height={260}>
              <BarChart data={chartData}>
                <defs>
                  <linearGradient id="colorRevenue" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor="#4f46e5" stopOpacity={0.9} />
                    <stop offset="100%" stopColor="#22c55e" stopOpacity={0.7} />
                  </linearGradient>
                </defs>

                <CartesianGrid strokeDasharray="3 3" opacity={0.2} />

                <XAxis dataKey="name" tick={{ fill: "#888" }} />

                <Tooltip content={CustomTooltip} />

                <Bar
                  dataKey="revenue"
                  fill="url(#colorRevenue)"
                  radius={[8, 8, 0, 0]}
                />
              </BarChart>
            </ResponsiveContainer>
          </div>

          {/* CONTACT */}
          <div className="contact-box">
            <h3 className="ttl03">Liên hệ mới</h3>

            {contacts.length === 0 && (
              <div className="empty">Không có liên hệ mới</div>
            )}

            {contacts.map((c) => (
              <div key={c.id} className="contact-item">
                <b>{c.name}</b>
                <span>{c.message}</span>
              </div>
            ))}

            <div
              className="view-all"
              onClick={() => (window.location.href = "/contact")}
            >
              Xem tất cả →
            </div>
          </div>
        </div>

        {/* BOTTOM */}
        <div className="dashboard-bottom">
          {/* ORDERS */}
          <div className="orders-box">
            <h3 className="ttl03">Đơn hàng mới nhất</h3>

            <table className="orders-table">
              <thead>
                <tr>
                  <th>Mã</th>
                  <th>Tên</th>
                  <th>Tổng</th>
                  <th>Trạng thái</th>
                </tr>
              </thead>

              <tbody>
                {orders.length === 0 && (
                  <tr>
                    <td colSpan="4">Không có đơn hàng</td>
                  </tr>
                )}

                {orders.map((o) => (
                  <tr key={o.id}>
                    <td>#{o.order_code}</td>
                    <td>{o.name}</td>
                    <td>{Number(o.total).toLocaleString()}đ</td>
                    <td className="actions">
                      <span className={`badge  ${getStatusClass(o.status)}`}>
                        {getStatusText(o.status)}
                      </span>
                      <button
                        className="btn view"
                        onClick={() => viewDetail(o.id)}
                      >
                        Xem
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
        {/* <div className="card dark">
          <i className="fa-solid fa-chart-line"></i>
          <h4>Lượt truy cập</h4>
          <h2>{stats.visitors}</h2>
          <span>Hôm nay: {stats.visitors_today}</span>
        </div> */}
      </div>
      {showDetail && orderDetail && (
        <div className="modal" onClick={() => setShowDetail(false)}>
          <div
            className="modal-box orders"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="modal-header">
              <h3>Chi tiết đơn #{orderDetail.order.id}</h3>
              <button
                className="btn-close-cart"
                onClick={() => setShowDetail(false)}
              >
                ✕
              </button>
            </div>
            <div className="order-info">
              <p className="info-item">Tên: {orderDetail.order.name}</p>
              <p className="info-item">Điện thoại: {orderDetail.order.phone}</p>
              <p className="info-item">Địa chỉ: {fullAddress}</p>
              <p className="info-item">
                Ngày: {formatDate(orderDetail.order.created_at)}
              </p>
            </div>

            <table className="orders-detail">
              <thead>
                <tr>
                  <th>Sản phẩm</th>
                  <th>SL</th>
                  <th>Giá</th>
                  <th>Tạm tính</th>
                </tr>
              </thead>
              <tbody>
                {orderDetail.items.map((it, i) => (
                  <tr key={i}>
                    <td>{it.product_name}</td>
                    <td>{it.qty}</td>
                    <td>{Number(it.product_price).toLocaleString()}đ</td>
                    <td>{Number(it.tamtinh).toLocaleString()}đ</td>
                  </tr>
                ))}
              </tbody>
              <tfoot>
                <tr>
                  <td colSpan="3">Tổng đơn</td>
                  <td className="total">{totalItems.toLocaleString()} đ</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      )}
    </>
  );
}
