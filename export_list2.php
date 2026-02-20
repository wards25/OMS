<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

    $column_query = mysqli_query($conn,"SELECT * FROM tbl_export WHERE list = 2 AND user = '$user' ORDER BY order_no ASC");
    while($fetch_column = mysqli_fetch_array($column_query)){
?>
    <tr>
        <?php echo '<td>'.ucfirst($fetch_column['col_name']).'</td>'; ?>
    	<td style="width:10%;"><center><a type="button" class="text-danger btn-xs btn-update" href="#" data-id="<?php echo $fetch_column['id']?>"><i class="fa fa-trash fa-xs"></i></a></td>
    </tr>
    
<?php
    }
?>