<?php
// Include the DOMPDF library
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

function generatePDF($htmlFilePath, $variables, $outputFilename) {
    // Read HTML template from file
    $htmlTemplate = file_get_contents($htmlFilePath);
    if ($htmlTemplate === false) {
        throw new Exception("Unable to read HTML file.");
    }

    // echo $htmlTemplate;
    // die;

    // Replace placeholders in HTML template with actual values
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


// Define variables
$variables = [
    'title' => 'Hare Krishna',
    'content' => 'This is the content.'
];

// Define output filename
$outputFilename = 'output.pdf';

// Generate and output the PDF
try {
    $htmlFilePath = "./template.html";
    generatePDF($htmlFilePath, $variables, $outputFilename);
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error: ' . $e->getMessage();
}


// // Example usage
// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
//     // Assuming the path to the HTML file and variables are passed as query parameters
//     $htmlFilePath = $_GET['htmlFilePath'];
//     $variables = json_decode($_GET['variables'], true);
//     $outputFilename = $_GET['outputFilename'];

//     try {
//         generatePDF($htmlFilePath, $variables, $outputFilename);
//     } catch (Exception $e) {
//         http_response_code(500);
//         echo 'Error: ' . $e->getMessage();
//     }
// }

