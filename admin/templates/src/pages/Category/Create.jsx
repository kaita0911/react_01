import { useParams, useNavigate } from "react-router-dom";
import { useState, useEffect, useMemo } from "react";
import { slugify } from "@/utils/slugify";
import UploadImage from "@/pages/components/UploadImage";
import Editor from "@/utils/Ckeditor";
import MetaKeywords from "@/utils/MetaKeywords";
export default function CategoryCreate() {
  const { module } = useParams();
  const navigate = useNavigate();

  const [compId, setCompId] = useState(null);
  const [fields, setFields] = useState([]);
  const [languages, setLanguages] = useState([]);
  const [categories, setCategories] = useState([]);
  const [activeTab, setActiveTab] = useState(null);
  const [fileMap, setFileMap] = useState({});

  const [form, setForm] = useState({
    parent_id: 0,
    active: 1,
    home: 1,
    languages: {},
  });

  /* ================= LOAD DATA ================= */

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
      setCompId(comp);

      const [fieldRes, cateRes] = await Promise.all([
        fetch(
          `/api/admin/component_fields.php?act=list&component=${comp}&target=category`
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

  /* ================= FORM ================= */
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
    const defaultLang = languages[0]; // ngôn ngữ mặc định
    const name = form.languages?.[defaultLang.id]?.name;
    if (!name || name.trim() === "") {
      setActiveTab(defaultLang.id);

      setTimeout(() => {
        document.querySelector("input")?.focus();
      }, 100);

      return; // không cho lưu
    }

    const fd = new FormData();

    fd.append("comp", compId);
    fd.append("parent_id", form.parent_id);
    fd.append("languages", JSON.stringify(form.languages));

    Object.keys(fileMap).forEach((k) => fd.append(k, fileMap[k]));

    const res = await fetch("/api/admin/category.php?act=add", {
      method: "POST",
      body: fd,
    });

    const result = await res.json();

    if (result.status) navigate(`/${module}/category`);
    else alert("Lỗi thêm danh mục");
  };

  /* ================= HELP ================= */

  const fieldMap = useMemo(() => {
    const map = {};
    fields.forEach((f) => (map[f.name] = f));
    return map;
  }, [fields]);

  /* ================= RENDER ================= */

  return (
    <main className="page-editor">
      <div className="action-bar">
        <button className="c-btn btn-save" onClick={handleCreate}>
          <i className="fa-regular fa-floppy-disk"></i> Lưu
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
            .filter((l) => languages.length === 1 || activeTab === l.id)
            .map((lang) => {
              const langData = form.languages?.[lang.id] || {};

              return (
                <div className="editor-card" key={lang.id}>
                  <div className="form-group">
                    <label>Tên</label>

                    <input
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
                  <div className="form-group">
                    <label>Meta tags</label>
                    <MetaKeywords
                      value={langData.keyword || ""}
                      onChange={(v) => handleLangChange(lang.id, "keyword", v)}
                    />
                  </div>
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
                </div>
              );
            })}
        </div>

        {/* SIDEBAR */}

        <div className="editor-sidebar">
          <div className="editor-card">
            {fieldMap.hinhdanhmuc && (
              <div className="form-group">
                <UploadImage
                  currentImage={form.img_vn}
                  onChange={(file) =>
                    setFileMap((p) => ({ ...p, hinhdanhmuc: file }))
                  }
                />
              </div>
            )}

            <div className="form-group">
              <label>Danh mục</label>
              <div className="cat-tree">{renderTree(categories)}</div>
            </div>
          </div>
        </div>
      </div>
    </main>
  );
}
