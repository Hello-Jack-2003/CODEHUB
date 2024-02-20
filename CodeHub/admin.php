<?php
    session_start();
    include('connection.php');
    if (!isset($_SESSION['email']) == "admin@gmail.com") {
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

    // Handle user addition
    if (isset($_POST['add_user'])) {
        $new_email = $_POST['new_email'];
        $new_password = $_POST['new_password'];

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $new_email, $hashed_password);

        if ($stmt->execute()) {
            echo "User Added Successfully . ";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Handle user removal
    if (isset($_POST['remove_user'])) {
        $email_to_remove = $_POST['email_to_remove'];

        $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
        $stmt->bind_param("s", $email_to_remove);

        if ($stmt->execute()) {
            echo "User Removed Successfully . ";
        } else {
            echo "User Not Exsist . ";
            echo "Error: " . $stmt->error;
        }
    }

    //change admin password
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
                    echo "Passwored Changed Successfully . ";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/css/admin_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1,h2 {
            color: #333;
            background-color: aqua;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,th,td {
            border: 1px solid #333;
        }

        th,td {
            padding: 10px;
            text-align: left;
        }

        form {
            margin-top: 20px;
        }

        input[type="email"],
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

    </style>
</head>
<body>

    <h1>Welcome to Admin Panel</h1>

    <h2>Manage Users</h2>
    <table border="1px">
        <tr>
            <th>Email</th> 
        </tr>
        <?php
            $result = $conn->query("SELECT * FROM users");

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['email'] . "</td>";
                echo "</tr>";
            }
        ?>
    </table>

    <br>

    <h2>Add User</h2>
    <form action="admin.php" method="post">
        Email: <input type="email" name="new_email" required>
        Password: <input type="password" name="new_password" required>
        <input type="submit" name="add_user" value="Add User">
    </form>

    <br>

    <h2>Remove User</h2>
    <form action="admin.php" method="post">
        Email to Remove: <input type="email" name="email_to_remove" required>
        <input type="submit" name="remove_user" value="Remove User">
    </form>

    <br>
    <h2>Change Admin Password</h2>
    <form action="admin.php" method="post">
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
    $conn->close();
?>
