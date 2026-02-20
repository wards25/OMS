<?php
session_start();
include_once 'dbconnect.php';

if (isset($_POST['btn-login'])) {
    $uname = mysqli_real_escape_string($conn, $_POST['username']);
    $upass = mysqli_real_escape_string($conn, $_POST['pass']);

    $uname = trim($uname);
    $upass = trim($upass);

    $detail_query = mysqli_query($conn, "SELECT * FROM tbl_users WHERE username = '$uname'");
    $row = mysqli_fetch_array($detail_query);

    $count = mysqli_num_rows($detail_query);

    if ($count == 1 && $row['password'] == md5($upass)) {
        if ($row['is_active'] == '1') {
            if (!empty($_POST['remember'])) {
                setcookie('username', $_POST['username'], time() + 3600);
                setcookie('pass', $_POST['pass'], time() + 3600);
            } else {
                setcookie('username', "", time() - 3600);
                setcookie('pass', "", time() - 3600);
            }

            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['fname'] . ' ' . $row['lname'];
            $_SESSION['tag'] = $row['tag'];
            $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['hub'] = $row['hub'];
            $user = $_SESSION['name'];

            $role_query = mysqli_query($conn, "SELECT * FROM tbl_roles WHERE id = " . $_SESSION['role_id']);
            $fetch_role = mysqli_fetch_array($role_query);
            $_SESSION['role'] = $fetch_role['role_name'];

            $id = $row['id'];
            $session_query = mysqli_query($conn, "SELECT user_id FROM tbl_sessions WHERE user_id = '$id'");
            $check_session = mysqli_num_rows($session_query);

            if ($check_session > 0) {
                // If session exists, update login timestamp
                $action = $user." HAS LOGGED OUT";
                $module = "IN AND OUT";
                mysqli_query($conn,"INSERT INTO tbl_history VALUES (NULL,'$user','$action','$module','$client_ip','$mac','$device','$model',NOW())");
                mysqli_query($conn, "UPDATE tbl_sessions SET ip_address='$client_ip', mac_address='$mac', device='$device', model='$model', login_time = NOW() WHERE user_id = '$id'");
            } else {
                mysqli_query($conn, "INSERT INTO tbl_sessions VALUES (NULL, '$id', '$client_ip', '$mac', '$device', '$model', NOW())");
            }

            // Insert login history
            $name = $row['fname'] . ' ' . $row['lname'];
            $action = "$name HAS LOGGED IN";
            $module = "IN AND OUT";
            mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");
            
            header("Location: load.php");
        } else {
            header("Location: login.php?status=activate");
        }
    } else {
        header("Location: login.php?status=err");
    }
}
?> 