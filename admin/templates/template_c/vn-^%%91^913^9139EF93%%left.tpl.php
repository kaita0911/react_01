<?php /* Smarty version 2.6.30, created on 2026-02-06 09:43:47
         compiled from left.tpl */ ?>
<a href="/" target="_blank" class="logo">
  <img src="/<?php echo $this->_tpl_vars['logoadmin']['img_thumb_vn']; ?>
" alt="Logo" />
</a>

<div class="menusidebar" id="sidebar">
  <?php $_from = $this->_tpl_vars['ListMenuLeft']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['menu']):
?>
  <div class="nav-item">
    <div class="nav-toggle" href="<?php echo $this->_tpl_vars['menu']['links']['list']; ?>
">
      <span><i class="fa <?php echo $this->_tpl_vars['menu']['icon']; ?>
"></i></span>
      <label><?php echo $this->_tpl_vars['menu']['name']; ?>
</label>
      <i class="fa fa-angle-down"></i>
    </div>

        <?php if (isset ( $this->_tpl_vars['menu']['brand'] ) || isset ( $this->_tpl_vars['menu']['category'] ) || isset ( $this->_tpl_vars['menu']['detail'] ) || isset ( $this->_tpl_vars['menu']['size'] ) || isset ( $this->_tpl_vars['menu']['color'] ) || isset ( $this->_tpl_vars['menu']['links']['add'] )): ?>
    <div class="list-sidebar">
      <a href="<?php echo $this->_tpl_vars['menu']['links']['list']; ?>
">Danh sách</a>

      <?php if (isset ( $this->_tpl_vars['menu']['category'] )): ?>
      <a href="<?php echo $this->_tpl_vars['menu']['category']; ?>
">Danh mục</a>
      <?php endif; ?>
      <?php if (isset ( $this->_tpl_vars['menu']['brand'] )): ?>
      <a href="index.php?do=categories&comp=76">Thương hiệu</a>
      <?php endif; ?>
      <!-- <?php if ($this->_tpl_vars['menu']['id'] == 3): ?>
      <a href="index.php?do=articlelist&comp=73">Gia trị cốt lõi</a>
      <a href="index.php?do=articlelist&comp=74">Báo chí đưa tin</a>
      <a href="index.php?do=articlelist&comp=77">Độc bản cho giới tinh hoa</a>
      <?php endif; ?> -->


      <?php if (isset ( $this->_tpl_vars['menu']['size'] )): ?>
      <a href="index.php?do=size">Kích thước</a>
      <?php endif; ?>

      <?php if (isset ( $this->_tpl_vars['menu']['color'] )): ?>
      <a href="index.php?do=color"> Màu sắc</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; endif; unset($_from); ?>
  <a class="nav-normal" href="index.php?do=footer">
    <span><i class="fa fa-globe"></i></span>
    <label>Thông tin chân trang</label>
  </a>
  <?php if ($_SESSION['admin_artseed_username'] == 'kaita'): ?>
  <div class="nav-item">
    <div class="nav-toggle">
      <span><i class="fa fa-globe"></i></span>
      <label>Thông tin website</label>
      <i class="fa fa-angle-down"></i>
    </div>
    <div class="list-sidebar">
      <a href="index.php?do=component"> Module</a>
      <a href="index.php?do=language">Ngôn ngữ</a>
      <a href="index.php?do=properties">Thuộc tính</a>
      <a href="index.php?do=menu">Menu trên</a>
      <a href="index.php?do=infos&comp=9">Cấu hình</a>
    </div>
  </div>
  <?php else: ?>

  <a class="nav-normal" href="index.php?do=infos&comp=9">
    <span><i class="fa fa-globe"></i></span>
    <label>Cấu hình</label>
  </a>

  <?php endif; ?>

  <a class="nav-normal" href="index.php">
    <span><i class="fa fa-globe"></i></span>
    <label>Tổng quan</label>
  </a>
</div>