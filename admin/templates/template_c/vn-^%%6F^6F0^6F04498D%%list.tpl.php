<?php /* Smarty version 2.6.30, created on 2026-02-08 14:38:59
         compiled from contact/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'contact/list.tpl', 42, false),)), $this); ?>
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
               <div class="acti2">
                  <button class="add" type="button" id="btnDelete" data-comp="0">
                     <i class="fa fa-trash"></i> Xóa
                  </button>
               </div>
            </div>
         </div>
         <div class="right_content-wrap">
            <form id="f" name="f"
               method="post"
               action="index.php?do=contact&act=dellist&cid=1&city=<?php echo $_REQUEST['city']; ?>
&type=<?php echo $_REQUEST['type']; ?>
">
               <table class="br1">
                  <thead>
                     <tr>
                        <th class="width-del" align="center">
                           <input type="checkbox" name="all" id="checkAll" />
                        </th>
                        <th class="width-ttl" align="left">Tiêu đề</th>
                        <!-- <th class="width-image" align="center">File đính kèm</th> -->
                        <th class="width-date" align="center">Ngày tháng</th>
                        <th class="width-action" align="center">Action</th>
                     </tr>
                  </thead>

                  <tbody>
                     <?php $_from = $this->_tpl_vars['view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
                     <tr data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
" class="<?php if ($this->_tpl_vars['item']['is_read'] == 0): ?>unread<?php endif; ?>">
                        <td align="center" class="brbottom">
                           <input class="c-item" type="checkbox" name="cid[]" value="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                        </td>

                        <td align="left" class=" brbottom">
                           <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

                        </td>
                           <!-- <td align="center" class=" brbottom">
                              <?php if ($this->_tpl_vars['item']['fileUpload'] != null): ?>
                              <a href="../../../<?php echo $this->_tpl_vars['item']['fileUpload']; ?>
" target="_blank">
                                 <i class="fa fa-book"></i> Xem file
                              </a>
                              <?php endif; ?>
                           </td> -->


                        <td align="center" class="brbottom">
                           <?php echo ((is_array($_tmp=$this->_tpl_vars['item']['dated'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>

                        </td>
                        <td align="center" class="brbottom">

                           <!-- <a href="index.php?do=contact&act=edit&id=<?php echo $this->_tpl_vars['item']['id']; ?>
&comp=<?php echo $_REQUEST['comp']; ?>
">
                              Chi tiết
                           </a> -->
                           <a href="javascript:void(0)" class="btn-order-view" data-popup="index.php?do=contact&act=popup&id=<?php echo $this->_tpl_vars['item']['id']; ?>
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
         </div>
         <div id="globalModal" class="modal">
               <div class="modal-box">
                  <span class="modal-close">&times;</span>
                  <div id="globalModalContent">Loading...</div>
               </div>
            </div>
      </div>
   </div>
</div>