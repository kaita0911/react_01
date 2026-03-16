import { useEffect, useState } from "react";
import Pagination from "@/pages/components/Pagination";
import "./Orders.scss";

export default function Orders() {
  const limit = 10;

  const [orders, setOrders] = useState([]);
  const [total, setTotal] = useState(0);
  const [page, setPage] = useState(1);
  const [detail, setDetail] = useState(null);
  const totalPage = Math.ceil(total / limit);
  const [keyword, setKeyword] = useState("");
  const [status, setStatus] = useState("");
  // ===== SCROLL TOP =====
  const scrollTop = () => {
    const content = document.querySelector(".content");
    content?.scrollTo({
      top: 0,
      behavior: "smooth",
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
  const loadData = () => {
    fetch(
      `/api/admin/orders.php?act=list&page=${page}&limit=${limit}&keyword=${keyword}&status=${status}`
    )
      .then((res) => res.json())
      .then((res) => {
        if (res.status) {
          setOrders(res.data);
          setTotal(res.total);
        }
      });
  };

  useEffect(() => {
    loadData();
  }, [page, status]);

  const viewDetail = (id) => {
    fetch(`/api/admin/orders.php?act=detail&id=${id}`)
      .then((res) => res.json())
      .then((res) => {
        if (res.status) {
          setDetail(res.data);
        }
      });
  };

  const deleteOrder = async (id) => {
    if (!window.confirm("Xóa đơn này ?")) return;

    const form = new FormData();
    form.append("id", id);

    const res = await fetch(`/api/admin/orders.php?act=delete`, {
      method: "POST",
      body: form,
    });

    const data = await res.json();

    if (data.status) loadData();
  };

  const updateStatus = async (id, newStatus) => {
    const form = new FormData();

    form.append("id", id);
    form.append("status", newStatus);

    const res = await fetch(`/api/admin/orders.php?act=update_status`, {
      method: "POST",
      body: form,
    });

    const data = await res.json();

    if (data.status) loadData();
  };

  const statusLabel = (s) => {
    if (s == 0) return <span className="badge pending">Pending</span>;
    if (s == 1) return <span className="badge shipping">Shipping</span>;
    if (s == 2) return <span className="badge done">Done</span>;
  };
  const fullAddress = [
    detail?.order?.address,
    detail?.order?.phuongxa,
    detail?.order?.quanhuyen,
    detail?.order?.thanhpho,
  ]
    .filter(Boolean)
    .join(", ");

  const totalItems = detail?.items?.reduce((sum, i) => {
    return sum + Number(i.tamtinh);
  }, 0);
  return (
    <div className="orders-page">
      <h2>Quản lý đơn hàng</h2>

      <div className="filters">
        <input
          placeholder="Tìm theo tên / phone..."
          value={keyword}
          onChange={(e) => setKeyword(e.target.value)}
        />

        <select value={status} onChange={(e) => setStatus(e.target.value)}>
          <option value="">Tất cả</option>
          <option value="0">Pending</option>
          <option value="1">Shipping</option>
          <option value="2">Done</option>
        </select>

        <button onClick={loadData}>Tìm</button>
      </div>

      <table className="orders-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Khách</th>
            <th>Phone</th>
            <th>Tổng</th>
            <th>Trạng thái</th>
            <th>Ngày</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          {orders.map((o) => (
            <tr key={o.id}>
              <td>#{o.id}</td>
              <td>{o.name}</td>
              <td>{o.phone}</td>
              <td>{Number(o.total).toLocaleString()} đ</td>
              <td>{statusLabel(o.status)}</td>
              <td>{formatDate(o.created_at)}</td>

              <td className="actions">
                <button onClick={() => viewDetail(o.id)}>Xem</button>
                <button onClick={() => updateStatus(o.id, 0)}>Pending</button>

                <button onClick={() => updateStatus(o.id, 1)}>Ship</button>

                <button onClick={() => updateStatus(o.id, 2)}>Done</button>

                <button className="danger" onClick={() => deleteOrder(o.id)}>
                  Xóa
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      <Pagination
        page={page}
        totalPage={totalPage}
        setPage={setPage}
        scrollTop={scrollTop}
      />

      {/* POPUP DETAIL */}

      {detail && (
        <div className="modal">
          <div className="modal-box orders">
            <div className="modal-header">
              <h3>Đơn hàng #{detail.order.id}</h3>
              <button onClick={() => setDetail(null)}>✕</button>
            </div>

            {/* INFO */}
            <div className="order-info">
              <div className="info-item">
                <span>Khách hàng</span>
                <b>{detail.order.name}</b>
              </div>

              <div className="info-item">
                <span>Điện thoại</span>
                <b>{detail.order.phone}</b>
              </div>

              <div className="info-item">
                <span>Địa chỉ</span>
                {fullAddress}
              </div>
            </div>

            {/* ITEMS */}
            <table className="orders-detail">
              <thead>
                <tr>
                  <th>Sản phẩm</th>
                  <th>Giá</th>
                  <th>SL</th>
                  <th>Tổng</th>
                </tr>
              </thead>

              <tbody>
                {detail.items.map((i, index) => (
                  <tr key={index}>
                    <td>{i.product_name}</td>
                    <td>{Number(i.product_price).toLocaleString()} đ</td>
                    <td>{i.qty}</td>
                    <td>{Number(i.tamtinh).toLocaleString()} đ</td>
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
    </div>
  );
}
