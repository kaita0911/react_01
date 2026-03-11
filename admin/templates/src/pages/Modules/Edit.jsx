import { useParams, useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";
import Editor from "@/utils/Ckeditor";
import UploadImage from "@/pages/components/UploadImage";
import { slugify } from "@/utils/slugify";

export default function Edit() {
  const { module, id } = useParams();
  const navigate = useNavigate();

  const [fields, setFields] = useState([]);
  const [languages, setLanguages] = useState([]);
  const [activeTab, setActiveTab] = useState(null);

  const [form, setForm] = useState({
    active: 1,
    languages: {},
  });

  const [fileMap, setFileMap] = useState({});

  const sidebarFields = ["hinhanh", "active", "price", "priceold"];
  const langFields = ["name", "slug", "short", "des"];

  const fieldLabels = {
    hinhanh: "Ảnh đại diện",
    des: "Mô tả chi tiết",
    short: "Mô tả ngắn",
    active: "Active",
    price: "Giá",
    priceold: "Giá cũ",
  };

  /* ================= LOAD COMPONENT ================= */

  useEffect(() => {
    fetch(`/api/admin/component.php?act=comp&module=${module}`)
      .then((r) => r.json())
      .then((res) => {
        if (res.status) {
          setFields(Object.keys(res.data));
        }
      });
  }, [module]);

  /* ================= LOAD LANGUAGES ================= */

  const fetchLanguages = async () => {
    const res = await fetch("/api/admin/language.php?act=list");
    const result = await res.json();

    if (result.status) {
      const activeLanguages = result.data.filter((l) => l.active == 1);

      setLanguages(activeLanguages);

      if (activeLanguages.length > 0) {
        setActiveTab(activeLanguages[0].id);
      }
    }
  };

  useEffect(() => {
    fetchLanguages();
  }, []);

  /* ================= LOAD DETAIL ================= */

  useEffect(() => {
    if (!id) return;

    fetch(`/api/admin/articlelist.php?act=detail&id=${id}`)
      .then((r) => r.json())
      .then((res) => {
        if (!res.status) return;

        const data = res.data;

        setForm({
          active: Number(data.active),
          hinhanh: data.img_thumb_vn,
          languages: data.languages || {},
          price: data.price || "",
          priceold: data.priceold || "",
        });
      });
  }, [id]);

  /* ================= UPDATE FORM ================= */

  const handleChange = (key, value) => {
    setForm((prev) => ({
      ...prev,
      [key]: value,
    }));
  };

  const handleLangChange = (langId, field, value) => {
    setForm((prev) => ({
      ...prev,
      languages: {
        ...prev.languages,
        [langId]: {
          ...prev.languages[langId],
          [field]: value,
        },
      },
    }));
  };

  /* ================= UPDATE ================= */

  const handleUpdate = async () => {
    const fd = new FormData();

    fd.append("id", id);
    fd.append("module", module);

    Object.keys(form).forEach((k) => {
      if (k === "languages") {
        fd.append("languages", JSON.stringify(form.languages));
      } else {
        fd.append(k, form[k]);
      }
    });

    Object.keys(fileMap).forEach((k) => {
      fd.append(k, fileMap[k]);
    });

    const res = await fetch("/api/admin/articlelist.php?act=update", {
      method: "POST",
      body: fd,
    });

    const result = await res.json();

    if (result.status) {
      navigate(`/${module}`);
    } else {
      alert("Lỗi cập nhật");
    }
  };

  /* ================= FIELD SPLIT ================= */

  const sideFields = fields.filter((f) => sidebarFields.includes(f));

  /* ================= RENDER ================= */

  return (
    <main className="page-editor">
      <div className="action-bar">
        <button className="c-btn btn-save" onClick={handleUpdate}>
          <i className="fa-regular fa-floppy-disk"></i> Cập nhật
        </button>

        <button className="c-btn btn-cancel" onClick={() => navigate(-1)}>
          Huỷ
        </button>
      </div>

      <div className="editor-layout">
        {/* MAIN */}
        <div className="editor-main">
          {languages.length > 1 && (
            <div className="lang-tabs-header">
              {languages.map((lang) => (
                <button
                  key={lang.id}
                  className={`lang-btn ${
                    activeTab === lang.id ? "active" : ""
                  }`}
                  onClick={() => setActiveTab(lang.id)}
                >
                  {lang.name}
                </button>
              ))}
            </div>
          )}

          {languages
            .filter((lang) => languages.length === 1 || activeTab === lang.id)
            .map((lang) => {
              const langData = form.languages?.[lang.id] || {};

              return (
                <div className="editor-card" key={lang.id}>
                  <div className="form-group">
                    <label>Tên</label>
                    <input
                      type="text"
                      value={langData.name || ""}
                      onChange={(e) => {
                        const value = e.target.value;

                        handleLangChange(lang.id, "name", value);
                        handleLangChange(lang.id, "slug", slugify(value));
                      }}
                    />
                  </div>

                  <div className="form-group">
                    <label>Slug</label>
                    <input type="text" value={langData.slug || ""} readOnly />
                  </div>

                  {langFields
                    .filter((f) => f !== "name" && f !== "slug")
                    .map((key) => (
                      <div className="form-group" key={key}>
                        <label>{fieldLabels[key] || key}</label>

                        {["short", "des"].includes(key) ? (
                          <Editor
                            value={langData[key] || ""}
                            onChange={(value) =>
                              handleLangChange(lang.id, key, value)
                            }
                          />
                        ) : (
                          <input
                            type="text"
                            value={langData[key] || ""}
                            onChange={(e) =>
                              handleLangChange(lang.id, key, e.target.value)
                            }
                          />
                        )}
                      </div>
                    ))}
                </div>
              );
            })}
        </div>

        {/* SIDEBAR */}
        <div className="editor-sidebar">
          <div className="editor-card">
            {sideFields.map((key) => {
              let field = null;

              if (key === "hinhanh") {
                field = (
                  <UploadImage
                    currentImage={form[key]}
                    onChange={(file) => {
                      setFileMap((prev) => ({ ...prev, [key]: file }));
                      handleChange(key, file.name);
                    }}
                  />
                );
              } else if (key === "active") {
                field = (
                  <label className="switch">
                    <input
                      type="checkbox"
                      checked={form[key] === 1}
                      onChange={(e) =>
                        handleChange(key, e.target.checked ? 1 : 0)
                      }
                    />
                    <span className="slider"></span>
                  </label>
                );
              } else if (key === "price" || key === "priceold") {
                field = (
                  <input
                    type="text"
                    className="form-control"
                    value={
                      form[key] ? Number(form[key]).toLocaleString("vi-VN") : ""
                    }
                    onChange={(e) => {
                      const raw = e.target.value.replace(/\D/g, ""); // chỉ giữ số
                      handleChange(key, raw);
                    }}
                  />
                );
              } else {
                field = (
                  <input
                    type="text"
                    className="form-control"
                    value={form[key] || ""}
                    onChange={(e) => handleChange(key, e.target.value)}
                  />
                );
              }

              return (
                <div className="form-group" key={key}>
                  <label>{fieldLabels[key] || key}</label>
                  {field}
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </main>
  );
}
