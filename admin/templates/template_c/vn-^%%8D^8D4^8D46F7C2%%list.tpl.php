<?php /* Smarty version 2.6.30, created on 2025-12-04 09:13:36
         compiled from footer/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'footer/list.tpl', 16, false),)), $this); ?>
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
         <div class="divright">
            <div class="acti2">
               <a class="add" href="index.php?do=footer&act=add">
                  <i class="fa fa-plus-circle"></i> Thêm mới
               </a>
            </div>
         </div>
         <div class="main-content">
            <?php if (count($this->_tpl_vars['languages']) > 1): ?>
            <ul class="tab-list">
               <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
               <li class="tab <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
"><?php echo $this->_tpl_vars['lang']['name']; ?>
</li>
               <?php endforeach; endif; unset($_from); ?>
            </ul>
            <?php endif; ?>
            <form class="form-all" method="post" action="">
               <table class="br1">
                  <thead>
                     <tr>
                        <th align="left" class="width-ttl">
                           <strong>Tiêu đề</strong>
                        </th>
                        <th class="width-action" align="center">
                           <strong>Action</strong>
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $_from = $this->_tpl_vars['view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>

                     <tr data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                        <td align="left" class="tab-mirror">
                           <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
                           <?php $this->assign('detail', null); ?>
                           <?php $_from = $this->_tpl_vars['item']['details']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ad']):
?>
                           <?php if ($this->_tpl_vars['ad']['languageid'] == $this->_tpl_vars['lang']['id']): ?>
                           <?php $this->assign('detail', $this->_tpl_vars['ad']); ?>
                           <?php endif; ?>
                           <?php endforeach; endif; unset($_from); ?>
                           <span class="tab <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
">
                              <?php echo $this->_tpl_vars['detail']['name']; ?>

                           </span>
                           <?php endforeach; endif; unset($_from); ?>
                        </td>
                        <td align="center">
                           <div class="flex-btn">
                              <a title="Chỉnh sửa" class="act-btn btnEdit" href="index.php?do=footer&act=edit&id=<?php echo $this->_tpl_vars['item']['id']; ?>
">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <button title="Xoá" type="button" class="act-btn btnDeleteRow" data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"> <i class="fa fa-trash"></i> </button>
                           </div>
                        </td>
                     </tr>
                     <?php endforeach; endif; unset($_from); ?>
                  </tbody>

               </table>
            </form>
         </div>

         <div class="clear"></div>
      </div>
   </div>
</div>