<?php
include_once("dbconnect.php");
session_start();

if (!empty($_POST['message']) && isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $user = $_SESSION['name'];
    $message_date = date('Y-m-d');
    $message_time = date('H:i:s');
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "INSERT INTO tbl_chat_messages (id,user_id,user_name,message,message_date,message_time) VALUES (NULL,'$user_id','$user','$message','$message_date','$message_time')");
}
?>