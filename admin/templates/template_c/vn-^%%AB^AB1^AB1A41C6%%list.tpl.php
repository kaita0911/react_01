<?php /* Smarty version 2.6.30, created on 2025-11-21 09:46:27
         compiled from size/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'size/list.tpl', 15, false),)), $this); ?>
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
               <a class="add" href="index.php?do=size&act=add">
                  <i class="fa fa-plus-circle"></i> Thêm mới
               </a>
            </div>
            <div class="acti2">
               <button class="add" type="button" id="saveOrderBtn" data-comp="<?php echo ((is_array($_tmp=@$_REQUEST['comp'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
">
                  <i class="fa-solid fa-up-down"></i> Sắp xếp
               </button>
            </div>
         </div>
         <div class="main-content">
            <form class="form-all" method="post" action="">
               <table class="br1">
                  <thead>
                     <tr>
                        <th align="center" class="width-del">
                           <input type="checkbox" name="all" id="checkAll" />
                        </th>
                        <th align="center" class="width-order">Thứ tự</th>

                        <th align="left" class="width-ttl">
                           Tiêu đề
                        </th>
                        <th align="center" class="width-show">Show</th>
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
                        <td align="center">
                           <input type="checkbox" class="c-item" name="cid[]" value="<?php echo $this->_tpl_vars['item']['id']; ?>
">
                        </td>
                        <td align="center">
                           <input type="text" class="numInput" value="<?php echo $this->_tpl_vars['item']['num']; ?>
" />
                        </td>

                        <td align="left">
                           <?php echo $this->_tpl_vars['item']['name']; ?>

                        </td>
                        <td align="center">
                           <button type="button"
                              class="btn_checks btn_toggle"
                              data-id="<?php echo $this->_tpl_vars['item']['id']; ?>
"
                              data-active="<?php echo $this->_tpl_vars['item']['active']; ?>
"
                              data-column="active"
                              data-table="size">
                              <img src="images/<?php echo $this->_tpl_vars['item']['active']; ?>
.png" alt="Hiển thị / Ẩn" />
                           </button>
                        </td>
                        <td align="center">
                           <div class="flex-btn">
                              <a title="Chỉnh sửa" class="act-btn btnEdit" href="index.php?do=size&act=edit&id=<?php echo $this->_tpl_vars['item']['id']; ?>
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
      </div>
   </div>
</div>
<?php echo '
<script>
   document.addEventListener("DOMContentLoaded", function() {
      const saveOrderBtn = document.getElementById("saveOrderBtn");

      if (saveOrderBtn) {
         saveOrderBtn.addEventListener("click", function() {
            const rows = document.querySelectorAll("tbody tr");
            const ids = [];
            const nums = [];

            rows.forEach(row => {
               const id = row.dataset.id;
               const numInput = row.querySelector(".numInput");
               if (id && numInput) {
                  ids.push(id);
                  nums.push(numInput.value.trim() || 0);
               }
            });

            if (ids.length === 0) {
               alert("Không có dữ liệu để sắp xếp!");
               return;
            }

            // Gửi AJAX bằng fetch
            fetch("index.php?do=size&act=updateOrder", {
                  method: "POST",
                  headers: {
                     "Content-Type": "application/x-www-form-urlencoded"
                  },
                  body: `id[]=${ids.join("&id[]=")}&num[]=${nums.join("&num[]=")}`
               })
               .then(response => response.json())
               .then(data => {
                  if (data.success) {
                     alert("✅ Đã lưu thứ tự thành công!");
                     location.reload(); // refresh để cập nhật lại
                  } else {
                     alert("❌ " + (data.message || "Cập nhật thất bại!"));
                  }
               })
               .catch(() => {
                  alert("⚠️ Lỗi kết nối server! Vui lòng thử lại sau.");
               });
         });
      }
   });
</script>

'; ?>