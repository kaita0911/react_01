<?php
include_once('../../includes/config.php');
// KHÔNG set header json vì file này trả HTML

$article_id = intval(isset($_GET['article_id']) ? $_GET['article_id'] : 0);

if ($article_id <= 0) {
    exit;
}

try {
    $sql = "
        SELECT id, password_hash, created_at
        FROM {$GLOBALS['db_sp']}.articlelist_password
        WHERE articlelist_id = ?
        ORDER BY id DESC
    ";

    $rows = $GLOBALS['sp']->getAll($sql, [$article_id]);

    if (!$rows || count($rows) == 0) {
        echo '<li style="text-align:center;color:#999;">Chưa có mật khẩu</li>';
        exit;
    }

    foreach ($rows as $row) {
?>
        <li style="display:flex;justify-content:space-between;align-items:center;">
            <span>
                🔑 <strong><?php echo ($row['password_hash']); ?></strong>
                <small style="color:#999; margin-left:6px;">
                    (<?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>)
                </small>
            </span>

            <div style="display:flex; gap:6px;">
                <button
                    onclick="copyRowPassword(this, '<?php echo $row['password_hash'], ENT_QUOTES; ?>')">
                    Copy
                </button>


                <button onclick="deletePassword(<?php echo (int)$row['id']; ?>)">
                    Xóa
                </button>
            </div>
        </li>
<?php
    }
} catch (Exception $e) {
    echo '<li style="color:red;">Lỗi tải mật khẩu</li>';
}
