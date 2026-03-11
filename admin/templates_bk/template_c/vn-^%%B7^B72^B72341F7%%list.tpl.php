<?php /* Smarty version 2.6.30, created on 2026-02-11 15:46:57
         compiled from component/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'component/list.tpl', 42, false),)), $this); ?>
<div class="contentmain">
   <div class="main">
      <!-- Sidebar -->
      <div class="left_sidebar padding10">
         <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </div>
      <!-- Main content -->
      <div class="right_content">
         <!-- Action buttons -->
         <div class="divright">
            <div class="acti2">
               <a class="add" href="index.php?do=component&act=add">
                  <i class="fa fa-plus-circle"></i> Thêm mới
               </a>
            </div>
         </div>
         <div class="main-content">
            <!-- Component Table -->
            <form name="f" id="f" method="post" action="index.php?do=component&act=dellist">
               <table class="br1" cellspacing="0" cellpadding="0">
                  <thead>
                     <tr>
                        <th align="center" class="width-del"><input type="checkbox" onclick="checkAll();" name="all" /></th>
                        <th align="center" class="width-order">Thứ tự</th>
                        <th class="width-image left">Type</th>
                        <th class="width-ttl">Tiêu đề</th>
                        <th class="width-show">Show</th>
                        <th class="width-action">Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $_from = $this->_tpl_vars['view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
                     <tr>
                        <td align="center">
                           <input type="checkbox" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" name="iddel[]" />
                        </td>
                        <td align="center">
                           <input type="text" name="ordering[]" class="InputOrder" value="<?php echo $this->_tpl_vars['item']['num']; ?>
" size="2">
                           <input type="hidden" name="id[]" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" />
                        </td>
                        <td class="paleft"><?php echo $this->_tpl_vars['item']['do']; ?>
</td>
                        <td class="paleft"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['detail_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
                        <td align="center" class="hidden-xs">
                          
                           <label class="toggle btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['active']; ?>
"
                              data-column="active"
                              data-table="component">
                           <input type="checkbox" <?php if ($this->_tpl_vars['item']['active'] == 1): ?>checked<?php endif; ?>>
                           <span class="track"></span>
                           </label>
                        </td>
                        <td align="center">
                           <div class="flex-btn">
                              <a class="act-btn btnEdit" href="index.php?do=component&act=edit&id=<?php echo $this->_tpl_vars['item']['id']; ?>
" title="Edit"><i class="fa fa-edit"></i></a>
                           </div>
                        </td>
                     </tr>
                     <?php endforeach; endif; unset($_from); ?>
                  </tbody>
               </table>
            </form>
         </div>

      </div>
   </div>
</div>