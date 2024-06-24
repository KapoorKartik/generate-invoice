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
    echo '<pre>'; print_r($currentYear); echo '</pre>';
    $currentMonth = date('m');
    echo '<pre>'; print_r($currentMonth); echo '</pre>';

    $query = "SELECT * FROM payment_data WHERE DATE_FORMAT(created_at, '%Y-%m') = '$currentYear-$currentMonth'";

    $result = executeQuery($query);
    echo '<pre> result '; print_r($result); echo '</pre>';
      // Check if the query was successful
      if ($result instanceof mysqli_result) {
        // Fetch all rows and print them
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        
        echo '<pre> Rows '; print_r($rows); echo '</pre>';
    } else {
        echo '<pre> Error: Failed to retrieve data. </pre>';
    }

    

}



if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Assuming the path to the HTML file and variables are passed as query parameters
    // $htmlFilePath = $_GET['htmlFilePath'];
    // $variables = json_decode($_GET['variables'], true);
    // $outputFilename = $_GET['outputFilename'];
    fetchCurrentMonthData();
}else if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    echo "post method";
    die;
    $variables = [
        'title' => 'Hare Krishna',
        'content' => 'This is the content.'
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

