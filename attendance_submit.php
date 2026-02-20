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
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_attendance_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1)
    {
        $qstring = '?status=err';
    }
    else
    {
        $employee_values = $_POST['employee_name'];
        foreach ($employee_values as $id => $name)
        {
            $position = $_POST['position'][$id];

            if(empty($_POST['attendance'][$id])){
                $attendance = 0;
            }else{
                $attendance = $_POST['attendance'][$id];
            }

            if(empty($_POST['uniform'][$id])){
                $uniform = 0;
            }else{
                $uniform = $_POST['uniform'][$id];
            }

            if(empty($_POST['identification'][$id])){
                $identification = 0;
            }else{
                $identification = $_POST['identification'][$id];
            }

            if(empty($_POST['vest'][$id])){
                $vest = 0;
            }else{
                $vest = $_POST['vest'][$id];
            }

            if(empty($_POST['safety_shoes'][$id])){
                $safety_shoes = 0;
            }else{
                $safety_shoes = $_POST['safety_shoes'][$id];
            }

            // Sanitize the input values
            $employee_name = mysqli_real_escape_string($conn, $name);

            if(empty($_POST['remarks'][$id])){
                $remarks = '';
            }else{
                $remarks = $_POST['remarks'][$id];
                if($remarks == 'Resigned'){
                    mysqli_query($conn,"UPDATE tbl_employees SET is_active='0' WHERE employee_name='$employee_name'");
                }else{

                }
            }

            // Sanitize the input values
            $position = mysqli_real_escape_string($conn, $position);
            $attendance = mysqli_real_escape_string($conn, $attendance);
            $uniform = mysqli_real_escape_string($conn, $uniform);
            $identification = mysqli_real_escape_string($conn, $identification);
            $vest = mysqli_real_escape_string($conn, $vest);
            $safety_shoes = mysqli_real_escape_string($conn, $safety_shoes);
            $remarks = mysqli_real_escape_string($conn, $remarks);
            
            mysqli_query($conn,"INSERT INTO tbl_attendance_raw (id,date,location,shift,shift_type,employee_name,position,attendance,reason,uniform,identification,vest,safety_shoes,remarks,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','$employee_name','$position','$attendance','$remarks','$uniform','$identification','$vest','$safety_shoes','','$user',NOW(),'0','','') ");
        }

        $year = '/'.date('Y');
        $month = '/'.date('F');
        $date_folder = '/'.date('mdY');
        $module = '/attendance';

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

        // upload image
        $filename = $_FILES["image"]["name"];
        if (!empty($filename)) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $new_file_name = $location_name.'_'.$date.'-'.$shift.'_'.$shift_type_folder.'.'.$ext;
            //$filelocation = "C:/public/www/f325.ramosco.net/filepicture/dbapps/";
            $filelocation = 'C:/xampp/htdocs/oms/upload/'.$location.''.$year.''.$month.''.$date_folder.''.$shift_folder.'/attendance/';
            move_uploaded_file($_FILES["image"]["tmp_name"], $filelocation.$new_file_name);

            $image_path = $filelocation.$new_file_name;

            // insert photo
            mysqli_query($conn,"INSERT INTO tbl_attendance (id,date,location,shift,shift_type,image_path,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','$image_path','$user',NOW(),'0','','') ");
        }else{
            
        }

        $action = "SUBMITTED ATTENDANCE CHECKLIST";
        $module = "WH ATTENDANCE";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
    }
}

// Redirect to the listing page
header("Location: checklist.php".$qstring);