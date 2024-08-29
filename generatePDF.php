<?php
require_once('tcpdf/tcpdf.php');

// Database connection
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user and client data
$userId = $_GET['user_id'];
$clientId = $_GET['client_id'];

// Fetch user data
$sqlUser = "SELECT * FROM users WHERE id = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("i", $userId);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userData = $resultUser->fetch_assoc();

// Fetch client data
$sqlClient = "SELECT * FROM clients WHERE id = ?";
$stmtClient = $conn->prepare($sqlClient);
$stmtClient->bind_param("i", $clientId);
$stmtClient->execute();
$resultClient = $stmtClient->get_result();
$clientData = $resultClient->fetch_assoc();

// Fetch meal plan
$sqlMeals = "SELECT meals.name, meals.description, meals.calories, meals.protein, meals.fat, meals.carbs, client_meals.quantity 
             FROM meals 
             JOIN client_meals ON meals.id = client_meals.meal_id 
             WHERE client_meals.client_id = ?";
$stmtMeals = $conn->prepare($sqlMeals);
$stmtMeals->bind_param("i", $clientId);
$stmtMeals->execute();
$resultMeals = $stmtMeals->get_result();
$mealPlan = $resultMeals->fetch_all(MYSQLI_ASSOC);

// Create a new PDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Diet Plan Generator');
$pdf->SetTitle('Diet Plan Invoice');
$pdf->SetSubject('Diet Plan Invoice');
$pdf->SetKeywords('TCPDF, PDF, invoice, diet, plan');

// Set default header data
$pdf->SetHeaderData('logo.png', 30, 'Diet Plan Generator', 'Generated Diet Plan Invoice');

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// HTML content
$html = '<h1>Diet Plan Invoice</h1>';
$html .= '<h3>User Information</h3>';
$html .= '<p>ID: ' . $userData['id'] . '<br>';
$html .= 'Name: ' . $userData['username'] . '<br>';
$html .= 'Type: ' . $userData['user_type'] . '</p>';

$html .= '<h3>Client Information</h3>';
$html .= '<p>Name: ' . $clientData['name'] . '<br>';
$html .= 'Phone: ' . $clientData['phone'] . '<br>';
$html .= 'Age: ' . $clientData['age'] . '<br>';
$html .= 'Weight: ' . $clientData['weight'] . ' kg<br>';
$html .= 'Height: ' . $clientData['height'] . ' cm<br>';
$html .= 'Calorie Goal: ' . $clientData['calorie_goal'] . ' kcal<br>';
$html .= 'Goal: ' . $clientData['goal'] . '<br>';
$html .= 'Exercise Intensity: ' . $clientData['exercise'] . '<br>';
$html .= 'Diseases: ' . $clientData['diseases'] . '</p>';

$html .= '<h3>Meal Plan</h3>';
$html .= '<table border="1" cellspacing="3" cellpadding="4">';
$html .= '<tr><th>Meal</th><th>Description</th><th>Calories</th><th>Fat (g)</th><th>Carbs (g)</th><th>Protein (g)</th><th>Quantity</th></tr>';
foreach ($mealPlan as $meal) {
    $html .= '<tr><td>' . $meal['name'] . '</td>';
    $html .= '<td>' . $meal['description'] . '</td>';
    $html .= '<td>' . $meal['calories'] . '</td>';
    $html .= '<td>' . $meal['fat'] . '</td>';
    $html .= '<td>' . $meal['carbs'] . '</td>';
    $html .= '<td>' . $meal['protein'] . '</td>';
    $html .= '<td>' . $meal['quantity'] . '</td></tr>';
}
$html .= '</table>';

$html .= '<p>Date of Printing: ' . date('Y-m-d') . '</p>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('diet_plan_invoice.pdf', 'I');

$conn->close();
?>
