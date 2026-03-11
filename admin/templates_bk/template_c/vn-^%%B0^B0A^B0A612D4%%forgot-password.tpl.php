<?php /* Smarty version 2.6.30, created on 2026-02-05 10:51:57
         compiled from forgot-password.tpl */ ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Quên mật khẩu</title>
    <?php echo '
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            font-family: "Segoe UI", Arial, sans-serif;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 360px;
            padding: 30px 25px;
            text-align: left;
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 22px;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #444;
            margin-bottom: 5px;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 15px;
            transition: border-color 0.2s;
        }

        input[type="email"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .login-btn {
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .login-btn:hover {
            background: #0056b3;
        }

        .footer-note {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-top: 15px;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: green;
        }

        .error {
            text-align: center;
            margin-bottom: 15px;
            color: red;
        }
    </style>
     '; ?>

</head>

<body>
    <form method="post" action="index.php?do=login&act=forgotsm">
        <div class="login-box">
            <h2>Quên mật khẩu</h2>

            <!-- Hiển thị thông báo -->
            <?php if (isset ( $this->_tpl_vars['msg'] )): ?>
            <div class="message"><?php echo $this->_tpl_vars['msg']; ?>
</div>
            <?php endif; ?>

            <?php if (isset ( $this->_tpl_vars['err'] )): ?>
            <div class="error"><?php echo $this->_tpl_vars['err']; ?>
</div>
            <?php endif; ?>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" maxlength="100" required>

            <button type="submit" class="login-btn">Gửi mật khẩu mới</button>

            <div class="footer-note">
                <a href="index.php?do=login" style="color:#007bff; text-decoration:none;">Quay lại đăng nhập</a>
            </div>
        </div>
    </form>
</body>

</html>