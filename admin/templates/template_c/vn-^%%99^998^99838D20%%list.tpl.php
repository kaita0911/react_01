<?php /* Smarty version 2.6.30, created on 2026-02-11 15:49:05
         compiled from language/list.tpl */ ?>
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
         <!-- Actions -->
         <div class="divtitle">
            <div class="divright">
               <div class="acti2">
                  <a class="add" href="index.php?do=language&act=add">
                     <i class="fa fa-plus-circle"></i> Thêm mới
                  </a>
               </div>
               <div class="acti2">
                  <button class="add" type="button" id="btnDelete" data-comp="0">
                     <i class="fa fa-trash"></i> Xóa
                  </button>
               </div>
            </div>
         </div>
         <div class="main-content">
            <form name="f" id="f" method="post" action="index.php?do=language&act=dellist">
               <table class="br1" cellspacing="0" cellpadding="0">
                  <thead>
                     <tr>
                        <th align="center" class="width-del">
                           <input type="checkbox" name="all" id="checkAll" />
                        </th>
                        <th align="center" class="width-show">Code</th>
                        <th align="left" class="width-ttl">Tiêu đề</th>
                        <th align="center" class="width-show">Mặc định</th>
                        <th align="center" class="width-show">Show</th>
                        <th align="center" class="width-action">Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $_from = $this->_tpl_vars['view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
                     <tr data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                        <td align="center">
                           <input type="checkbox" value="<?php echo $this->_tpl_vars['item']['id']; ?>
" name="cid[]" class="c-item">
                        </td>
                        <td align="center" class="hidden-xs"><?php echo $this->_tpl_vars['item']['code']; ?>
</td>
                        <td align="left"><?php echo $this->_tpl_vars['item']['name']; ?>
</td>
                        <td align="center">
                           
                           <label class="toggle btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['is_default']; ?>
"
                              data-column="is_default"
                              data-table="language">
                           <input type="checkbox" <?php if ($this->_tpl_vars['item']['is_default'] == 1): ?>checked<?php endif; ?>>
                           <span class="track"></span>
                           </label>
                        </td>
                        <td align="center">
                          
                           <label class="toggle btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['active']; ?>
"
                              data-column="active"
                              data-table="language">
                           <input type="checkbox" <?php if ($this->_tpl_vars['item']['active'] == 1): ?>checked<?php endif; ?>>
                           <span class="track"></span>
                           </label>
                        </td>
                        <td align="center">
                           <div class="flex-btn">
                              <a class="act-btn btnEdit" href="index.php?do=language&act=edit&id=<?php echo $this->_tpl_vars['item']['id']; ?>
" title="Edit">
                                 <i class="fa fa-edit"></i>
                              </a>
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