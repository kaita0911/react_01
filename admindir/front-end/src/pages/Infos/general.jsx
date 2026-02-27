import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
export default function General() {
  const [config, setConfig] = useState({});
  const navigate = useNavigate();
  const handleEdit = (key, value) => {
    console.log("Đang sửa:", key, value);

    // Ví dụ đơn giản: prompt
    const newValue = prompt(`Nhập giá trị mới cho ${key}`, value);

    if (newValue !== null) {
      setConfig((prev) => ({
        ...prev,
        [key]: newValue,
      }));
    }
  };
  useEffect(() => {
    fetch("/api/infos.php?act=list", {
      credentials: "include",
    })
      .then((res) => res.json())
      .then((result) => {
        if (result.status) {
          const obj = {};

          result.data.forEach((item) => {
            // tìm value đầu tiên có dữ liệu
            let value = "";

            for (let i = 0; i <= 25; i++) {
              if (item[i] && item[i] !== "") {
                value = item[i];
                break;
              }
            }

            obj[item.name_vn] = value;
          });

          console.log(obj); // debug
          setConfig(obj);
        }
      });
  }, []);

  if (!Object.keys(config).length) return <p>Đang tải...</p>;

  return (
    <div>
      <table className="admin-table">
        <thead>
          <tr>
            <th className="col-order txt-center">Thứ tự</th>
            <th>Tên</th>
            <th className="col-status txt-center">Active</th>
          </tr>
        </thead>

        <tbody>
          {Object.entries(config).map(([key], index) => (
            <tr key={key}>
              <td className="txt-center">{index + 1}</td>
              <td>{key}</td>
              <td className="txt-center">
                <button
                  className="btn-edit"
                  onClick={() =>
                    navigate(
                      `/admin/Infos/general/edit/${encodeURIComponent(key)}`
                    )
                  }
                >
                  Chỉnh sửa
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
