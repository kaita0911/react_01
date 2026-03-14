import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
export default function ComponentFields() {
  const { component } = useParams();
  const navigate = useNavigate();
  const [fields, setFields] = useState([]);
  const [selected, setSelected] = useState([]);
  console.log("component:", component);
  /* ================= LOAD ================= */

  const loadData = async () => {
    /* all fields */

    const res1 = await fetch("/api/admin/fields.php?act=list");
    const data1 = await res1.json();

    /* component fields */

    const res2 = await fetch(
      `/api/admin/component_fields.php?act=list&component=${component}`
    );
    const data2 = await res2.json();

    if (data1.status) {
      setFields(data1.data);
    }

    if (data2.status) {
      const ids = data2.data.map((f) => f.field_id);
      console.log(ids);
      setSelected(ids);
    }
  };

  useEffect(() => {
    const init = async () => {
      await loadData();
    };
    init();
  }, []);
  /* ================= GROUP FIELD ================= */
  const order = ["article", "category"];
  const groupFields = () => {
    const groups = {};

    fields.forEach((f) => {
      if (!groups[f.target]) {
        groups[f.target] = [];
      }

      groups[f.target].push(f);
    });

    return groups;
  };

  const fieldGroups = groupFields();

  /* ================= CHECK ================= */

  const toggleField = (id) => {
    if (selected.includes(id)) {
      setSelected(selected.filter((i) => i !== id));
    } else {
      setSelected([...selected, id]);
    }
  };

  /* ================= SAVE ================= */

  const save = async () => {
    const fd = new FormData();

    fd.append("component", component);

    selected.forEach((id) => {
      fd.append("fields[]", id);
    });

    const res = await fetch("/api/admin/component_fields.php?act=save", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      navigate("/component");
    }
  };

  /* ================= UI ================= */

  return (
    <div>
      <div className="component-edit">
        <div className="action-bar">
          <button onClick={save} className="c-btn btn-save">
            <i className="fa fa-save"></i> Save
          </button>
        </div>
        {Object.keys(fieldGroups)
          .sort((a, b) => order.indexOf(a) - order.indexOf(b))
          .map((target) => (
            <fieldset key={target}>
              <legend>Nhóm {target}</legend>

              <div className="box-feature">
                {fieldGroups[target].map((f) => (
                  <label className="feature-item" key={f.id}>
                    <span>
                      <small>{f.name}</small>
                      {f.label}
                    </span>

                    <input
                      type="checkbox"
                      checked={selected.includes(f.id)}
                      onChange={() => toggleField(f.id)}
                    />
                  </label>
                ))}
              </div>
            </fieldset>
          ))}
      </div>
      <br />
    </div>
  );
}
