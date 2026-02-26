<?php /* Smarty version 2.6.30, created on 2026-01-06 14:51:57
         compiled from orders/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'number_format', 'orders/edit.tpl', 85, false),)), $this); ?>
<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </div>

      <div class="right_content">
         <div class="detail_Cart">

            <div class="brtit">
               <h3>Thông tin khách hàng</h3>

               <div class="info-title">
                  <div class="title">
                     <label>Mã đơn :</span> <?php echo $this->_tpl_vars['edit']['id']; ?>

                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Tên khách hàng :</label> <?php echo $this->_tpl_vars['edit']['name']; ?>

                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Điện thoại :</label> <?php echo $this->_tpl_vars['edit']['phone']; ?>

                  </div>
               </div>

               <div class="info-title hide">
                  <div class="title">
                     <label>Email :</label> <?php echo $this->_tpl_vars['edit']['email']; ?>

                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Địa chỉ nhận hàng:</label> <?php echo $this->_tpl_vars['edit']['address']; ?>
, <?php echo $this->_tpl_vars['edit']['phuongxa']; ?>
, <?php echo $this->_tpl_vars['edit']['quanhuyen']; ?>
, <?php echo $this->_tpl_vars['edit']['thanhpho']; ?>

                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Phương thức thanh toán :</label> <?php echo $this->_tpl_vars['edit']['descs']; ?>

                  </div>
               </div>

               <div class="info-title">
                  <div class="title">
                     <label>Ghi chú :</label> <?php echo $this->_tpl_vars['edit']['content']; ?>

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
                  <?php $_from = $this->_tpl_vars['order_line_view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['i'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['i']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['item']):
        $this->_foreach['i']['iteration']++;
?>
                  <tr class="item">
                     <td align="center" class="order brbottom">
                        <?php echo $this->_tpl_vars['item']['id']; ?>

                     </td>
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
            <div class="total-end-table">
               <div class="qulty">
                  Số lượng <span><?php echo $this->_tpl_vars['edit']['qty']; ?>
</span>
               </div>
               <div class="sumqty">
                  Tổng tiền <span><?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['totalend'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 0, ",", ".") : number_format($_tmp, 0, ",", ".")); ?>
 đ</span>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>