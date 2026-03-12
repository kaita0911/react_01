import { useRef } from "react";
import { DndContext, closestCenter } from "@dnd-kit/core";
import {
  SortableContext,
  useSortable,
  arrayMove,
  rectSortingStrategy,
} from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";

function SortableItem({ img, index, removeImage, getPreview, getId }) {
  const { attributes, listeners, setNodeRef, transform, transition } =
    useSortable({ id: getId(img, index) });

  const style = {
    transform: CSS.Transform.toString(transform),
    transition,
  };

  return (
    <div ref={setNodeRef} style={style} className="gallery-item">
      {/* drag handle */}
      <img src={getPreview(img)} alt="" {...attributes} {...listeners} />

      {/* delete button */}
      <span
        className="remove-btn"
        onClick={(e) => {
          e.preventDefault();
          e.stopPropagation();
          removeImage(index);
        }}
      >
        <i className="fa-solid fa-xmark"></i>
      </span>
    </div>
  );
}

export default function UploadMultipleImages({
  images = [],
  setImages,
  setDeletedIds,
}) {
  const inputRef = useRef();
  const API_URL = import.meta.env.VITE_API_URL;

  /* id cho drag */
  const getId = (img, index) => {
    if (img?.id) return img.id;
    if (img instanceof File) return img.name + index;
    return index;
  };
  /* upload file */
  const handleFiles = (files) => {
    const newFiles = Array.from(files);

    setImages((prev) => [...prev, ...newFiles]);

    inputRef.current.value = "";
  };

  /* xoá ảnh */
  const removeImage = (index) => {
    const img = images[index];

    if (img?.id && setDeletedIds) {
      setDeletedIds((prev) => [...prev, img.id]);
    }

    setImages((prev) => prev.filter((_, i) => i !== index));
  };

  /* preview */
  const getPreview = (img) => {
    if (img instanceof File) {
      return URL.createObjectURL(img);
    }

    if (img?.img_vn) {
      return `${API_URL}/${img.img_vn}`;
    }

    return "";
  };

  /* drag end */
  const handleDragEnd = (event) => {
    const { active, over } = event;

    if (!over || active.id === over.id) return;

    const oldIndex = images.findIndex((i, idx) => getId(i, idx) === active.id);
    const newIndex = images.findIndex((i, idx) => getId(i, idx) === over.id);

    const newArr = arrayMove(images, oldIndex, newIndex);

    // chỉ update num cho object DB
    const updated = newArr.map((img, i) => {
      if (img instanceof File) {
        return img;
      }

      return {
        ...img,
        num: i,
      };
    });

    setImages(updated);
  };

  return (
    <div className="upload-gallery">
      {/* upload button */}
      <div className="upload-box" onClick={() => inputRef.current.click()}>
        <i className="fa-solid fa-cloud-arrow-up"></i>
        <p>Tải nhiều hình</p>

        <input
          ref={inputRef}
          type="file"
          multiple
          accept="image/*"
          hidden
          onChange={(e) => handleFiles(e.target.files)}
        />
      </div>

      {/* gallery */}
      <DndContext collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
        <SortableContext
          items={images.map((img, i) => getId(img, i))}
          strategy={rectSortingStrategy}
        >
          <div className="gallery-preview">
            {images.map((img, index) => (
              <SortableItem
                key={getId(img, index)}
                img={img}
                index={index}
                removeImage={removeImage}
                getPreview={getPreview}
                getId={getId}
              />
            ))}
          </div>
        </SortableContext>
      </DndContext>
    </div>
  );
}
