<?php /* Smarty version 2.6.30, created on 2026-02-11 15:34:34
         compiled from infos/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'infos/list.tpl', 25, false),)), $this); ?>
<div class="contentmain">
   <div class="main">
      <div class="left_sidebar padding10">
         <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "left.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </div>
      <div class="right_content conten">
         <div class="tbtitle2 link00">
            <form id="f" method="post" action="index.php?do=infos&act=dellist&cid=1">
               <table class="br1" style="border-bottom:0" width="100%" cellspacing="0" cellpadding="0">
                  <thead>
                     <tr>
                        <th class="width-order">Thứ tự</th>
                        <th class="width-ttl">Tiêu đề</th>
                        <?php if ($_SESSION['admin_artseed_username'] == 'kaita'): ?>
                        <th class="width-show">Show</th>
                        <th class="width-show">Kích hoạt</th>
                        <?php endif; ?>
                        <th class="width-action">Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php $_from = $this->_tpl_vars['view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['index'] => $this->_tpl_vars['item']):
?>
                     <tr>
                        <td align="center" class="brbottom"><?php echo $this->_tpl_vars['index']+1+$this->_tpl_vars['number']; ?>
</td>
                        <td class="paleft brbottom linkblack"><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['name_vn'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</td>
                        <?php if ($_SESSION['admin_artseed_username'] == 'kaita'): ?>
                        <td align="center" class="brbottom">
                          
                             
                           <label class="toggle btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['active']; ?>
"
                              data-column="active"
                              data-table="infos">
                           <input type="checkbox" <?php if ($this->_tpl_vars['item']['active'] == 1): ?>checked<?php endif; ?>>
                           <span class="track"></span>
                           </label>
                        </td>
                        <td align="center" class="brbottom">
                           <?php if ($this->_tpl_vars['item']['id'] == 2 || $this->_tpl_vars['item']['id'] == 12 || $this->_tpl_vars['item']['id'] == 13 || $this->_tpl_vars['item']['id'] == 14 || $this->_tpl_vars['item']['id'] == 15 || $this->_tpl_vars['item']['id'] == 16 || $this->_tpl_vars['item']['id'] == 19 || $this->_tpl_vars['item']['id'] == 20 || $this->_tpl_vars['item']['id'] == 21 || $this->_tpl_vars['item']['id'] == 22 || $this->_tpl_vars['item']['id'] == 23 || $this->_tpl_vars['item']['id'] == 24 || $this->_tpl_vars['item']['id'] == 25 || $this->_tpl_vars['item']['id'] == 26 || $this->_tpl_vars['item']['id'] == 27 || $this->_tpl_vars['item']['id'] == 28 || $this->_tpl_vars['item']['id'] == 30): ?>

                           
                           <label class="toggle btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['open']; ?>
"
                              data-column="open"
                              data-table="infos">
                           <input type="checkbox" <?php if ($this->_tpl_vars['item']['open'] == 1): ?>checked<?php endif; ?>>
                           <span class="track"></span>
                           </label>
                           <?php endif; ?>
                        </td>
                        <?php endif; ?>

                        <td align=" center" class="brbottom">
                           <div class="flex-btn">
                              <a class="act-btn btnEdit" href="index.php?do=infos&act=edit&comp=<?php echo $_REQUEST['comp']; ?>
&id=<?php echo $this->_tpl_vars['item']['id']; ?>
" title="Sửa">
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