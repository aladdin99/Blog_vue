<?php
header("Content-type: text/html; charset=utf-8");
header('Access-Control-Allow-Origin:*');  // 解决跨域问题

//接受前端数据
$authorId = $_GET['authorId'];

// 用于返回数据
$return = array();
$data = array();

// 包含数据库配置信息
include_once('../config.php');

// mysqli 面向对象 编程
// 1、创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 2、检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 3、查询数据
$sql_search = "SELECT * FROM `points`  WHERE `authorId` = '$authorId'";

$total = 0;
//转换库中数据
$result = $conn->query($sql_search);
if($result->num_rows > 0){
    // 输出数据 fetch_assoc，遍历表中的每一行数据
    while($row = $result->fetch_assoc()) {
        $total++;
    }
} else {
    $total = 0;
}
//$get = json_encode($data[0]['region'],JSON_UNESCAPED_UNICODE);
//print_r(); //JSON_UNESCAPED_UNICODE防止中文乱码

// 4、检查是否插入成功
if($conn->query($sql_search)) {
    print_r($total); //JSON_UNESCAPED_UNICODE防止中文乱码
}else {
    echo "Error: " . $sql_search . "<br>" . $conn->error;
}
// 5、关闭数据库
$conn->close();
?>