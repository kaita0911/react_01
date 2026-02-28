<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content">
         <!-- <div class="divright">
            <div class="acti2">
               <button class="add" type="button" id="btnDelete" data-comp="0">
                  <i class="fa fa-trash"></i> Xóa
               </button>
            </div>
         </div> -->
         <div class="tbtitle2 main-content">
            <div class="box-meta">
               <label>Tình trạng</label>
               <span><i class="box-meta__status"></i>Chờ duyệt</span>
               <span><i class="box-meta__status --02"></i>Xác nhận</span>
               <span><i class="box-meta__status --03"></i>Đang giao</span>
               <span><i class="box-meta__status --04"></i>Hoàn thành</span>
            </div>
            <form name="f" id="f" method="post" action="">
               <table class="br1 order">
                  <thead>
                     <tr>
                        <!-- <th class="width-del" align="center">
                           <input type="checkbox" name="all" id="checkAll" />
                        </th> -->
                        <th class="width-date" align="center">Mã đơn</th>
                        <th class="width-ttl">Tiêu đề</th>
                        <th class="width-image" align="center">Trạng thái</th>
                        <th class="width-image" align="center">Tình trạng</th>
                        <th class="width-date" align="center">Ngày đặt</th>
                        <th class="width-action" align="center">Tổng tiền</th>
                        <th class="width-action" align="center">Action</th>
                     </tr>
                  </thead>

                  <tbody>
                     {foreach from=$view key=i item=item}
                     <tr data-id="{$item.id}" class="{if $item.is_read == 0}unread{/if} {if $item.status=='Chờ duyệt' }highlight{/if} ">
                        <!-- <td class="brbottom" align="center">
                           <input class="c-item" type="checkbox" name="cid[]" value="{$item.id}" />
                        </td> -->

                        <td class="brbottom" align="center">
                           {$item.order_code}
                        </td>

                        <td class="paleft brbottom">
                           {$item.name}
                        </td>
                        <td class="brbottom" align="center">
                           <select class="status-select" data-id="{$item.id}">
                              {foreach from=$item.steps key=idx item=step}
                              <option value="{$step}" {if $item.currentIndex==$idx}selected{/if}>
                                 {$step}
                              </option>
                              {/foreach}
                           </select>

                        </td>
                        <td class="brbottom" align="center">
                           <div class="box-status-orders">
                              {section name=s loop=$item.steps}
                              {if $smarty.section.s.index <= $item.currentIndex}
                                 <span class="step"></span>
                                 {else}
                                 <span class="none-step"></span>
                                 {/if}
                                 {/section}
                           </div>
                        </td>
                        <td class="brbottom" align="center">
                           {$item.created_at|date_format:"%H:%M:%S, %d-%m-%Y"}
                        </td>

                        <td class="brbottom" align="center">
                           {$item.totalend|number_format:0:".":","} đ
                        </td>

                        <td class="brbottom editorder" align="center">
                           <!-- <a href="index.php?do=orders&act=edit&id={$item.id}" title="Chi tiết">
                              Chi tiết
                           </a> -->
                           <a href="javascript:void(0)" class="btn-order-view" data-popup="index.php?do=orders&act=popup&id={$item.id}"><i class="fa fa-eye"></i> Chi tiết
                           </a>
                           
                        </td>
                     </tr>
                     {/foreach}
                  </tbody>
               </table>
            </form>
            <!-- <div class="modal-overlay" id="modalView">
               <div class="modal-box">
                  <div class="modal-header">
                     <h3>CHI TIẾT</h3>
                     <span class="modal-close">&times;</span>
                  </div>
                  <div class="modal-body" id="modalContent">
                     Đang tải dữ liệu...
                  </div>
               </div>
            </div> -->
            <div id="globalModal" class="modal">
               <div class="modal-box">
                  <span class="modal-close">&times;</span>
                  <div id="globalModalContent">Loading...</div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>