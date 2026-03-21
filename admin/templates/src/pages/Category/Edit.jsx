import { useParams, useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";
import { slugify } from "@/utils/slugify";
import Editor from "@/utils/Ckeditor";
import UploadImage from "@/pages/components/UploadImage";
import MetaKeywords from "@/utils/MetaKeywords";
export default function CategoryEdit() {
  const { module, id } = useParams();
  const navigate = useNavigate();

  const [compId, setCompId] = useState(null);
  const [fields, setFields] = useState([]);
  const [languages, setLanguages] = useState([]);
  const [categories, setCategories] = useState([]);

  const [fileMap, setFileMap] = useState({});
  const [activeTab, setActiveTab] = useState(null);
  const [selectedId, setSelectedId] = useState(0);
  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState({
    parent_id: 0,
    languages: {},
  });
  /* ================= load component ================= */
  useEffect(() => {
    const loadComponent = async () => {
      const res = await fetch(
        `/api/admin/component.php?act=comp&module=${module}`
      );

      const data = await res.json();

      if (data.status) {
        setCompId(data.data.id);
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
          `/api/admin/component_fields.php?act=list&component=${compId}&target=category`
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
  /* ================= load detail ================= */
  const buildLangData = (languages, fields, dbLangs = []) => {
    const result = {};

    languages.forEach((lang) => {
      result[lang.id] = {};

      // tạo field mặc định theo DB fields
      fields
        .filter((f) => f.lang == 1)
        .forEach((f) => {
          result[lang.id][f.name] = "";
        });

      // gán dữ liệu từ DB
      const row = dbLangs.find((x) => x.languageid == lang.id);

      if (row) {
        Object.keys(row).forEach((k) => {
          if (k !== "languageid") {
            result[lang.id][k] = row[k];
          }
        });
      }
    });

    return result;
  };
  useEffect(() => {
    const loadDetail = async () => {
      const res = await fetch(`/api/admin/category.php?act=detail&id=${id}`);
      const data = await res.json();

      if (!data.status) return;

      const cat = data.category;
      const langs = buildLangData(languages, fields, data.languages);

      setSelectedId(cat.parent_id);

      setForm({
        parent_id: cat.parent_id,
        ...cat,
        languages: langs,
      });
    };

    loadDetail();
  }, [id, languages, fields]);

  /* ================= RENDER fields ================= */
  const getField = (name) => {
    return fields.find((f) => f.name === name);
  };

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
  /* ================= SELECT CATEGORY ================= */

  const handleSelect = (id) => {
    setSelectedId(id);

    setForm((p) => ({
      ...p,
      parent_id: id,
    }));
  };

  /* ================= FIND PARENT ================= */

  const findParentIds = (id, list, parents = []) => {
    for (const item of list) {
      if (item.id === id) {
        return parents;
      }

      if (item.children?.length) {
        const result = findParentIds(id, item.children, [...parents, item.id]);

        if (result.length) {
          return result;
        }
      }
    }

    return [];
  };
  /* ================= TREE ================= */
  const parentIds = findParentIds(selectedId, categories);
  const renderTree = (list, level = 0) => {
    const parents = parentIds;
    return list.map((item) => {
      const checked = selectedId === item.id || parents.includes(item.id);

      return (
        <div key={item.id} className="cat-item">
          <label className="cat-label">
            <input
              type="checkbox"
              checked={checked}
              onChange={() => handleSelect(item.id)}
            />

            <span className="cat-name">
              {"-- ".repeat(level)}
              {item.names?.[activeTab] || "-"}
            </span>
          </label>

          {item.children?.length > 0 && renderTree(item.children, level + 1)}
        </div>
      );
    });
  };

  /* ================= UPDATE va add================= */

  const handleSave = async () => {
    setSaving(true); // bắt đầu cập nhật
    try {
      const fd = new FormData();

      fd.append("id", id);
      fd.append("comp", compId);
      fd.append("parent_id", form.parent_id);
      fd.append("languages", JSON.stringify(form.languages));

      for (const k in form) {
        if (k !== "languages" && k !== "parent_id") {
          fd.append(k, form[k]);
        }
      }

      for (const k in fileMap) {
        fd.append(k, fileMap[k]);
      }

      const res = await fetch("/api/admin/category.php?act=add", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.status) navigate(`/${module}/category`);
      else alert("Lỗi cập nhật danh mục");
    } finally {
      setSaving(false); // kết thúc cập nhật
    }
  };

  /* ================= RENDER ================= */

  return (
    <main className="page-editor">
      <div className="action-bar">
        <button
          className="c-btn btn-save"
          onClick={handleSave}
          disabled={saving}
        >
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
              const langData = form.languages?.[lang.id] || {};

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
                  {getField("content") && (
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
            {getField("hinhdanhmuc") && (
              <div className="form-group">
                <UploadImage
                  currentImage={form.img_vn}
                  onChange={(file) => {
                    setFileMap((p) => ({ ...p, hinhdanhmuc: file })); ///này sẽ truyền vào php
                    handleChange("hinhdanhmuc", file.name);
                  }}
                />
              </div>
            )}
            <div className="form-group">
              <label>Danh mục cha</label>
              <div className="cat-tree">{renderTree(categories)}</div>
            </div>

            {/* <div className="form-group c-active">
              <label>Active</label>
              <label className="switch">
                <input
                  type="checkbox"
                  checked={form.active == 1}
                  onChange={() => {
                    // console.log("home:", form.home);
                    handleToggle(form.id, "active", form.active);
                  }}
                />
                <span className="slider"></span>
              </label>
            </div> */}
          </div>
        </div>
      </div>
    </main>
  );
}
