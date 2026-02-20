<?php
include_once("dbconnect.php");
session_start();

$query = mysqli_query($conn, "SELECT m.*, u.* 
    FROM tbl_chat_messages m
    LEFT JOIN tbl_users u ON m.user_id = u.id
    ORDER BY m.message_time ASC");

while ($row = mysqli_fetch_assoc($query)) {
    $self = ($row['user_id'] == $_SESSION['id']) ? "text-end text-primary" : "text-start text-dark";
    $message_date = date("M d, Y", strtotime($row['message_date']));
    $message_time = date("h:i A", strtotime($row['message_time']));

    echo "<div class='$self'>
            <hr style='margin-top: 3px; margin-bottom: 3px;'>
            <div class='text-left text-muted' style='font-size: 9px;'>
                Sent: $message_date $message_time
            </div>
            <small><b>{$row['fname']} {$row['lname']} </b>: {$row['message']}</small>
        </div>";
}
?>