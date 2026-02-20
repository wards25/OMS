<?php
$database = 'db_oms';

date_default_timezone_set("Asia/Manila");

// connect to server
$conn = mysqli_connect('localhost', 'root', '', $database);
if (!$conn){
    die("Database Connection Failed" . mysqli_error($conn));
}
	// select database
    $select_db = mysqli_select_db($conn, $database);
    if (!$select_db){
        die("Database Selection Failed" . mysqli_error($conn));
}

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
        $model = "iPhone (Model Unknown)";
    } elseif (strpos($user_agent, 'ipad') !== false) {
        $device = "iPadOS";
        $model = "iPad (Model Unknown)";
    } elseif (strpos($user_agent, 'android') !== false) {
        $device = "Android";
        if (preg_match('/android\s([\d\.]+);\s+([\w\s-]+)\s+build/i', $user_agent, $matches)) {
            $model = trim($matches[2]);  // Extract model name
        }
    } elseif (strpos($user_agent, 'linux') !== false) {
        $device = "Linux";
    }

    return [$device, $model];
}

// Function to get MAC address (only works reliably on local machines)
function getMacAddress() {
    $mac = exec("getmac");
    $mac = strtok($mac, " ");
    $mac = str_replace("-", ":", $mac);
    return $mac ?: "Unknown";
}

// Function to get real client IP (behind proxy/load balancer)
function getClientIp() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $client_ip = trim($ip_list[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $client_ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $client_ip = $_SERVER['REMOTE_ADDR'];
    }

    // Normalize IPv6 localhost
    if ($client_ip == "::1") {
        $client_ip = "127.0.0.1";
    }

    return $client_ip;
}

// Get IP, MAC, device info
$client_ip = getClientIp();
$mac = getMacAddress();
list($device, $model) = getDeviceInfo();

// Validate session IP address
// if (isset($_SESSION['id'])) {
    
//     $user_id = intval($_SESSION['id']);
//     $session_query = mysqli_query($conn, "SELECT user_id, ip_address FROM tbl_sessions WHERE user_id = $user_id");
//     $fetch_session = mysqli_fetch_assoc($session_query);

//     if ($fetch_session && $fetch_session['ip_address'] !== $client_ip) {
//         // Log out user on IP mismatch
//         $_SESSION = array();
//         session_destroy();
//         header("Location: login.php?status=session_mismatch");
//         exit();
//     }

//     $location_query = mysqli_query($conn, "SELECT tbl_user_locations.user_id, tbl_user_locations.location_id, tbl_locations.location_name, tbl_locations.is_active FROM tbl_user_locations LEFT JOIN tbl_locations ON tbl_user_locations.location_id = tbl_locations.id WHERE tbl_locations.is_active = 1 AND tbl_user_locations.user_id = " . $_SESSION['id']);

//     if (!$location_query) {
//         die("Location Query Error: " . mysqli_error($conn));
//     }

//     $check_location = mysqli_num_rows($location_query);

//     if ($check_location > 1) {
//         $header = [];
//         while ($fetch_location = mysqli_fetch_array($location_query)) {
//             $header[] = $fetch_location['location_name'];
//         }
//         $location = "'" . implode("','", $header) . "'";
//     } else {
//         $fetch_location = mysqli_fetch_array($location_query);
//         $location = "'" . $fetch_location['location_name'] . "'";
//     }
// }
?>