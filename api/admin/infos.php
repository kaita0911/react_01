<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once("../../includes/config.php");

$act = isset($_GET['act']) ? $_GET['act'] : '';

switch ($act) {

    // ================= LIST =================
    case "list":

        $sql = "SELECT * FROM infos ORDER BY id ASC";
        $rs  = $GLOBALS["sp"]->getAll($sql);

        echo json_encode(array(
            "status" => true,
            "data"   => $rs
        ));

        break;

    // ================= DETAIL =================
    case "detail":

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $sql = "SELECT * FROM infos WHERE id = ?";
        $rs  = $GLOBALS["sp"]->getRow($sql, array($id));

        echo json_encode(array(
            "status" => true,
            "data"   => $rs
        ));

        break;
    // ================= UPDATE =================
    case "update":
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
        if ($id <= 0) {
            echo json_encode(["status" => false, "message" => "ID không hợp lệ"]);
            break;
        }
    
        $fields = [];
        $values = [];
    
        // ===== TEXT =====
        foreach ($_POST as $key => $value) {
            if ($key == "id") continue;
    
            $fields[] = "$key = ?";
            $values[] = $value;
        }
    
        // ===== FILE =====
        foreach ($_FILES as $fieldName => $file) {

            if ($file['error'] == 0) {
        
                $uploadDir = __DIR__ . "/../../hinh-anh/trung-gian/";
        
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
        
                // ===== 1. LẤY ẢNH CŨ =====
                $sqlOld = "SELECT $fieldName FROM infos WHERE id = ?";
                $oldImage = $GLOBALS['sp']->GetOne($sqlOld, array($id));
        
                if (!empty($oldImage)) {
                    $oldPath = __DIR__ . "/../../" . $oldImage;
        
                    if (file_exists($oldPath)) {
                        unlink($oldPath); // 👉 XOÁ ẢNH CŨ
                    }
                }
        
                // ===== 2. UPLOAD ẢNH MỚI =====
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newName = time() . "_" . rand(1000,9999) . "." . $ext;
        
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        
                    $relativePath = "hinh-anh/trung-gian/" . $newName;
        
                    $fields[] = "$fieldName = ?";
                    $values[] = $relativePath;
        
                } else {
                    echo json_encode(array(
                        "status" => false,
                        "message" => "Move file thất bại"
                    ));
                    die;
                }
            }
        }
    
        if (empty($fields)) {
            echo json_encode(["status" => false, "message" => "Không có dữ liệu"]);
            break;
        }
    
        $values[] = $id;
    
        $sql = "UPDATE infos SET " . implode(", ", $fields) . " WHERE id = ?";
    
        $GLOBALS['sp']->execute($sql, $values);
    
        echo json_encode(["status" => true]);
    
        break;


      

    // ===== CASE CÓ UPLOAD ẢNH =====
    // if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

    //     $upload_dir = "../../hinh-anh/trung-gian/";

    //     if (!is_dir($upload_dir)) {
    //         mkdir($upload_dir, 0777, true);
    //     }

    //     $file_name = time() . "_" . basename($_FILES["image"]["name"]);
    //     $target_file = $upload_dir . $file_name;

    //     if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

    //         $image_path = "hinh-anh/trung-gian/" . $file_name;

    //         $sql = "UPDATE infos SET img_thumb_vn = ? WHERE id = ?";
    //         $GLOBALS["sp"]->execute($sql, [$image_path, $id]);

    //         echo json_encode([
    //             "status" => true,
    //             "message" => "Cập nhật ảnh thành công"
    //         ]);
    //     } else {
    //         echo json_encode([
    //             "status" => false,
    //             "message" => "Upload thất bại"
    //         ]);
    //     }

    // } else {

    //     // ===== CASE TEXT =====
    //     $sql = "UPDATE infos SET value = ? WHERE id = ?";
    //     $GLOBALS["sp"]->execute($sql, [$value, $id]);

    //     echo json_encode([
    //         "status" => true,
    //         "message" => "Cập nhật thành công"
    //     ]);
    // }

    // break;
    default:
        echo json_encode(array(
            "status" => false,
            "message" => "Invalid action"
        ));
}