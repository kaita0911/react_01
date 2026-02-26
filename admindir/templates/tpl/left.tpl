<a href="/" target="_blank" class="logo">
  <img src="/{$logoadmin.img_thumb_vn}" alt="Logo" />
</a>

<div class="menusidebar" id="sidebar">
  {foreach from=$ListMenuLeft item=menu}
  <div class="nav-item">
    <div class="nav-toggle" href="{$menu.links.list}">
      <span><i class="fa {$menu.icon}"></i></span>
      <label>{$menu.name}</label>
      <i class="fa fa-angle-down"></i>
    </div>

    {* Nếu menu có link con thì hiển thị *}
    {if isset($menu.brand) || isset($menu.category) || isset($menu.detail) || isset($menu.size) || isset($menu.color) || isset($menu.links.add)}
    <div class="list-sidebar">
      <a href="{$menu.links.list}">Danh sách</a>

      {if isset($menu.category)}
      <a href="{$menu.category}">Danh mục</a>
      {/if}
      {if isset($menu.brand)}
      <a href="index.php?do=categories&comp=76">Thương hiệu</a>
      {/if}
      <!-- {if $menu.id == 3}
      <a href="index.php?do=articlelist&comp=73">Gia trị cốt lõi</a>
      <a href="index.php?do=articlelist&comp=74">Báo chí đưa tin</a>
      <a href="index.php?do=articlelist&comp=77">Độc bản cho giới tinh hoa</a>
      {/if} -->


      {if isset($menu.size)}
      <a href="index.php?do=size">Kích thước</a>
      {/if}

      {if isset($menu.color)}
      <a href="index.php?do=color"> Màu sắc</a>
      {/if}
    </div>
    {/if}
  </div>
  {/foreach}
  <a class="nav-normal" href="index.php?do=footer">
    <span><i class="fa fa-globe"></i></span>
    <label>Thông tin chân trang</label>
  </a>
  {if $smarty.session.admin_artseed_username == 'kaita'}
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
  {else}

  <a class="nav-normal" href="index.php?do=infos&comp=9">
    <span><i class="fa fa-globe"></i></span>
    <label>Cấu hình</label>
  </a>

  {/if}

  <a class="nav-normal" href="index.php">
    <span><i class="fa fa-globe"></i></span>
    <label>Tổng quan</label>
  </a>
</div>