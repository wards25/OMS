<?php
session_start();
include_once("dbconnect.php");

    $result = mysqli_query($conn,"SELECT * FROM tbl_roles");
    while($row = mysqli_fetch_array($result)){
?>
    <tr>
        <?php echo '<td><center>'.$row['id'].'</center></td>'; ?>
        <?php echo '<td><center>'.$row['role_name'].'</center></td>'; ?>
    	<td style="width:10%;"><center><a class="text-danger btn-xs" href="#"><i class="fa fa-trash fa-xs"></i></a></center></td>
    </tr>

<?php
    }
?>
