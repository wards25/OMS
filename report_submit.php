<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $location = $_POST['location'];
    $date = $_POST['date'];
    $shift = $_POST['shift'];
    $shift_type = $_POST['shift_type'];
    $module_id = $_POST['module_id'];
    $table = $_POST['table'];
    $tbl_column = $_POST['tbl_column'];
    $subject = $_POST['subject'];
    $tag = $_POST['tag'];
    $ir_when = ucfirst($_POST['ir_when']);
    $ir_where = ucfirst($_POST['ir_where']);
    $ir_what = ucfirst($_POST['ir_what']);
    $ir_how = ucfirst($_POST['ir_how']);
    $remarks = ucfirst($_POST['remarks']);
    $report_date = date("Y-m-d");

    // check if already submitted
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_report_involved WHERE user = '$user'");
    $row = mysqli_num_rows($check_query);

    if ($row <=0)
    {
        $qstring = '?status=err';
    }
    else
    {

        $person_query = mysqli_query($conn,"SELECT * FROM tbl_report_involved WHERE user = '$user'");
        $check_person = mysqli_num_rows($person_query);

        if($check_person > 1){
            while($fetch_person = mysqli_fetch_array($person_query)){
                $person[] = $fetch_person['person_involved'];
                $person_list = implode(",", $person);

                // Collect departments into the array
                $position[] = $fetch_person['position'];

                // Remove duplicate departments
                $unique_position = array_unique($position);

                // Create a comma-separated list of unique departments
                if (count($unique_position) > 0) {
                    // Filter out any empty departments before imploding
                    $unique_position = array_filter($unique_position);
                    
                    // Create a comma-separated list only if there are valid departments
                    if (!empty($unique_position)) {
                        $position_list = implode(",", $unique_position);
                    } else {
                        // Handle the case where all departments are empty (if needed)
                        $position_list = '';
                    }
                } else {
                    // Handle the case where there are no unique departments (optional)
                    $position_list = '';
                }

                // Collect departments into the array
                $department[] = $fetch_person['department'];

                // Remove duplicate departments
                $unique_departments = array_unique($department);

                // Create a comma-separated list of unique departments
                if (count($unique_departments) > 0) {
                    // Filter out any empty departments before imploding
                    $unique_departments = array_filter($unique_departments);
                    
                    // Create a comma-separated list only if there are valid departments
                    if (!empty($unique_departments)) {
                        $department_list = implode(",", $unique_departments);
                    } else {
                        // Handle the case where all departments are empty (if needed)
                        $department_list = '';
                    }
                } else {
                    // Handle the case where there are no unique departments (optional)
                    $department_list = '';
                }
            }
        }else{
            $fetch_person = mysqli_fetch_array($person_query);
            $person_list = $fetch_person['person_involved'];
            $position_list = $fetch_person['position'];
            $department_list = $fetch_person['department'];
        }

        // load next reference
        $load_query = mysqli_query($conn,"SELECT * FROM tbl_report_raw ORDER BY id DESC LIMIT 1");
        $row = mysqli_num_rows($load_query);

        if($row >=1){
            $fetch_load = mysqli_fetch_array($load_query);
            $last_number = explode('-', $fetch_load['ref_no']);
            $incident_no = "IR-".date("Ym").'-'.($last_number[count($last_number)-1]+1);
        }else{
            $incident_no = "IR-".date("Ym")."-10001";
        }

        mysqli_query($conn,"INSERT INTO tbl_report_raw (id,date,location,shift,shift_type,ref_no,module_id,table_name,tbl_column,subject,tag,person_involved,position,department,ir_when,ir_where,ir_what,ir_how,remarks,report_date,reported_by,resolve_date,resolved_by,resolution,status) VALUES (NULL,'$date','$location','$shift','$shift_type','$incident_no','$module_id','$table','$tbl_column','$subject','$tag','$person_list','$position_list','$department_list','$ir_when','$ir_where','$ir_what','$ir_how','$remarks','$report_date','$user','0000-00-00','','','0') ");

        //update selected tables
        mysqli_query($conn,"UPDATE $table SET report=report+1 WHERE id = '$module_id'");

        $action = "SUBMITTED INCIDENT REPORT: ".$incident_no;
        $module = "INCIDENT REPORT";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '&?x=1&status=incident';
    }
}

// Redirect to the listing page
if (isset($_SESSION['previous_pages'][0])) {
    // Get the URL of the page two steps back
    $url = $_SESSION['previous_pages'][0];

    // Redirect to that URL
    header("Location: ".$url.$qstring);
    exit();
} else {
    // No previous pages stored
    echo "No previous pages to go back to.";
}
