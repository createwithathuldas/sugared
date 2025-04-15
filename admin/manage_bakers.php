<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../assets/connection/connection.php'); 

if (!isset($_SESSION['aid'])) {
    header("Location: ../quest/login.php");
    exit;
}

$selectQ = "SELECT b.bakerid, b.name, b.email, b.phone, b.fssai_number, b.bank_name, b.branch_name, b.ifsc, b.account_number, ba.street, ba.district, ba.pin, ba.lat, ba.lon, bp.profile_pic, COUNT(p.product_id) AS product_count 
            FROM bakers b
            JOIN baker_address ba ON ba.bakerid = b.bakerid
            JOIN baker_profile bp ON bp.bakerid = b.bakerid
            LEFT JOIN products p ON p.userid = b.bakerid
            GROUP BY b.bakerid";
$selectE = $conn->query($selectQ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bakers</title>
    <style>
        /* Basic styling */
        body { margin: 0; font-family: Arial, sans-serif; background-color: #2c2c2c; color: #fff; }
        nav { height: 88px; width: 100%; background-color: rgba(0, 0, 0, 0.7); display: flex; align-items: center; padding: 0 20px; }
        nav a.navbar { display: flex; align-items: center; color: #fff; text-decoration: none; }
        nav img { height: 70px; }
        #navAdmin { font-size: 30px; margin-left: 10px; }
        .dashboard-container { display: flex; }
        .sidebar { width: 400px; background-color: #333; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar a { color: #ccc; text-decoration: none; display: block; margin: 15px 0; font-size: 25px; }
        .sidebar a:hover { color: #fff; }
        .content { flex: 1; padding: 30px; }
        table { width: 100%; color: #ccc; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #444; padding: 10px; text-align: left; }
        th { background-color: #444; }
        tr:hover { cursor: pointer; background-color: #555; }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #333;
            padding: 20px;
            border-radius: 5px;
            width: 90%;
            max-width: 600px;
            color: #fff;
        }
        .modal-content h2 { margin-top: 0; }
        .modal-close {
            float: right;
            font-size: 24px;
            color: #fff;
            cursor: pointer;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn { padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; }
        .btn-mail { background-color: #4caf50; color: #fff; }
        .btn-delete { background-color: #f44336; color: #fff; }
    </style>
</head>
<body>
    <div class="nav_container">
        <nav>
            <a class="navbar" href="./dashboard.php">
                <img src="/sugared/PROJECT/assets/img/logo.png" alt="SUGARED">
                <span id="navAdmin">Admin</span>
            </a>
        </nav>
    </div>

    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="./dashboard.php">Dashboard</a>
            <a href="./manage_users.php">Manage Users</a>
            <a href="./manage_bakers.php">Manage Bakers</a>
            <a href="./reports.php">Reports</a>
            <a href="./login.php">Add new Admin</a>
        </div>

        <div class="content">
            <h1>Manage Bakers</h1>
            <table>
                <tr><th>bakerId</th><th>Profile</th><th>Name</th><th>FSSAI Number</th><th>Email</th><th>Phone</th></tr>
                <?php while ($select = $selectE->fetch_assoc()) { ?>
                <tr onclick="showModal(<?php echo htmlspecialchars(json_encode($select)); ?>)">
                    <td><?php echo $select['bakerid']; ?></td>
                    <td><img src="<?php echo $select['profile_pic']; ?>" alt="Profile Pic" style="height: 40px; width: 40px;"></td>
                    <td><?php echo $select['name']; ?></td>
                    <td><?php echo $select['fssai_number']; ?></td>
                    <td><?php echo $select['email']; ?></td>
                    <td><?php echo $select['phone']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>


    <div id="bakerModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 id="modalName"></h2>
            <p><strong>Email:</strong> <span id="modalEmail"></span></p>
            <p><strong>Phone:</strong> <span id="modalPhone"></span></p>
            <p><strong>Address:</strong> <span id="modalAddress"></span></p>
            <p><strong>FSSAI Number:</strong> <span id="modalFssai"></span></p>
            <p><strong>Bank Details:</strong> <span id="modalBank"></span></p>
            <div class="modal-buttons">
                <button class="btn btn-mail" onclick="sendMail()">Mail Baker</button>
                <button id="deleteButton" class="btn btn-delete">Delete Baker</button>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showModal(bakerData) {
            document.getElementById('modalName').innerText = bakerData.name;
            document.getElementById('modalEmail').innerText = bakerData.email;
            document.getElementById('modalPhone').innerText = bakerData.phone;

            $('#deleteButton').data('bakerid', bakerData.bakerid);

            document.getElementById('modalAddress').innerText = bakerData.street + ', ' + bakerData.district + ', ' + bakerData.pin;
            document.getElementById('modalFssai').innerText = bakerData.fssai_number;
            document.getElementById('modalBank').innerText = bakerData.bank_name + ', ' + bakerData.branch_name + ', ' + bakerData.ifsc + ', Acc: ' + bakerData.account_number;
            document.getElementById('bakerModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('bakerModal').style.display = 'none';
        }

        function sendMail() {
            const email = document.getElementById('modalEmail').innerText;
            window.location.href = `mailto:${email}`;
        }

        $('#deleteButton').on('click', function() {
            const bakerid = $(this).data('bakerid');
            deleteBaker(bakerid);
        });


        function deleteBaker(bakerid) {
    if (confirm("Are you sure you want to delete this baker?")) {
        $.ajax({
            url: 'delete_baker.php',
            type: 'POST',
            data: { bakerid: bakerid },
            success: function(response) {
                if (response === "success") {
                    alert("Baker deleted successfully!");
                    $('#bakerModal').hide();  // Close modal
                    loadBakers();  // Refresh the list of bakers
                } else {
                    alert("Error deleting baker: " + response);
                }
            },
            error: function(xhr, status, error) {
                alert("AJAX error: " + error);
            }
        });
    }
}


    </script>
</body>
</html>
