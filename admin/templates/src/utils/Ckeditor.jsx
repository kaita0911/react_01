import { CKEditor } from "ckeditor4-react";
import { useRef, useEffect } from "react";
const API = import.meta.env.VITE_API_URL;
export default function Editor({ value, onChange }) {
  const editorRef = useRef(null);
  const API = import.meta.env.VITE_API_URL;
  useEffect(() => {
    if (editorRef.current && value !== undefined) {
      if (editorRef.current.getData() !== value) {
        editorRef.current.setData(value);
      }
    }
  }, [value]);

  return (
    <CKEditor
      editorUrl="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"
      config={{
        height: 300,
        versionCheck: false,
        filebrowserBrowseUrl: "/ckfinder/ckfinder.html",
        filebrowserImageBrowseUrl: "/ckfinder/ckfinder.html?type=Images",
        filebrowserUploadUrl:
          "/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files",
        filebrowserImageUploadUrl:
          "/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images",

        filebrowserUploadMethod: "form",
      }}
      onInstanceReady={(evt) => {
        editorRef.current = evt.editor;
        if (value) {
          evt.editor.setData(value);
        }
      }}
      onChange={(evt) => {
        const data = evt.editor.getData();
        onChange(data);
      }}
    />
  );
}
