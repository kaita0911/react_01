<?php /* Smarty version 2.6.30, created on 2026-02-09 11:20:49
         compiled from menu/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'menu/list.tpl', 16, false),array('modifier', 'count', 'menu/list.tpl', 29, false),)), $this); ?>
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
               <a class="add" href="index.php?do=menu&act=add">
                  <i class="fa fa-plus-circle"></i> Thêm mới
               </a>
            </div>
            <div class="acti2">
               <button class="add" type="button" id="btnDelete" data-comp="<?php echo ((is_array($_tmp=@$_REQUEST['comp'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
">
                  <i class="fa fa-trash"></i> Xóa
               </button>
            </div>
            <div class="acti2">
               <button class="add" type="button" id="saveOrderBtn" data-comp="<?php echo ((is_array($_tmp=@$_REQUEST['comp'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
">
                  <i class="fa-solid fa-up-down"></i> Sắp xếp
               </button>
            </div>
         </div>
         <div class="main-content">

            <div class="tbtitle2">
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
                           <th align="center" class="width-del">
                              <input type="checkbox" name="all" id="checkAll" />
                           </th>
                           <th align="center" class="width-order"><strong>Thứ tự</strong></th>
                           <th align="left" class="width-ttl"><strong>Tiêu đề</strong></th>
                           <th align="center" class="width-show"><strong>Show</strong></th>
                           <th align="center" class="width-action"><strong>Action</strong></th>
                        </tr>
                     </thead>

                     <tbody>
                        <?php $_from = $this->_tpl_vars['view']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['menuLoop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['menuLoop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['row']):
        $this->_foreach['menuLoop']['iteration']++;
?>
                        <tr data-id="<?php echo $this->_tpl_vars['row']['id']; ?>
">
                           <td class="brbottom" align="center">
                              <input class="c-item" type="checkbox" value="<?php echo $this->_tpl_vars['row']['id']; ?>
" name="cid[]">
                           </td>

                           <td align="center" class="brbottom">
                              <input type="text" class="numInput" value="<?php echo $this->_tpl_vars['row']['num']; ?>
">
                           </td>


                           <td align="left" class="tab-mirror">
                              <?php $_from = $this->_tpl_vars['languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['lang']):
?>
                              <?php $this->assign('detail', null); ?>
                              <?php $_from = $this->_tpl_vars['row']['details']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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


                           <td align="center" class="brbottom">
                              <!-- <button type="button"
                                 class="btn_checks btn_toggle"
                                 data-id="<?php echo $this->_tpl_vars['row']['id']; ?>
"
                                 data-active="<?php echo $this->_tpl_vars['row']['active']; ?>
"
                                 data-column="active"
                                 data-table="menu">
                                 <img src="images/<?php echo $this->_tpl_vars['row']['active']; ?>
.png" alt="Show/Hide">
                              </button> -->
                              <label class="toggle btn_toggle"
                                 data-id="<?php echo $this->_tpl_vars['row']['id']; ?>
"
                                 data-active="<?php echo $this->_tpl_vars['row']['active']; ?>
"
                                 data-column="active"
                                 data-table="menu">
                                 <input type="checkbox" <?php if ($this->_tpl_vars['row']['active'] == 1): ?>checked<?php endif; ?>>
                                 <span class="track"></span>
                              </label>
                           </td>

                           <td align="center" class="brbottom">
                              <div class="flex-btn">
                                 <a class="act-btn btnEdit"
                                    href="index.php?do=menu&act=edit&id=<?php echo $this->_tpl_vars['row']['id']; ?>
"
                                    title="Sửa">
                                    <i class="fa fa-edit"></i>
                                 </a>
                                 <button title="Xoá" type="button"
                                    class="act-btn btnDeleteRow"
                                    data-id="<?php echo $this->_tpl_vars['row']['id']; ?>
" data-comp="0">
                                    <i class="fa fa-trash"></i>
                                 </button>
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
</div>
<!-- <?php echo '
<script>
   let menuOrderConfirm = false;
   // document.addEventListener("DOMContentLoaded", function() {
   //    const saveOrderBtn = document.getElementById("saveOrderBtn");

   //    if (saveOrderBtn) {
   //       saveOrderBtn.addEventListener("click", function() {
   //          const rows = document.querySelectorAll("tbody tr");
   //          const ids = [];
   //          const nums = [];

   //          rows.forEach(row => {
   //             const id = row.dataset.id;
   //             const numInput = row.querySelector(".numInput");
   //             if (id && numInput) {
   //                ids.push(id);
   //                nums.push(numInput.value.trim() || 0);
   //             }
   //          });

   //          if (ids.length === 0) {
   //             alert("Không có dữ liệu để sắp xếp!");
   //             return;
   //          }

   //          // Gửi AJAX bằng fetch
   //          fetch("index.php?do=menu&act=updateOrder", {
   //                method: "POST",
   //                headers: {
   //                   "Content-Type": "application/x-www-form-urlencoded"
   //                },
   //                body: `id[]=${ids.join("&id[]=")}&num[]=${nums.join("&num[]=")}`
   //             })
   //             .then(response => response.json())
   //             .then(data => {
   //                if (data.success) {
   //                   alert("✅ Đã lưu thứ tự thành công!");
   //                   location.reload(); // refresh để cập nhật lại
   //                } else {
   //                   alert("❌ " + (data.message || "Cập nhật thất bại!"));
   //                }
   //             })
   //             .catch(() => {
   //                alert("⚠️ Lỗi kết nối server! Vui lòng thử lại sau.");
   //             });
   //       });
   //    }
   // });
</script>

'; ?>
 -->