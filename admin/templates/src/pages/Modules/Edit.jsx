import { useParams, useNavigate } from "react-router-dom";
import { useState, useEffect, useMemo } from "react";
import Editor from "@/utils/Ckeditor";
import UploadImage from "@/pages/components/UploadImage";
import UploadMultipleImages from "@/pages/components/UploadMultipleImages";
import MetaKeywords from "@/utils/MetaKeywords";
import { slugify } from "@/utils/slugify";

export default function Edit() {
  const { module, id } = useParams();
  const navigate = useNavigate();

  const [fields, setFields] = useState([]);
  const [languages, setLanguages] = useState([]);
  const [activeTab, setActiveTab] = useState(null);
  const [categories, setCategories] = useState([]);
  const [compId, setCompId] = useState(null);
  const [compInfo, setCompInfo] = useState({});
  const [form, setForm] = useState({ languages: {} });
  const [fileMap, setFileMap] = useState({});
  const [gallery, setGallery] = useState([]);
  const [deletedGallery, setDeletedGallery] = useState([]);
  const [saving, setSaving] = useState(false);
  /* ================= load component ================= */
  useEffect(() => {
    const loadComponent = async () => {
      const res = await fetch(
        `/api/admin/component.php?act=comp&module=${module}`
      );

      const data = await res.json();

      if (data.status) {
        setCompId(data.data.id);
        const compInfolst = data.data;
        setCompInfo(compInfolst); // ⭐ lưu toàn bộ component
      }
    };

    loadComponent();
  }, [module]);
  /* ================= load field languges cate ================= */
  useEffect(() => {
    if (!compId) return;

    const loadData = async () => {
      const [fieldRes, langRes, cateRes] = await Promise.all([
        fetch(
          `/api/admin/component_fields.php?act=list&component=${compId}&target=article`
        ),
        fetch(`/api/admin/language.php?act=list`),
        fetch(`/api/admin/category.php?act=list&comp=${compId}`),
      ]);

      const fieldData = await fieldRes.json();
      const langData = await langRes.json();
      const cateData = await cateRes.json();

      if (fieldData.status) setFields(fieldData.data);
      // console.log(fieldData.data);
      if (langData.status) {
        const activeLang = langData.data.filter((l) => l.active == 1);
        setLanguages(activeLang);
        if (activeLang.length) setActiveTab(activeLang[0].id);
      }

      if (cateData.status) setCategories(cateData.data);
    };

    loadData();
  }, [compId]);
  /* ================= LOAD DATA ================= */

  useEffect(() => {
    const loadData = async () => {
      if (id) {
        const detail = await fetch(
          `/api/admin/articlelist.php?act=detail&id=${id}`
        ).then((r) => r.json());

        if (detail.status) {
          const d = detail.data;

          setForm({
            parent_id: d.parent_id, // QUAN TRỌNG
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
  /* ================= HELP ================= */

  const fieldMap = useMemo(() => {
    const map = {};
    fields.forEach((f) => (map[f.name] = f));
    return map;
  }, [fields]);

  /* ================= UPDATE FORM ================= */

  const handleChange = (key, value) => {
    setForm((p) => ({ ...p, [key]: value }));
  };

  const handleLangChange = (langId, field, value) => {
    const key = String(langId); // ⭐ FIX

    setForm((p) => ({
      ...p,
      languages: {
        ...p.languages,
        [key]: {
          ...p.languages[key],
          [field]: value,
        },
      },
    }));
  };
  const handleSelect = (id) => {
    setForm((p) => ({ ...p, parent_id: id }));
  };
  /* ================= UPDATE ================= */

  const handleUpdate = async () => {
    if (saving) return; // tránh spam click

    setSaving(true);

    try {
      const fd = new FormData();

      fd.append("id", id);
      fd.append("module", module);
      fd.append("parent_id", form.parent_id || 0);

      // form thường
      for (const k in form) {
        if (k === "languages") {
          fd.append("languages", JSON.stringify(form.languages));
        } else if (k !== "parent_id") {
          fd.append(k, form[k] ?? "");
        }
      }

      // file đơn
      for (const k in fileMap) {
        if (fileMap[k]) {
          fd.append(k, fileMap[k]);
        }
      }

      const uploadedImages = [];
      if (fieldMap.multi_images) {
        // 👉 upload từng ảnh mới
        for (let i = 0; i < gallery.length; i++) {
          const img = gallery[i];

          if (img instanceof File) {
            const fdImg = new FormData();
            fdImg.append("file", img);

            const res = await fetch("/api/admin/articlelist/upload-image.php", {
              method: "POST",
              body: fdImg,
            });

            const data = await res.json();

            if (data.status) {
              uploadedImages.push({
                path: data.path,
                num: i,
              });
            } else {
              alert("Upload ảnh lỗi");
            }
          }

          // 👉 ảnh cũ (chỉ update vị trí)
          else if (img?.id) {
            fd.append(
              "gallery_update[]",
              JSON.stringify({
                id: img.id,
                num: i,
              })
            );
          }
        }
        fd.append("delete_gallery", JSON.stringify(deletedGallery || []));
        fd.append("gallery_new", JSON.stringify(uploadedImages));
      }

      const res = await fetch("/api/admin/articlelist.php?act=update", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) {
        navigate(`/${module}`);
      } else {
        alert(result.message || "Lỗi cập nhật");
      }
    } catch (err) {
      console.error(err);
      alert("Lỗi kết nối server");
    } finally {
      setSaving(false);
    }
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

  /* ================= RENDER ================= */

  return (
    <main className="page-editor">
      <div className="action-bar">
        <button className="c-btn btn-save" onClick={handleUpdate}>
          {saving ? (
            <>
              <i className="fa fa-spinner fa-spin"></i> Đang cập nhật...
            </>
          ) : (
            <>
              <i className="fa-regular fa-floppy-disk"></i> Cập nhật
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
                        handleLangChange(lang.id, {
                          name: v,
                          slug: slugify(v),
                        });
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
                  currentImage={form.hinhanh}
                  onChange={(file) => {
                    setFileMap((p) => ({ ...p, hinhanh: file }));
                    handleChange("hinhanh", file.name);
                  }}
                />
              </div>
            )}
            {fieldMap.multi_images && (
              <div className="form-group">
                <label>Ảnh liên quan (upload nhiều hình cùng lúc)</label>
                <UploadMultipleImages
                  images={gallery}
                  setImages={setGallery}
                  setDeletedIds={setDeletedGallery}
                />
              </div>
            )}
            {compInfo.nhomcon && (
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
          </div>
        </div>
      </div>
    </main>
  );
}
