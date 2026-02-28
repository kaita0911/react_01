<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         {include file="left.tpl"}
      </div>

      <div class="right_content">
         <div class="detail_Cart">

            <div class="brtit">
               <h3>Thông tin khách hàng</h3>

               <div class="info-title">
                  <div class="title">
                     <label>Mã đơn :</span> {$edit.id}
                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Tên khách hàng :</label> {$edit.name}
                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Điện thoại :</label> {$edit.phone}
                  </div>
               </div>

               <div class="info-title hide">
                  <div class="title">
                     <label>Email :</label> {$edit.email}
                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Địa chỉ nhận hàng:</label> {$edit.address}, {$edit.phuongxa}, {$edit.quanhuyen}, {$edit.thanhpho}
                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Phương thức thanh toán :</label> {$edit.descs}
                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Ghi chú :</label> {$edit.content}
                  </div>
               </div>
            </div>

            <table class="br1 order">
               <thead>
                  <tr>
                     <th width="2%" class="order brbottom"><strong>STT</strong></th>
                     <th width="5%" class="order brbottom brleft hidden-xs"><strong>Hình ảnh</strong></th>
                     <th width="20%" class="titles brbottom brleft"><strong>Tiêu đề</strong></th>
                     <th width="5%" class="attr brbottom brleft"><strong>Số lượng</strong></th>
                     <th width="10%" class="amount text-right brbottom brleft"><strong>Đơn giá</strong></th>
                     <th width="10%" class="amount text-right brbottom brleft"><strong>Tạm tính</strong></th>
                  </tr>
               </thead>

               <tbody>
                  {foreach from=$order_line_view item=item name=i}
                  <tr class="item">
                     <td align="center" class="order brbottom">
                        {$item.id}
                     </td>
                     <td align="center" class="titles paleft brbottom brleft hidden-xs">
                        <img src="{$item.product_image}" alt="" width="50" height="50" />
                     </td>
                     <td align="left" class="titles paleft brbottom brleft">
                        {$item.product_name}
                     </td>

                     <td align="center" class="attr brbottom brleft">
                        {$item.qty}
                     </td>
                     <td align="center" class="amount text-right brbottom brleft">
                        {$item.product_price|number_format:0:",":"."} đ
                     </td>
                     <td align="center" class="amount text-right brbottom brleft">
                        {$item.tamtinh|number_format:0:",":"."} đ
                     </td>
                  </tr>
                  {/foreach}
               </tbody>
            </table>
            <div class="total-end-table">
               <div class="qulty">
                  Số lượng <span>{$edit.qty}</span>
               </div>
               <div class="sumqty">
                  Tổng tiền <span>{$edit.totalend|number_format:0:",":"."} đ</span>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>