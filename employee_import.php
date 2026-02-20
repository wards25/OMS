<?php
session_start();
// include mysql database configuration file
include_once("dbconnect.php");
$user = $_SESSION['name'];

header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");  

if (isset($_POST['submit']) || isset($_POST['location']))
{
    $location = $_POST['location'];
    mysqli_query($conn,"DELETE FROM tbl_employees_error WHERE user = '$user'");
 
    // Allowed mime types
    $fileMimes = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain'
    );
 
    // Validate whether selected file is a CSV file
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $fileMimes))
    {

        // Open uploaded CSV file with read-only mode
        $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

        // Skip the first line
        fgetcsv($csvFile);

        $allData = [];

        // Parse data from CSV file and collect into an array
        while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE) {
            $name = strtolower($getData[0]);
            $name = ucwords($name);
            $position = strtolower($getData[1]);
            $position = ucwords($position);
            $shift = utf8_encode(str_replace(array("'"), '', $getData[2]));
            $department = ucfirst($getData[3]);

            $error = 0;

            if (empty($name)) {
                $name = '';
                $error = 1;
            }

            if (empty($position)) {
                $position = '';
                $error = 1;
            }

            if (!is_numeric($shift) || $shift < 1 || $shift > 2) {
                $shift = 0;
                $error = 1;
            }

            if (empty($department)) {
                $department = '';
                $error = 1;
            }

            // Collect the row data
            $allData[] = [
                'name' => $name,
                'position' => $position,
                'shift' => $shift,
                'department' => $department,
                'error' => $error,
                'location' => $location,  // Assuming location comes from another source
            ];
        }

        // Sort the data by name
        usort($allData, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        // Insert sorted data into the table
        foreach ($allData as $row) {
            mysqli_query($conn, "INSERT INTO tbl_employees_error (id, user, employee_name, position, shift, department, location, error) 
            VALUES (NULL, '" . mysqli_real_escape_string($conn, $user) . "', '" . mysqli_real_escape_string($conn, $row['name']) . "', '" . mysqli_real_escape_string($conn, $row['position']) . "', '" . mysqli_real_escape_string($conn, $row['shift']) . "', '" . mysqli_real_escape_string($conn, $row['department']) . "', '" . mysqli_real_escape_string($conn, $row['location']) . "', '" . mysqli_real_escape_string($conn, $row['error']) . "')");
        }

        // Close opened CSV file
        fclose($csvFile);

        $qstring = '?status=succ';
    }else{
        $qstring = '?status=err';
    }

}else{
    $qstring = '?status=invalid_file';
}

// Redirect to the listing page
header("Location: employee_error.php?location=".$location);