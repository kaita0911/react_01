<?php


////////////////////////////////////////
// FUNCTIONS
////////////////////////////////////////
function logout()
{
	session_unset();
	session_destroy();
	header("Location: index.php");
	exit;
}

function login()
{
	$username = trim(isset($_POST['username']) ? $_POST['username'] : '');
	$password = trim(isset($_POST['password']) ? $_POST['password'] : '');

	if ($username === '' || $password === '') {
		return ['msg' => 'Vui lòng nhập đầy đủ thông tin.', 'page' => 'index.php?do=login&error=1'];
	}

	$sql = "SELECT * FROM {$GLOBALS['db_sp']}.admin WHERE username = ?";
	$result = $GLOBALS['sp']->getRow($sql, [$username]);

	if (!$result) {
		return ['msg' => 'Tên đăng nhập không đúng.', 'page' => 'index.php?do=login&error=1'];
	}

	$dbPass = trim($result['password']);
	$hashedInput = md5($password);
	$doubleMd5 = md5($hashedInput);

	if ($dbPass !== $password && $dbPass !== $hashedInput && $dbPass !== $doubleMd5) {
		return ['msg' => 'Mật khẩu không đúng !', 'page' => 'index.php?do=login&error=1'];
	}

	$_SESSION['store_anthinh_login'] = true;
	$_SESSION['admin_artseed_username'] = $username;
	$_SESSION['group_artseed_user'] = isset($result['group']) ? $result['group'] : '';
	$_SESSION['admin_artseed_id'] = isset($result['id']) ? $result['id'] : 0;

	if ($dbPass !== $hashedInput) {
		$GLOBALS['sp']->execute(
			"UPDATE {$GLOBALS['db_sp']}.admin SET password=? WHERE id=?",
			[$hashedInput, $result['id']]
		);
	}

	return ['msg' => 'success', 'page' => 'index.php'];
}

function forgotPass()
{
	global $smarty;

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$email = trim(isset($_POST['email']) ? $_POST['email'] : '');
		if ($email === '') {
			$smarty->assign('err', 'Vui lòng nhập email.');
		} else {
			$user = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.admin WHERE email=?", [$email]);
			if ($user) {
				$newPass = substr(md5(time() . rand()), 0, 8);
				$hashed = md5($newPass);

				$GLOBALS['sp']->execute(
					"UPDATE {$GLOBALS['db_sp']}.admin SET password=? WHERE email=?",
					[$hashed, $email]
				);

				$smarty->assign('msg', "Mật khẩu mới của bạn là: <strong>{$newPass}</strong>");
			} else {
				$smarty->assign('err', 'Email không tồn tại.');
			}
		}
	}

	$smarty->display('forgot-password.tpl');
}

/**
 * Thêm chức năng đổi mật khẩu cho user/admin đã login
 */
function changePass()
{
	global $smarty;

	if (!isset($_SESSION['admin_artseed_id'])) {
		header("Location: index.php");
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$oldPass = trim(isset($_POST['old_password']) ? $_POST['old_password'] : '');
		$newPass = trim(isset($_POST['new_password']) ? $_POST['new_password'] : '');
		$confirm = trim(isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '');

		if ($oldPass === '' || $newPass === '' || $confirm === '') {
			$smarty->assign('err', 'Vui lòng nhập đầy đủ thông tin.');
		} else if ($newPass !== $confirm) {
			$smarty->assign('err', 'Mật khẩu mới và xác nhận không khớp.');
		} else {
			$adminId = (int)$_SESSION['admin_artseed_id'];
			$user = $GLOBALS['sp']->getRow("SELECT password FROM {$GLOBALS['db_sp']}.admin WHERE id=?", [$adminId]);
			if (!$user) {
				$smarty->assign('err', 'Tài khoản không tồn tại.');
			} else {
				$dbPass = trim($user['password']);
				$hashedOld = md5($oldPass);
				$doubleMd5 = md5($hashedOld);

				if ($dbPass !== $oldPass && $dbPass !== $hashedOld && $dbPass !== $doubleMd5) {
					$smarty->assign('err', 'Mật khẩu cũ không đúng.');
				} else {
					$hashedNew = md5($newPass);
					$GLOBALS['sp']->execute(
						"UPDATE {$GLOBALS['db_sp']}.admin SET password=? WHERE id=?",
						[$hashedNew, $adminId]
					);
					$smarty->assign('msg', 'Đổi mật khẩu thành công!');
				}
			}
		}
	}

	$smarty->display('change-password.tpl');
}

////////////////////////////////////////
// MAIN
////////////////////////////////////////
$act = isset($_GET['act']) ? $_GET['act'] : '';

switch ($act) {
	case 'log_out':
		logout();
		break;

	case 'sm':
		$res = login();
		if ($res['msg'] === 'success') {
			header("Location: {$res['page']}");
			exit;
		} else {
			$smarty->assign('msg', $res['msg']);
		}
		break;

	case 'forgot':
	case 'forgotsm':
		forgotPass();
		exit;
		break;

	case 'changepass':
		changePass();
		exit;
		break;

	default:
		$smarty->display('login.tpl');
		break;
}
