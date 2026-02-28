<?php /* Smarty version 2.6.30, created on 2026-02-09 11:12:15
         compiled from categories/category_row_lang.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'categories/category_row_lang.tpl', 32, false),array('modifier', 'escape', 'categories/category_row_lang.tpl', 32, false),array('modifier', 'count', 'categories/category_row_lang.tpl', 95, false),)), $this); ?>
<tr data-id="<?php echo $this->_tpl_vars['category']['id']; ?>
">
    <td align="center">
        <input class="c-item" type="checkbox" name="cid[]" value="<?php echo $this->_tpl_vars['category']['id']; ?>
">
    </td>
    <td align="center">
        <input type="text" class="numInput" value="<?php echo $this->_tpl_vars['category']['num']; ?>
">
    </td>

    <?php if ($this->_tpl_vars['tinhnang']['hinhdanhmuc'] == 1): ?>
    <td align="center" class="img-table">
        <?php if ($this->_tpl_vars['category']['img_vn']): ?>
        <div class="c-imgs">
            <img src="/<?php echo $this->_tpl_vars['category']['img_vn']; ?>
?width=60&height=60&mode=scale" alt="img">
        </div>
        <?php endif; ?>
    </td>
    <?php endif; ?>

    <td align="left">


        <div class="tab-mirror">
            <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
            <?php $this->assign('detail', null); ?>
            <?php $_from = $this->_tpl_vars['category']['detailsList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ad']):
?>
            <?php if ($this->_tpl_vars['ad']['languageid'] == $this->_tpl_vars['lang']['id']): ?>
            <?php $this->assign('detail', $this->_tpl_vars['ad']); ?>
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>

            <span class="tab c-name <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>" data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
">
                <span class="view-text"> <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['level']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
?>&nbsp;--<?php endfor; endif; ?> <?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['detail']['name'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
</span>
                <input type="text" class="edit-input form-control" value="<?php echo ((is_array($_tmp=((is_array($_tmp=@$this->_tpl_vars['detail']['name'])) ? $this->_run_mod_handler('default', true, $_tmp, '') : smarty_modifier_default($_tmp, '')))) ? $this->_run_mod_handler('escape', true, $_tmp, 'html', 'UTF-8') : smarty_modifier_escape($_tmp, 'html', 'UTF-8')); ?>
" style="display:none;">
            </span>
            <?php endforeach; endif; unset($_from); ?>
        </div>
    </td>

    <?php if ($this->_tpl_vars['tinhnang']['danhmuchome'] == 1): ?>
    <td align="center">
        
        <label class="toggle btn_toggle"
            data-id="<?php echo $this->_tpl_vars['category']['id']; ?>
" 
            data-active="<?php echo $this->_tpl_vars['category']['home']; ?>
" 
            data-column="home" 
            data-table="categories">
            <input type="checkbox" <?php if ($this->_tpl_vars['category']['home'] == 1): ?>checked<?php endif; ?>>
            <span class="track"></span>
        </label>
    </td>
    <?php endif; ?>

    <td align="center">
       
        <label class="toggle btn_toggle"
            data-id="<?php echo $this->_tpl_vars['category']['id']; ?>
" 
            data-active="<?php echo $this->_tpl_vars['category']['active']; ?>
" 
            data-column="active" 
            data-table="categories">
            <input type="checkbox" <?php if ($this->_tpl_vars['category']['active'] == 1): ?>checked<?php endif; ?>>
            <span class="track"></span>
        </label>
    </td>

    <td align="center">
        <div class="flex-btn tab-mirror">

            <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
            <?php $this->assign('detail', null); ?>
            <?php $_from = $this->_tpl_vars['category']['detailsList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['ad']):
?>
            <?php if ($this->_tpl_vars['ad']['languageid'] == $this->_tpl_vars['lang']['id']): ?>
            <?php $this->assign('detail', $this->_tpl_vars['ad']); ?>
            <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>

            <a data-lang="<?php echo $this->_tpl_vars['lang']['id']; ?>
" class="tab act-btn btnView <?php if ($this->_tpl_vars['lang']['id'] == $this->_tpl_vars['currentLang']): ?>active<?php endif; ?>"
                href="<?php echo $this->_tpl_vars['web_base_url']; ?>
/<?php echo $this->_tpl_vars['detail']['unique_key']; ?>
/" target="_blank" title="Xem nhanh">
                <i class="fa fa-eye"></i>
            </a>
            <?php endforeach; endif; unset($_from); ?>

            <a title="Chỉnh sửa" class="act-btn btnEdit" href="index.php?do=categories&act=edit&id=<?php echo $this->_tpl_vars['category']['id']; ?>
&comp=<?php echo $_REQUEST['comp']; ?>
">
                <i class="fa fa-edit"></i>
            </a>
            <button title="Làm mới" type="button" class="act-btn btnUpdateNum" data-id="<?php echo $this->_tpl_vars['category']['id']; ?>
" data-comp="<?php echo $_REQUEST['comp']; ?>
">
                <i class="fa fa-refresh"></i>
            </button>
            <button title="Xoá" type="button" class="act-btn btnDeleteRow" data-id="<?php echo $this->_tpl_vars['category']['id']; ?>
">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </td>
</tr>

<?php if (count($this->_tpl_vars['category']['children']) > 0): ?>
<?php $_from = $this->_tpl_vars['category']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['child']):
?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "categories/category_row_lang.tpl", 'smarty_include_vars' => array('category' => $this->_tpl_vars['child'],'level' => $this->_tpl_vars['level']+1,'languages' => $this->_tpl_vars['languages'],'currentLang' => $this->_tpl_vars['currentLang'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>