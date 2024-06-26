<?php
// Include the DOMPDF library
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
    // Database connection parameters
    $servername = "localhost";
    $username = "root"; // Default username for XAMPP MySQL
    $password = ""; // Default password for XAMPP MySQL
    $database = "user_db"; // Replace with your database name

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

function fetchCurrentMonthData() {

    $currentYear = date('Y');
    // echo '<pre>'; print_r($currentYear); echo '</pre>';
    $currentMonth = date('m');
    // echo '<pre>'; print_r($currentMonth); echo '</pre>';

    $query = "SELECT * FROM payment_data WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentYear-$currentMonth' Order By id DESC";

    $result = executeQuery($query);
      // Check if the query was successful
      if ($result instanceof mysqli_result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        return $rows;
    } else {
        return 'Error: Failed to retrieve data.';
    }

    

}



if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = fetchCurrentMonthData();
    echo json_encode($res);
}else if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // capture data from from request
    $data = json_decode(file_get_contents('php://input'), true);
    // print_r($data);
    // data is Array ( [id] => 26 [product] => PDF (Send on WhatsApp) [name] => allen [mobile] => 3213213215 [email] => kartikkapoor485@gmail.com [address] => 456 Oak Avenue, Metropolis, NY 10001 [pincode] => 321321 [state] => Jammu and Kashmir [amount] => 100.00 [order_id] => order_OIxkHLd6DtMbSu [receipt_id] => rcptid_665ff64aad8fe [payment_status] => Successful [created_at] => 2024-06-05 10:53:23 [invoiceNum] => 9876 )
    // capture all in variables
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
    // die;
    $variables = [
        'title' => 'Hare Krishna',
        'content' => 'This is the content.',
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

