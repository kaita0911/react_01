<div class="contentmain">
   <div class="main">
      <aside class="left_sidebar padding10">
         {include file="left.tpl"}
      </aside>
      <section class="right_content">
         <div class="divright">
            <div class="acti2">
               <button class="add" type="button" id="btnDelete" data-comp="">
                  <i class="fa fa-trash"></i> Xóa
               </button>
            </div>
         </div>
         <div class="right_content-wrap">
            <form class="form-all" method="post" action="">
               <table class="br1 w-full border-collapse">
                  <thead>
                     <tr>
                        <th align="center" class="width-del">
                           <input type="checkbox" name="all" id="checkAll" />
                        </th>
                        <th class="width-del">STT</th>

                        <th class="width-ttl">Tiêu đề</th>
                        <th class="width-date">Ngày tháng</th>
                        <th class="width-action">Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     {foreach from=$articlelist item=item name=loop}
                     <tr data-id="{$item.id}" class="{if $item.is_read == 0}unread{/if}">

                        <td class="text-center">
                           <input type="checkbox" class="c-item" name="cid[]" value="{$item.id}">
                        </td>

                        <td align="center" class=" text-center">
                           {$smarty.foreach.loop.iteration}
                        </td>
                        <td class=" text-left linkblack">
                           {$item.fullname}
                        </td>
                        <td align="center" class=" text-center linkblack">
                            {$item.created_at|date_format:"%H:%M:%S  %d-%m-%Y"}
                        </td>
                        <td align="center">
                           <!-- <a href="javascript:void(0)" class="btn-view" data-id="{$item.id}"> <i class="fa fa-eye"></i> Xem chi tiết</a> -->
                           <a href="javascript:void(0)" class="btn-order-view" data-popup="index.php?do=register_info&act=popup&id={$item.id}"><i class="fa fa-eye"></i> Chi tiết
                           </a>
                        </td>
                     </tr>
                     {/foreach}
                  </tbody>
               </table>
            </form>
            <div class="pagination-wrapper">
               {$pagination nofilter}
            </div>
            <div id="globalModal" class="modal">
               <div class="modal-box">
                  <span class="modal-close">&times;</span>
                  <div id="globalModalContent">Loading...</div>
               </div>
            </div>
         </div>
      </section>
   </div>
</div>