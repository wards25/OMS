<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include_once("dbconnect.php");

if(isset($_POST['submit'])){

    $tag = $_POST['tag'];
    $type = 'N';
    $ewt = !empty($_POST['ewt']) ? strtoupper(trim($_POST['ewt'])) : 0;
    $business_name = strtoupper(trim($_POST['business_name']));
    $business_name = str_replace([".", "/", "'"], [",", "", ""], $business_name);

    $business_type = $_POST['business_type'];
    if ($business_type === 'Others') {
        $business_type = ucwords(str_replace("'", "", $_POST['business_type_other'])); // Remove single quote
    } else {
        $business_type = str_replace("'", "", $business_type); // Remove single quote
    }

    $ship_to = isset($_POST['ship_to']) ? strtoupper(str_replace(["'", ","], "", trim($_POST['ship_to']))) : '';
    $station_name = isset($_POST['station_name']) ? strtoupper(str_replace(["'", ","], "", trim($_POST['station_name']))) : '';
    $clinic_name = isset($_POST['clinic_name']) ? strtoupper(str_replace(["'", ","], "", trim($_POST['clinic_name']))) : '';
    $delegate_name = isset($_POST['delegate_name']) ? strtoupper(str_replace(["'", ","], "", trim($_POST['delegate_name']))) : '';

    $tin_no = trim($_POST['tin_no']);
    $contact_no = trim($_POST['contact_no']);
    $license_no = isset($_POST['license_no']) ? strtoupper(trim($_POST['license_no'])) : '';
    $validity_date = '0000-00-00';

    if (!empty($_POST['validity_date'])) {
        $timestamp = strtotime($_POST['validity_date']);
        if ($timestamp !== false) {
            $validity_date = date("Y-m-d", $timestamp);
        }
    }

    $bldg_name = strtoupper(str_replace([",", "/", "'"], "", $_POST['bldg_name']));

    if($tag == 'GASCON'){
        $address = $bldg_name;
    }else{
        $address = strtoupper(str_replace(["'", ","], "", trim($_POST['address'])));
    }

    $landmark = strtoupper(str_replace([",", "/", "'"], "", $_POST['landmark']));
    $street = isset($_POST['street']) ? strtoupper(str_replace([",", "/", "'"], "", $_POST['street'])) : '';
    $province = strtoupper($_POST['province_name']);
    $city = strtoupper($_POST['city_name']);
    $brgy = strtoupper($_POST['brgy_name']);
    $zip_code = strtoupper(trim($_POST['zip_code']));
    $email = strtolower(trim($_POST['email']));
    $telephone = trim($_POST['telephone']);

    if(!empty($_POST['delivery_sched'])) {
        $delivery_sched_array = $_POST['delivery_sched'];
        $delivery_sched = implode(',', $delivery_sched_array);
    }else{
        $delivery_sched = '';
    }

    $hours_from = !empty($_POST['hours_from']) ? $_POST['hours_from'] : '00:00:00';
    $hours_to = !empty($_POST['hours_to']) ? $_POST['hours_to'] : '00:00:00';
    $authorized_name = strtoupper(trim($_POST['authorized_name']));
    $authorized_name = str_replace('/', ',', $authorized_name);
    $authorized_pos = strtoupper(trim($_POST['authorized_pos']));
    $authorized_contact = trim($_POST['authorized_contact']);
    $signatory1_name = strtoupper(trim($_POST['signatory1_name']));
    $signatory1_pos = strtoupper(trim($_POST['signatory1_pos']));
    $signatory1_contact = trim($_POST['signatory1_contact']);
    $application_date = date("Y-m-d");
    $del_address2 = !empty($_POST['del_address2']) ? strtoupper(trim($_POST['del_address2'])) : '';
    $del_address3 = !empty($_POST['del_address3']) ? strtoupper(trim($_POST['del_address3'])) : '';

    $last_query = mysqli_query($conn, "SELECT serial_no FROM tbl_customer WHERE tag = '$tag' ORDER BY serial_no DESC LIMIT 1");
    $fetch_last = mysqli_fetch_assoc($last_query);

    if (!$fetch_last) {
        $code = $_POST['code'];
        $fetch_last = $code . '-0001';
    } else {
        $last_serial_no = $fetch_last['serial_no'];
        if (preg_match('/([A-Za-z]+)-(\d+)/', $last_serial_no, $matches)) {
            $prefix = $matches[1];
            $numeric_part = (int)$matches[2];
            $numeric_part++;
            $fetch_last = $prefix . '-' . str_pad($numeric_part, 4, '0', STR_PAD_LEFT);
        } else {
            $fetch_last = $last_serial_no;
        }
    }

    $serial_no = $fetch_last;

    // Check if already submitted
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_customer WHERE business_name = '$business_name' AND email = '$email' AND tag = '$tag'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1){
        $qstring = '?status=err';
    }else{

        // Directory
        $directory = 'C:/xampp/htdocs/oms/upload/customers/';
        // $directory = 'D:/public/www/oms.ramosco.net/upload/customers/';

        if (file_exists($directory.'/'.$tag)){}else{mkdir($directory.'/'.$tag);}
        if (file_exists($directory.'/'.$tag.'/'.$business_name)){}else{mkdir($directory.'/'.$tag.'/'.$business_name);}
        if (file_exists($directory.'/'.$tag.'/'.$business_name.'/'.$application_date)){}else{mkdir($directory.'/'.$tag.'/'.$business_name.'/'.$application_date);}

        // Upload photos
        $upload_fields = [
            "prc_license"        => "PRC_{$license_no}_{$validity_date}",
            "sec"                => "SEC",
            "by_laws"            => "BYLAWS",
            "gis"                => "GIS",
            "mayors_permit_main" => "PERMIT",
            "mayors_permit_prc"  => "PERMIT",
            "id_speciment"       => "ID",
            "gas_facade"         => "FACADE",
            "gas_station"        => "STATION"
        ];

        // File location
        $filelocation = 'C:/xampp/htdocs/oms/upload/customers/'.$tag.'/'.$business_name.'/'.$application_date.'/';
        // $filelocation = 'D:/public/www/oms.ramosco.net/upload/customers/'.$tag.'/'.$business_name.'/'.$application_date.'/';

        // Ensure the folder exists
        if (!file_exists($filelocation)) {
            mkdir($filelocation, 0777, true);
        }

        $uploaded_file_paths = [];

        // Initialize with empty strings
        foreach ($upload_fields as $field_name => $custom_name) {
            $uploaded_file_paths[$field_name] = ''; // default
        }

        // Handle uploaded files
        foreach ($upload_fields as $field_name => $custom_name) {
            if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] == 0) {
                $original_name = $_FILES[$field_name]['name'];
                $tmp_name = $_FILES[$field_name]['tmp_name'];
                $ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $new_filename = $custom_name . '.' . $ext;
                $destination = $filelocation . $new_filename;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $uploaded_file_paths[$field_name] = $destination;
                }
            }
        }

        // Access individual paths safely
        $prc_license_path = $uploaded_file_paths['prc_license'];
        // $bir_path = $uploaded_file_paths['bir'];
        $sec_path = $uploaded_file_paths['sec'];
        $by_laws_path = $uploaded_file_paths['by_laws'];
        $gis_path = $uploaded_file_paths['gis'];
        $mayors_permit_main_path = $uploaded_file_paths['mayors_permit_main'];
        $mayors_permit_prc_path  = $uploaded_file_paths['mayors_permit_prc'];
        // $mayors_permit_path = $uploaded_file_paths['mayors_permit'];
        $id_speciment_path = $uploaded_file_paths['id_speciment'];
        $mayors_permit_path = $mayors_permit_main_path ?: $mayors_permit_prc_path;
        $facade_path = $uploaded_file_paths['gas_facade'];
        $station_path = $uploaded_file_paths['gas_station'];

        $bir_file_paths = ["", "", "", "", ""]; // up to 5 files

        if (isset($_FILES['bir']) && is_array($_FILES['bir']['name'])) {
            $total_files = count($_FILES['bir']['name']);
            $total_files = min($total_files, 5); // limit to 5

            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['bir']['error'][$i] === 0 && !empty($_FILES['bir']['name'][$i])) {
                    $original_name = $_FILES['bir']['name'][$i];
                    $tmp_name = $_FILES['bir']['tmp_name'][$i];
                    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

                    // New filename format
                    $new_filename = "BIR_" . ($i + 1) . "." . $ext;
                    $destination = $filelocation . $new_filename;

                    // Move file to final destination (same folder as $filelocation)
                    if (move_uploaded_file($tmp_name, $destination)) {
                        $bir_file_paths[$i] = $destination;
                    }
                }
            }
        }

        // Assign variables safely
        $bir1 = $bir_file_paths[0] ?? '';
        $bir2 = $bir_file_paths[1] ?? '';
        $bir3 = $bir_file_paths[2] ?? '';
        $bir4 = $bir_file_paths[3] ?? '';
        $bir5 = $bir_file_paths[4] ?? '';

        // Upload e-sig
        $data = $_POST['signature_data'];

        // Clean and decode Base64 string
        $data = str_replace('data:image/png;base64,', '', $data);
        $data = str_replace(' ', '+', $data);
        $imageData = base64_decode($data);

        $filePath = $directory.$tag.'/'.$business_name.'/'.$application_date.'/ESIG_'.$authorized_name.'.png';

        // Save the image
        file_put_contents($filePath, $imageData);

        // Insert data
        mysqli_query($conn,"INSERT INTO tbl_customer (id,serial_no,tag,ewt,type,business_name,business_type,ship_to,station_name,delegate_name,clinic_name,address,contact,license_no,prc_license,validity_date,bir1,bir2,bir3,bir4,bir5,sec,by_laws,gis,mayors_permit,id_speciment,gas_facade,gas_station,tin_no,bldg_name,landmark,street,province,city,brgy,zip_code,area_code,email,telephone,delivery_sched,hours_from,hours_to,authorized_name,authorized_pos,authorized_contact,authorized_sign,signatory1_name,signatory1_pos,signatory1_contact,signatory1_sign,application_date,validated,validated_by,validated_date,del_address2,del_address3) VALUES (NULL,'$serial_no','$tag','$ewt','$type','$business_name','$business_type','$ship_to','$station_name','$delegate_name','$clinic_name','$address','$contact_no','$license_no','$prc_license_path','$validity_date','$bir1','$bir2','$bir3','$bir4','$bir5','$sec_path','$by_laws_path','$gis_path','$mayors_permit_path','$id_speciment_path','$facade_path','$station_path','$tin_no','$bldg_name','$landmark','$street','$province','$city','$brgy','$zip_code','+63','$email','$telephone','$delivery_sched','$hours_from','$hours_to','$authorized_name','$authorized_pos','$authorized_contact','$filePath','$signatory1_name','$signatory1_pos','$signatory1_contact','','$application_date','0','','0000-00-00','$del_address2','$del_address3') ");


        header("Location: cif_success.php");
    }
}else{
    $qstring = '?status=tag';
    header("Location: index.php".$qstring);
}