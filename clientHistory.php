<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client History</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="history-container">
        <h1>Client History</h1>
        <table id="clientHistoryTable">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>Age</th>
                    <th>Weight</th>
                    <th>Height</th>
                    <th>Calorie Goal</th>
                    <th>Goal</th>
                    <th>Exercise</th>
                    <th>Diseases</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                session_start();
                $userId = $_SESSION['user_id'];
                $servername = "localhost";
                $username = "username";
                $password = "password";
                $dbname = "diet_planner";
                // Database connection
                $conn = new mysqli("localhost", "username", "password", "diet_planner");

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM clients WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['name']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['age']}</td>
                            <td>{$row['weight']}</td>
                            <td>{$row['height']}</td>
                            <td>{$row['calorie_goal']}</td>
                            <td>{$row['goal']}</td>
                            <td>{$row['exercise']}</td>
                            <td>{$row['diseases']}</td>
                            <td><button onclick=\"printClientHistory({$row['id']})\">Print</button></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No client history found.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function printClientHistory(id) {
            window.open('printClientHistory.php?id=' + id, '_blank');
        }
    </script>
</body>
</html>
