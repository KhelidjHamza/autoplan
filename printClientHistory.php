<?php
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Database connection
    $conn = new mysqli("localhost", "username", "password", "database");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM clients WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $client = $result->fetch_assoc();
    } else {
        die("No client found.");
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Client History</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="print-container">
        <h1>Client History</h1>
        <table>
            <tr>
                <th>Client Name</th>
                <td><?php echo $client['name']; ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo $client['phone']; ?></td>
            </tr>
            <tr>
                <th>Age</th>
                <td><?php echo $client['age']; ?></td>
            </tr>
            <tr>
                <th>Weight</th>
                <td><?php echo $client['weight']; ?></td>
            </tr>
            <tr>
                <th>Height</th>
                <td><?php echo $client['height']; ?></td>
            </tr>
            <tr>
                <th>Calorie Goal</th>
                <td><?php echo $client['calorie_goal']; ?></td>
            </tr>
            <tr>
                <th>Goal</th>
                <td><?php echo $client['goal']; ?></td>
            </tr>
            <tr>
                <th>Exercise</th>
                <td><?php echo $client['exercise']; ?></td>
            </tr>
            <tr>
                <th>Diseases</th>
                <td><?php echo $client['diseases']; ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
