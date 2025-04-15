<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../assets/connection/connection.php'); 

$order_id = $_GET['order_id'] ?? null;
$advance_amount = $_GET['advance_amount'] ?? null;

$selIdQ = "SELECT o.userid, u.phone,u.name,u.email FROM orders o JOIN users u ON o.userid = u.userid WHERE order_id=$order_id";
$selIdE = $conn->query($selIdQ);
if ($selId = $selIdE->fetch_assoc()) {
    $userid = $selId['userid'];
    $phone = $selId['phone'];
    $name = $selId['name'];
    $usermail=$selId['email'];
}


if(isset($_POST['submitBtn'])){
    $refund_transaction = $_POST['refund_transaction'];
    $updateQuery = "UPDATE orders 
                    SET refund_status = 'paid', 
                        refund_amount = $advance_amount, 
                        refund_transaction = '$refund_transaction' 
                    WHERE order_id = $order_id";
    
    if ($conn->query($updateQuery) === TRUE) {

        $to = $usermail;
            $subject = "Refund Processed for Your Order #$order_id";
            $message = "
                Dear Customer,

                Your refund of amount ₹$advance_amount has been successfully processed.
                Transaction ID: $refund_transaction.

                Thank you for your patience.

                Regards,
                Sugared Team";
            $headers = "From: noreply@sugared.com";

            if (mail($to, $subject, $message, $headers)) {
                ?>
                <script>alert("Refund processed and email sent successfully.");</script>
                <?php
            } else {
                echo "Refund updated, but failed to send email.";
            }
    
            $deleteOrderQ = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
            $deleteOrderQ->bind_param("i", $order_id);
            $deleteOrderQ->execute();
            ?><script>window.location="./baker_orders.php";</script><?php 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Payment</title>
    <link rel="stylesheet" href="assets/css/theme.css"> <!-- Sugared Theme CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .refund-container {
            width: 50%;
            margin: 50px auto;
            background: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .details p {
            font-size: 16px;
            margin: 5px 0;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #980000;
            color: #ffffff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #ccc;
            border: 1px solid #980000;
            color: #980000;
        }
    </style>
</head>
<body>
    <div class="refund-container">
        <h1>Refund Payment</h1>
        <div class="details">
            <p><strong>User ID:</strong> <?php echo $userid; ?></p>
            <p><strong>Name:</strong> <?php echo $name; ?></p>
            <p><strong>Phone Number:</strong> <?php echo $phone; ?></p>
            <i>contact the user or use upi payment using the mobile number to pay back user advance payment</i>
        </div>
        <form method="POST">
            <label for="refund_amount">Refund Amount:</label>
            <p style="font-size: 40px;margin:4px;"><strong><?php echo $advance_amount; ?>/-</strong></p>

            <label for="refund_method">Refund Transaction id:</label>
            <input type="number" placeholder="Enter payment transaction id" name="refund_transaction" required>

            <button type="submit" name="submitBtn">Submit Refund</button>
        </form>
    </div>
</body>
</html>
