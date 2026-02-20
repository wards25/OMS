<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$involved_query = mysqli_query($conn,"SELECT * FROM tbl_report_involved WHERE user = '$user'");
$check = mysqli_num_rows($involved_query);

if($check >= 1){
    while($row = mysqli_fetch_array($involved_query)){
    ?>
        <tr>
            <td><?php echo htmlspecialchars($row['person_involved']); ?></td>
            <td><?php echo htmlspecialchars($row['position']); ?></td>
            <td><?php echo htmlspecialchars($row['department']); ?></td>
            <td style="width:5%;">
                <center><a type="button" class="text-danger btn-xs delete-btn" data-id="<?php echo htmlspecialchars($row['id']); ?>"><i class="fa fa-times fa-xs"></i></a></center>
            </td>
        </tr>
    <?php
    }
}else{
    echo '<td colspan="4"><center><i>No person(s) involved selected.</i></center></td>';
}
?>
