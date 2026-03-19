import { useParams, useNavigate } from "react-router-dom";
import { useState, useEffect, useMemo } from "react";
import Editor from "@/utils/Ckeditor";
import UploadImage from "@/pages/components/UploadImage";
import UploadMultipleImages from "@/pages/components/UploadMultipleImages";
import MetaKeywords from "@/utils/MetaKeywords";
import { slugify } from "@/utils/slugify";

export default function Create() {
  const { module } = useParams();
  const navigate = useNavigate();

  const [languages, setLanguages] = useState([]);
  const [categories, setCategories] = useState([]);
  const [activeTab, setActiveTab] = useState(null);

  //const [compId, setCompId] = useState(null);
  const [fields, setFields] = useState([]);

  const [fileMap, setFileMap] = useState({});
  const [multiimages, setGallery] = useState([]);
  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState({
    languages: {},
    active: 1,
    hot: 1,
    new: 1,
    mostview: 1,
  });

  useEffect(() => {
    const init = async () => {
      const [compRes, langRes] = await Promise.all([
        fetch(`/api/admin/component.php?act=comp&module=${module}`),
        fetch(`/api/admin/language.php?act=list`),
      ]);
      const compData = await compRes.json();
      const langData = await langRes.json();

      if (!compData.status) return;
      const comp = compData.data;
      //setCompId(comp);

      const [fieldRes, cateRes] = await Promise.all([
        fetch(
          `/api/admin/component_fields.php?act=list&component=${comp}&target=article`
        ),
        fetch(`/api/admin/category.php?act=list&comp=${comp}`),
      ]);

      const fieldData = await fieldRes.json();
      const cateData = await cateRes.json();

      if (fieldData.status) setFields(fieldData.data);

      if (langData.status) {
        const activeLang = langData.data.filter((l) => l.active == 1);
        setLanguages(activeLang);
        if (activeLang.length) setActiveTab(activeLang[0].id);
      }

      if (cateData.status) setCategories(cateData.data);
    };

    init();
  }, [module]);
  /* ================= FIELD SPLIT ================= */
  const fieldMap = useMemo(() => {
    const map = {};
    fields.forEach((f) => (map[f.name] = f));
    return map;
  }, [fields]);
  /* ================= FORM ================= */

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
  const handleSelect = (id) => {
    setForm((p) => ({ ...p, parent_id: id }));
  };
  /* ================= TREE ================= */
  const isParentOfSelected = (item) => {
    if (form.parent_id === item.id) return true;

    const checkChildren = (children) => {
      for (let c of children || []) {
        if (c.id === form.parent_id) return true;
        if (checkChildren(c.children)) return true;
      }
      return false;
    };

    return checkChildren(item.children);
  };
  const renderTree = (list, level = 0) =>
    list.map((item) => (
      <div key={item.id} className="cat-item">
        <label className="cat-label">
          <input
            type="checkbox"
            checked={isParentOfSelected(item)}
            onChange={() => handleSelect(item.id)}
          />

          <span className="cat-name">
            {"-- ".repeat(level)}
            {item.names?.[activeTab] || "-"}
          </span>
        </label>

        {item.children?.length > 0 && renderTree(item.children, level + 1)}
      </div>
    ));

  /* ================= CREATE ================= */

  const handleCreate = async () => {
    const defaultLang = languages[0];
    const name = form.languages?.[defaultLang.id]?.name;

    if (!name || name.trim() === "") {
      setActiveTab(defaultLang.id);
      setTimeout(() => {
        document.querySelector("input")?.focus();
      }, 100);
      return;
    }

    setSaving(true); // bắt đầu
    try {
      const fd = new FormData();
      fd.append("module", module);
      fd.append("parent_id", form.parent_id || 0);
      fd.append("languages", JSON.stringify(form.languages));
      fd.append("price", form.price || "");
      fd.append("priceold", form.priceold || "");

      Object.keys(fileMap).forEach((k) => fd.append(k, fileMap[k]));

      const uploadedImages = [];

      for (let i = 0; i < multiimages.length; i++) {
        if (multiimages[i] instanceof File) {
          const fdImg = new FormData();
          fdImg.append("file", multiimages[i]);

          const res = await fetch("/api/admin/articlelist/upload-image.php", {
            method: "POST",
            body: fdImg,
          });

          const data = await res.json();

          if (data.status) {
            uploadedImages.push(data.path);
          } else {
            alert("Upload ảnh lỗi");
          }
        }
      }
      fd.append("gallery_paths", JSON.stringify(uploadedImages));
      const res = await fetch("/api/admin/articlelist.php?act=create", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        navigate(`/${module}`);
      } else {
        alert(result.message || "Lỗi thêm");
      }
    } finally {
      setSaving(false); // bắt đầu
    }
  };
  /* ================= RENDER ================= */

  return (
    <main className="page-editor">
      <div className="action-bar">
        <button
          className="c-btn btn-save"
          onClick={handleCreate}
          disabled={saving}
        >
          {saving ? (
            <>
              <i className="fa fa-spinner fa-spin"></i> Đang lưu...
            </>
          ) : (
            <>
              <i className="fa-regular fa-floppy-disk"></i> Lưu
            </>
          )}
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
                  {fieldMap.short && (
                    <div className="form-group">
                      <label>Mô tả vắn tắt</label>
                      <textarea
                        className="form-textarea"
                        value={langData.short || ""}
                        onChange={(e) =>
                          handleLangChange(lang.id, "short", e.target.value)
                        }
                      />
                    </div>
                  )}
                  {fieldMap.content && (
                    <div className="form-group">
                      <label>Mô tả chi tiết</label>
                      <Editor
                        value={langData.content || ""}
                        onChange={(v) =>
                          handleLangChange(lang.id, "content", v)
                        }
                      />
                    </div>
                  )}
                  {fieldMap.tag && (
                    <div className="form-group">
                      <label>Meta tags</label>
                      <MetaKeywords
                        value={langData.keyword || ""}
                        onChange={(v) =>
                          handleLangChange(lang.id, "keyword", v)
                        }
                      />
                    </div>
                  )}
                  {fieldMap.meta_description && (
                    <div className="form-group">
                      <label>Meta description</label>
                      <textarea
                        className="form-textarea"
                        value={langData.des || ""}
                        onChange={(e) =>
                          handleLangChange(lang.id, "des", e.target.value)
                        }
                      />
                    </div>
                  )}
                </div>
              );
            })}
        </div>

        {/* SIDEBAR */}
        <div className="editor-sidebar">
          <div className="editor-card">
            {fieldMap.hinhanh && (
              <div className="form-group">
                <label>Ảnh đại diện</label>
                <UploadImage
                  currentImage={form.img_thumb_vn}
                  onChange={(file) =>
                    setFileMap((p) => ({ ...p, hinhanh: file }))
                  }
                />
              </div>
            )}
            {fieldMap.multi_images && (
              <div className="form-group">
                <label>Ảnh liên quan (upload nhiều hình cùng lúc)</label>
                <UploadMultipleImages
                  images={multiimages}
                  setImages={setGallery}
                />
              </div>
            )}
            {fieldMap.category && (
              <div className="form-group">
                <label>Danh mục</label>
                <div className="cat-tree">{renderTree(categories)}</div>
              </div>
            )}
            {fieldMap.price && (
              <div className="form-group">
                <label>Giá</label>
                <input
                  type="text"
                  className="form-control"
                  value={
                    form.price ? Number(form.price).toLocaleString("vi-VN") : ""
                  }
                  onChange={(e) =>
                    handleChange("price", e.target.value.replace(/\D/g, ""))
                  }
                />
              </div>
            )}
            {fieldMap.priceold && (
              <div className="form-group">
                <label>Giá cũ</label>
                <input
                  type="text"
                  className="form-control"
                  value={
                    form.priceold
                      ? Number(form.priceold).toLocaleString("vi-VN")
                      : ""
                  }
                  onChange={(e) =>
                    handleChange("priceold", e.target.value.replace(/\D/g, ""))
                  }
                />
              </div>
            )}
            {/* {sideFields.map((key) => (
              <div
                key={key}
                className={`form-group ${
                  toggleFields.includes(key) ? "c-active" : ""
                }`}
              >
                <label>{fieldLabels[key] || key}</label>
                {renderField(key)}
              </div>
            ))} */}
          </div>
        </div>
      </div>
    </main>
  );
}
