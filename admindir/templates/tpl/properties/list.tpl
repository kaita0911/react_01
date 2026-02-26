<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content">
         <!-- Top action buttons -->
         <div class="divright">
            <div class="acti2">
               <a class="add" href="index.php?do=properties&act=add">
                  <i class="fa fa-plus-circle"></i> Thêm mới
               </a>
            </div>
            <div class="acti2">
               <button class="add" type="button" id="btnDelete" data-comp="0">
                  <i class="fa fa-trash"></i> Xóa
               </button>
            </div>
         </div>
         <div class="main-content">
            <div class="tbtitle2">
               <form name="f" id="f" method="post" action="index.php?do=properties&act=dellist&cid=1">
                  <table class="br1">
                     <thead>
                        <tr>
                           <th align="center" class="width-del">
                              <input type="checkbox" id="checkAll" />
                           </th>
                           <th align="center" class="width-order">Thứ tự</th>
                           <th align="left" class="width-ttl">Tiêu đề</th>
                           <th align="center" class="width-show">Show</th>
                           <th align="center" class="width-action">Action</th>
                        </tr>
                     </thead>

                     <tbody>
                        {foreach from=$view key=index item=item}
                        <tr data-id="{$item.id}">
                           <td align="center">
                              <input type="checkbox" value="{$item.id}" name="cid[]" class="c-item">
                           </td>
                           <td align="center">
                              <input type="text" name="ordering[]" class="InputOrder" value="{$item.num}" size="2" />
                              <input type="hidden" name="id[]" value="{$item.id}" />
                           </td>
                           <td align="left" class="linkblack">{$item.name_vn|escape}</td>
                           <td align="center">
                              <button type="button"
                                 class="btn_checks btn_toggle"
                                 data-id="{$item.id}"
                                 data-active="{$item.active}"
                                 data-column="active"
                                 data-table="banner">
                                 <img src="images/{$item.active}.png" alt="Show/Hide">
                              </button>
                           </td>
                           <td align="center">
                              <div class="flex-btn">
                                 <a class="act-btn btnEdit" href="index.php?do=properties&act=edit&id={$item.id}" title="Edit">
                                    <i class="fa fa-edit"></i>
                                 </a>
                              </div>
                           </td>
                        </tr>
                        {foreachelse}
                        <tr>
                           <td colspan="5" align="center"><em>Không có dữ liệu</em></td>
                        </tr>
                        {/foreach}
                     </tbody>
                  </table>
               </form>
            </div>
         </div>
         <!-- Table listing -->
      </div>
   </div>
</div>