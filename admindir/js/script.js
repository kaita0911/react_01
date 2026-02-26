// ==================== Main Script ====================
(function ($) {
  // Chạy khi DOM sẵn sàng
  $(function () {
    const currentUrl = window.location.href;

    // ==================== CKEditor ====================
    // ["content", "short"].forEach(function (baseId) {
    //   var textareas = document.querySelectorAll(
    //     "textarea[id^='" + baseId + "']"
    //   );

    //   textareas.forEach(function (el) {
    //     var langId = el.id.split("_").pop();
    //     CKEDITOR.replace(el.id, {
    //       language: langId === "2" ? "en" : "vi", // tùy theo lang_id
    //       removePlugins: "exportpdf",
    //       height: 300,
    //     });
    //   });
    // });
   CKEDITOR.config.removePlugins = "exportpdf";

  document.querySelectorAll("textarea.ckeditor").forEach(function (el) {
    if (!el.id) {
      el.id = "ckeditor_" + Math.random().toString(36).slice(2);
    }

    if (CKEDITOR.instances[el.id]) return;
    CKEDITOR.replace(el.id, {
      language: el.dataset.langId == 2 ? "en" : "vi",
      height: 300,
    });
  });

    // ==================== Slug ====================
    function slugify(str) {
      return str
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[đð]/g, "d")
        .replace(/[^a-z0-9\s-]/g, "")
        .trim()
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-");
    }

    // Auto slug theo từng ngôn ngữ
    $(".title-input").on("input", function () {
      const lang = $(this).data("lang");
      const slugInput = $(`.slug-input[data-lang="${lang}"]`);

      // chỉ auto nếu chưa sửa tay
      if (!slugInput.data("edited")) {
        slugInput.val(slugify($(this).val()));
      }
    });

    // Khi tự chỉnh slug => đánh dấu đã sửa
    $(".slug-input").on("input", function () {
      $(this).data("edited", true);
    });

    // ==================== Checkbox tree ====================
    const parentCheckboxes = $('input[name="parentids[]"]');

    function checkAncestors(parentId) {
      let pid = parentId;
      while (pid && pid != 0) {
        const parent = parentCheckboxes.filter('[value="' + pid + '"]');
        parent
          .prop("checked", true)
          .attr("data-autocheck", "1")
          .prop("disabled", true);
        pid = parent.data("parent");
      }
    }

    function uncheckChildren(parentId) {
      parentCheckboxes
        .filter('[data-parent="' + parentId + '"]')
        .each(function () {
          $(this)
            .prop("checked", false)
            .removeAttr("data-autocheck")
            .prop("disabled", false);
          uncheckChildren($(this).val());
        });
    }

    parentCheckboxes.on("change", function () {
      const current = $(this);
      const currentId = current.val();
      const currentParent = current.data("parent");
      if (current.is(":checked")) {
        parentCheckboxes
          .not(current)
          .prop("checked", false)
          .removeAttr("data-autocheck")
          .prop("disabled", false);
        checkAncestors(currentParent);
      } else {
        uncheckChildren(currentId);
      }
    });

    parentCheckboxes.filter(":checked").each(function () {
      const pid = $(this).data("parent");
      if (pid && pid != 0) checkAncestors(pid);
    });

    // ==================== Chọn tất cả ====================
    const checkAll = $("#checkAll");
    const items = $(".c-item");
    if (checkAll.length) {
      checkAll.on("change", function () {
        items.prop("checked", this.checked);
      });
      items.on("change", function () {
        checkAll.prop(
          "checked",
          items.toArray().every((i) => i.checked)
        );
      });
    }

    // ==================== AutoNumeric / Format giá ====================
    if ($(".autoNumeric").length)
      $(".autoNumeric").autoNumeric("init", { aSep: ".", aDec: "none" });
    $(".InputPrice").on("input", function () {
      const number = this.value.replace(/\D/g, "");
      this.value = number ? Number(number).toLocaleString("vi-VN") : "";
    });

    // ==================== Button actions ====================
    function ajaxButton(selector, urlSuffix, dataMapper, onSuccess) {
      $(document).on("click", selector, function () {
        const btn = $(this);
        const data = dataMapper(btn);

        // Nếu hàm dataMapper trả về false thì hủy
        if (data === false) return;

        const url = currentUrl + urlSuffix;

        $.ajax({
          url,
          type: "POST",
          data,
          dataType: "json",
          success: function (res) {
            onSuccess(res, btn);
          },
          error: function (xhr, status, error) {
            console.error(xhr.responseText);
            alert("Lỗi kết nối máy chủ: " + error);
          },
        });
      });
    }

    // --- XÓA 1 DÒNG ---
    let deleteRowId = null;
    let deleteRowBtn = null;
    let allowDeleteRow = false;
    ajaxButton(
      ".btnDeleteRow",
      "&act=dellistajax",
      (btn) => {
        if (!allowDeleteRow) {
          deleteContext = "row";
          deleteRowId = btn.data("id");
          deleteRowBtn = btn;
    
          if (!deleteRowId) {
            showPopupMessage("Không xác định được mục cần xoá!");
            return false;
          }
          resetPopup(); // ⭐ QUAN TRỌNG
          $("#confirmPopup h3").text("🗑️ Xoá bài viết");
          $("#confirmPopup p").text("Bạn có chắc chắn muốn xoá không?");
          $("#confirmPopup").fadeIn(200);
          return false;
        }
    
        allowDeleteRow = false;
        return { cid: deleteRowId };
      },
      function (res, btn) {
        if (res.success) {
          let row = btn.closest("tr,.item,.gallery-item");
          row.fadeOut(300, () => row.remove());
        } else {
          showPopupMessage(res.message || "Lỗi khi xoá!");
        }
      }
    );
    
  
    // --- XÓA NHIỀU DÒNG ---

    let deleteIds = [];
    let allowDelete = false;
    ajaxButton(
      "#btnDelete",
      "&act=dellistajax",
      () => {
        if (!allowDelete) {
          deleteContext = "multi";
          deleteIds = $('input[name="cid[]"]:checked')
            .map((_, el) => el.value)
            .get();
    
          if (!deleteIds.length) {
            showPopupMessage("Vui lòng chọn ít nhất một mục!");
            return false;
          }
          resetPopup(); // ⭐ QUAN TRỌNG
          $("#confirmPopup h3").text("🗑️ Xoá bài viết");
          $("#confirmPopup p").text("Bạn có chắc chắn muốn xoá không?");
          $("#confirmPopup").fadeIn(200);
          return false;
        }
    
        allowDelete = false;
        return { cid: deleteIds.join(",") };
      },
      function (res) {
        if (res.success) {
          deleteIds.forEach(id =>
            $(`tr[data-id="${id}"]`).fadeOut(300, function () {
              $(this).remove();
            })
          );
        } else {
          showPopupMessage(res.message || "Không thể xoá!");
        }
      }
    );
  
    
    ///////
    ajaxButton(
      "#btnRefresh",
      "&act=refreshlistajax",
      () => {
        const ids = $('input[name="cid[]"]:checked')
          .map((_, el) => $(el).val())
          .get();
        return { cid: ids.join(",") };
      },
      (res) => {
        if (res.success) location.reload();
        else alert(res.message || "Lỗi không xác định");
      }
    );

    $("#btnAddnew").on("click", function () {
      const comp = $(this).data("comp") || 0;
      window.location.href =
        currentUrl + "&act=add" + (comp ? "&comp=" + comp : "");
    });
    /////LÀM MỚI 1 DÒNG
    let updateNumConfirm = false;
    let updateNumBtn = null;
    ajaxButton(
      ".btnUpdateNum",
      "&act=updatenumajax",
      (btn) => {
    
        // CHƯA xác nhận → mở popup
        if (!updateNumConfirm) {
          deleteContext = "updateNum";
          updateNumBtn = btn;
    
          resetPopup(); // ⭐ QUAN TRỌNG
          $("#confirmPopup h3").text("🔄 Làm mới");
          $("#confirmPopup p").text("Bạn có chắc chắn muốn thực hiện không?");
          $("#confirmPopup").fadeIn(200);
          return false;
        }
    
        // ĐÃ xác nhận → cho ajax chạy
        updateNumConfirm = false;
    
        const nums = $(".numInput")
          .map((_, el) => $(el).val())
          .get();
    
        const id = btn.data("id") || 0;
    
        return {
          id: id,
          num: nums,
        };
      },
      function (res) {
        if (res.success) {
            location.reload();
        } else {
          showPopupMessage(res.message || "Lỗi khi cập nhật num!");
        }
      }
    );
  
    ///SAP XEP NHIỀU DÒNG
    let orderConfirm = false;
    ajaxButton(
      "#saveOrderBtn",
      "&act=order",
      (btn) => {
    
        // CHƯA xác nhận → mở popup
        if (!orderConfirm) {
          deleteContext = "order";
    
          $("#confirmPopup .popup-title").text("Xác nhận cập nhật");
          $("#confirmPopup .popup-content").text(
            "Bạn có chắc muốn cập nhật lại thứ tự không?"
          );
    
          $("#confirmPopup").fadeIn(200);
          return false;
        }
    
        // ĐÃ xác nhận → cho ajax chạy
        orderConfirm = false;
    
        const ids = $(".numInput")
          .map((_, el) => $(el).closest("tr").data("id"))
          .get();
    
        const nums = $(".numInput")
          .map((_, el) => $(el).val())
          .get();
    
        return {
          id: ids,
          num: nums,
        };
      },
      function (res) {
        if (res.success) {
          location.reload();
        } else {
          showPopupMessage(res.message || "Lỗi khi cập nhật thứ tự!");
        }
      }
    );

     ////xac nhan xoa
    // Click ra ngoài overlay → đóng popup
    $("#confirmPopup").on("click", function () {
      $(this).fadeOut(200);
      deleteContext = null;
    });

    // Click bên trong popup-box → không đóng
    $("#confirmPopup .popup-box").on("click", function (e) {
      e.stopPropagation();
    });

    // Nút Huỷ
    $("#popupCancel").on("click", function () {
      $("#confirmPopup").fadeOut(200);
      deleteContext = null;
    });

    // Nút Xác nhận
    $("#popupOk").on("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      $("#confirmPopup").fadeOut(200);

      if (deleteContext === "row") {
        allowDeleteRow = true;
        deleteRowBtn.trigger("click");
      }

      if (deleteContext === "multi") {
        allowDelete = true;
        $("#btnDelete").trigger("click");
      }

      if (deleteContext === "order") {
        orderConfirm = true;
        $("#saveOrderBtn").trigger("click");
      }

      if (deleteContext === "updateNum") {
        updateNumConfirm = true;
        updateNumBtn.trigger("click");
      }
    
      if (deleteContext === "editPrice") {
        const price = $("#popupPriceInput").val().replace(/\D/g, "");
        const newPrice = parseInt(price) || 0;
      
        if (newPrice <= 0) {
          showPopupMessage("Giá không hợp lệ!");
          return;
        }
      
        $.ajax({
          url: "/admindir/functions/update_price.php",
          type: "POST",
          dataType: "json",
          data: {
            id: editPriceId,
            price: newPrice,
          },
          success: function (res) {
            if (res.success) {
              editPriceEl
                .text(new Intl.NumberFormat("vi-VN").format(newPrice) + "₫")
                .data("price", newPrice)
                .css("background", "#f1faff");
      
              setTimeout(() => editPriceEl.css("background", ""), 600);
      
              $("#orderMsg")
                .addClass("show")
                .html('<span><i class="fa fa-check"></i> Cập nhật giá thành công!</span>');
      
              setTimeout(() => $("#orderMsg").removeClass("show"), 1200);
            } else {
              showPopupMessage(res.message || "Không thể cập nhật giá");
            }
          },
          error: function (xhr) {
            console.error(xhr.responseText);
            showPopupMessage("Lỗi AJAX khi cập nhật giá!");
          },
          complete: function () {
            $(".popup-extra").hide();
            $("#popupPriceInput").val("");
            deleteContext = null;
          },
        });
      }
      if (deleteContext === "editName") {
        const name = $("#popupNameInput").val().trim();
        if (!name) return alert("Tên không được để trống");
    
        $.post("/admindir/functions/update_name.php", {
          id: popupData.id,
          lang: popupData.lang,
          name: name
        }, function (res) {
          if (res.success) {
            popupData.el.find("span").text(name);
            popupData.el.css("background", "#f1faff");
            setTimeout(() => popupData.el.css("background", ""), 600);
            closePopup();
          } else {
            alert(res.message || "Không thể cập nhật tên");
          }
        }, "json");
      }
      deleteContext = null;
    });

    
    //=======upload image đại diện======================

    const inputs = document.querySelectorAll(".img-thumb-input");
    if (inputs) {
      const preview = document.getElementById("preview-img");
      const current = document.getElementById("current-img");
      inputs.forEach((input) => {
        input.addEventListener("change", function () {
          const file = this.files[0];
          if (!file) {
            if (preview) preview.style.display = "none";
            if (current) current.style.display = "block";
            return;
          }

          if (!file.type.startsWith("image/")) {
            alert("Vui lòng chọn đúng định dạng ảnh (JPG, PNG, GIF)!");
            this.value = "";
            return;
          }

          const reader = new FileReader();
          reader.onload = function (e) {
            if (preview) {
              preview.src = e.target.result;
              preview.style.display = "block";
            }
            if (current) current.style.display = "none";
          };
          reader.readAsDataURL(file);
        });
      });
    }

    // ==================== Upload & Preview nhiều image ====================
    ////////di chuyển vị trí ảnh////////////////
    const gallery = document.querySelector(".preview-gallery");
    if (gallery) {
      // Khởi tạo SortableJS
      Sortable.create(gallery, {
        animation: 200,
        easing: "cubic-bezier(0.25, 1, 0.5, 1)",
        ghostClass: "sortable-ghost",
        swapThreshold: 0.65,
        onEnd: function () {
          collectGalleryNums(); // gọi luôn
        },
      });
    }

    function collectGalleryNums() {
      $(".preview-gallery .gallery-item").each(function (i) {
        const id = $(this).data("id");
        const num = i + 1; // thứ tự mới
        $(this).find("input[name='num_old[]']").val(num);
        $(this).find("input[name='id_old[]']").val(id);
      });
    }
    // Khi chọn nhiều ảnh mới

    let dt = new DataTransfer(); // quản lý file mới

    // Upload ảnh mới
    const $multiimages = $("#multiimages"); // jQuery object
    //const multiimages = document.getElementById("multiimages");
    if ($multiimages.length) {
      $multiimages.on("change", function () {
        const preview = $(".preview-gallery");

        for (const file of this.files) {
          if (!file.type.startsWith("image/")) continue;

          dt.items.add(file); // thêm vào DataTransfer

          const reader = new FileReader();
          reader.onload = function (e) {
            const html = `
              <div class="gallery-item" data-name="${file.name}">
                <img src="${e.target.result}">
                <div class="overlay">
                  <button type="button" class="remove-image">&times;</button>
                </div>
              </div>
            `;
            preview.append(html);
          };
          reader.readAsDataURL(file);
        }

        // cập nhật lại input
        this.files = dt.files;
      }); // Xóa ảnh

      // Trước khi submit form, rebuild file mới theo thứ tự DOM
      $("#ArticleForm").on("submit", function () {
        const newDt = new DataTransfer();
        $(".preview-gallery .gallery-item").each(function () {
          const name = $(this).data("name");
          if (name) {
            for (let i = 0; i < dt.files.length; i++) {
              if (dt.files[i].name === name) {
                newDt.items.add(dt.files[i]);
                break;
              }
            }
          }
        });
        dt = newDt;
        $("#multiimages")[0].files = dt.files;
      });
    }
    $(document).on("click", ".remove-image", function () {
      const galleryItem = $(this).closest(".gallery-item");
      const id = galleryItem.data("id");

      if (id) {
        // ảnh cũ → xóa bằng Ajax
        if (!confirm("Bạn có chắc muốn xóa ảnh này?")) return;
        $.post(
          "index.php?do=articlelist&act=deleteimage",
          { id },
          function (res) {
            if (res.success) galleryItem.remove();
            else alert("Xóa thất bại!");
          },
          "json"
        );
      } else {
        // ảnh mới → remove khỏi DataTransfer
        const name = galleryItem.data("name");
        for (let i = 0; i < dt.items.length; i++) {
          if (dt.items[i].getAsFile().name === name) {
            dt.items.remove(i);
            break;
          }
        }
        galleryItem.remove();
        multiimages[0].files = dt.files;
      }
    });
    /////////////////////MENU LEFT/////////////////////////
    $(".nav-toggle").on("click", function (e) {
      e.preventDefault();

      const $parent = $(this).closest(".nav-item");
      const $submenu = $parent.find(".list-sidebar");

      // Đóng các menu khác
      $(".list-sidebar").not($submenu).slideUp(200);
      $(".nav-item").not($parent).removeClass("active");

      // Toggle menu hiện tại
      $parent.toggleClass("active");
      $submenu.stop(true, true).slideToggle(200);
    });
    // ====== Khi click menu con ======
    $(document).on("click", ".list-sidebar a", function () {
      const href = $(this).attr("href");
      const $parent = $(this).closest(".nav-item");

      // Lưu trạng thái vào sessionStorage
      sessionStorage.setItem("activeMenuHref", href);
      sessionStorage.setItem("activeMenuParent", $parent.index());
    });

    // ====== Khi load lại trang ======
    const activeHref = sessionStorage.getItem("activeMenuHref");
    if (activeHref) {
      // Tìm link trùng với URL đã lưu
      const $activeLink = $(`.list-sidebar a[href='${activeHref}']`);
      if ($activeLink.length) {
        // Mở menu cha
        const $parent = $activeLink.closest(".nav-item");
        $parent.addClass("active");
        $parent.find(".list-sidebar").show();

        // Đánh dấu link con
        $(".list-sidebar a").removeClass("active");
        $activeLink.addClass("active");
      }
    }

    // ==================== Xóa trạng thái menu khi logout ====================
    $(document).on("click", 'a[href*="act=log_out"]', function () {
      sessionStorage.removeItem("activeMenu");
      sessionStorage.removeItem("activeSubmenu");
    });

    // Khi load trang login hoặc log_out
    if (
      window.location.href.includes("do=login") ||
      window.location.href.includes("act=log_out")
    ) {
      sessionStorage.removeItem("activeMenu");
      sessionStorage.removeItem("activeSubmenu");
    }
    /////////////////active-///////////////
    $(document).on("change", ".btn_toggle input", function () {
      const input = $(this);
      const btn = input.closest(".btn_toggle");
    
      const id = btn.data("id");
      const table = btn.data("table");
      const column = btn.data("column");
      const newValue = input.is(":checked") ? 1 : 0;
    
      input.prop("disabled", true);
    
      $.ajax({
        type: "POST",
        url: "/admindir/functions/toggle.php",
        data: {
          id: id,
          value: newValue,
          table: table,
          column: column
        },
        success: function () {
          btn.data("active", newValue);
        },
        error: function () {
          alert("Lỗi AJAX");
          input.prop("checked", !newValue); // rollback
        },
        complete: function () {
          input.prop("disabled", false);
        }
      });
    });
    
    /////CẬP NHẬT TÊN
    let popupData = {};
    $(document).on("click", ".editable-name", function () {
      resetPopup(); // ⭐ QUAN TRỌNG
      deleteContext = "editName";
      popupData.el = $(this);
      popupData.id = $(this).data("id");
      popupData.lang = $(this).data("lang");
    
      const name = $(this).find("span").text().trim();
    
      $("#confirmPopup h3").text("✏️ Cập nhật tên");
      $("#confirmPopup p").text("Nhập tiêu đề mới cho bài viết");

      $("#popupNameInput")
    .val(name)
    .show()
    .focus();
      $(".popup-extra").show();
      $("#confirmPopup").fadeIn(200);
    });
    
    ///////////////CẬP NHẬT GIÁ
    let editPriceId = null;
    let editPriceEl = null;
    $(document).on("click", ".btn_edit_price", function () {
      resetPopup(); // ⭐ CỰC QUAN TRỌNG
      editPriceEl = $(this);
      editPriceId = editPriceEl.data("id");
    
      const currentPrice = editPriceEl.data("price");
    
      deleteContext = "editPrice";
      // ✅ SỬA ĐÚNG SELECTOR
      $("#confirmPopup h3").text("💰 Cập nhật giá");
      $("#confirmPopup p").text("Nhập giá mới cho sản phẩm");
    
      $("#popupPriceInput")
    .val(currentPrice)
    .show()
    .focus();

      $(".popup-extra").show();
    
      $("#confirmPopup").fadeIn(200);
    });
    ///reset popup
    function resetPopup() {
      $("#popupPriceInput").hide().val("");
      $("#popupNameInput").hide().val("");
      $(".popup-extra").hide();
    }
  }); // end ready
})(jQuery);
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".tags-group").forEach((group) => {
    const lang = group.dataset.lang;
    const tagsInput = group.querySelector(`.tagsInput[data-lang="${lang}"]`);
    const tagInput = group.querySelector(`.tagInput[data-lang="${lang}"]`);
    const tagsWrapper = group.querySelector(
      `.tagsWrapper[data-lang="${lang}"]`
    );

    if (!tagsInput || !tagInput || !tagsWrapper) return;

    let tags = [];
    try {
      tags = JSON.parse(tagsInput.value); // parse JSON
      if (!Array.isArray(tags)) tags = [];
    } catch (e) {
      tags = [];
    }

    function renderTags() {
      tagsWrapper.innerHTML = "";
      tags.forEach((label) => {
        const div = document.createElement("div");
        div.className = "tag";
        div.textContent = label;

        const closeBtn = document.createElement("span");
        closeBtn.className = "remove-tag";
        closeBtn.textContent = "×";
        closeBtn.onclick = () => {
          tags = tags.filter((t) => t !== label);
          renderTags();
        };

        div.appendChild(closeBtn);
        tagsWrapper.appendChild(div);
      });

      tagsInput.value = JSON.stringify(tags);
    }

    tagInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === ",") {
        e.preventDefault();
        const value = tagInput.value.trim();
        if (value && !tags.includes(value)) {
          tags.push(value);
          renderTags();
        }
        tagInput.value = "";
      }
    });

    tagInput.addEventListener("paste", (e) => {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData).getData("text");
      paste.split(/[\n,]+/).forEach((item) => {
        const value = item.trim();
        if (value && !tags.includes(value)) tags.push(value);
      });
      renderTags();
    });

    renderTags(); // render tag cũ ngay khi load page
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // tất cả các tab: tab-list + các mirror
  const allTabs = document.querySelectorAll(".tab-list .tab, .tab-mirror .tab");
  const mainTabs = document.querySelectorAll(".tab-list .tab"); // tab chính
  const contents = document.querySelectorAll(".tab-content");

  mainTabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      const langId = this.getAttribute("data-lang");

      // Active tất cả các tab (tab chính + mirror) cùng lang
      allTabs.forEach((t) => t.classList.remove("active"));
      allTabs.forEach((t) => {
        if (t.getAttribute("data-lang") === langId) {
          t.classList.add("active");
        }
      });

      // Active content tương ứng
      contents.forEach((c) => {
        c.classList.remove("active");
        if (c.getAttribute("data-lang") === langId) {
          c.classList.add("active");
        }
      });
    });
  });

  const form = document.getElementById("frmEdit");
  if (form) {
    form.addEventListener("keydown", function (e) {
      // Nếu nhấn Enter và KHÔNG phải textarea → chặn submit
      if (e.key === "Enter" && e.target.tagName.toLowerCase() !== "textarea") {
        e.preventDefault();
        return false;
      }
    });
  }
});
///Tạo password cho bài viết
$(".btnPassword").click(function () {
  let id = $(this).data("id");
  $("#article_id").val(id);
  loadPasswords(id);
  $("#generatedBox").hide();
  $("#passwordModal").show();
});

$("#passwordModal").on("click", function () {
  closeModal();
});
$(".modal-content").on("click", function (e) {
  e.stopPropagation();
});
function closeModal() {
  $("#passwordModal").hide();
}

function loadPasswords(articleId) {
  $.get(
    "/admindir/functions/article_password_list.php",
    {
      article_id: articleId,
    },
    function (html) {
      $("#passwordList").html(html);
    }
  );
}

$("#btnGeneratePassword").click(function () {
  $.post(
    "/admindir/functions/article_password_generate.php",
    { article_id: $("#article_id").val() },
    function (data) {
      if (!data.success) {
        alert(data.message);
        return;
      }

      $("#generatedBox").show();
      $("#generatedPassword").val(data.password);
      loadPasswords($("#article_id").val());
    },
    "json" // 🔥 QUAN TRỌNG
  );
});

function deletePassword(id) {
  $.post(
    "/admindir/functions/article_password_delete.php",
    {
      id: id,
    },
    function () {
      loadPasswords($("#article_id").val());
    }
  );
}

function copyPassword() {
  navigator.clipboard.writeText(
    document.getElementById("generatedPassword").value
  );
}
function copyRowPassword(btn, text) {
  var tempInput = document.createElement("textarea");
  tempInput.value = text;
  tempInput.style.position = "fixed";
  tempInput.style.opacity = "0";

  document.body.appendChild(tempInput);
  tempInput.select();

  var success = false;
  try {
    success = document.execCommand("copy");
  } catch (e) {}

  document.body.removeChild(tempInput);

  if (success) {
    var oldText = btn.innerHTML;
    btn.innerHTML = "✓ Đã copy";
    btn.disabled = true;

    setTimeout(function () {
      btn.innerHTML = oldText;
      btn.disabled = false;
    }, 1500);
  }
}
//////
var wrapper = document.getElementById("product-code-wrapper");
if (wrapper) {
  var productIndex = 0;
  var items = wrapper.querySelectorAll(".product-code");
  items.forEach(function (item) {
    var idx = parseInt(item.dataset.index || 0);
    if (idx > productIndex) productIndex = idx;
  });
  // event delegation
  wrapper.addEventListener("click", function (e) {
    // ➕ thêm màu
    if (e.target.classList.contains("add-variant")) {
      var productDiv = e.target.closest(".product-code");
      var variantWrapper = productDiv.querySelector(".variant-wrapper");
      var pIndex = productDiv.dataset.index;
      var vIndex = variantWrapper.children.length;

      var variantHTML = `
      <div class="variant-item">
      <div class="variant-handle" draggable="true">⇅</div>
       <!-- sort order -->
      <input type="hidden"
           class="variant-sort"
           name="products[${pIndex}][variants][${vIndex}][sort_order]"
           value="${vIndex}" />
      <div class="variant-item-flex">
         <input type="text"
               name="products[${pIndex}][variants][${vIndex}][color_name]"
               placeholder="Tên màu (Đỏ, Xanh...)"/>
                 <input type="text" class="price-input"
               name="products[${pIndex}][variants][${vIndex}][price]"
               placeholder="Giá"/>
        <div class="remove-variant">✖</div>
        </div>
       <div class="variant-item-flex">
           <input type="color"
               class="color-picker"
               name="products[${pIndex}][variants][${vIndex}][color_code]"
               value="#000000"/>

        <input type="text"
               class="color-code-text"
               value="#000000"
               style="width:90px"
               placeholder="#HEX"/>
               </div>
       <div class="color-upload-box">
            <strong>Ảnh màu: <span class="color-label">#000000</span></strong>
            <input type="file"
              name="images[000000][]"
              multiple
              accept="image/*">
              <div class="image-preview"></div>
          </div>
            </div>
    `;

      variantWrapper.insertAdjacentHTML("afterbegin", variantHTML);
    }
    // ❌ xoá toàn bộ variant-item
    if (e.target.classList.contains("remove-variant")) {
      var variantItem = e.target.closest(".variant-item");
      if (variantItem) {
        var wrapper = variantItem.closest(".variant-wrapper");

        variantItem.remove();

        // cập nhật lại sort_order
        // updateVariantSort(wrapper);
      }
    }
    // ❌ xoá MÃ sản phẩm
    if (e.target.classList.contains("remove-product")) {
      var productCode = e.target.closest(".product-code");
      if (!productCode) return;

      if (!confirm("Xoá mã sản phẩm này và toàn bộ màu + giá?")) return;

      productCode.remove();
    }
  });
  // 🎨 ĐỒNG BỘ MÀU ↔ MÃ HEX
  wrapper.addEventListener("input", function (e) {
    // đổi color → cập nhật text
    if (e.target.classList.contains("color-picker")) {
      var parent = e.target.closest(".variant-item");
      parent.querySelector(".color-code-text").value = e.target.value;
    }

    // nhập mã → đổi color
    if (e.target.classList.contains("color-code-text")) {
      var parent = e.target.closest(".variant-item");
      var colorInput = parent.querySelector(".color-picker");
      var val = e.target.value;

      if (/^#([0-9A-F]{3}){1,2}$/i.test(val)) {
        colorInput.value = val;
      }
    }
    if (e.target.classList.contains("price-input")) {
      var value = e.target.value.replace(/\D/g, ""); // chỉ lấy số

      if (value === "") {
        e.target.value = "";
        return;
      }

      e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    // ///đổi màu thì đổi mã màu của ảnh theo
    // if (!e.target.classList.contains("color-picker")) return;

    // var variantItem = e.target.closest(".variant-item");
    // var hex = e.target.value;
    // var key = hex.replace("#", "");

    // // lưu màu
    // variantItem.dataset.color = key;

    // // update text
    // variantItem.querySelector(".color-code-text").value = hex;
    // variantItem.querySelector(".color-label").textContent = hex;

    // // update name input file
    // variantItem.querySelector("input[type=file]").name = `images[${key}][]`;
    if (!e.target.classList.contains("color-code-text")) return;
    syncVariantColor(e.target.closest(".variant-item"), e.target.value);
  });
  wrapper.addEventListener("input", function (e) {
    if (!e.target.classList.contains("color-picker")) return;

    const variantItem = e.target.closest(".variant-item");
    syncVariantColor(variantItem, e.target.value);
  });
  // 📋 copy / paste từ bên ngoài
  wrapper.addEventListener("paste", function (e) {
    if (!e.target.classList.contains("color-code-text")) return;

    setTimeout(() => {
      syncVariantColor(e.target.closest(".variant-item"), e.target.value);
    }, 0);
  });
  ////đổi vị trí mã sản phẩm
  let draggedCode = null;
  let draggedVariant = null;

  /* =========================
   DRAG START
========================= */
  wrapper.addEventListener("dragstart", function (e) {
    const codeHandle = e.target.closest(".product-handle");
    const variantHandle = e.target.closest(".variant-handle");

    if (codeHandle) {
      draggedCode = codeHandle.closest(".product-code");
      draggedCode.classList.add("dragging");
      return;
    }

    if (variantHandle) {
      e.stopPropagation();
      draggedVariant = variantHandle.closest(".variant-item");
      draggedVariant.classList.add("dragging-variant");
      return;
    }

    e.preventDefault();
  });

  /* =========================
   DRAG OVER
========================= */
  wrapper.addEventListener("dragover", function (e) {
    e.preventDefault();

    /* ---- DRAG PRODUCT CODE ---- */
    if (draggedCode) {
      const target = e.target.closest(".product-code");
      if (!target || target === draggedCode) return;

      const rect = target.getBoundingClientRect();
      const after = e.clientY > rect.top + rect.height / 2;
      wrapper.insertBefore(draggedCode, after ? target.nextSibling : target);
      return;
    }

    /* ---- DRAG VARIANT ---- */
    if (draggedVariant) {
      const target = e.target.closest(".variant-item");
      if (!target || target === draggedVariant) return;

      const w1 = draggedVariant.closest(".variant-wrapper");
      const w2 = target.closest(".variant-wrapper");
      if (w1 !== w2) return;

      const rect = target.getBoundingClientRect();
      const after = e.clientY > rect.top + rect.height / 2;
      w1.insertBefore(draggedVariant, after ? target.nextSibling : target);
    }
  });

  /* =========================
   DRAG END (CHỈ 1 CÁI)
========================= */
  wrapper.addEventListener("dragend", function () {
    if (draggedCode) {
      draggedCode.classList.remove("dragging");
      updateCodeSort();
      draggedCode = null;
    }

    if (draggedVariant) {
      draggedVariant.classList.remove("dragging-variant");
      updateVariantSort(draggedVariant.closest(".variant-wrapper"));
      draggedVariant = null;
    }
  });
}

function syncVariantColor(variantItem, hex) {
  if (!variantItem) return;

  if (!hex) return;

  if (!hex.startsWith("#")) hex = "#" + hex;

  // chỉ validate đúng HEX
  if (!/^#[0-9a-fA-F]{6}$/.test(hex)) return;

  hex = hex.toLowerCase();
  const key = hex.slice(1);

  variantItem.dataset.color = key;

  const picker = variantItem.querySelector(".color-picker");
  const text = variantItem.querySelector(".color-code-text");
  const label = variantItem.querySelector(".color-label");
  const file = variantItem.querySelector('input[type="file"]');

  if (picker && picker.value !== hex) picker.value = hex;
  if (text && text.value !== hex) text.value = hex;
  if (label) label.textContent = hex;
  if (file) file.name = `images[${key}][]`;
}
var addcode = document.getElementById("add-product-code");
if (addcode) {
  addcode.onclick = function () {
    productIndex++;

    var html = `
      <div class="product-code" data-index="${productIndex}" style="border:1px solid #ccc;padding:10px;margin-top:10px">
       <div class="product-handle" draggable="true">⇅</div>
      <!-- sort order -->
      <input type="hidden"
             class="code-sort"
             name="products[${productIndex}][sort_order]"
             value="${items.length}" />  
        <div class="product-code-top">
          <label>Mã sản phẩm:</label>
          <input type="text" name="products[${productIndex}][code]" placeholder="VD: IP14-128" />
          </div>
          <button type="button" class="add-variant">➕ Thêm màu</button>
        
  
        <div class="variant-wrapper"></div>
      </div>
    `;

    wrapper.insertAdjacentHTML("afterbegin", html);
  };
}

/* =========================
   SORT UPDATE
========================= */
function updateCodeSort() {
  wrapper.querySelectorAll(".product-code").forEach((el, i) => {
    const input = el.querySelector(".code-sort");
    if (input) input.value = i;
  });
}

function updateVariantSort(wrapper) {
  wrapper.querySelectorAll(".variant-item").forEach((el, i) => {
    const input = el.querySelector(".variant-sort");
    if (input) input.value = i;
  });
}

//////status don hang
$(document).ready(function () {
  $(".status-select").change(function () {
    var orderId = $(this).data("id");
    var status = $(this).val();
    var selectElem = $(this);

    $.post(
      "index.php?do=orders&act=ajax_update_status",
      { id: orderId, status: status },
      function (res) {
        res = res.trim(); // loại bỏ khoảng trắng thừa
        if (res == "ok") {
          alert("Cập nhật trạng thái thành công");
          // Update thanh tiến trình ngay
          var steps = selectElem
            .find("option")
            .map(function () {
              return $(this).val();
            })
            .get();
          var currentIndex = steps.indexOf(status);
          var iconsDiv = selectElem.next("div");
          iconsDiv.html("");
          for (var i = 0; i < steps.length; i++) {
            iconsDiv.append(
              i <= currentIndex
                ? '<span class="step"></span>'
                : '<span class="none-step"></span>'
            );
          }
          location.reload();
        } else {
          alert("Lỗi cập nhật trạng thái");
        }
      }
    );
  });
});
////update image truc tiep

document.addEventListener("change", function (e) {
  if (!e.target.classList.contains("img-input")) return;

  const file = e.target.files[0];
  if (!file) return;

  const tr = e.target.closest("tr");
  const id = tr.dataset.id;
  const imgWrap = tr.querySelector(".c-img");
  const comp = imgWrap.dataset.comp; // CHUẨN
  const img = tr.querySelector("img");

  // preview
  const reader = new FileReader();
  reader.onload = (ev) => (img.src = ev.target.result);
  reader.readAsDataURL(file);

  const formData = new FormData();
  formData.append("id", id);
  formData.append("comp", comp);
  formData.append("img_thumb_vn", file);

  fetch("/admindir/functions/update_image.php", {
    method: "POST",
    body: formData,
  })
    .then((r) => r.json())
    .then((r) => {
      if (!r.success) alert(r.message);
    });
});

window.addEventListener("pageshow", function (event) {
  if (event.persisted) {
    // Trang được load từ BFCache
    window.location.reload();
  }
});

document.querySelectorAll('.month-tabs li').forEach(function(tab){
  tab.addEventListener('click', function(){
      // bỏ active hết
      document.querySelectorAll('.month-tabs li').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

      // active cái được click
      this.classList.add('active');
      document.getElementById(this.dataset.tab).classList.add('active');
  });
});
  // popup xem chi tiet mail
//   document.addEventListener("DOMContentLoaded", function () {

//     const modal = document.getElementById("modalView");
//     if (!modal) return; // 👈 CỰC KỲ QUAN TRỌNG
 
//     const closeBtn = modal.querySelector(".modal-close");
//     const content  = modal.querySelector("#modalContent");
 
//     document.querySelectorAll(".btn-view").forEach(btn => {
//        btn.addEventListener("click", function () {
//           const id = this.dataset.id;
 
//           modal.style.display = "flex";
//           content.innerHTML = "Đang tải dữ liệu...";
 
//           fetch("index.php?do=register_info&act=popup&id=" + id)
//              .then(res => res.text())
//              .then(html => content.innerHTML = html);
//        });
//     });
 
//     closeBtn.onclick = () => {
//        modal.style.display = "none";
//     };
 
//     modal.onclick = (e) => {
//        if (e.target === modal) modal.style.display = "none";
//     };
 
//  });


 ////
 document.addEventListener("DOMContentLoaded", function () {

  const modal   = document.getElementById("globalModal");
  const content = document.getElementById("globalModalContent");
  const closeBtn = modal?.querySelector(".modal-close");

  if (!modal || !content) return;

  function openModal(url) {
     modal.style.display = "flex";
     content.innerHTML = "Đang tải dữ liệu...";

     fetch(url)
        .then(res => res.text())
        .then(html => content.innerHTML = html)
        .catch(() => content.innerHTML = "Lỗi tải dữ liệu");
  }

  // CLICK BUTTON BẤT KỲ CÓ data-popup
  document.addEventListener("click", function (e) {
     const btn = e.target.closest("[data-popup]");
     if (!btn) return;

     e.preventDefault();
     openModal(btn.dataset.popup);
  });

  closeBtn.onclick = () => modal.style.display = "none";

  modal.onclick = (e) => {
     if (e.target === modal) modal.style.display = "none";
  };

  document.addEventListener("keydown", function (e) {
     if (e.key === "Escape") modal.style.display = "none";
  });

});
