<?php /* Smarty version 2.6.30, created on 2026-02-09 13:59:11
         compiled from footer.tpl */ ?>
<!-- <div id="orderMsg"></div> -->
<div id="confirmPopup" class="popup-overlay">
  <div class="popup-box">
    <h3>Xác nhận !</h3>
    <p>Bạn có chắc chắn muốn thực hiện không?</p>
     <!-- 👇 THÊM PHẦN NÀY -->
     <div class="popup-extra" style="display:none">
       <!-- input giá -->
       <input
        type="text"
        id="popupPriceInput"
        placeholder="Nhập giá mới"
        style="width:100%; padding:8px; margin-top:10px; display:none">

      <!-- input tên -->
      <input
        type="text"
        id="popupNameInput"
        placeholder="Nhập tên mới"
        style="width:100%; padding:8px; margin-top:10px; display:none">
    </div>
    <div class="popup-actions">
      <button id="popupOk" class="btn-ok">Xác nhận</button>
      <button id="popupCancel" class="btn-cancel">Huỷ</button>
    </div>
  </div>
</div>

<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/Sortable.min.js"></script>
<script src="js/script.js"></script>
<script src="ckeditor4/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>