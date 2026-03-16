import { useEffect, useState } from "react";
export default function FieldsList() {
  const [fields, setFields] = useState([]);

  const [name, setName] = useState("");
  const [label, setLabel] = useState("");
  const [type, setType] = useState("");
  const [target, setTarget] = useState("article");

  const [showModal, setShowModal] = useState(false);
  const [editField, setEditField] = useState(null);

  /* ================= LOAD ================= */

  const loadFields = async () => {
    const res = await fetch("/api/admin/fields.php?act=list");
    const data = await res.json();

    if (data.status) {
      setFields(data.data);
    }
  };

  useEffect(() => {
    const init = async () => {
      await loadFields();
    };
    init();
  }, []);
  /* ================= ADD ================= */

  const addField = async () => {
    if (!name) return alert("Enter name");

    const fd = new FormData();

    fd.append("name", name);
    fd.append("label", label);
    fd.append("type", type);
    fd.append("target", target);

    const res = await fetch("/api/admin/fields.php?act=add", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      setName("");
      setLabel("");
      setType("input");
      setTarget("article");

      loadFields();
    }
  };

  /* ================= DELETE ================= */

  const deleteField = async (id) => {
    if (!window.confirm("Delete field?")) return;

    const fd = new FormData();
    fd.append("id", id);

    const res = await fetch("/api/admin/fields.php?act=delete", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      loadFields();
    }
  };

  /* ================= OPEN EDIT ================= */

  const openEdit = (field) => {
    setEditField({ ...field });
    setShowModal(true);
  };

  /* ================= SAVE EDIT ================= */

  const saveEdit = async () => {
    const fd = new FormData();

    fd.append("id", editField.id);
    fd.append("label", editField.label);
    fd.append("type", editField.type);
    fd.append("target", editField.target);

    const res = await fetch("/api/admin/fields.php?act=update", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      setShowModal(false);
      setEditField(null);

      loadFields();
    }
  };

  /* ================= UI ================= */

  return (
    <main>
      <h2 className="ttl-pages">Danh sách fields</h2>

      {/* ADD FIELD */}

      <div className="action-fields">
        <input
          placeholder="code"
          value={name}
          onChange={(e) => setName(e.target.value)}
        />
        <input
          placeholder="label"
          value={label}
          onChange={(e) => setLabel(e.target.value)}
        />
        <select value={type} onChange={(e) => setType(e.target.value)}>
          <option value="Input">Input</option>
          <option value="number">Number</option>
          <option value="image">Image</option>
          <option value="gallery">Gallery</option>
          <option value="editor">Editor</option>
          <option value="textarea">Textarea</option>
          <option value="toggle">Toggle</option>
        </select>
        <select value={target} onChange={(e) => setTarget(e.target.value)}>
          <option value="article">Bài viết</option>
          <option value="category">Danh mục</option>
        </select>
        <div className="action-bar">
          <button className="c-btn btn-add" onClick={addField}>
            <i className="fa-solid fa-circle-plus"></i> Thêm mới
          </button>
        </div>
      </div>

      {/* LIST */}

      <table className="admin-table">
        <thead>
          <tr>
            <th className="col-order txt-center">ID</th>
            <th className="col-actions">Code</th>
            <th className="col-actions">Loại</th>
            <th className="col-actions">Phân loại</th>
            <th>Tên</th>
            <th className="col-action txt-center">Action</th>
          </tr>
        </thead>

        <tbody>
          {fields.map((f) => (
            <tr key={f.id}>
              <td className="txt-center">{f.id}</td>
              <td>{f.name}</td>
              <td>{f.type}</td>
              <td>{f.target}</td>
              <td>{f.label}</td>

              <td className="col-actions txt-center">
                <div className="btn-actions">
                  <button className="btn-edit act" onClick={() => openEdit(f)}>
                    <i className="fa-regular fa-pen-to-square"></i>
                  </button>
                  <button
                    className="btn-delete act"
                    onClick={() => deleteField(f.id)}
                  >
                    <i className="fa-solid fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* POPUP EDIT */}

      {showModal && (
        <div className="modal" onClick={() => setShowModal(false)}>
          <div className="modal-box" onClick={(e) => e.stopPropagation()}>
            <h3>Chỉnh sửa Field</h3>

            <p>
              <b>Code:</b> {editField.name}
            </p>

            <input
              value={editField.label}
              onChange={(e) =>
                setEditField({ ...editField, label: e.target.value })
              }
            />

            <select
              value={editField.type}
              onChange={(e) =>
                setEditField({ ...editField, type: e.target.value })
              }
            >
              <option value="Input">Input</option>
              <option value="number">Number</option>
              <option value="image">Image</option>
              <option value="gallery">Gallery</option>
              <option value="editor">Editor</option>
              <option value="textarea">Textarea</option>
              <option value="toggle">Toggle</option>
            </select>

            <select
              value={editField.target}
              onChange={(e) =>
                setEditField({ ...editField, target: e.target.value })
              }
            >
              <option value="article">Bài viết</option>
              <option value="category">Danh mục</option>
            </select>

            <div className="modal-actions">
              <button className="btn-confirm" onClick={saveEdit}>
                <i className="fa-regular fa-floppy-disk"></i> Lưu
              </button>
              <button
                className="btn-cancel"
                onClick={() => setShowModal(false)}
              >
                Hủy
              </button>
            </div>
          </div>
        </div>
      )}
    </main>
  );
}
