import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import Editor from "@/utils/Ckeditor";
//import { API_URL } from "@/config";
// import { CKEditor } from "@ckeditor/ckeditor5-react";
// import { editorConfig } from "@/utils/ckeditorConfig";

export default function FooterEdit() {
  // const [content, setContent] = useState("");
  const navigate = useNavigate();
  const { id } = useParams();

  const [languages, setLanguages] = useState([]);
  const [loading, setLoading] = useState(true);

  const [form, setForm] = useState({
    email: "",
    hotline: "",
    active: 1,
    languages: {},
  });

  // ================= LOAD FOOTER =================
  const [activeTab, setActiveTab] = useState(null);
  const fetchLanguages = async () => {
    try {
      const res = await fetch("/api/admin/language.php?act=list");
      const result = await res.json();

      if (result.status) {
        const activeLanguages = result.data
          .filter((lang) => lang.active == 1)
          .sort((a, b) => (a.code === "vi" ? -1 : b.code === "vi" ? 1 : 0));

        setLanguages(activeLanguages);
        // tab mặc định
        if (activeLanguages.length > 0) {
          setActiveTab(activeLanguages[0].id);
        }
        const langObj = {};

        activeLanguages.forEach((lang) => {
          langObj[lang.id] = {
            name: "",
            address: "",
            content: "",
          };
        });

        setForm((prev) => ({
          ...prev,
          languages: langObj,
        }));
      }

      setLoading(false);
    } catch (err) {
      console.error(err);
    }
  };
  const fetchFooter = async () => {
    if (id === "new") return;

    try {
      const res = await fetch(`/api/admin/footer.php?act=detail&id=${id}`);
      const result = await res.json();

      if (result.success) {
        setForm((prev) => ({
          ...prev,
          email: result.data.email || "",
          hotline: result.data.hotline || "",
          active: result.data.active || 1,
          languages: {
            ...prev.languages,
            ...result.data.languages,
          },
        }));
      }
    } catch (err) {
      console.error(err);
    }
  };
  useEffect(() => {
    fetchLanguages();
  }, []);

  useEffect(() => {
    if (languages.length > 0) {
      fetchFooter();
    }
  }, [languages]);
  // ================= CHANGE LANGUAGE =================
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

  // ================= SAVE =================
  const handleSubmit = async () => {
    const fd = new FormData();

    fd.append("email", form.email);
    fd.append("hotline", form.hotline);
    fd.append("active", form.active);
    fd.append("languages", JSON.stringify(form.languages));

    if (id !== "new") {
      fd.append("id", id);
    }

    try {
      const res = await fetch("/api/admin/footer.php?act=save", {
        method: "POST",
        body: fd,
      });

      const result = await res.json();

      if (result.success) {
        navigate("/footer");
      }
    } catch (err) {
      console.error(err);
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <main className="page-editor">
      {/* ACTION BAR */}
      <div className="action-bar">
        <button className="c-btn btn-save" onClick={handleSubmit}>
          <i className="fa-regular fa-floppy-disk"></i> Lưu
        </button>

        <button
          className="c-btn btn-cancel"
          onClick={() => navigate("/footer")}
        >
          Huỷ
        </button>
      </div>

      <div className="editor-layout">
        {/* LEFT CONTENT */}
        <div className="editor-main">
          {/* LANGUAGE TABS */}
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
                <div key={lang.id} className="editor-card">
                  <label>Tiêu đề</label>
                  <input
                    value={langData.name || ""}
                    onChange={(e) =>
                      handleLangChange(lang.id, "name", e.target.value)
                    }
                  />

                  <label>Địa chỉ</label>
                  <input
                    value={langData.address || ""}
                    onChange={(e) =>
                      handleLangChange(lang.id, "address", e.target.value)
                    }
                  />

                  <label>Nội dung</label>

                  <div className="editor-box">
                    <Editor
                      key={lang.id}
                      value={langData.content || ""}
                      onChange={(value) =>
                        handleLangChange(lang.id, "content", value)
                      }
                    />
                  </div>
                </div>
              );
            })}
        </div>

        {/* RIGHT SIDEBAR */}
        <div className="editor-sidebar">
          <div className="editor-card">
            <label>Email</label>
            <input
              value={form.email}
              onChange={(e) =>
                setForm((prev) => ({ ...prev, email: e.target.value }))
              }
            />

            <label>Hotline</label>
            <input
              value={form.hotline}
              onChange={(e) =>
                setForm((prev) => ({ ...prev, hotline: e.target.value }))
              }
            />

            <label>Hiển thị</label>
            <label className="switch">
              <input
                type="checkbox"
                checked={form.active == 1}
                onChange={(e) =>
                  setForm((prev) => ({
                    ...prev,
                    active: e.target.checked ? 1 : 0,
                  }))
                }
              />
              <span className="slider"></span>
            </label>
          </div>
        </div>
      </div>
    </main>
  );
}
