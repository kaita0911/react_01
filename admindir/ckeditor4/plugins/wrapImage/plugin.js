CKEDITOR.plugins.add("wrapImage", {
  init: function (editor) {
    editor.on("afterInsertHtml", function (evt) {
      const temp = document.createElement("div");
      temp.innerHTML = evt.data.dataValue;

      temp.querySelectorAll("img").forEach((img) => {
        const wrapper = document.createElement("div");
        wrapper.className = "img";
        img.parentNode.insertBefore(wrapper, img);
        wrapper.appendChild(img);
      });

      evt.data.dataValue = temp.innerHTML;
    });
  },
});
