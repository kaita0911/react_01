<?php /* Smarty version 2.6.30, created on 2026-02-08 14:49:06
         compiled from properties/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'properties/list.tpl', 47, false),)), $this); ?>
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
                        <?php $_from = $this->_tpl_vars['view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['index'] => $this->_tpl_vars['item']):
?>
                        <tr data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                           <td align="center">
                              <input type="checkbox" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" name="cid[]" class="c-item">
                           </td>
                           <td align="center">
                              <input type="text" name="ordering[]" class="InputOrder" value="<?php echo $this->_tpl_vars['item']['num']; ?>
" size="2" />
                              <input type="hidden" name="id[]" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" />
                           </td>
                           <td align="left" class="linkblack"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['name_vn'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
                           <td align="center">
                              <button type="button"
                                 class="btn_checks btn_toggle"
                                 data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                                 data-active="<?php echo $this->_tpl_vars['item']['active']; ?>
"
                                 data-column="active"
                                 data-table="banner">
                                 <img src="images/<?php echo $this->_tpl_vars['item']['active']; ?>
.png" alt="Show/Hide">
                              </button>
                           </td>
                           <td align="center">
                              <div class="flex-btn">
                                 <a class="act-btn btnEdit" href="index.php?do=properties&act=edit&id=<?php echo $this->_tpl_vars['item']['id']; ?>
" title="Edit">
                                    <i class="fa fa-edit"></i>
                                 </a>
                              </div>
                           </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                           <td colspan="5" align="center"><em>Không có dữ liệu</em></td>
                        </tr>
                        <?php endif; unset($_from); ?>
                     </tbody>
                  </table>
               </form>
            </div>
         </div>
         <!-- Table listing -->
      </div>
   </div>
</div>