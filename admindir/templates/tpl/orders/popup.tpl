<h3 style="margin-bottom:15px; margin-top:0;">
   Mã đơn: #{$order.order_code}
</h3>

<table class="table-popup" width="100%" cellpadding="8">
   <tr>
      <td width="30%"><strong>Khách hàng</strong></td>
      <td>{$order.name}</td>
   </tr>

   <tr>
      <td><strong>Điện thoại</strong></td>
      <td>{$order.phone}</td>
   </tr>
   <tr>
      <td><strong>Địa chỉ</strong></td>
      <td> {$order.address}, {$order.phuongxa}, {$order.quanhuyen}, {$order.thanhpho}</td>
   </tr>
   <tr>
      <td><strong>Phương thức thanh toán</strong></td>
      <td> {$order.descs}</td>
   </tr>
   <!-- <tr>
      <td><strong>Email</strong></td>
      <td>{$order.email}</td>
   </tr> -->

   <tr>
      <td><strong>Ngày đặt</strong></td>
      <td>{$order.created_at|date_format:"%H:%M:%S %d-%m-%Y"}</td>
   </tr>

   
   <tr>
      <td><strong>Số lượng</strong></td>
      <td style="color:red;font-weight:bold">
      {$order.qty}
      </td>
   </tr>
   <tr>
      <td><strong>Tổng tiền</strong></td>
      <td style="color:red;font-weight:bold">
         {$order.totalend|number_format:0:".":","} đ
      </td>
   </tr>
</table>

<h4 style="margin:15px 0 10px;">Chi tiết đơn hàng</h4>

<table class="br1 order">
    <thead>
        <tr>
          
            <th width="5%" class="order brbottom brleft hidden-xs"><strong>Hình ảnh</strong></th>
            <th width="20%" class="titles brbottom brleft"><strong>Tên</strong></th>
            <th width="5%" class="attr brbottom brleft"><strong>Số lượng</strong></th>
            <th width="10%" class="amount text-right brbottom brleft"><strong>Đơn giá</strong></th>
            <th width="10%" class="amount text-right brbottom brleft"><strong>Tạm tính</strong></th>
        </tr>
    </thead>

    <tbody>
        {foreach from=$order_line_view item=item name=i}
        <tr class="item">
           
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
