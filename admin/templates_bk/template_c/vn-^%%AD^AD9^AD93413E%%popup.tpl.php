<?php /* Smarty version 2.6.30, created on 2026-02-25 08:50:37
         compiled from orders/popup.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'orders/popup.tpl', 30, false),array('modifier', 'number_format', 'orders/popup.tpl', 43, false),)), $this); ?>
<h3 style="margin-bottom:15px; margin-top:0;">
   Mã đơn: #<?php echo $this->_tpl_vars['order']['order_code']; ?>

</h3>

<table class="table-popup" width="100%" cellpadding="8">
   <tr>
      <td width="30%"><strong>Khách hàng</strong></td>
      <td><?php echo $this->_tpl_vars['order']['name']; ?>
</td>
   </tr>

   <tr>
      <td><strong>Điện thoại</strong></td>
      <td><?php echo $this->_tpl_vars['order']['phone']; ?>
</td>
   </tr>
   <tr>
      <td><strong>Địa chỉ</strong></td>
      <td> <?php echo $this->_tpl_vars['order']['address']; ?>
, <?php echo $this->_tpl_vars['order']['phuongxa']; ?>
, <?php echo $this->_tpl_vars['order']['quanhuyen']; ?>
, <?php echo $this->_tpl_vars['order']['thanhpho']; ?>
</td>
   </tr>
   <tr>
      <td><strong>Phương thức thanh toán</strong></td>
      <td> <?php echo $this->_tpl_vars['order']['descs']; ?>
</td>
   </tr>
   <!-- <tr>
      <td><strong>Email</strong></td>
      <td><?php echo $this->_tpl_vars['order']['email']; ?>
</td>
   </tr> -->

   <tr>
      <td><strong>Ngày đặt</strong></td>
      <td><?php echo ((is_array($_tmp=$this->_tpl_vars['order']['created_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M:%S %d-%m-%Y") : smarty_modifier_date_format($_tmp, "%H:%M:%S %d-%m-%Y")); ?>
</td>
   </tr>

   
   <tr>
      <td><strong>Số lượng</strong></td>
      <td style="color:red;font-weight:bold">
      <?php echo $this->_tpl_vars['order']['qty']; ?>

      </td>
   </tr>
   <tr>
      <td><strong>Tổng tiền</strong></td>
      <td style="color:red;font-weight:bold">
         <?php echo ((is_array($_tmp=$this->_tpl_vars['order']['totalend'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 0, ".", ",") : number_format($_tmp, 0, ".", ",")); ?>
 đ
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
        <?php $_from = $this->_tpl_vars['order_line_view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['i'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['i']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['item']):
        $this->_foreach['i']['iteration']++;
?>
        <tr class="item">
           
            <td align="center" class="titles paleft brbottom brleft hidden-xs">
            <img src="<?php echo $this->_tpl_vars['item']['product_image']; ?>
" alt="" width="50" height="50" />
            </td>
            <td align="left" class="titles paleft brbottom brleft">
            <?php echo $this->_tpl_vars['item']['product_name']; ?>

            </td>

            <td align="center" class="attr brbottom brleft">
            <?php echo $this->_tpl_vars['item']['qty']; ?>

            </td>
            <td align="center" class="amount text-right brbottom brleft">
            <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['product_price'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 0, ",", ".") : number_format($_tmp, 0, ",", ".")); ?>
 đ
            </td>
            <td align="center" class="amount text-right brbottom brleft">
            <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['tamtinh'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 0, ",", ".") : number_format($_tmp, 0, ",", ".")); ?>
 đ
            </td>
        </tr>
        <?php endforeach; endif; unset($_from); ?>
    </tbody>
</table>