<?php
session_start();
include_once 'dbconnect.php';
// Function to get device type and model
function getDeviceInfo() {
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $device = "Unknown";
    $model = "Unknown";

    if (strpos($user_agent, 'windows') !== false) {
        $device = "Windows";
    } elseif (strpos($user_agent, 'macintosh') !== false || strpos($user_agent, 'mac os') !== false) {
        $device = "MacOS";
    } elseif (strpos($user_agent, 'iphone') !== false) {
        $device = "iOS";
        preg_match('/iphone\s([\d,\s]+)/i', $user_agent, $matches);
        if (!empty($matches[1])) {
            $model = "iPhone " . trim($matches[1]);
        }
    } elseif (strpos($user_agent, 'ipad') !== false) {
        $device = "iPadOS";
        preg_match('/ipad\s([\d,\s]+)/i', $user_agent, $matches);
        if (!empty($matches[1])) {
            $model = "iPad " . trim($matches[1]);
        }
    } elseif (strpos($user_agent, 'android') !== false) {
        $device = "Android";
        preg_match('/android\s([\d\.]+);?\s*(\w+\s[\w-]+)/i', $user_agent, $matches);
        if (!empty($matches[2])) {
            $model = trim($matches[2]);
        }
    } elseif (strpos($user_agent, 'linux') !== false) {
        $device = "Linux";
    }

    return [$device, $model];
}

// Function to get MAC address (Windows)
function getMacAddress() {
    $mac = exec("getmac"); // Get MAC address (Windows)

    // Extract only the first MAC address from output
    $mac = strtok($mac, " "); 

    // Convert hyphens (-) to colons (:)
    $mac = str_replace("-", ":", $mac);

    if (!$mac) {
        $mac = "Unknown";
    }

    return $mac;
}

// Capture user details
$client_ip = $_SERVER['REMOTE_ADDR'];

// Convert IPv6 localhost to IPv4
if ($client_ip == "::1") {
    $client_ip = "127.0.0.1";
}

$mac = getMacAddress(); // Get MAC address in standard format (00:1A:2B:3C:4D:5E)
list($device, $model) = getDeviceInfo(); // Get device type & model
    
    
// Capture user details
$client_ip = $_SERVER['REMOTE_ADDR'];
if ($client_ip == "::1") {
    $client_ip = "127.0.0.1";
}

$mac = getMacAddress(); 
list($device, $model) = getDeviceInfo(); 
    
if(isset($_POST['btn-login']))
{
  $uname = mysqli_real_escape_string($conn, $_POST['username']);
  $upass = mysqli_real_escape_string($conn, $_POST['pass']);

  $uname = trim($uname);
  $upass = trim($upass);

  $detail_query = mysqli_query($conn,"SELECT * FROM tbl_users WHERE username = '$uname'");
  $row = mysqli_fetch_array($detail_query);

  $count = mysqli_num_rows($detail_query);

  if($count == 1 && $row['password'] == md5($upass))
  {
    if($row['is_active'] == '1'){
        
        // Check if user is already logged in from another device
        $check_session = mysqli_query($conn, "SELECT * FROM tbl_sessions WHERE user_id = '{$row['id']}'");
        if(mysqli_num_rows($check_session) > 0) {
            echo "<script>
            if(confirm('You are already logged in on another device. Do you want to log out from other device?')) {
                window.location.href = 'force_logout.php?user_id={$row['id']}';
            } else {
                window.location.href = 'login.php?status=cancel';
            }
            </script>";
            exit();
        }
        
        $_SESSION['id'] = $row['id'];
        $_SESSION['name'] = $row['fname'].' '.$row['lname'];
        $_SESSION['tag'] = $row['tag'];
        $_SESSION['role_id'] = $row['role_id'];

        $role_query = mysqli_query($conn,"SELECT * FROM tbl_roles WHERE id = ".$_SESSION['role_id']);
        $fetch_role = mysqli_fetch_array($role_query);
        $_SESSION['role'] = $fetch_role['role_name'];

        // Insert login session
        mysqli_query($conn, "INSERT INTO tbl_sessions (user_id, client_ip, mac_address, device, model, login_time) VALUES ('{$row['id']}', '$client_ip', '$mac', '$device', '$model', NOW())");
        
        // Insert login history
        $name = $row['fname'].' '.$row['lname'];
        $action = $name." HAS LOGGED IN";
        $module = "IN AND OUT";
        mysqli_query($conn,"INSERT INTO tbl_history VALUES (NULL, '$name', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");
        
        header("Location: load.php");
    }
    else
    {
      header("Location: login.php?status=activate");
    }
  }
  else
  {
    header("Location: login.php?status=err");
  }
}
?>
