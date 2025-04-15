<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../assets/connection/connection.php');
$bakerid=$_SESSION['bid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baker Orders</title>
    <style>
         .container{
            display: grid;
            grid-template-columns: 1fr 4fr;
            grid-template-areas: 
            "nav body";
        }
        .navbar{
            grid-area: nav;
        }
        .all{
            grid-area: body;
            margin-top: 88px;
            width: 1600px;
            height: 800px;
            position: relative;
            left: -70px;
            box-shadow: #ce3434 0px 20px 30px -10px;
            background-color: #171717;
            border-radius: 25px;
            margin-left: 100px;
            margin-top: 100px;
            overflow-y: auto;
            padding-top: 5px;
            padding-inline: 5px; 
            overflow-y: scroll;       }
        body{
            padding: 0px;
            margin: 0px;
            background-color: #1e1e1e;}

        /* From Uiverse.io by LightAndy1 */ 
.wrapper {
  --font-color-dark: #bebfc5;
  --font-color-light: #bebfc5;
  --bg-color: #212121;
  --main-color: #0c0c0c;
  --secondary-color: #141414;
  position: relative;
  width: 350px;
  height: 36px;
  background-color: var(--bg-color);
  border: 2px solid var(--main-color);
  border-radius: 34px;
  display: flex;
  flex-direction: row;
  box-shadow: 4px 4px var(--main-color);
  position: relative;
  left: 600px;
  margin-bottom: 10px;
}

.option {
  margin-right: 5px;
  width: 180.5px;
  height: 28px;
  position: relative;
  top: 2px;
  left: 2px;
  border-radius: 34px;
  transition: 0.25s cubic-bezier(0, 0, 0, 1);
}

.option:last-child {
  margin-right: 4px;
}

.option:hover {
  background-color: var(--secondary-color);
}

.option:hover .span {
  color: var(--font-color-light);
}

.input {
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  appearance: none;
  cursor: pointer;
}

.btn {
  width: 100%;
  height: 100%;
  border-radius: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.span {
  color: var(--font-color-dark);
}

.input:checked + .btn {
  background-color: var(--main-color);
  transition: 0.2s cubic-bezier(0, 0, 0, 1);
}

.input:checked + .btn .span {
  color: var(--font-color-light);
  transition: 0.25s cubic-bezier(0, 0, 0, 1);
}


table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 1rem;
    text-align: left;
    border-radius: 8px;
    overflow: hidden;
}

table th, table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
}

/* Table header styling */
table th {
    background-color: #f8f9fa;
    color: #333;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* Table row styling */
table tr {
    transition: background-color 0.3s;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}
table tr:nth-child(odd){
    background-color: #f1f1f1;
}
table tr:hover {
    background-color: #ce3434;
}

/* Image styling */
.orderImg {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
}

/* Status text colors */
.deliverystatus {
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: bold;
}

.deliverystatus.pending {
    color: #ff9800;
    background-color: #fff3e0;
}

.deliverystatus.delivered {
    color: #4caf50;
    background-color: #e8f5e9;
}

.deliverystatus.cancelled {
    color: #f44336;
    background-color: #ffebee;
}

td p, td div {
    margin: 0;
}

td p {
    font-size: 0.9rem;
    color: #555;
}

/* Responsive styling */
@media screen and (max-width: 768px) {
    table, table tr, table td, table th {
        display: block;
        width: 100%;
    }

    table tr {
        margin-bottom: 15px;
    }

    table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }

    table td:before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.8rem;
        color: #333;
    }
}


.modal {
  display: none; /* Hidden by default */
  position: fixed; 
  z-index: 1; 
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto; 
  background-color: rgb(0,0,0); 
  background-color: rgba(0,0,0,0.4); 
  padding-top: 60px;

}

.modal-content {
  background-color: #fefefe;
  margin: 0px; 
  padding: 20px;
  border-radius: 45px;
  border: 1px solid #888;
  width: 80%; 
  position: relative;
  left: 10%;
  top: 10%;
}

.modal-footer {
  padding: 10px;
  text-align: right;
}
.modal-close{
  font-size: 30px;
  font-family: Arial, Helvetica, sans-serif;
  font-weight: 500;
  text-decoration: none;
  color: #950000;
  position: relative;
  top: -32px;
  left: 50%;
}
.modalbody{
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
}
    </style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
 $(document).ready(function() {
    // Initially show the acceptedOrders and hide requestedOrders
    $('.acceptedOrders').show();
    $('.requestedOrders').hide();

    // Add event listener for radio button change
    $('input[name="btn"]').change(function() {
        // Check which radio button is selected and toggle visibility
        if ($('input[name="btn"]:checked').val() === 'acceptedOrders') {
            $('.acceptedOrders').show();
            $('.requestedOrders').hide();
        } else if ($('input[name="btn"]:checked').val() === 'requestedOrders') {
            $('.requestedOrders').show();
            $('.acceptedOrders').hide();
        }
    });



    $('.order-row').click(function() {
    // Get the order ID from the clicked row
    var orderId = $(this).data('order-id');
    
    // Send the order ID to the PHP backend via AJAX to set session variable
    $.ajax({
        url: '../assets/component/bakerCartSetOrderId.php', // Backend PHP file to handle the request
        type: 'POST',
        data: { order_id: orderId },
        success: function(response) {
            // After setting the session, fetch the order details based on the session variable
            $.ajax({
                url: '../assets/component/fetchOrderDetails.php', // New PHP file to fetch order details
                type: 'GET',
                success: function(orderDetails) {
                    // Display the fetched order details in the modal
                    $('#orderDetails').html(orderDetails);

                    // Show the modal
                    $('#orderModal').show();
                }
            });
        }
    });
});




    // Close the modal when the close button is clicked
    $('.modal-close').click(function() {
        $('#orderModal').hide();
    });


  


    

});
</script>

</head>
<body>
<?php 
include('../assets/component/header.php');
?>
<div class="container">
     <div class="navbar">
      <?php include('../assets/component/baker_navbar.php'); ?>
      <style>#orders{ color:red;}</style>
    </div>
        <div class="all">

        <div class="wrapper">
  <div class="option">
    <input checked="" value="acceptedOrders" name="btn" type="radio" class="input" />
    <div class="btn">
      <span class="span">Live Orders</span>
    </div>
  </div>
  <div class="option">
    <input value="requestedOrders" name="btn" type="radio" class="input" />
    <div class="btn">
      <span class="span">Requested Orders</span>
    </div>
  </div>
</div>

         <div class="acceptedOrders"> 
            <table><tr><th>slno</th><th>customer</th><th>Image</th><th>Name</th><th>Delivery Date & Time</th><th>customer Location</th><th>Pickup/Delivery</th><th>Order Status</th></tr>
            <?php 
             $q1q="SELECT o.order_id,p.product_id,u.userid,u.name,p.product_name,p.delivery_status,o.delivery_date,o.delivery_time,o.quantity,ua.city,ua.district,ua.pin FROM orders o JOIN products p ON o.product_id=p.product_id JOIN users u ON o.userid=u.userid JOIN user_address ua ON u.userid=ua.userid WHERE o.bakerid=$bakerid AND o.baker_accept_status=1 AND (o.order_delivered != 1 OR o.order_delivered IS NULL)";
             $q1e=$conn->query($q1q);
             $j=1;
             while($q1=$q1e->fetch_assoc()){
                $pid=$q1['product_id'];
                $q1iq="SELECT file_path FROM product_images WHERE product_id=$pid LIMIT 1";
                $q1ie=$conn->query($q1iq);
                $q1i=$q1ie->fetch_assoc();
            ?>

            <tr class="order-row" data-order-id="<?php echo $q1['order_id']; ?>">
                <td><?php echo $j++; ?></td>
                <td><?php echo $q1['name'];?></td>
                <td><img src="<?php echo $q1i['file_path']; ?>" alt="Product Image" class="orderImg"></td>
                <td><?php echo $q1['product_name']; ?></td>
                <td><?php echo $q1['delivery_time'] . " " . $q1['delivery_date']; ?></td>
                <td><?php echo $q1['delivery_status'] == "Deliverable" ? "Order will be delivered to your location" : $q1['city'] . ", " . $q1['district'] . ", " . $q1['pin']; ?></td>
                <td><?php echo $q1['delivery_status']; ?></td>
                <td><?php echo $q1['quantity']; ?></td>
            </tr>
            <?php } ?>
        </table>
        </div>


        <div class="requestedOrders">
        <table>
            <tr><th>slno</th><th>customer</th><th>Image</th><th>Name</th><th>Delivery Date & Time</th><th>customer Location</th><th>Pickup/Delivery</th><th>Number of Order</th></tr>
        
            <?php 
             $q2q="SELECT o.order_id,p.product_id,u.userid,u.name,p.product_name,p.delivery_status,o.delivery_date,o.order_amount,o.delivery_time,o.quantity,ua.city,ua.district,ua.pin FROM orders o JOIN products p ON o.product_id=p.product_id JOIN users u ON o.userid=u.userid JOIN user_address ua ON u.userid=ua.userid WHERE o.bakerid=$bakerid AND o.baker_accept_status=0";
             $q2e=$conn->query($q2q);
             $j=1;
             while($q2=$q2e->fetch_assoc()){
                $pid=$q2['product_id'];
                $q2iq="SELECT file_path FROM product_images WHERE product_id=$pid LIMIT 1";
                $q2ie=$conn->query($q2iq);
                $q2i=$q2ie->fetch_assoc();
            ?>

            <tr class="order-row" data-order-id="<?php echo $q2['order_id']; ?>">
                <td><?php echo $j++;?></td>
                <td><?php echo $q2['name'];?></td>
                <td><img src="<?php echo $q2i['file_path']; ?>" alt="Product Image" class="orderImg"></td>
                <td><?php echo $q2['product_name']; ?></td>
                <td><?php echo $q2['delivery_time'] . " " . $q2['delivery_date']; ?></td>
                <td><?php echo $q2['delivery_status'] == "Deliverable" ? "Order will be delivered to your location" : $q2['city'] . ", " . $q2['district'] . ", " . $q2['pin']; ?></td>
                <td><?php echo $q2['delivery_status']; ?></td>
                <td><?php echo $q2['quantity']; ?></td>
            </tr>
            <?php } ?>
        </table>
        </div>
     </div>
</div>
<!-- modal 1 -->
<div id="orderModal" class="modal">
  <div class="modal-content">
             <a href="#!" class="modal-close btn">X</a>
    <h2>Order Details</h2>
    <div class="modalbody" id="orderDetails">
    
    </div>
  </div>
</div>

<?php
  include('../assets/component/Footer.php');

?>
</body>
</html>