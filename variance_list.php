<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$result = mysqli_query($conn,"SELECT * FROM tbl_variance_list WHERE user = '$user'");
while($row = mysqli_fetch_array($result)) {

    echo '<tr class="text-center">';
        echo '<td class="align-middle">'.$row['error_type'].'</td>';
        echo '<td class="align-middle">'.$row['invoiced_sku'].' - '.$row['invoiced_desc'].'</td>';
        echo '<td class="align-middle">'.$row['invoiced_qty'].'</td>';
        echo '<td class="align-middle">'.$row['picked_sku'].' - '.$row['picked_desc'].'</td>';
        echo '<td class="align-middle">'.$row['picked_qty'].'</td>';
        echo '<td class="align-middle">'.$row['uom'].'</td>';

        if($row['qty'] < 0){
            echo '<td class="align-middle table-danger">'.$row['qty'].'</td>';
        }else{
            echo '<td class="align-middle">'.$row['qty'].'</td>';
        }

        if($row['return_qty'] < 0){
            echo '<td class="align-middle table-danger">'.$row['return_qty'].'</td>';
        }else{
            echo '<td class="align-middle">'.$row['return_qty'].'</td>';
        }
?>
        <td class="align-middle"><center><a class="btn btn-danger btn-sm delete-btn" href="#" data-id="<?php echo $row['id']?>"><i class="fa fa-times fa-xs"></i></a></center></td>
    </tr>

<?php
    }
?>
