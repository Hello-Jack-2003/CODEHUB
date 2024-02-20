<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validate</title>
</head>
<body>

</body>
</html>
<?php
    ob_start(); // Start output buffering
    session_start();

    include('connection.php');

    $sname = 'localhost';
    $suname = 'root';
    $spasswd = '';
    $dbase = 'codehub';

    // new connection
    $conn = new mysqli($sname, $suname, $spasswd, $dbase);

    // check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Use password_verify for secure password comparison
            if (password_verify($password, $user['password'])) {
                echo "Logged in successfully";
                $_SESSION['email'] = $email;
                if ($email == "admin@gmail.com") 
                {
                    header('Location: admin.php');
                } 
                else {
                    header('Location: dashboard.php');
                }
                exit();
            } else {
                //echo "<script>alert('Invalid Details');</script>";
                header('Location: login.html');
                exit();
            }
        } 
        else {
            echo "Invalid Details . ";
            header('Location: login.html');
            exit();
        }
    }

    if (isset($_POST['signin'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Use password_hash for secure password storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['email'] = $email;
            echo"User Added Successfully . ";
            header('Location: login.html');
            exit();
        } else {
            echo "Error: " . $stmt->error;
            header('Location: login.html');
            exit();
        }
    }

    $conn->close();
    ob_end_flush(); // Flush and turn off output buffering
?>
