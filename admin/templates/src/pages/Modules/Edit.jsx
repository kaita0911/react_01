import { useParams, useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";
import Editor from "@/utils/Ckeditor";
import UploadImage from "@/pages/components/UploadImage";
import UploadMultipleImages from "@/pages/components/UploadMultipleImages";

import { slugify } from "@/utils/slugify";

export default function Edit() {
  const { module, id } = useParams();
  const navigate = useNavigate();

  const [fields, setFields] = useState([]);
  const [languages, setLanguages] = useState([]);
  const [activeTab, setActiveTab] = useState(null);

  const [form, setForm] = useState({ languages: {} });
  const [fileMap, setFileMap] = useState({});
  const [gallery, setGallery] = useState([]);
  const [deletedGallery, setDeletedGallery] = useState([]);
  const sidebarFields = [
    "hinhanh",
    "nhieuhinh",
    "active",
    "hot",
    "mostview",
    "new",
    "price",
    "priceold",
  ];

  const toggleFields = ["active", "hot", "mostview", "new"];
  const priceFields = ["price", "priceold"];

  const langFields = ["name", "slug", "short", "des"];

  const fieldLabels = {
    hinhanh: "Ảnh đại diện",
    nhieuhinh: "Ảnh liên quan",
    des: "Mô tả chi tiết",
    short: "Mô tả ngắn",
    active: "Active",
    hot: "Nổi bật",
    mostview: "Xem nhiều",
    new: "Mới",
    price: "Giá",
    priceold: "Giá cũ",
  };

  /* ================= LOAD DATA ================= */

  useEffect(() => {
    const loadData = async () => {
      // component
      const comp = await fetch(
        `/api/admin/component.php?act=comp&module=${module}`
      ).then((r) => r.json());

      if (comp.status) setFields(Object.keys(comp.data));

      // languages
      const lang = await fetch("/api/admin/language.php?act=list").then((r) =>
        r.json()
      );

      if (lang.status) {
        const activeLang = lang.data.filter((l) => l.active == 1);
        setLanguages(activeLang);
        if (activeLang.length) setActiveTab(activeLang[0].id);
      }

      // detail
      if (id) {
        const detail = await fetch(
          `/api/admin/articlelist.php?act=detail&id=${id}`
        ).then((r) => r.json());

        if (detail.status) {
          const d = detail.data;

          console.log("DETAIL DATA:", d);
          // console.log("LANGUAGES:", d.languages);

          setForm({
            active: Number(d.active),
            hot: Number(d.hot),
            mostview: Number(d.mostview),
            new: Number(d.new),
            hinhanh: d.img_thumb_vn,
            languages: d.languages || {},
            price: d.price || "",
            priceold: d.priceold || "",
          });

          setGallery(d.gallery || []);
        }
      }
    };

    loadData();
  }, [module, id]);

  /* ================= UPDATE FORM ================= */

  const handleChange = (key, value) => {
    setForm((p) => ({ ...p, [key]: value }));
  };

  const handleLangChange = (langId, field, value) => {
    setForm((p) => ({
      ...p,
      languages: {
        ...p.languages,
        [langId]: {
          ...p.languages[langId],
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

    for (const k in form) {
      if (k === "languages") {
        fd.append("languages", JSON.stringify(form.languages));
      } else {
        fd.append(k, form[k]);
      }
    }

    for (const k in fileMap) {
      fd.append(k, fileMap[k]);
    }
    gallery.forEach((img, index) => {
      // ảnh upload mới
      if (img instanceof File) {
        fd.append("gallery[]", img);
        fd.append("gallery_new_num[]", index);
      }

      // ảnh đã có trong DB → cập nhật num
      else if (img.id) {
        fd.append(
          "gallery_update[]",
          JSON.stringify({
            id: img.id,
            num: index,
          })
        );
      }
    });
    fd.append("delete_gallery", JSON.stringify(deletedGallery));
    const res = await fetch("/api/admin/articlelist.php?act=update", {
      method: "POST",
      body: fd,
    });

    const result = await res.json();

    if (result.status) navigate(`/${module}`);
    else alert("Lỗi cập nhật");
  };

  /* ================= FIELD SPLIT ================= */

  const sideFields = fields.filter((f) => sidebarFields.includes(f));

  /* ================= RENDER FIELD ================= */

  const renderField = (key) => {
    if (key === "hinhanh") {
      return (
        <UploadImage
          currentImage={form[key]}
          onChange={(file) => {
            setFileMap((p) => ({ ...p, [key]: file }));
            handleChange(key, file.name);
          }}
        />
      );
    }
    if (key === "nhieuhinh") {
      return (
        <UploadMultipleImages
          images={gallery}
          setImages={setGallery}
          setDeletedIds={setDeletedGallery}
        />
      );
    }

    if (toggleFields.includes(key)) {
      return (
        <label className="switch">
          <input
            type="checkbox"
            checked={Number(form[key]) === 1}
            onChange={(e) => handleChange(key, e.target.checked ? 1 : 0)}
          />
          <span className="slider"></span>
        </label>
      );
    }

    if (priceFields.includes(key)) {
      return (
        <input
          type="text"
          className="form-control"
          value={form[key] ? Number(form[key]).toLocaleString("vi-VN") : ""}
          onChange={(e) => handleChange(key, e.target.value.replace(/\D/g, ""))}
        />
      );
    }

    return (
      <input
        type="text"
        className="form-control"
        value={form[key] || ""}
        onChange={(e) => handleChange(key, e.target.value)}
      />
    );
  };

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
              const langData = form.languages?.[String(lang.id)] || {};

              return (
                <div className="editor-card" key={lang.id}>
                  <div className="form-group">
                    <label>Tên</label>
                    <input
                      type="text"
                      value={langData.name || ""}
                      onChange={(e) => {
                        const v = e.target.value;
                        handleLangChange(lang.id, "name", v);
                        handleLangChange(lang.id, "slug", slugify(v));
                      }}
                    />
                  </div>

                  <div className="form-group">
                    <label>Slug</label>
                    <input value={langData.slug || ""} readOnly />
                  </div>

                  {langFields
                    .filter((f) => !["name", "slug"].includes(f))
                    .map((key) => (
                      <div className="form-group" key={key}>
                        <label>{fieldLabels[key] || key}</label>

                        {["short", "des"].includes(key) ? (
                          <Editor
                            key={`${lang.id}-${key}`}
                            value={langData[key] || ""}
                            onChange={(v) => handleLangChange(lang.id, key, v)}
                          />
                        ) : (
                          <input
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
            {sideFields.map((key) => (
              <div
                key={key}
                className={`form-group ${
                  toggleFields.includes(key) ? "c-active" : ""
                }`}
              >
                <label>{fieldLabels[key] || key}</label>
                {renderField(key)}
              </div>
            ))}
          </div>
        </div>
      </div>
    </main>
  );
}
