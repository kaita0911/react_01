<div class="contentmain">

   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content">
         <div class="divright">
            <div class="acti2">
               <a class="add" href="index.php?do=menu&act=add">
                  <i class="fa fa-plus-circle"></i> Thêm mới
               </a>
            </div>
            <div class="acti2">
               <button class="add" type="button" id="btnDelete" data-comp="{$smarty.request.comp|default:0}">
                  <i class="fa fa-trash"></i> Xóa
               </button>
            </div>
            <div class="acti2">
               <button class="add" type="button" id="saveOrderBtn" data-comp="{$smarty.request.comp|default:0}">
                  <i class="fa-solid fa-up-down"></i> Sắp xếp
               </button>
            </div>
         </div>
         <div class="main-content">

            <div class="tbtitle2">
               {if $languages|@count > 1}
               <ul class="tab-list">
                  {foreach from=$languages item=lang}
                  <li class="tab {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">{$lang.name}</li>
                  {/foreach}
               </ul>
               {/if}
               <form class="form-all" method="post" action="">
                  <table class="br1">
                     <thead>
                        <tr>
                           <th align="center" class="width-del">
                              <input type="checkbox" name="all" id="checkAll" />
                           </th>
                           <th align="center" class="width-order"><strong>Thứ tự</strong></th>
                           <th align="left" class="width-ttl"><strong>Tiêu đề</strong></th>
                           <th align="center" class="width-show"><strong>Show</strong></th>
                           <th align="center" class="width-action"><strong>Action</strong></th>
                        </tr>
                     </thead>

                     <tbody>
                        {foreach from=$view item=row name=menuLoop}
                        <tr data-id="{$row.id}">
                           <td class="brbottom" align="center">
                              <input class="c-item" type="checkbox" value="{$row.id}" name="cid[]">
                           </td>

                           <td align="center" class="brbottom">
                              <input type="text" class="numInput" value="{$row.num}">
                           </td>


                           <td align="left" class="tab-mirror">
                              {foreach from=$languages item=lang}
                              {assign var=detail value=null}
                              {foreach from=$row.details item=ad}
                              {if $ad.languageid == $lang.id}
                              {assign var=detail value=$ad}
                              {/if}
                              {/foreach}
                              <span class="tab {if $lang.id==$currentLang}active{/if}" data-lang="{$lang.id}">
                                 {$detail.name}
                              </span>
                              {/foreach}
                           </td>


                           <td align="center" class="brbottom">
                              <!-- <button type="button"
                                 class="btn_checks btn_toggle"
                                 data-id="{$row.id}"
                                 data-active="{$row.active}"
                                 data-column="active"
                                 data-table="menu">
                                 <img src="images/{$row.active}.png" alt="Show/Hide">
                              </button> -->
                              <label class="toggle btn_toggle"
                                 data-id="{$row.id}"
                                 data-active="{$row.active}"
                                 data-column="active"
                                 data-table="menu">
                                 <input type="checkbox" {if $row.active == 1}checked{/if}>
                                 <span class="track"></span>
                              </label>
                           </td>

                           <td align="center" class="brbottom">
                              <div class="flex-btn">
                                 <a class="act-btn btnEdit"
                                    href="index.php?do=menu&act=edit&id={$row.id}"
                                    title="Sửa">
                                    <i class="fa fa-edit"></i>
                                 </a>
                                 <button title="Xoá" type="button"
                                    class="act-btn btnDeleteRow"
                                    data-id="{$row.id}" data-comp="0">
                                    <i class="fa fa-trash"></i>
                                 </button>
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
</div>
<!-- {literal}
<script>
   let menuOrderConfirm = false;
   // document.addEventListener("DOMContentLoaded", function() {
   //    const saveOrderBtn = document.getElementById("saveOrderBtn");

   //    if (saveOrderBtn) {
   //       saveOrderBtn.addEventListener("click", function() {
   //          const rows = document.querySelectorAll("tbody tr");
   //          const ids = [];
   //          const nums = [];

   //          rows.forEach(row => {
   //             const id = row.dataset.id;
   //             const numInput = row.querySelector(".numInput");
   //             if (id && numInput) {
   //                ids.push(id);
   //                nums.push(numInput.value.trim() || 0);
   //             }
   //          });

   //          if (ids.length === 0) {
   //             alert("Không có dữ liệu để sắp xếp!");
   //             return;
   //          }

   //          // Gửi AJAX bằng fetch
   //          fetch("index.php?do=menu&act=updateOrder", {
   //                method: "POST",
   //                headers: {
   //                   "Content-Type": "application/x-www-form-urlencoded"
   //                },
   //                body: `id[]=${ids.join("&id[]=")}&num[]=${nums.join("&num[]=")}`
   //             })
   //             .then(response => response.json())
   //             .then(data => {
   //                if (data.success) {
   //                   alert("✅ Đã lưu thứ tự thành công!");
   //                   location.reload(); // refresh để cập nhật lại
   //                } else {
   //                   alert("❌ " + (data.message || "Cập nhật thất bại!"));
   //                }
   //             })
   //             .catch(() => {
   //                alert("⚠️ Lỗi kết nối server! Vui lòng thử lại sau.");
   //             });
   //       });
   //    }
   // });
</script>

{/literal} -->