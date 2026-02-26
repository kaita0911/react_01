<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content">
         <div class="divright">
            <div class="acti2">
               <a class="add" href="index.php?do=size&act=add">
                  <i class="fa fa-plus-circle"></i> Thêm mới
               </a>
            </div>
            <div class="acti2">
               <button class="add" type="button" id="saveOrderBtn" data-comp="{$smarty.request.comp|default:0}">
                  <i class="fa-solid fa-up-down"></i> Sắp xếp
               </button>
            </div>
         </div>
         <div class="main-content">
            <form class="form-all" method="post" action="">
               <table class="br1">
                  <thead>
                     <tr>
                        <th align="center" class="width-del">
                           <input type="checkbox" name="all" id="checkAll" />
                        </th>
                        <th align="center" class="width-order">Thứ tự</th>

                        <th align="left" class="width-ttl">
                           Tiêu đề
                        </th>
                        <th align="center" class="width-show">Show</th>
                        <th class="width-action" align="center">
                           <strong>Action</strong>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     {foreach from=$view item=item}
                     <tr data-id="{$item.id}">
                        <td align="center">
                           <input type="checkbox" class="c-item" name="cid[]" value="{$item.id}">
                        </td>
                        <td align="center">
                           <input type="text" class="numInput" value="{$item.num}" />
                        </td>

                        <td align="left">
                           {$item.name}
                        </td>
                        <td align="center">
                           <button type="button"
                              class="btn_checks btn_toggle"
                              data-id="{$item.id}"
                              data-active="{$item.active}"
                              data-column="active"
                              data-table="size">
                              <img src="images/{$item.active}.png" alt="Hiển thị / Ẩn" />
                           </button>
                        </td>
                        <td align="center">
                           <div class="flex-btn">
                              <a title="Chỉnh sửa" class="act-btn btnEdit" href="index.php?do=size&act=edit&id={$item.id}">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <button title="Xoá" type="button" class="act-btn btnDeleteRow" data-id="{$item.id}"> <i class="fa fa-trash"></i> </button>
                           </div>
                        </td>
                     </tr>
                     {/foreach}
                  </tbody>
               </table>
            </form>
         </div>
      </div>
   </div>
</div>
{literal}
<script>
   document.addEventListener("DOMContentLoaded", function() {
      const saveOrderBtn = document.getElementById("saveOrderBtn");

      if (saveOrderBtn) {
         saveOrderBtn.addEventListener("click", function() {
            const rows = document.querySelectorAll("tbody tr");
            const ids = [];
            const nums = [];

            rows.forEach(row => {
               const id = row.dataset.id;
               const numInput = row.querySelector(".numInput");
               if (id && numInput) {
                  ids.push(id);
                  nums.push(numInput.value.trim() || 0);
               }
            });

            if (ids.length === 0) {
               alert("Không có dữ liệu để sắp xếp!");
               return;
            }

            // Gửi AJAX bằng fetch
            fetch("index.php?do=size&act=updateOrder", {
                  method: "POST",
                  headers: {
                     "Content-Type": "application/x-www-form-urlencoded"
                  },
                  body: `id[]=${ids.join("&id[]=")}&num[]=${nums.join("&num[]=")}`
               })
               .then(response => response.json())
               .then(data => {
                  if (data.success) {
                     alert("✅ Đã lưu thứ tự thành công!");
                     location.reload(); // refresh để cập nhật lại
                  } else {
                     alert("❌ " + (data.message || "Cập nhật thất bại!"));
                  }
               })
               .catch(() => {
                  alert("⚠️ Lỗi kết nối server! Vui lòng thử lại sau.");
               });
         });
      }
   });
</script>

{/literal}