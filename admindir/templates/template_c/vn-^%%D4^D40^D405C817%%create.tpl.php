<?php /* Smarty version 2.6.30, created on 2025-12-28 11:11:14
         compiled from menu/create.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'menu/create.tpl', 18, false),)), $this); ?>
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
         <form id="frmEdit" name="frmEdit" action="index.php?do=menu&act=<?php if ($_REQUEST['act'] == 'add'): ?>addsm<?php else: ?>editsm<?php endif; ?>" method="post" enctype="multipart/form-data">
            <div class="col00">
               <div class="content">
                  <input type="hidden" name="cat" value="<?php echo $_REQUEST['cid']; ?>
">
                  <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['edit']['id']; ?>
">
                  <div class="divright">
                     <div class="acti2"><button class="add" type="submit"><i class="fa fa-save"></i> Save</button></div>
                     <div class="acti2"> <a class="add" href="javascript:history.back()"><i class="fa fa-mail-reply"></i> Trở về</a></div>
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
                     <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
                     <div class="tab-content <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
">

                     </div>
                     <?php endforeach; endif; unset($_from); ?>
                     <div class="item">
                        <div class="title">Tiêu đề</div>
                        <div class="info-title">
                           <input type="text" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][name]" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
" id="title_<?php echo $this->_tpl_vars['lang']['code']; ?>
"
                              class="InputText title-input" <?php if ($this->_tpl_vars['lang']['code'] == 'vi'): ?>required<?php endif; ?> />
                        </div>
                     </div>
                     <div class="item">
                        <div class="title">URL</div>
                        <div class="info-title">
                           <input type="text" id="slug_<?php echo $this->_tpl_vars['lang']['code']; ?>
" name="languages[<?php echo $this->_tpl_vars['lang']['id']; ?>
][unique_key]" data-lang="<?php echo $this->_tpl_vars['lang']['code']; ?>
"
                              class="InputText slug-input" />
                        </div>
                     </div>
                     <div class="item">
                        <div class="title">Liên kết</div>
                        <div class="option_link">
                           <label class="radio-inline"><input type="radio" id="yes2" name="choose" value="1" checked> Loại bài viết</label>
                           <label class="radio-inline"><input type="radio" id="yes1" name="choose" value="0"> Link</label>
                        </div>
                        <select id="menu" name="menu" class="show">
                           <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['lienket']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
                           <option value="<?php echo $this->_tpl_vars['lienket'][$this->_sections['i']['index']]['component_id']; ?>
" <?php if ($this->_tpl_vars['lienket'][$this->_sections['i']['index']]['component_id'] == $this->_tpl_vars['edit']['comp']): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['lienket'][$this->_sections['i']['index']]['name']; ?>
</option>
                           <?php endfor; endif; ?>
                        </select>
                        <div class="linkngoai hide"><input type="text" id="link" name="link" value="<?php echo $this->_tpl_vars['edit']['link']; ?>
" class="InputText"></div>
                     </div>
                     <div class="item">
                        <div class="title">Có menu con <input type="checkbox" class="CheckBox" name="menucon" value="menucon" <?php if ($this->_tpl_vars['edit']['menucon'] == 1): ?>checked<?php endif; ?>></div>
                     </div>
                     <div class="item">
                        <div class="title">Hiển thị <input type="checkbox" class="CheckBox" name="active" value="active" <?php if ($this->_tpl_vars['edit']['active'] == 1 || $_REQUEST['act'] == 'add'): ?>checked<?php endif; ?>></div>
                     </div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
<?php echo '
<script>
   function toggleLinkOption() {
      const isLink = document.getElementById(\'yes1\').checked;
      document.getElementById(\'menu\').classList.toggle(\'hide\', isLink);
      document.querySelector(\'.linkngoai\').classList.toggle(\'show\', isLink);
      document.querySelector(\'.linkngoai\').classList.toggle(\'hide\', !isLink);
   }
   document.getElementById(\'yes1\').addEventListener(\'click\', toggleLinkOption);
   document.getElementById(\'yes2\').addEventListener(\'click\', toggleLinkOption);
   toggleLinkOption();

   function SubmitFromGo(formId) {
      if (typeof updateAllSlugs === \'function\') updateAllSlugs();
      const form = document.getElementById(formId);
      if (form) form.submit();
      else console.error("Không tìm thấy form:", formId);
   }
</script>
'; ?>