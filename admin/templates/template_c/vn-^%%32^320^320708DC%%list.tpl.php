<?php /* Smarty version 2.6.30, created on 2026-02-08 11:32:13
         compiled from orders/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'orders/list.tpl', 76, false),array('modifier', 'number_format', 'orders/list.tpl', 80, false),)), $this); ?>
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
                     <?php $_from = $this->_tpl_vars['view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
                     <tr data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
" class="<?php if ($this->_tpl_vars['item']['is_read'] == 0): ?>unread<?php endif; ?> <?php if ($this->_tpl_vars['item']['status'] == 'Chờ duyệt'): ?>highlight<?php endif; ?> ">
                        <!-- <td class="brbottom" align="center">
                           <input class="c-item" type="checkbox" name="cid[]" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" />
                        </td> -->

                        <td class="brbottom" align="center">
                           <?php echo $this->_tpl_vars['item']['order_code']; ?>

                        </td>

                        <td class="paleft brbottom">
                           <?php echo $this->_tpl_vars['item']['name']; ?>

                        </td>
                        <td class="brbottom" align="center">
                           <select class="status-select" data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                              <?php $_from = $this->_tpl_vars['item']['steps']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['idx'] => $this->_tpl_vars['step']):
?>
                              <option value="<?php echo $this->_tpl_vars['step']; ?>
" <?php if ($this->_tpl_vars['item']['currentIndex'] == $this->_tpl_vars['idx']): ?>selected<?php endif; ?>>
                                 <?php echo $this->_tpl_vars['step']; ?>

                              </option>
                              <?php endforeach; endif; unset($_from); ?>
                           </select>

                        </td>
                        <td class="brbottom" align="center">
                           <div class="box-status-orders">
                              <?php unset($this->_sections['s']);
$this->_sections['s']['name'] = 's';
$this->_sections['s']['loop'] = is_array($_loop=$this->_tpl_vars['item']['steps']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['s']['show'] = true;
$this->_sections['s']['max'] = $this->_sections['s']['loop'];
$this->_sections['s']['step'] = 1;
$this->_sections['s']['start'] = $this->_sections['s']['step'] > 0 ? 0 : $this->_sections['s']['loop']-1;
if ($this->_sections['s']['show']) {
    $this->_sections['s']['total'] = $this->_sections['s']['loop'];
    if ($this->_sections['s']['total'] == 0)
        $this->_sections['s']['show'] = false;
} else
    $this->_sections['s']['total'] = 0;
if ($this->_sections['s']['show']):

            for ($this->_sections['s']['index'] = $this->_sections['s']['start'], $this->_sections['s']['iteration'] = 1;
                 $this->_sections['s']['iteration'] <= $this->_sections['s']['total'];
                 $this->_sections['s']['index'] += $this->_sections['s']['step'], $this->_sections['s']['iteration']++):
$this->_sections['s']['rownum'] = $this->_sections['s']['iteration'];
$this->_sections['s']['index_prev'] = $this->_sections['s']['index'] - $this->_sections['s']['step'];
$this->_sections['s']['index_next'] = $this->_sections['s']['index'] + $this->_sections['s']['step'];
$this->_sections['s']['first']      = ($this->_sections['s']['iteration'] == 1);
$this->_sections['s']['last']       = ($this->_sections['s']['iteration'] == $this->_sections['s']['total']);
?>
                              <?php if ($this->_sections['s']['index'] <= $this->_tpl_vars['item']['currentIndex']): ?>
                                 <span class="step"></span>
                                 <?php else: ?>
                                 <span class="none-step"></span>
                                 <?php endif; ?>
                                 <?php endfor; endif; ?>
                           </div>
                        </td>
                        <td class="brbottom" align="center">
                           <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M:%S, %d-%m-%Y") : smarty_modifier_date_format($_tmp, "%H:%M:%S, %d-%m-%Y")); ?>

                        </td>

                        <td class="brbottom" align="center">
                           <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['totalend'])) ? $this->_run_mod_handler('number_format', true, $_tmp, 0, ".", ",") : number_format($_tmp, 0, ".", ",")); ?>
 đ
                        </td>

                        <td class="brbottom editorder" align="center">
                           <!-- <a href="index.php?do=orders&act=edit&id=<?php echo $this->_tpl_vars['item']['id']; ?>
" title="Chi tiết">
                              Chi tiết
                           </a> -->
                           <a href="javascript:void(0)" class="btn-order-view" data-popup="index.php?do=orders&act=popup&id=<?php echo $this->_tpl_vars['item']['id']; ?>
"><i class="fa fa-eye"></i> Chi tiết
                           </a>
                           
                        </td>
                     </tr>
                     <?php endforeach; endif; unset($_from); ?>
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