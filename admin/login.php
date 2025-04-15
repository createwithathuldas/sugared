<?php 
session_start();
include('../assets/connection/connection.php'); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO admin (email, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Admin added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="nav_container">
    <style>
        body{
            padding: 0px;
            margin: 0px;
            background-color: rgba(38,38,38);
        }
        nav{
            height: 88px;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10);
            margin: 0px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar img{
            height: 100px;
            width: auto;
            padding-left: 20px;
        }
        a{
            text-decoration: none;
        }
        #navAdmin{
            font-size: 30px;
            text-decoration:none;
            color: #fff;
            position: relative;
            top: -37px;
            left: 10px;
            font-family: Georgia, 'Times New Roman', Times, serif;
        }
        .container{
            width: 100%;
            height: fit-content;
            min-height: 90vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card{
            width: 400px;
            min-height: 50vh;
            height: fit-content;
            background-color: #3c3c3cb8;
            border-radius: 30px;
            backdrop-filter: blur();
            color: #fff;
            padding-left: 20px;
            padding-right: 20px;
            padding-bottom: 30px;
            font-family: monospace;
            font-size: 20px;
        }
        .FormAddAdmin{
            display: flex;
            justify-content: center;
        }
        form input{
            width: 380px;
            height: 30px;
            border-radius: 5px;
            margin-top: 15px;
        }
        form button{
            width: 170px;
            height: 40px;
            color:#fff;
            background-color: #7e0000;
            border: none;
            border-radius: 8px;
            margin-top: 20px;
        }
        form button:hover{
            background-color: #420000;
        }
    </style>
    <nav>
        <a class="navbar" href="<?php echo $home; ?>"><img src="/sugared/PROJECT/assets/img/logo.png" alt="SUGARED"><span id="navAdmin">/ Admin</span></a>
        <div class="items">
        </div>
    </nav>
</div>


<div class="container">
    <div class="card">
        <h1>Add Admin</h1>
        <form method="post" id="FormAddAdmin">
            <label for="email">Email </label><br>
            <input type="email" name="email" id="Email" required><br>

            <label for="password">Password</label><br>
            <input type="password" name="password" id="Password" required><br>

            <label for="confirm_password">Confirm Password</label><br>
            <input type="password" name="confirm_password" id="ConfirmPassword" required><br>

            <button type="button" id="subBtn">Submit</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('subBtn').addEventListener('click', function () {
        const email = document.getElementById('Email').value;
        const password = document.getElementById('Password').value;
        const confirmPassword = document.getElementById('ConfirmPassword').value;

        if (!email || !password || !confirmPassword) {
            alert("All fields are required.");
            return;
        }

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return;
        }

        document.getElementById('FormAddAdmin').submit();
    });
</script>

</body>
</html>