<?php
// Include the DOMPDF library
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

function generatePDF($htmlFilePath, $variables, $outputFilename) {
    $htmlTemplate = file_get_contents($htmlFilePath);
    if ($htmlTemplate === false) {
        throw new Exception("Unable to read HTML file.");
    }

    foreach ($variables as $key => $value) {
        $htmlTemplate = str_replace('{{' . $key . '}}', $value, $htmlTemplate);
    }

    // Initialize DOMPDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($htmlTemplate);
    $dompdf->setPaper('A4', 'portrait'); // (Optional) Set paper size and orientation
    $dompdf->render();

    // Output the generated PDF (force download)
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $outputFilename . '"');
    header('Cache-Control: max-age=0');

    echo $dompdf->output();
}

function executeQuery($sql_query) {
    $environment = 'development'; // Change to 'production' for production environment
    // $environment = 'production';

      if ($environment === 'development') {
        $servername = "localhost";
        $username = "root"; 
        $password = ""; 
        $database = "user_db"; 
    } else if ($environment === 'production') {
        $servername = "localhost";
        $username = "u850205723_payments"; // Default username for XAMPP MySQL
        $password = "A7N?XkO[Snc|"; // Default password for XAMPP MySQL
        $database = "u850205723_payments"; // Replace with your database name
    } else {
        die("Unknown environment");
    }

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Execute query
    $result = $conn->query($sql_query);

    // Close connection
    $conn->close();

    // Return result
    return $result;
}

    function fetchMonthData($currentMonth) {

    $currentYear = date('Y');
    // echo '<pre>'; print_r($currentYear); echo '</pre>';
    // $currentMonth = date('m', strtotime('-5 month'));
    // echo '<pre>'; print_r($currentMonth); echo '</pre>';

    // $query = "SELECT * FROM payment_data where payment_status = 'Successful' 
        // --   ORDER BY id DESC";

    $query = "SELECT * FROM payment_data WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentYear-$currentMonth' AND  payment_status = 'Successful' Order By id DESC";



    $result = executeQuery($query);
      // Check if the query was successful
      if ($result instanceof mysqli_result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        return $rows;
    } else {
        return 'Error: Failed to retrieve data.';
    }

    

}

function convertNumberToWords($number) {
    $dictionary = [
        0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
        5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
        14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
        18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
        80 => 'eighty', 90 => 'ninety', 100 => 'hundred', 1000 => 'thousand'
    ];

    if (!is_numeric($number)) {
        return false;
    }

    if ($number < 0 || $number > 10000) {
        return false;
    }

    if ($number == 0) {
        return $dictionary[0];
    }

    $string = '';

    if ($number >= 1000) {
        $string .= $dictionary[(int)($number / 1000)] . ' thousand ';
        $number %= 1000;
    }

    if ($number >= 100) {
        $string .= $dictionary[(int)($number / 100)] . ' hundred ';
        $number %= 100;
        if ($number > 0) {
            $string .= 'and ';
        }
    }

    if ($number > 0) {
        if ($number < 20) {
            $string .= $dictionary[$number];
        } else {
            $string .= $dictionary[(int)($number / 10) * 10];
            if ($number % 10 > 0) {
                $string .= '-' . $dictionary[$number % 10];
            }
        }
    }

    return ucwords($string);
}


function getStateCode($state){
    $stateCodeArr = array(
        "Jammu & Kashmir" => 1,
        "Himachal Pradesh" => 2,
        "Punjab" => 3,
        "Chandigarh" => 4,
        "Uttarakhand" => 5,
        "Haryana" => 6,
        "Delhi" => 7,
        "Rajasthan" => 8,
        "Uttar Pradesh" => 9,
        "Bihar" => 10,
        "Sikkim" => 11,
        "Arunachal Pradesh" => 12,
        "Nagaland" => 13,
        "Manipur" => 14,
        "Mizoram" => 15,
        "Tripura" => 16,
        "Meghalaya" => 17,
        "Assam" => 18,
        "West Bengal" => 19,
        "Jharkhand" => 20,
        "Odisha" => 21,
        "Chattisgarh" => 22,
        "Madhya Pradesh" => 23,
        "Gujarat" => 24,
        "Daman & Diu" => 25,
        "Dadra & Nagar Haveli" => 26,
        "Maharashtra" => 27,
        "Andhra Pradesh (Before Division)" => 28,
        "Karnataka" => 29,
        "Goa" => 30,
        "Lakshadweep" => 31,
        "Kerala" => 32,
        "Tamil Nadu" => 33,
        "Puducherry" => 34,
        "Andaman & Nicobar Islands" => 35,
        "Telangana" => 36,
        "Andhra Pradesh (Newly Added)" => 37,
        "Ladakh" => 38,
        "Other Territory" => 97,
        "Centre Jurisdiction" => 99
    );

    return  $stateCodeArr[$state];
}



if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $month = isset($_GET['month']) ? $_GET['month'] : null;
    $res = fetchMonthData($month);
    echo json_encode($res);

}else if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // capture data from from request
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['id'];
    $product = $data['product'];
    $name = $data['name'];
    $mobile = $data['mobile'];
    $email = $data['email'];
    $address = $data['address'];
    $pincode = $data['pincode'];
    $state = $data['state'];
    $amount = $data['amount'];
    $order_id = $data['order_id'];
    $receipt_id = $data['receipt_id'];
    $payment_status = $data['payment_status'];
    $created_at = $data['created_at'];
    $invoiceNum = $data['invoiceNum'];

    $amtWithoutGst = round($amount  * 100/118,2);
    $gst = $amount - $amtWithoutGst;
    // die;
    $variables = [
        'invoiceNumber' => $invoiceNum,
        'id' => $id,
        'product' => $product,
        'name' => $name,
        'mobile' => $mobile,
        'email' => $email,
        'address' => $address,
        'pincode' => $pincode,
        'state' => $state,
        'amount' => $amount,
        'order_id' => $order_id,
        'receipt_id' => $receipt_id,
        'payment_status' => $payment_status,
        'created_at' => $created_at,
        'gst' => $gst,
        'amountWithoutGst' => $amtWithoutGst,
        'amountInWords' => convertNumberToWords($amount),
        'stateCode' => getStateCode($state),
    ];
    $outputFilename = 'output.pdf';
    $htmlFilePath = "./template.html";
    generatePDF($htmlFilePath, $variables, $outputFilename);

    try {
        generatePDF($htmlFilePath, $variables, $outputFilename);
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Error: ' . $e->getMessage();
    }
}

