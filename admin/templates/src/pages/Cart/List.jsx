import { useEffect, useState, useRef } from "react";
import Pagination from "@/pages/components/Pagination";
import "./Orders.scss";

export default function Orders() {
  const limit = 40;

  const [orders, setOrders] = useState([]);
  const [total, setTotal] = useState(0);
  const [page, setPage] = useState(1);
  const [detail, setDetail] = useState(null);
  const totalPage = Math.ceil(total / limit);
  const [keyword, setKeyword] = useState("");
  const [status, setStatus] = useState("");

  const [newCount, setNewCount] = useState(0);
  const [showToast, setShowToast] = useState(false);
  const audioRef = useRef(null);
  const [audioReady, setAudioReady] = useState(false);
  const prevCountRef = useRef(null);
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
  // ===== LOAD NEW ORDER =====
  const loadNewOrders = async () => {
    try {
      const res = await fetch("/api/admin/orders.php?act=count_new");
      const data = await res.json();

      if (data.status) {
        setNewCount(data.total);
      }
    } catch (err) {
      console.log(err);
    }
  };
  useEffect(() => {
    const audio = new Audio("/notification.mp3");
    audio.preload = "auto";
    audioRef.current = audio;

    const unlock = () => {
      audio
        .play()
        .then(() => {
          audio.pause();
          audio.currentTime = 0;
          setAudioReady(true); // ✅ đánh dấu đã unlock
        })
        .catch(() => {});

      document.removeEventListener("click", unlock);
    };

    document.addEventListener("click", unlock);
  }, []);

  useEffect(() => {
    if (prevCountRef.current === null) {
      prevCountRef.current = newCount;
      return;
    }

    if (newCount > prevCountRef.current) {
      setTimeout(() => {
        setShowToast(true);

        if (audioReady && audioRef.current) {
          audioRef.current.currentTime = 0;
          audioRef.current.play().catch(() => {
            // console.log("Audio lỗi:", e);
          });
        } else {
          // console.log("Chưa unlock audio");
        }
      }, 0);

      setTimeout(() => {
        setShowToast(false);
      }, 3000);
    }

    prevCountRef.current = newCount;
  }, [newCount]);

  useEffect(() => {
    const init = setTimeout(() => {
      loadNewOrders();
    }, 0); // 👈 delay 1 tick là hết warning

    const interval = setInterval(() => {
      loadNewOrders();
    }, 5000);

    return () => {
      clearTimeout(init);
      clearInterval(interval);
    };
  }, []);
  // ===== LOAD DATA =====
  const loadData = () => {
    const params = new URLSearchParams({
      act: "list",
      page,
      limit,
      keyword: keyword.trim(),
      status,
    });

    fetch(`/api/admin/orders.php?${params.toString()}`)
      .then((res) => res.json())
      .then((res) => {
        console.log("API:", res); // 👈 debug

        if (res.status) {
          setOrders(res.data);
          setTotal(res.total);
        }
      })
      .catch((err) => console.error("Fetch lỗi:", err));
  };

  useEffect(() => {
    loadData();
  }, [page, status, keyword]);

  const viewDetail = (id) => {
    fetch(`/api/admin/orders.php?act=detail&id=${id}`)
      .then((res) => res.json())
      .then((res) => {
        if (res.status) {
          setDetail(res.data);
          loadData();
          loadNewOrders();
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
    if (s == 0) return <span className="badge pending">Đang chờ</span>;
    if (s == 1) return <span className="badge shipping">Đang giao</span>;
    if (s == 2) return <span className="badge done">Hoàn thành</span>;
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
    <>
      <div className="orders-page">
        <h2 className="ttl-pages">
          Quản lý đơn hàng
          {/* {newCount > 0 && <span className="badge">{newCount}</span>} */}
        </h2>

        <div className="filters">
          <input
            placeholder="Tìm theo tên / phone..."
            value={keyword}
            onChange={(e) => {
              setKeyword(e.target.value);
              setPage(1);
            }}
          />

          <select
            value={status}
            onChange={(e) => {
              setStatus(e.target.value);
              setPage(1);
            }}
          >
            <option value="">Tất cả</option>
            <option value="0">Đang chờ</option>
            <option value="1">Đang giao</option>
            <option value="2">Hoàn thành</option>
          </select>

          <button
            onClick={() => {
              setKeyword("");
              setStatus("");
              setPage(1);
            }}
          >
            Reset
          </button>
        </div>

        <table className="orders-table admin-table">
          <thead>
            <tr>
              <th className="txt-center">Thứ tự</th>
              <th className="txt-center">ID</th>
              <th>Tên</th>
              <th>Phone</th>
              <th>Tổng</th>
              <th className="txt-center">Trạng thái</th>
              <th>Ngày</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            {orders.map((o, index) => (
              <tr key={o.id} className={o.is_read == 0 ? "unread" : ""}>
                <td className="txt-center">{index + 1}</td>
                <td className="txt-center">#{o.order_code}</td>
                <td>{o.name}</td>
                <td>{o.phone}</td>
                <td>{Number(o.total).toLocaleString()} đ</td>
                <td className="txt-center">{statusLabel(o.status)}</td>
                <td>{formatDate(o.created_at)}</td>
                <td className="actions">
                  <button className="btn view" onClick={() => viewDetail(o.id)}>
                    Xem
                  </button>

                  <button
                    className="btn pending"
                    onClick={() => updateStatus(o.id, 0)}
                  >
                    Đang chờ
                  </button>

                  <button
                    className="btn ship"
                    onClick={() => updateStatus(o.id, 1)}
                  >
                    Đang giao
                  </button>

                  <button
                    className="btn done"
                    onClick={() => updateStatus(o.id, 2)}
                  >
                    Hoàn thành
                  </button>

                  <button
                    className="btn danger"
                    onClick={() => deleteOrder(o.id)}
                  >
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
          <div className="modal" onClick={() => setDetail(null)}>
            <div
              className="modal-box orders"
              onClick={(e) => e.stopPropagation()}
            >
              <div className="modal-header">
                <h3>Đơn hàng #{detail.order.order_code}</h3>
                <button
                  className="btn-close-cart"
                  onClick={() => setDetail(null)}
                >
                  ✕
                </button>
              </div>

              {/* INFO */}
              <div className="order-info">
                <div className="info-item">
                  <span>Tên</span>
                  {detail.order.name}
                </div>

                <div className="info-item">
                  <span>Điện thoại</span>
                  {detail.order.phone}
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
      {showToast && <div className="toast">🛒 Có đơn hàng mới!</div>}
    </>
  );
}
