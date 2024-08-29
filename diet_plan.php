<?php
@include 'config.php';
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the JSON data from the request
$inputData = json_decode(file_get_contents('php://input'), true);

// Extract data from the input
$userId = $inputData['userId'];
$userName = $inputData['userName'];
$userPhone = $inputData['userPhone'];
$userType = $inputData['userType'];
$clientName = $inputData['clientName'];
$clientPhone = $inputData['clientPhone'];
$clientAge = $inputData['clientAge'];
$clientWeight = $inputData['clientWeight'];
$clientHeight = $inputData['clientHeight'];
$clientCalorieGoal = $inputData['clientCalorieGoal'];
$numberOfMeals = $inputData['numberOfMeals'];
$clientGoal = $inputData['clientGoal'];
$clientExercise = $inputData['clientExercise'];
$clientDiseases = $inputData['clientDiseases'];

// Insert user data into users table
$userQuery = "INSERT INTO users (id, name, phone, type) VALUES ('$userId', '$userName', '$userPhone', '$userType')";
if (!$conn->query($userQuery)) {
    echo json_encode(['error' => $conn->error]);
    $conn->close();
    exit();
}

// Insert client data into clients table
$clientQuery = "INSERT INTO clients (name, phone, age, weight, height, calorie_goal, number_of_meals, goal, exercise_intensity, diseases, user_id)
                VALUES ('$clientName', '$clientPhone', '$clientAge', '$clientWeight', '$clientHeight', '$clientCalorieGoal', '$numberOfMeals', '$clientGoal', '$clientExercise', '$clientDiseases', '$userId')";

if ($conn->query($clientQuery)) {
    // Return a success response
    echo json_encode(['message' => 'Client data inserted successfully.']);
} else {
    echo json_encode(['error' => $conn->error]);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diet Plan Generator</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
   
    <div class="form-container">
        <form id="userClientForm">
            <h1>Diet Plan Generator</h1>
            <h3>User Information</h3>
            <label for="userId">User ID:</label>
            <input type="text" id="userId" name="userId" required>
            <label for="userName">User Name:</label>
            <input type="text" id="userName" name="userName" required>
            <label for="userPhone">User Phone:</label>
            <input type="text" id="userPhone" name="userPhone" required>
            <label for="userType">Type of User:</label>
            <select id="userType" name="userType" required>
                <option value="">Select User Type</option>
                <option value="nutritionist">Nutritionist</option>
                <option value="fitness-coach">Fitness Coach</option>
            </select>

            <h3>Client Information</h3>
            <label for="clientName">Client Name:</label>
            <input type="text" id="clientName" name="clientName" required>
            <label for="clientPhone">Client Phone:</label>
            <input type="text" id="clientPhone" name="clientPhone" required>
            <label for="clientAge">Client Age:</label>
            <input type="number" id="clientAge" name="clientAge" required>
            <label for="clientWeight">Client Weight (kg):</label>
            <input type="number" id="clientWeight" name="clientWeight" required>
            <label for="clientHeight">Client Height (cm):</label>
            <input type="number" id="clientHeight" name="clientHeight" required>
            <label for="clientCalorieGoal">Desired Calorie Intake (kcal):</label>
            <input type="number" id="clientCalorieGoal" name="clientCalorieGoal" required>
            <label for="numberOfMeals">Number of Meals:</label>
            <select id="numberOfMeals" name="numberOfMeals" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <label>Client Goal:</label>
            <div id="clientGoal">
                <button type="button" value="lose-weight">Lose Weight</button>
                <button type="button" value="gain-weight">Gain Weight</button>
                <button type="button" value="maintain-weight">Maintain Weight</button>
            </div>

            <label for="clientExercise">Exercise Intensity:</label>
            <select id="clientExercise" name="clientExercise" required>
                <option value="everyday">Every Day</option>
                <option value="2-times-week">2 Times a Week</option>
                <option value="not-everyday">Not Every Day</option>
                <option value="none">None</option>
            </select>
            <label>Client Diseases:</label>
            <div id="clientDiseases">
                <button type="button" value="diabetes">Diabetes</button>
                <button type="button" value="blood-pressure">Blood Pressure</button>
                <button type="button" value="cholesterol">Cholesterol</button>
                <button type="button" value="heart-disease">Heart Disease</button>
                <button type="button" value="none">None</button>
            </div>

            <input type="submit" value="Generate Diet Plan">
        </form>
    </div>

    <div id="dietPlan" class="diet-plan"></div>

    <script>
        document.getElementById('userClientForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const userId = document.getElementById('userId').value;
            const userName = document.getElementById('userName').value;
            const userPhone = document.getElementById('userPhone').value;
            const userType = document.getElementById('userType').value;
            const clientName = document.getElementById('clientName').value;
            const clientPhone = document.getElementById('clientPhone').value;
            const clientAge = document.getElementById('clientAge').value;
            const clientWeight = document.getElementById('clientWeight').value;
            const clientHeight = document.getElementById('clientHeight').value;
            const clientCalorieGoal = document.getElementById('clientCalorieGoal').value;
            const clientExercise = document.getElementById('clientExercise').value;
            const numberOfMeals = document.getElementById('numberOfMeals').value;
            
            // Collect selected goal
            const clientGoal = Array.from(document.querySelectorAll('#clientGoal button.active'))
                .map(button => button.value)
                .join(',');
            
            // Collect selected diseases
            const diseases = Array.from(document.querySelectorAll('#clientDiseases button.active'))
                .map(button => button.value)
                .join(',');
        
            const clientData = {
                userId,
                userName,
                userPhone,
                userType,
                clientName,
                clientPhone,
                clientAge,
                clientWeight,
                clientHeight,
                clientCalorieGoal,
                numberOfMeals,
                clientGoal,
                clientExercise,
                clientDiseases: diseases
            };
        
            fetch('api/dietPlan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(clientData)
            })
            .then(response => response.json())
            .then(data => {
                displayDietPlan(data);
            })
            .catch(error => {
                console.error('Error fetching diet plan:', error);
            });
        });
        
        // Toggle active class on goal and disease buttons
        document.querySelectorAll('#clientGoal button, #clientDiseases button').forEach(button => {
            button.addEventListener('click', () => {
                button.classList.toggle('active');
            });
        });
        
        function displayDietPlan(data) {
            const dietPlanDiv = document.getElementById('dietPlan');
            dietPlanDiv.innerHTML = '';
        
            data.meals.forEach(meal => {
                const mealDiv = document.createElement('div');
                mealDiv.className = 'diet-meal';
                mealDiv.innerHTML = `
                    <h3>${meal.name}</h3>
                    <p>${meal.description}</p>
                    <div class="chart-container">
                        <canvas id="chart-${meal.id}"></canvas>
                    </div>
                `;
                dietPlanDiv.appendChild(mealDiv);
        
                const ctx = document.getElementById(`chart-${meal.id}`).getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Calories', 'Fat', 'Carbs', 'Protein'],
                        datasets: [{
                            data: [meal.calories, meal.fat, meal.carbs, meal.protein],
                            backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56'],
                            hoverBackgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56']
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });
            });
        }
        </script>
</body>
</html>
