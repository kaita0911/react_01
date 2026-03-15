import { useState } from "react";

export default function MetaKeywords({ value = "", onChange }) {
  const [input, setInput] = useState("");

  const tags = value ? value.split(",").filter(Boolean) : [];

  const addTag = () => {
    const v = input.trim();
    if (!v) return;

    const newTags = [...tags, v];
    onChange(newTags.join(","));
    setInput("");
  };

  const removeTag = (index) => {
    const newTags = tags.filter((_, i) => i !== index);
    onChange(newTags.join(","));
  };

  return (
    <div className="meta-keywords">
      <div className="tag-wrapper">
        {tags.map((tag, i) => (
          <span key={i} className="tag">
            {tag}
            <button type="button" onClick={() => removeTag(i)}>
              ×
            </button>
          </span>
        ))}

        <input
          type="text"
          value={input}
          placeholder="Nhập keyword rồi nhấn Enter"
          onChange={(e) => setInput(e.target.value)}
          onKeyDown={(e) => e.key === "Enter" && addTag()}
        />
      </div>
    </div>
  );
}
