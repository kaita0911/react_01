<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content">
         <div class="box-foles">
            <div class="divright">
               <div class="acti2">
                  <button class="add" type="button" id="btnAddnew" data-comp="0">
                     <i class="fa fa-plus-circle"></i> Thêm mới
                  </button>
               </div>
               <div class="acti2">
                  <button class="add" type="button" id="btnDelete" data-comp="{$smarty.request.comp}">
                     <i class="fa fa-trash"></i> Xóa
                  </button>
               </div>
               <div class="acti2">
                  <button class="add" type="button" id="saveOrderBtn" data-comp="{$smarty.request.comp|default:0}">
                     <i class="fa-solid fa-up-down"></i> Sắp xếp
                  </button>
               </div>
               <div class="acti2">
                  <button class="add" type="button" id="btnRefresh" data-comp="{$smarty.request.comp}">
                     <i class="fa fa-copy"></i> Copy
                  </button>
               </div>
            </div>
            {if $smarty.request.comp == 2}
            <!-- ====== Bộ lọc tìm kiếm ====== -->
            <form method="get" action="index.php">
               <input type="hidden" name="do" value="articlelist">
               <input type="hidden" name="comp" value="{$smarty.request.comp|default:0}">

               <div class="filter-bar">
                  <select name="cate_id">
                     <option value="">-- Tất cả danh mục --</option>
                     {foreach from=$categories item=node}
                     {include file="articlelist/category_tree_search.tpl" node=$node}
                     {/foreach}
                  </select>

                  <input type="text" name="keyword" value="{$smarty.request.keyword}" placeholder="Từ khóa sản phẩm..." style="min-width:200px; margin-left:10px;" />

                  <button type="submit" name="search" value="1" style="margin-left:10px;">Tìm kiếm</button>
               </div>
            </form>
            {/if}
         </div>

         <div class="main-content">
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
                        <!-- <th align="center" class="width-order">Thứ tự</th> -->
                        <th align="center" class="width-order">Thứ tự</th>
                        {if $tinhnang.hinhanh == 1}
                        <th align="center" class="width-image">Hình ảnh</th>
                        {/if}

                        <!-- {if $tinhnang.id == 15}
                        <th align="center" class="width-image">Mã màu</th>
                        {/if} -->

                        <th align="left" class="width-ttl">Tiêu đề</th>

                        {if $tinhnang.price == 1}
                        <th align="center" class="width-image">Giá</th>
                        {/if}

                        <!-- <th align="center" class="width-image">Ngày tạo</th> -->
                        <th align="center" class="width-date">Ngày</th>

                        {if $tinhnang.new == 1}
                        <th align="center" class="width-show">Mới</th>
                        {/if}

                        {if $tinhnang.hot == 1}
                        <th align="center" class="width-show">Nổi bật</th>
                        {/if}

                        {if $tinhnang.mostview == 1}
                        <th align="center" class="width-show">Cập nhật</th>
                        {/if}

                        <th align="center" class="width-show">Show</th>

                        <th align="center" class="width-action">Action</th>
                     </tr>
                  </thead>

                  <tbody>
                     {foreach from=$articlelist item=item name=loop}
                     <tr data-id="{$item.id}">
                        <td align="center">
                           <input type="checkbox" class="c-item" name="cid[]" value="{$item.id}">
                        </td>
                        <!-- <td align="center">
                           {$smarty.foreach.loop.iteration}
                        </td> -->
                        <td align="center">
                           <input type="text" class="numInput" value="{$item.num}" />
                        </td>

                        {if $tinhnang.hinhanh == 1}

                        <td align="center">
                           {if $item.img_thumb_vn neq ""}
                           <div class="c-img {$item.comp}" data-comp="{$item.comp}" title="Làm mới">
                              <label class="img-change">
                                 <img src="/{$item.img_thumb_vn}?width=80&height=80&mode=contain"
                                    class="preview-img" />
                                 <span class="img-overlay">
                                    <i class="fa fa-camera"></i>
                                    <small>Đổi ảnh</small>
                                 </span>
                                 <input type="file" class="img-input" hidden accept="image/*">
                              </label>
                           </div>
                           {/if}
                        </td>

                        {/if}

                        <!-- {if $tinhnang.id == 15}
                        <td align="center">
                           <span class="bg-color" style="background: {$item.color};"></span>
                        </td>
                        {/if} -->

                        <td align="left">
                           <div class="tab-mirror">
                              {foreach from=$languages item=lang}
                              {assign var=detail value=null}
                              {foreach from=$item.details item=ad}
                              {if $ad.languageid == $lang.id}
                              {assign var=detail value=$ad}
                              {/if}
                              {/foreach}
                              <div title="Click vào cập nhật tên" data-lang="{$lang.id}" class="act-btn price-view tab c-name editable-name {if $lang.id==$currentLang}active{/if}" data-id="{$item.id}">
                                 <span>{$detail.name}</span>
                              </div>
                              {/foreach}
                           </div>
                        </td>

                        {if $tinhnang.price == 1}

                        <td align="center">
                           <span title="Click vào cập nhật giá"  class="act-btn price-view btn_edit_price"
                              data-id="{$item.id}"
                              data-price="{$item.price.price}">
                              {$item.price.price}₫
                           </span>
                        </td>
                        {/if}

                        <!-- <td align="center">
                           {$item.dated|date_format:"%d/%m/%Y %H:%M"}
                        </td>-->

                        <td align="center">
                           {$item.dated_edit|date_format:"%H:%M - %d/%m/%Y"}
                        </td> 

                        {if $tinhnang.new == 1}
                        <td align="center">
                           <!-- <button type="button"
                              class="btn_checks btn_toggle"
                              data-id="{$item.id}"
                              data-active="{$item.new}"
                              data-column="new"
                              data-table="articlelist">
                              <img src="images/{$item.new}.png" alt="Trạng thái Mới" />
                           </button> -->

                           <label class="toggle btn_toggle"
                              data-id="{$item.id}"
                              data-active="{$item.new}"
                              data-column="new"
                              data-table="articlelist">
                           <input type="checkbox" {if $item.new == 1}checked{/if}>
                           <span class="track"></span>
                           </label>
                        </td>
                        {/if}

                        {if $tinhnang.hot == 1}
                        <td align="center">
                           <!-- <button type="button"
                              class="btn_checks btn_toggle"
                              data-id="{$item.id}"
                              data-active="{$item.hot}"
                              data-column="hot"
                              data-table="articlelist">
                              <img src="images/{$item.hot}.png" alt="Trạng thái Hot" />
                           </button> -->

                           <label class="toggle btn_toggle"
                              data-id="{$item.id}"
                              data-active="{$item.hot}"
                              data-column="hot"
                              data-table="articlelist">
                           <input type="checkbox" {if $item.hot == 1}checked{/if}>
                           <span class="track"></span>
                           </label>
                        </td>
                        {/if}

                        {if $tinhnang.mostview == 1}
                        <td align="center">
                           <!-- <button type="button"
                              class="btn_checks btn_toggle"
                              data-id="{$item.id}"
                              data-active="{$item.mostview}"
                              data-column="mostview"
                              data-table="articlelist">
                              <img src="images/{$item.mostview}.png" alt="Trạng thái Xem nhiều" />
                           </button> -->
                           <label class="toggle btn_toggle"
                              data-id="{$item.id}"
                              data-active="{$item.mostview}"
                              data-column="mostview"
                              data-table="articlelist">
                           <input type="checkbox" {if $item.mostview == 1}checked{/if}>
                           <span class="track"></span>
                           </label>
                        </td>
                        {/if}

                        <td align="center">
                           <label class="toggle btn_toggle"
                                 data-id="{$item.id}"
                                 data-active="{$item.active}"
                                 data-column="active"
                                 data-table="articlelist">

                           <input type="checkbox" {if $item.active == 1}checked{/if}>
                           <span class="track"></span>
                           </label>
                        </td>

                        <td align="center">
                           <div class="flex-btn">

                              {if $smarty.request.comp == 1
                              || $smarty.request.comp == 2
                              || $smarty.request.comp == 3
                              || $smarty.request.comp == 25
                              || $smarty.request.comp == 27
                              || $smarty.request.comp == 10}
                              <div class="tab-mirror">
                                 {foreach from=$languages item=lang}
                                 {assign var=detail value=null}
                                 {foreach from=$item.details item=ad}
                                 {if $ad.languageid == $lang.id}
                                 {assign var=detail value=$ad}
                                 {/if}
                                 {/foreach}
                                 <!-- <a data-lang="{$lang.id}" class="tab act-btn btnView {if $lang.id==$currentLang}active{/if}" href="{$web_base_url}/{$detail.unique_key}.html" target="_blank" title="Xem nhanh">
                                    <i class="fa fa-eye"></i>
                                 </a> -->
                                 <a data-lang="{$lang.id}" class="tab act-btn btnView {if $lang.id==$currentLang}active{/if}" href="{$web_base_url}/{$detail.unique_key}.html" target="_blank" title="Xem nhanh">
                                    <i class="fa fa-eye"></i>
                                 </a>
                                 {/foreach}
                              </div>
                              {/if}
                              <a class="act-btn btnEdit" title="Chỉnh sửa" href="index.php?do=articlelist&act=edit&id={$item.id}&comp={$smarty.request.comp}">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <button type="button" class="act-btn btnUpdateNum" title="Làm mới" data-id="{$item.id}" data-comp="{$smarty.request.comp}">
                                 <i class="fa fa-refresh"></i>
                              </button>
                              <button  type="button" class="act-btn btnDeleteRow" title="Xoá" data-id="{$item.id}" data-comp="{$smarty.request.comp}">
                                 <i class="fa fa-trash"></i>
                              </button>
                              <!-- <span class="act-btn btnPassword"
                                 data-id="{$item.id}"
                                 title="Quản lý mật khẩu">
                                 <i class="fa fa-lock"></i>
                              </span> -->
                           </div>
                        </td>
                     </tr>
                     {/foreach}
                  </tbody>
               </table>
            </form>
            <div class="pagination-wrapper">
               {$pagination nofilter}
            </div>

         </div>
      </div>
   </div>
</div>
<div id="passwordModal" style="display:none" class="modal">
   <div class="modal-content">
      <h3>Quản lý mật khẩu bài viết</h3>

      <input type="hidden" id="article_id">

      <button id="btnGeneratePassword">
         🔑 Tạo mật khẩu
      </button>

      <div id="generatedBox" style="display:none; margin-top:10px;">
         <strong>Mật khẩu vừa tạo</strong>
         <input type="text" id="generatedPassword" readonly>
         <button onclick="copyPassword()">Copy</button>
      </div>

      <ul id="passwordList"></ul>

      <button onclick="closeModal()">Đóng</button>
   </div>
</div>