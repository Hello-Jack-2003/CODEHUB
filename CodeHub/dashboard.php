<?php
    session_start();

    if (!isset($_SESSION['email'])) {
        header('Location: login.html');
        exit();
    }

    $sname = 'localhost';
    $suname = 'root';
    $spasswd = '';
    $dbase = 'codehub';

    $conn = new mysqli($sname, $suname, $spasswd, $dbase);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        /* dashboard.css */

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1, h2 {
            color: #333;
            background-color: aqua;
        }

        form {
            margin-top: 20px;
        }

        input[type="password"] {
            width: 200px;
            padding: 5px;
            margin-right: 10px;
        }

        input[type="submit"] {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Add more styles as needed */

    </style>
</head>
<body>
    
    <h1>Welcome to User Dashboard</h1>

    <h2>Change Password</h2>
    <form action="dashboard.php" method="post">
        Old Password: <input type="password" name="old_password" required>
        New Password: <input type="password" name="new_password" required>
        Conform Password: <input type="password" name="con_password" required>
        <input type="submit" name="change_password" value="Change Password">
    </form>

    <br>

    <form action="logout.php" method="post">
        <input type="submit" value="Log Out">
    </form>

</body>
</html>

<?php
    // Handle password change
    if (isset($_POST['change_password'])) 
    {
        //password manager 
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $conform_password = $_POST['con_password'];

        //hashed password
        $hased_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        $email = $_SESSION['email'];

        // Fetch the hashed password from the database
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($org_password);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($old_password, $org_password)) 
        {

            if($new_password==$conform_password)
            {

                $stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt2->bind_param("ss", $hased_new_password, $email);

                if ($stmt2->execute()) {
                    echo "Password Changed Successfully . ";
                } else {
                    echo "Error: " . $stmt2->error;
                }
            }
            else
            {
                echo "Password Not Matched : Please Type Properly . ";
            }
        } 
        else {
            echo "Invalid Details . ";
        }
    }

    $conn->close();
?>
