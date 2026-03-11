<!DOCTYPE html>
<html lang="vi" xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta charset="utf-8" />
  <meta name="robots" content="NOINDEX, NOFOLLOW" />

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrator</title>

  <!-- Styles -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/font-awesome.css">
  <script src="js/chart.js"></script>
  <!-- <link rel="stylesheet" href="css/bootstrap.css"> -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

  <div class="popupqc"><img src="images/giahan.jpg" alt="Gia hạn" /></div>
  <div class="header">
    
  <div class="box-time">
      <p>Ngày hết hạn 05/02/2027</p>
      <p id="demo"></p>

      {literal}
      <script>
        // FIX TIMEZONE
        var endTime = new Date("2027-02-05T11:00:00+07:00").getTime();

        function updateCountdown() {
          var now = Date.now();
          var distance = Math.max(0, Math.floor((endTime - now) / 1000));

          var days = Math.floor(distance / 86400);
          var hours = Math.floor((distance % 86400) / 3600);
          var minutes = Math.floor((distance % 3600) / 60);
          var seconds = distance % 60;

          document.getElementById("demo").innerHTML =
            days + " Ngày " +
            hours + " Giờ " +
            minutes + " Phút " +
            seconds + " Giây";

          if (distance <= 0) {
            clearInterval(timer);
            document.getElementById("demo").innerHTML = "EXPIRED";
          }
        }

        updateCountdown();
        var timer = setInterval(updateCountdown, 1000);
      </script>
      {/literal}
    </div>
    <div class="box-cart">
     
    </div>
    <div class="box-register">
      {if $showcart.open eq 1}
      <a class="c-cart fnc-bell" href="index.php?do=orders">
        <i class="fa fa-shopping-cart"></i>
        Danh sách đơn hàng
        {if $new_order_count > 0}
        <span class="icon-new"></span>
        {/if}
      </a>
      {/if}
      <a class="box-register__btn" href="index.php?do=contact&comp=23"><i class="fa-solid fa-address-book"></i> Form liên hệ 
      {if $new_contact_count > 0}
        <span class="icon-new"></span>
        {/if}</a>
      {if $showform.open eq 1}
        <a class="box-register__btn" href="index.php?do=register_info"><i class="fa-solid fa-address-book"></i> Form đăng ký tư vấn
        {if $new_register_count > 0}
        <span class="icon-new"></span>
        {/if}</a>
      {/if}
    </div>
    <div class="box-time-ad">
      <div class="welcome">
        <span>Hi, <strong>{$smarty.session.admin_artseed_username}</strong> - </span>
        <div>
        {assign var=day value=$now|date_format:"%w"}

        {if $day == 0}Chủ nhật
        {elseif $day == 1}Thứ Hai
        {elseif $day == 2}Thứ Ba
        {elseif $day == 3}Thứ Tư
        {elseif $day == 4}Thứ Năm
        {elseif $day == 5}Thứ Sáu
        {elseif $day == 6}Thứ Bảy
        {/if}
        , {$now|date_format:"%d-%m-%Y"}

        </div>
      </div>
      <div class="date linkorg">
        <a target="_blank" href="/">Xem trang chủ</a>
        <a href="index.php?do=login&act=log_out">Thoát</a>
        <a href="index.php?do=login&act=changepass">Đổi mật khẩu</a>
      </div>
    </div>
    
  </div>
</body>

</html>
{literal}
<script>
  const langSelect = document.getElementById('language-select');
  if (langSelect) {
    langSelect.addEventListener('change', function() {
      const lang = this.value;
      console.log(lang);
      fetch('./set_language.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'language=' + encodeURIComponent(lang)
      }).then(response => {
        if (response.ok) {
          //alert(sss);
          // Có thể reload trang hiện tại để áp dụng ngôn ngữ
          location.reload();
        }
      });
    });
  }
</script>
{/literal}