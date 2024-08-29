<?php
session_start();
@include 'config.php';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5(mysqli_real_escape_string($conn, $_POST['password'])); // Hash the input password using MD5

    // Fetch user based on email and hashed password
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND pass = '$password'") or die("Select Error: " . mysqli_error($conn));
    $row = mysqli_fetch_assoc($result);

    if (is_array($row) && !empty($row)) {
        $_SESSION['valid'] = $row['email'];
        $_SESSION['username'] = $row['name'];
        $_SESSION['user_type'] = $row['user_type'];
        $_SESSION['id'] = $row['id'];

        // Redirect based on user type
        if ($row['user_type'] == 'nutritionist' && $row['user_type'] == 'fitness-coach') {
            header('Location: diet_plan.php');
        }
        exit();
    } else {
        echo "<div class='error-msg'>
              <p>Incorrect email or password!</p>
              </div><br>";
        echo "<a href='index.php'><button class='btn'>Go Back</button></a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Login</title>
</head>
<body>
    <div class="form-container">
        <form action="" method="post">
            <h3>Login Now</h3>
            <?php if(!isset($_POST['submit'])) { ?>
            <div class="field input">
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="field input">
                <input type="password" name="password" required placeholder="Enter your password">
            </div>
            <div class="field">
                <input type="submit" name="submit" value="Login Now" class="form-btn">
            </div>
            <p>Don't have an account? <a href="register_form.php">Register now</a></p>
            <?php } ?>
        </form>
    </div>
</body>
</html>
