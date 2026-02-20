<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['update'])){

  $oldpass = md5($_POST['oldpass']);
  $newpass1 = md5($_POST['newpass1']);
  $newpass2 = md5($_POST['newpass2']);
  $id = $_SESSION['id'];

  $check_query = mysqli_query($conn,"SELECT * FROM tbl_users WHERE id = '$id'");
  $fetch_check = mysqli_fetch_array($check_query);
  $pass = $fetch_check['password'];

  if($oldpass == $pass){

    if($newpass1 == $newpass2){

      mysqli_query($conn,"UPDATE tbl_users SET password='$newpass1' WHERE id = '$id'");

      // insert login history
      $action = $user." UPDATED HIS/HER PASSWORD";
      $module = "CHANGE PASSWORD";
      mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

      $qstring = '?status=update';

    }else{
      $qstring = '?status=err';
    }

  }else{
    $qstring = '?status=pass';
  }
  
}else{

}

header("Location: changepass.php".$qstring);
