<?php /* Smarty version 2.6.30, created on 2026-02-06 09:09:33
         compiled from register_info/register_popup.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'register_info/register_popup.tpl', 53, false),)), $this); ?>


<table class="table-view w-full" cellspacing="0" cellpadding="0">

<?php if ($this->_tpl_vars['edit']['title']): ?>
<tr>
   <th class="tbl-label">Bài viết đã xem</th>
   <td>
   <?php echo $this->_tpl_vars['edit']['title']; ?>

   </td>
</tr>
<?php endif; ?>

<tr>
   <th class="tbl-label">Họ tên</th>
   <td>
   <?php echo $this->_tpl_vars['edit']['fullname']; ?>

   </td>
</tr>

<tr>
   <th class="tbl-label">Điện thoại</th>
   <td>
   <?php echo $this->_tpl_vars['edit']['phone']; ?>

   </td>
</tr>

<tr>
   <th class="tbl-label">Email</th>
   <td>
   <?php echo $this->_tpl_vars['edit']['email']; ?>

   </td>
</tr>

<?php if ($this->_tpl_vars['edit']['address']): ?>
<tr>
   <th class="tbl-label">Địa chỉ</th>
   <td>
   <?php echo $this->_tpl_vars['edit']['address']; ?>

   </td>
</tr>
<?php endif; ?>

<tr>
   <th class="tbl-label">Nội dung</th>
   <td>
   <?php echo $this->_tpl_vars['edit']['message']; ?>

   </td>
</tr>
<tr>
   <th class="tbl-label">Thời gian</th>
   <td>
   <?php echo ((is_array($_tmp=$this->_tpl_vars['edit']['created_at'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%H:%M:%S  %d-%m-%Y") : smarty_modifier_date_format($_tmp, "%H:%M:%S  %d-%m-%Y")); ?>

   </td>
</tr>

</table>