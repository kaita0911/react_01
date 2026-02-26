<?php /* Smarty version 2.6.30, created on 2026-02-05 10:56:22
         compiled from login.tpl */ ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Administrator Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <?php echo '
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        .login-fr {
            position: relative;
        }

        .login-fr i.fa-solid {
            position: absolute;
            right: 10px;
            top: 36%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #ccc;
            cursor: pointer;
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
            font-weight: 500;
            color: #444;
            font-size: 14px;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 15px;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
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

        .error {
            text-align: center;
            color: red;
        }
    </style>
    

    '; ?>

</head>

<body>
    <form name="login" method="post" action="index.php?do=login&act=sm">
        <div class="login-box">
            <h2>Admin Login</h2>
            <?php if (isset ( $this->_tpl_vars['msg'] )): ?>
            <div class="error"><?php echo $this->_tpl_vars['msg']; ?>
</div>
            <?php endif; ?>
            <label for="username">Username</label>
            <div class="login-fr">
                <input class="text" name="username" id="username" maxlength="50" type="text" required>
                <i class="fa-solid fa-user"></i>
            </div>
            
            <label for="password">Password</label>
            <div class="login-fr">
                <input class="text" name="password" id="password" maxlength="50" type="password" required>
                <i class="fa-solid fa-eye toggle-password" data-target="password"></i>
            </div>
            <button type="submit" class="login-btn">Đăng nhập</button>
            <!-- Link quên mật khẩu -->
            <div style="text-align:center; margin-top:10px;">
                <a href="index.php?do=login&act=forgot" style="color:#007bff; text-decoration:none; font-size:14px;">Quên mật khẩu?</a>
            </div>
            <div class="footer-note">© Administrator</div>
        </div>
    </form>
</body>   
 <?php echo '
<script>
        document.querySelectorAll(\'.toggle-password\').forEach(function(icon) {
            icon.addEventListener(\'click\', function () {
                const input = document.getElementById(this.dataset.target);

                if (input.type === \'password\') {
                    input.type = \'text\';
                    this.classList.remove(\'fa-eye\');
                    this.classList.add(\'fa-eye-slash\');
                } else {
                    input.type = \'password\';
                    this.classList.remove(\'fa-eye-slash\');
                    this.classList.add(\'fa-eye\');
                }
            });
        });
        </script>   
         '; ?>

</html>