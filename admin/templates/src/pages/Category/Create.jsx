import { useParams, useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";
import { slugify } from "@/utils/slugify";

export default function CategoryCreate() {
  const { module } = useParams();
  const navigate = useNavigate();

  const [languages, setLanguages] = useState([]);
  const [activeTab, setActiveTab] = useState(null);

  const [modules, setModules] = useState([]);
  const [categories, setCategories] = useState([]);

  const comp = modules.find((m) => m.do === module)?.id ?? null;

  const [form, setForm] = useState({
    parent_id: 0,
    active: 1,
    languages: {},
  });

  const [selectedId, setSelectedId] = useState(null);

  /* ================= LOAD ================= */

  const loadModules = async () => {
    const res = await fetch("/api/admin/component.php?act=list");
    const data = await res.json();

    if (data.status) setModules(data.data);
  };

  const loadCategories = async (compId) => {
    const res = await fetch(`/api/admin/category.php?act=list&comp=${compId}`);
    const data = await res.json();

    if (data.status) setCategories(data.data);
  };

  const loadLanguages = async () => {
    const res = await fetch("/api/admin/language.php?act=list");
    const data = await res.json();

    if (data.status) {
      const activeLang = data.data.filter((l) => l.active == 1);

      setLanguages(activeLang);

      if (activeLang.length) {
        setActiveTab(activeLang[0].id);
      }
    }
  };

  useEffect(() => {
    const init = async () => {
      await loadModules();
      await loadLanguages();
    };

    init();
  }, []);

  useEffect(() => {
    if (comp === null) return;

    const load = async () => {
      await loadCategories(comp);
    };

    load();
  }, [comp]);
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

  const handleChange = (key, value) => {
    setForm((p) => ({ ...p, [key]: value }));
  };

  /* ================= SELECT CATEGORY ================= */

  const handleSelect = (id) => {
    setSelectedId(id);

    setForm((p) => ({
      ...p,
      parent_id: id,
    }));
  };

  /* ================= TREE ================= */
  const findParentIds = (id, list, parents = []) => {
    for (let item of list) {
      if (item.id === id) return parents;

      if (item.children?.length) {
        const found = findParentIds(id, item.children, [...parents, item.id]);
        if (found) return found;
      }
    }

    return [];
  };
  const renderTree = (list, level = 0) => {
    const parents = findParentIds(selectedId, categories);

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
              {item.name}
            </span>
          </label>

          {item.children?.length > 0 && renderTree(item.children, level + 1)}
        </div>
      );
    });
  };

  /* ================= CREATE ================= */

  const handleCreate = async () => {
    const fd = new FormData();

    fd.append("comp", comp);
    fd.append("parent_id", form.parent_id);
    fd.append("active", form.active);
    fd.append("languages", JSON.stringify(form.languages));

    const res = await fetch("/api/admin/category.php?act=add", {
      method: "POST",
      body: fd,
    });

    const result = await res.json();

    if (result.status) {
      navigate(`/${module}/category`);
    } else {
      alert("Lỗi thêm danh mục");
    }
  };

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

                        handleLangChange(lang.id, "name", v);
                        handleLangChange(lang.id, "slug", slugify(v));
                      }}
                    />
                  </div>

                  <div className="form-group">
                    <label>Slug</label>
                    <input value={langData.slug || ""} readOnly />
                  </div>
                </div>
              );
            })}
        </div>

        {/* SIDEBAR */}

        <div className="editor-sidebar">
          <div className="editor-card">
            <div className="form-group">
              <label>Danh mục cha</label>

              <div className="cat-tree">{renderTree(categories)}</div>
            </div>

            <div className="form-group c-active">
              <label>Active</label>

              <label className="switch">
                <input
                  type="checkbox"
                  checked={form.active === 1}
                  onChange={(e) =>
                    handleChange("active", e.target.checked ? 1 : 0)
                  }
                />

                <span className="slider"></span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </main>
  );
}
