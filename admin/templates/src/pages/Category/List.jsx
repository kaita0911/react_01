import { useEffect, useState } from "react";
import { useParams, Link, useNavigate } from "react-router-dom";
import { API_URL } from "@/config";
import { DndContext, closestCenter } from "@dnd-kit/core";
import {
  SortableContext,
  verticalListSortingStrategy,
  useSortable,
  arrayMove,
} from "@dnd-kit/sortable";

import { CSS } from "@dnd-kit/utilities";

/* ================= ROW ================= */

function SortableRow({ item, children }) {
  const { attributes, listeners, setNodeRef, transform, transition } =
    useSortable({ id: item.id });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
  };

  return (
    <tr ref={setNodeRef} style={style}>
      {children({ attributes, listeners })}
    </tr>
  );
}

/* ================= PAGE ================= */

export default function CategoryList() {
  const { module } = useParams();
  const [componentFields, setComponentFields] = useState([]);
  const navigate = useNavigate();

  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  /* ================= FLATTEN ================= */

  const flatten = (list, level = 0, parent = 0) => {
    let arr = [];

    list.forEach((item) => {
      arr.push({
        ...item,
        level,
        parent_id: parent,
      });

      if (item.children?.length) {
        arr = arr.concat(flatten(item.children, level + 1, item.id));
      }
    });

    return arr;
  };
  useEffect(() => {
    const loadData = async () => {
      /* ================= Load module ================= */
      const res_module = await fetch(
        `/api/admin/component.php?act=comp&module=${module}`
      );
      const comp = await res_module.json();

      if (!comp.status) return;

      const compId = comp.data;

      /* ================= Load fields ================= */

      const res_fields = await fetch(
        `/api/admin/component_fields.php?act=list&component=${compId}&target=category`
      );
      const fields = await res_fields.json();
      if (fields.status) {
        setComponentFields(fields.data);
        // console.log(fields.data);
      }
      /* ================= Load cate ================= */
      const res_cat = await fetch(
        `/api/admin/category.php?act=list&comp=${compId}`
      );

      const cat = await res_cat.json();

      if (cat.status) {
        setRows(flatten(cat.data));
      }

      setLoading(false);
    };
    loadData();
  });

  /* ================= DELETE ================= */

  const handleDelete = async (id) => {
    if (!window.confirm("Xóa danh mục này?")) return;

    const fd = new FormData();
    fd.append("id", id);

    const res = await fetch("/api/admin/category.php?act=delete", {
      method: "POST",
      body: fd,
    });

    const data = await res.json();

    if (data.status) {
      setRows((prev) => prev.filter((item) => item.id !== id));
    }
  };

  /* ================= TREE REBUILD ================= */

  const rebuildTree = (list) => {
    return list.map((item, index) => {
      let parent_id = 0;

      for (let i = index - 1; i >= 0; i--) {
        if (list[i].level < item.level) {
          parent_id = list[i].id;
          break;
        }
      }

      return {
        ...item,
        parent_id,
        num: index + 1,
      };
    });
  };

  /* ================= SAVE ================= */

  const saveTree = async (list) => {
    const fd = new FormData();

    list.forEach((row) => {
      fd.append("id[]", row.id);
      fd.append("parent_id[]", row.parent_id);
      fd.append("num[]", row.num);
    });

    await fetch("/api/admin/category.php?act=reorder_tree", {
      method: "POST",
      body: fd,
    });
  };

  /* ================= DRAG ================= */

  const handleDragEnd = async (event) => {
    const { active, over } = event;

    if (!over || active.id === over.id) return;

    const oldIndex = rows.findIndex((r) => r.id === active.id);
    const newIndex = rows.findIndex((r) => r.id === over.id);

    const moved = arrayMove(rows, oldIndex, newIndex);

    const updated = rebuildTree(moved);

    setRows(updated);

    saveTree(updated);
  };

  /* ================= INDENT RIGHT ================= */

  const indentRight = (id) => {
    const index = rows.findIndex((r) => r.id === id);

    if (index <= 0) return;

    const newRows = [...rows];

    newRows[index].level++;

    const updated = rebuildTree(newRows);

    setRows(updated);
    saveTree(updated);
  };

  /* ================= INDENT LEFT ================= */

  const indentLeft = (id) => {
    const index = rows.findIndex((r) => r.id === id);

    const newRows = [...rows];

    newRows[index].level = Math.max(0, newRows[index].level - 1);

    const updated = rebuildTree(newRows);

    setRows(updated);
    saveTree(updated);
  };

  /* ================= LOADING ================= */

  if (loading) {
    return <div>Loading...</div>;
  }
  // ================= TOGGLE ACTIVE =================
  const handleToggle = async (id, column, currentValue) => {
    const newValue = currentValue == 1 ? 0 : 1;

    const formData = new FormData();
    formData.append("id", id);
    formData.append("table", "categories");
    formData.append("column", column);
    formData.append("value", newValue);

    try {
      const res = await fetch("/api/admin/update-active.php", {
        method: "POST",
        body: formData,
      });

      const result = await res.json();
      // console.log(result);

      if (result.success) {
        setRows((prev) =>
          prev.map((item) =>
            item.id === id ? { ...item, [column]: newValue } : item
          )
        );
      }
    } catch {
      alert("Lỗi server");
    }
  };
  /* ================= RENDER fields ================= */
  const getField = (name) => {
    return componentFields.find((f) => f.name === name);
  };
  /* ================= RENDER ================= */

  return (
    <main>
      <div className="action-bar">
        <Link to={`/${module}/category/create`} className="c-btn btn-add">
          <i className="fa-solid fa-circle-plus"></i> Thêm mới
        </Link>
      </div>

      <DndContext collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
        <table className="admin-table">
          <thead>
            <tr>
              <th className="col-order txt-center">Thứ tự</th>
              {getField("hinhdanhmuc") && (
                <th className="col-order txt-center">Hình</th>
              )}

              <th>Tên</th>
              {getField("active_cate_home") && (
                <th className="col-action txt-center">Hiện trang chủ</th>
              )}
              <th className="col-status txt-center">Active</th>
              <th className="txt-center">Action</th>
            </tr>
          </thead>

          <SortableContext
            items={rows.map((r) => r.id)}
            strategy={verticalListSortingStrategy}
          >
            <tbody>
              {rows.map((item) => (
                <SortableRow key={item.id} item={item}>
                  {({ attributes, listeners }) => (
                    <>
                      <td className="txt-center">{item.num}</td>
                      {getField("hinhdanhmuc") && (
                        <td className="txt-center">
                          {item.img_thumb_vn && (
                            <img
                              className="img-thumbs"
                              src={API_URL + `/${item.img_vn}`}
                              alt={item.name}
                            />
                          )}
                        </td>
                      )}
                      <td>
                        <div
                          className="cat-name"
                          style={{ paddingLeft: item.level * 20 }}
                        >
                          {"-- ".repeat(item.level)}
                          {item.name}
                        </div>
                      </td>
                      {getField("active_cate_home") && (
                        <td className="txt-center">
                          <label className="switch">
                            <input
                              type="checkbox"
                              checked={item.home == 1}
                              onChange={() => {
                                console.log("home:", item.home);
                                handleToggle(item.id, "home", item.home);
                              }}
                            />
                            <span className="slider"></span>
                          </label>
                        </td>
                      )}

                      <td className="txt-center">
                        <label className="switch">
                          <input
                            type="checkbox"
                            checked={item.active == 1}
                            onChange={() =>
                              handleToggle(item.id, "active", item.active)
                            }
                          />
                          <span className="slider"></span>
                        </label>
                      </td>
                      <td className="col-actions txt-center">
                        <div className="btn-actions">
                          <button
                            className="act btn-edit"
                            onClick={() =>
                              navigate(`/${module}/category/edit/${item.id}`)
                            }
                          >
                            <i className="fa-solid fa-pen"></i>
                          </button>

                          <button
                            className="act btn-delete"
                            onClick={() => handleDelete(item.id)}
                          >
                            <i className="fa-solid fa-trash"></i>
                          </button>
                          <button
                            className="act drag-handle"
                            onClick={() => indentLeft(item.id)}
                          >
                            <i className="fa-solid fa-outdent"></i>
                          </button>

                          <button
                            className="act drag-handle"
                            onClick={() => indentRight(item.id)}
                          >
                            <i className="fa-solid fa-indent"></i>
                          </button>
                          <button
                            className="act drag-handle"
                            {...attributes}
                            {...listeners}
                          >
                            <i className="fa-solid fa-up-down"></i>
                          </button>
                        </div>
                      </td>
                    </>
                  )}
                </SortableRow>
              ))}
            </tbody>
          </SortableContext>
        </table>
      </DndContext>
    </main>
  );
}
