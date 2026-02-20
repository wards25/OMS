<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $location = $_POST['location'];
    $date = $_POST['date'];
    $shift = $_POST['shift'];
    $shift_type = $_POST['shift_type'];

    // check if already submitted
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_warehouselock_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1)
    {
        $qstring = '?status=err';
    }
    else
    {

        $year = '/'.date('Y');
        $month = '/'.date('F');
        $date_folder = '/'.date('mdY');
        $module = '/warehouselock';

        $directory = 'C:/xampp/htdocs/oms/upload/';

        if($_POST['shift'] == 1){
            $shift_folder = '/1st Shift';
        }else{
            $shift_folder = '/2nd Shift';
        }

        if (file_exists($directory.''.$location)){}else{mkdir($directory.''.$location);}
        if (file_exists($directory.''.$location.''.$year)){}else{mkdir($directory.''.$location.''.$year);}
        if (file_exists($directory.''.$location.''.$year.''.$month)){}else{mkdir($directory.''.$location.''.$year.''.$month);}
        if (file_exists($directory.''.$location.''.$year.''.$month.''.$date_folder)){}else{mkdir($directory.''.$location.''.$year.''.$month.''.$date_folder);}
        if (file_exists($directory.''.$location.''.$year.''.$month.''.$date_folder.''.$shift_folder)){}else{mkdir($directory.''.$location.''.$year.''.$month.''.$date_folder.''.$shift_folder);}
        if (file_exists($directory.''.$location.''.$year.''.$month.''.$date_folder.''.$shift_folder.''.$module)){}else{mkdir($directory.''.$location.''.$year.''.$month.''.$date_folder.''.$shift_folder.''.$module);}

        if($_POST['location'] == 'CAINTA'){
            $location_name = 'CNT';
        }else if($_POST['location'] == 'CDO'){
            $location_name = 'CDO';
        }else if($_POST['location'] == 'CEBU'){
            $location_name = 'CEB';    
        }else if($_POST['location'] == 'DAVAO'){
            $location_name = 'DAV';
        }else if($_POST['location'] == 'ILOILO'){
            $location_name = 'ILO';
        }else if($_POST['location'] == 'PANGASINAN'){
            $location_name = 'PAG';
        }else if($_POST['location'] == 'MARIKINA'){
            $location_name = 'MAR';
        }else{

        }

        if($_POST['shift_type'] == 1){
            $shift_type_folder = 'BEG';
        }else{
            $shift_type_folder = 'END';
        }

        $location_values = $_POST['location_name'];
        foreach ($location_values as $id => $locations)
        {
            // Sanitize the input values
            $lock_name = mysqli_real_escape_string($conn, $locations);

            // upload image
            $filename = $_FILES["image"]["name"][$id];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $new_file_name = $lock_name.'-'.$location_name.'_'.$date.'-'.$shift.'_'.$shift_type_folder.'.'.$ext;
            //$filelocation = "C:/public/www/f325.ramosco.net/filepicture/dbapps/";
            $filelocation = 'C:/xampp/htdocs/oms/upload/'.$location.''.$year.''.$month.''.$date_folder.''.$shift_folder.'/warehouselock/';
            move_uploaded_file($_FILES["image"]["tmp_name"][$id], $filelocation.$new_file_name);
            
            $image_path = $filelocation.$new_file_name;
            
            mysqli_query($conn,"INSERT INTO tbl_warehouselock_raw (id,date,location,shift,shift_type,location_name,path,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','$lock_name','$image_path','$user',NOW(),'0','','') ");
        }

        $action = "SUBMITTED WAREHOUSELOCK CHECKLIST";
        $module = "WAREHOUSE LOCK";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
    }

}else if(isset($_POST['bypass'])){

    $location = $_POST['location'];
    $date = $_POST['date'];
    $shift = $_POST['shift'];
    $shift_type = $_POST['shift_type'];

    // check if already submitted
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_warehouselock_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1)
    {
        $qstring = '?status=err';
    }
    else
    {
        mysqli_query($conn,"INSERT INTO tbl_warehouselock_raw (id,date,location,shift,shift_type,location_name,path,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','24 hrs Operation','','$user',NOW(),'1','$user','') ");

        $action = "SUBMITTED WAREHOUSELOCK CHECKLIST";
        $module = "WAREHOUSE LOCK";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
    }
}

// Redirect to the listing page
header("Location: checklist_admin.php".$qstring);