<?php /* Smarty version 2.6.30, created on 2026-02-08 10:47:55
         compiled from register_info/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'register_info/list.tpl', 44, false),)), $this); ?>
<div class="contentmain">
   <div class="main">
      <aside class="left_sidebar padding10">
         <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
                     <?php $_from = $this->_tpl_vars['articlelist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['item']):
        $this->_foreach['loop']['iteration']++;
?>
                     <tr data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
" class="<?php if ($this->_tpl_vars['item']['is_read'] == 0): ?>unread<?php endif; ?>">

                        <td class="text-center">
                           <input type="checkbox" class="c-item" name="cid[]" value="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                        </td>

                        <td align="center" class=" text-center">
                           <?php echo $this->_foreach['loop']['iteration']; ?>

                        </td>
                        <td class=" text-left linkblack">
                           <?php echo $this->_tpl_vars['item']['fullname']; ?>

                        </td>
                        <td align="center" class=" text-center linkblack">
                            <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['created_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M:%S  %d-%m-%Y") : smarty_modifier_date_format($_tmp, "%H:%M:%S  %d-%m-%Y")); ?>

                        </td>
                        <td align="center">
                           <!-- <a href="javascript:void(0)" class="btn-view" data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"> <i class="fa fa-eye"></i> Xem chi tiết</a> -->
                           <a href="javascript:void(0)" class="btn-order-view" data-popup="index.php?do=register_info&act=popup&id=<?php echo $this->_tpl_vars['item']['id']; ?>
"><i class="fa fa-eye"></i> Chi tiết
                           </a>
                        </td>
                     </tr>
                     <?php endforeach; endif; unset($_from); ?>
                  </tbody>
               </table>
            </form>
            <div class="pagination-wrapper">
               <?php echo $this->_tpl_vars['pagination']; ?>

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