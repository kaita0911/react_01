import { useParams, useNavigate } from "react-router-dom";
import { useState, useEffect } from "react";
import { slugify } from "@/utils/slugify";
import UploadImage from "@/pages/components/UploadImage";
export default function CategoryEdit() {
  const { module, id } = useParams();
  const navigate = useNavigate();
  const [fields, setFields] = useState([]);
  const [fileMap, setFileMap] = useState({});
  const [languages, setLanguages] = useState([]);
  const [activeTab, setActiveTab] = useState(null);
  const [compId, setCompId] = useState(null);
  //const [modules, setModules] = useState([]);
  const [categories, setCategories] = useState([]);

  //const comp = modules.find((m) => m.do === module)?.id ?? null;
  const [selectedId, setSelectedId] = useState(null);

  const [form, setForm] = useState({
    parent_id: 0,
    active: 1,
    languages: {},
  });
  const sidebarFields = [
    "hinhdanhmuc",
    "nhieuhinh",
    "active",
    "hot",
    "mostview",
    "new",
    "price",
    "priceold",
  ];
  const toggleFields = ["active", "hot", "mostview", "new"];
  const sideFields = fields.filter((f) => sidebarFields.includes(f));
  const fieldLabels = {
    hinhdanhmuc: "Ảnh danh mục",
    des: "Mô tả chi tiết",
    short: "Mô tả ngắn",
    active: "Active",
    hot: "Nổi bật",
    mostview: "Xem nhiều",
    new: "Mới",
    price: "Giá",
    priceold: "Giá cũ",
  };
  /* ================= RENDER FIELD ================= */

  const renderField = (key) => {
    if (key === "hinhdanhmuc") {
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

    // if (priceFields.includes(key)) {
    //   return (
    //     <input
    //       type="text"
    //       className="form-control"
    //       value={form[key] ? Number(form[key]).toLocaleString("vi-VN") : ""}
    //       onChange={(e) => handleChange(key, e.target.value.replace(/\D/g, ""))}
    //     />
    //   );
    // }

    return (
      <input
        type="text"
        className="form-control"
        value={form[key] || ""}
        onChange={(e) => handleChange(key, e.target.value)}
      />
    );
  };
  /* ================= LOAD DATA ================= */
  useEffect(() => {
    const loadData = async () => {
      ///Gọi thuộc tính module
      const comp = await fetch(
        `/api/admin/component.php?act=comp&module=${module}`
      ).then((r) => r.json());
      //console.log("DETAIL DATA:", comp.data.id);
      if (comp.status) {
        setFields(Object.keys(comp.data));
        setCompId(comp.data.id); // ✅ lưu compId
      }
      // languages
      const lang = await fetch("/api/admin/language.php?act=list").then((r) =>
        r.json()
      );

      if (lang.status) {
        const activeLang = lang.data.filter((l) => l.active == 1);
        setLanguages(activeLang);
        if (activeLang.length) setActiveTab(activeLang[0].id);
      }
      if (id) {
        const res = await fetch(`/api/admin/category.php?act=detail&id=${id}`);
        const data = await res.json();

        if (!data.status) return;

        const cat = data.category;

        const langs = {};

        data.languages.forEach((l) => {
          langs[l.languageid] = {
            name: l.name,
            slug: l.slug || "",
          };
        });

        setSelectedId(cat.parent_id);

        setForm({
          parent_id: cat.parent_id,
          active: cat.active,
          languages: langs,
        });
      }
      ////load cate

      if (comp.status) {
        const res = await fetch(
          `/api/admin/category.php?act=list&comp=${comp.data.id}`
        );
        const data = await res.json();

        if (data.status) setCategories(data.data);
      }
    };
    loadData();
  }, [module, id]);

  /* ================= LOAD DETAIL ================= */

  // const loadDetail = async () => {};

  // useEffect(() => {
  //   const init = async () => {
  //     await loadModules();
  //     await loadLanguages();
  //   };

  //   init();
  // }, []);

  // useEffect(() => {
  //   if (comp === null) return;

  //   const load = async () => {
  //     await loadCategories(comp);
  //     await loadDetail();
  //   };

  //   load();
  // }, [comp]);

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
              {item.name}
            </span>
          </label>

          {item.children?.length > 0 && renderTree(item.children, level + 1)}
        </div>
      );
    });
  };

  /* ================= UPDATE va add================= */

  const handleSave = async () => {
    const fd = new FormData();

    fd.append("id", id);
    fd.append("comp", compId);
    fd.append("parent_id", form.parent_id);
    fd.append("active", form.active);
    fd.append("languages", JSON.stringify(form.languages));

    for (const k in fileMap) {
      fd.append(k, fileMap[k]);
    }
    const res = await fetch("/api/admin/category.php?act=add", {
      method: "POST",
      body: fd,
    });

    const result = await res.json();

    if (result.status) {
      navigate(`/${module}/category`);
    } else {
      alert("Lỗi cập nhật");
    }
  };

  /* ================= RENDER ================= */

  return (
    <main className="page-editor">
      <div className="action-bar">
        <button className="c-btn btn-save" onClick={handleSave}>
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
