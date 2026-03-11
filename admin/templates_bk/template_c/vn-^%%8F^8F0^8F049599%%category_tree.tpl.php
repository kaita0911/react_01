<?php /* Smarty version 2.6.30, created on 2025-11-21 09:49:33
         compiled from articlelist/category_tree.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'articlelist/category_tree.tpl', 6, false),array('modifier', 'is_array', 'articlelist/category_tree.tpl', 7, false),array('modifier', 'count', 'articlelist/category_tree.tpl', 12, false),)), $this); ?>
<li>
    <label>
        <input type="checkbox"
            name="parentids[]"
            value="<?php echo $this->_tpl_vars['node']['id']; ?>
"
            data-parent="<?php echo ((is_array($_tmp=@$this->_tpl_vars['node']['parent_id'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
"
            <?php if (is_array($this->_tpl_vars['selected']) && in_array ( $this->_tpl_vars['node']['id'] , $this->_tpl_vars['selected'] )): ?>checked="checked" <?php endif; ?>>
        <?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['node']['level']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
?>--&nbsp;<?php endfor; endif; ?>
        <?php echo ((is_array($_tmp=@$this->_tpl_vars['node']['detailsList'][$this->_tpl_vars['currentLang']]['name'])) ? $this->_run_mod_handler('default', true, $_tmp, @$this->_tpl_vars['node']['name']) : smarty_modifier_default($_tmp, @$this->_tpl_vars['node']['name'])); ?>

    </label>

    <?php if (count($this->_tpl_vars['node']['children']) > 0): ?>
    <ul>
        <?php $_from = $this->_tpl_vars['node']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['child']):
?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "articlelist/category_tree.tpl", 'smarty_include_vars' => array('node' => $this->_tpl_vars['child'],'selected' => $this->_tpl_vars['selected'],'currentLang' => $this->_tpl_vars['currentLang'],'languages' => $this->_tpl_vars['languages'],'level' => $this->_tpl_vars['child']['level'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endforeach; endif; unset($_from); ?>
    </ul>
    <?php endif; ?>
</li>