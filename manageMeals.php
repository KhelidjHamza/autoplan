<?php
session_start();
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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit();
}

// Handle form submission for adding or updating a meal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mealId']) && !empty($_POST['mealId'])) {
        // Update meal
        $stmt = $conn->prepare("UPDATE meals SET name = ?, description = ?, category = ?, calories = ?, protein = ?, fat = ?, carbs = ? WHERE id = ?");
        $stmt->bind_param("ssssdssi", $name, $description, $category, $calories, $protein, $fat, $carbs, $id);
        $id = $_POST['mealId'];
    } else {
        // Add new meal
        $stmt = $conn->prepare("INSERT INTO meals (name, description, category, calories, protein, fat, carbs) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdss", $name, $description, $category, $calories, $protein, $fat, $carbs);
    }
    
    $name = $_POST['mealName'];
    $description = $_POST['mealDescription'];
    $category = $_POST['mealCategory'];
    $calories = $_POST['mealCalories'];
    $protein = $_POST['mealProtein'];
    $fat = $_POST['mealFat'];
    $carbs = $_POST['mealCarbs'];

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Handle deletion of a meal
if (isset($_POST['mealId']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM meals WHERE id = ?");
    $stmt->bind_param("i", $id);
    $id = $_POST['mealId'];

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Meals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .meals-container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:nth-of-type(2) {
            background-color: #dc3545;
        }
        button:nth-of-type(2):hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="meals-container">
        <h1>Manage Meals</h1>
        <form id="mealForm">
            <input type="hidden" id="mealId" name="mealId">
            <label for="mealName">Meal Name:</label>
            <input type="text" id="mealName" name="mealName" required>
            <label for="mealDescription">Meal Description:</label>
            <textarea id="mealDescription" name="mealDescription" required></textarea>
            <label for="mealCategory">Category:</label>
            <select id="mealCategory" name="mealCategory" required>
                <option value="breakfast">Breakfast</option>
                <option value="lunch">Lunch</option>
                <option value="snacks">Snacks</option>
                <option value="dinner">Dinner</option>
                <option value="traditional">Traditional</option>
                <option value="fast-food">Fast Food</option>
            </select>
            <label for="mealCalories">Calories:</label>
            <input type="text" id="mealCalories" name="mealCalories" required>
            <label for="mealProtein">Protein (g):</label>
            <input type="text" id="mealProtein" name="mealProtein" required>
            <label for="mealFat">Fat (g):</label>
            <input type="text" id="mealFat" name="mealFat" required>
            <label for="mealCarbs">Carbohydrates (g):</label>
            <input type="text" id="mealCarbs" name="mealCarbs" required>
            <input type="submit" value="Save Meal">
        </form>

        <table id="mealsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Calories</th>
                    <th>Protein (g)</th>
                    <th>Fat (g)</th>
                    <th>Carbs (g)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM meals");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr data-id='{$row['id']}'>
                            <td>{$row['name']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['category']}</td>
                            <td>{$row['calories']}</td>
                            <td>{$row['protein']}</td>
                            <td>{$row['fat']}</td>
                            <td>{$row['carbs']}</td>
                            <td>
                                <button onclick=\"editMeal({$row['id']}, '{$row['name']}', '{$row['description']}', '{$row['category']}', '{$row['calories']}', '{$row['protein']}', '{$row['fat']}', '{$row['carbs']}')\">Edit</button>
                                <button onclick=\"deleteMeal({$row['id']})\">Delete</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No meals found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('mealForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch(location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error saving meal');
                }
            })
            .catch(error => console.error('Error:', error));
        });

        function editMeal(id, name, description, category, calories, protein, fat, carbs) {
            document.getElementById('mealId').value = id;
            document.getElementById('mealName').value = name;
            document.getElementById('mealDescription').value = description;
            document.getElementById('mealCategory').value = category;
            document.getElementById('mealCalories').value = calories;
            document.getElementById('mealProtein').value = protein;
            document.getElementById('mealFat').value = fat;
            document.getElementById('mealCarbs').value = carbs;
        }

        function deleteMeal(id) {
            if (confirm('Are you sure you want to delete this meal?')) {
                fetch(location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ mealId: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting meal');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>
</html>
