<?php /* Smarty version 2.6.30, created on 2026-02-05 11:04:50
         compiled from change-password.tpl */ ?>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<div class="form-container">
    <h2>Đổi mật khẩu</h2>

    <?php if (isset ( $this->_tpl_vars['err'] )): ?>
    <div class="alert alert-error"><?php echo $this->_tpl_vars['err']; ?>
</div>
    <?php endif; ?>

    <?php if (isset ( $this->_tpl_vars['msg'] )): ?>
    <div class="alert alert-success"><?php echo $this->_tpl_vars['msg']; ?>
</div>
    <?php endif; ?>

    <form method="post" action="" id="changePassForm">
        <div class="form-group">
            <label>Mật khẩu cũ</label>
            <div class="login-fr">
                <input type="password" name="old_password" required > 
                <i class="fa-solid fa-eye toggle-password" ></i>
            </div>
        </div>

        <div class="form-group">
            <label>Mật khẩu mới</label>
            <div class="login-fr">
                <input type="password" name="new_password" required>
                <i class="fa-solid fa-eye toggle-password" ></i>
            </div>
        </div>

        <div class="form-group">
            <label>Xác nhận mật khẩu mới</label>
            <div class="login-fr">
                <input type="password" name="confirm_password" required>
                <i class="fa-solid fa-eye toggle-password" ></i>
            </div>
        </div>

        <button type="submit" class="btn-primary">Đổi mật khẩu</button>
    </form>
    <div class="login-link">
        <a href="index.php?do=login" title="Quay lại đăng nhập">← Quay lại đăng nhập</a>
    </div>
</div>
<?php echo '
<style>
    /* Container */
    .form-container {
        max-width: 400px;
        margin: 60px auto;
        padding: 40px 30px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        font-family: Arial, sans-serif;
    }

    /* Tiêu đề */
    .form-container h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    /* Form group */
    .form-group {
        margin-bottom: 20px;
    }

    /* Label */
    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #444;
        font-weight: 500;
        font-size: 14px;
    }

    /* Input */
    .form-group input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-group input:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Button */
    .btn-primary {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        border: none;
        color: #fff;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Thông báo lỗi/thành công */
    .alert {
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    /* Link quay lại đăng nhập */
    .login-link {
        margin-top: 15px;
        text-align: center;
    }

    .login-link a {
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }

    .login-link a:hover {
        color: #0056b3;
    }
    .login-fr {
            position: relative;
        }

    .login-fr i.fa-solid {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: #ccc;
        cursor: pointer;
    }
</style>

<script>
    document.getElementById(\'changePassForm\').addEventListener(\'submit\', function(e) {
        const newPass = this.new_password.value.trim();
        const confirm = this.confirm_password.value.trim();

        if (newPass === \'\' || confirm === \'\') {
            alert(\'Vui lòng nhập đầy đủ thông tin.\');
            e.preventDefault();
            return;
        }

        if (newPass !== confirm) {
            alert(\'Mật khẩu mới và xác nhận không khớp.\');
            e.preventDefault();
        }
    });
</script>
<script>
document.querySelectorAll(\'.toggle-password\').forEach(function(icon) {
    icon.addEventListener(\'click\', function () {

        // tìm input trong cùng khối
        const wrapper = this.closest(\'.login-fr\');
        const input = wrapper.querySelector(\'input\');

        if (!input) return;

        const isPassword = input.type === \'password\';
        input.type = isPassword ? \'text\' : \'password\';

        this.classList.toggle(\'fa-eye\', !isPassword);
        this.classList.toggle(\'fa-eye-slash\', isPassword);
    });
});
</script>

'; ?>