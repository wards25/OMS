<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']))
{

$tmno = $_GET['tmno'];
$dtr = $_GET['dtr'];
$sorter = $_GET['sorter'];
?>

<style>
    @media print {
        @page {
            size: landscape;
        }
    }
    .container-fluid {
      width: 100%; /* Ensure it uses the full width */
      padding: 0;  /* Remove any padding */
    }
    img {
      max-width: 100%; /* Ensure images fit the page */
      height: auto;
    }
    .table {
      width: 100%; /* Tables should take the full width */
      border-collapse: collapse; /* Remove gaps */
    }
    th, td {
      padding: 5px; /* Add some padding */
      border: 1px solid #000; /* Ensure borders are visible */
    }
    /* Hide unnecessary elements for printing */
    .no-print {
      display: none; 
    }
    .top, .bottom {
        height: 50%;
        box-sizing: border-box;
    }
    .nowrap {
      white-space: nowrap;
    }
  }
</style>

    <!-- Begin Page Content -->
    <div class="container-fluid">     
        <div class="table-responsive">
            <table class="table table-bordered table-sm" style="color:#000000;">
                <tbody>
                    <tr>
                        <td><b>TM No.:</b></td>
                        <td><?php echo 'TM/'.$tmno; ?></td>
                        <td><b>Location:</b></td>
                        <td>CAINTA</td>
                    </tr>
                    <tr>
                        <td><b>Sorted By:</b></td>
                        <td style="width:38%;"><?php echo $sorter; ?></td>
                        <td><b>Date Printed:</b></td>
                        <td><?php echo date("F d, Y H:i:s"); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" style="color:#000000;">
                <thead>
                    <tr class="text-center">
                        <th>Barcode</th>
                        <th>SKU</th>
                        <?php
                        // Query to get all unique brcodes
                        $header_query = mysqli_query($conn, "SELECT DISTINCT brcode FROM tbl_trips_raw WHERE tmno = '$tmno'");
                        $brcodes = [];

                        while ($fetch_header = mysqli_fetch_assoc($header_query)) {
                            $brcodes[] = $fetch_header['brcode'];
                            echo '<th>MDC-' . $fetch_header['brcode'] . '</th>';
                        }
                        ?>
                    </tr>
                </thead>
                <tbody> 
                    <?php
                    // Query to select SKU and company details
                    $result = mysqli_query($conn, "SELECT sku, description, barcode, company, picklistno FROM tbl_trips_raw WHERE tmno = '$tmno' GROUP BY sku ORDER BY picklistno");

                    while ($row = mysqli_fetch_assoc($result)) {
                        $sku = $row['sku'];
                        $total_sku_qty = 0;

                        // Calculate total quantity for this SKU across all brcodes
                        foreach ($brcodes as $brcode) {
                            $brcode_qty_query = mysqli_query($conn, "SELECT SUM(finalqty) as total_qty FROM tbl_trips_raw WHERE tmno = '$tmno' AND brcode = '$brcode' AND sku = '$sku'");
                            $brcode_qty = mysqli_fetch_assoc($brcode_qty_query);
                            $total_sku_qty += $brcode_qty['total_qty'] ?? 0;
                        }

                        // Display the row only if the total quantity is greater than zero
                        if ($total_sku_qty > 0) {
                            echo '<tr>';
                            echo '<td><center>' . $row['barcode'] . '</center></td>';
                            echo '<td>' . $row['sku'] . ' - ' . $row['description'] . '</td>';

                            // Display quantity per brcode
                            foreach ($brcodes as $brcode) {
                                $brcode_qty_query = mysqli_query($conn, "SELECT SUM(finalqty) as total_qty FROM tbl_trips_raw WHERE tmno = '$tmno' AND brcode = '$brcode' AND sku = '$sku'");
                                $brcode_qty = mysqli_fetch_assoc($brcode_qty_query);
                                $qty = $brcode_qty['total_qty'] ?? 0;

                                echo '<td><center>' . ($qty > 0 ? $qty : '') . '</center></td>';
                            }

                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- End of Main Content -->
</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

</body>

</html>

<?php
}else{
    header("Location: denied.php");
}