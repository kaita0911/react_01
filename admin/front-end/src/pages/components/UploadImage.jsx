import { useState } from "react";

export default function UploadImage({ currentImage, onChange }) {
  const API_URL = import.meta.env.VITE_API_URL;

  const [previewLocal, setPreviewLocal] = useState(null);
  const [fileName, setFileName] = useState("Chưa chọn file");

  const preview =
    previewLocal || (currentImage ? `${API_URL}/${currentImage}` : null);

  const handleChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    setFileName(file.name);
    setPreviewLocal(URL.createObjectURL(file));

    if (onChange) onChange(file);
  };

  return (
    <div className="upload-box">
      <label className="upload-label">
        Chọn ảnh
        <input type="file" accept="image/*" onChange={handleChange} />
      </label>

      <div className="upload-name">{fileName}</div>

      {preview && (
        <div className="upload-preview">
          <img src={preview} alt="preview" />
        </div>
      )}
    </div>
  );
}
