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

$selectQ = "SELECT report_id, reporter_id, `type`, `name`, email, phone, `subject`, `message` FROM reports";
$selectE = $conn->query($selectQ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <style>
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
            <h1>Issue Reports</h1>
            <table>
                <tr><th>Report_ID</th><th>Reporter_ID</th><th>ReporterType</th><th>Name</th><th>Email</th><th>Phone</th></tr>
                <?php while ($select = $selectE->fetch_assoc()) { ?>
                <tr onclick="showUserModal(<?php echo htmlspecialchars(json_encode($select)); ?>)">
                    <td><?php echo $select['report_id']; ?></td>
                    <td><?php echo $select['reporter_id']; ?></td>
                    <td><?php echo $select['type']; ?></td>
                    <td><?php echo $select['name']; ?></td>
                    <td><?php echo $select['email']; ?></td>
                    <td><?php echo $select['phone']; ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeUserModal()">&times;</span>
            <h2 id="modalUserName"></h2>
            <div style="display: flex; justify-content:space-around;">
            <p><strong>Report ID:</strong> <span id="modalReportId"></span></p>
            <p><strong>Reporter ID:</strong> <span id="modalUserId"></span></p>
        
            <p><strong>Type:</strong> <span id="modalUserType"></span></p>
            </div>
            <div style="display: flex; justify-content:space-around;">
            <p><strong>Email:</strong> <span id="modalUserEmail"></span></p>
            <p><strong>Phone:</strong> <span id="modalUserPhone"></span></p>
            </div>
            <div style="height:fit-content; width:fit-content;background-color:#595959; padding-inline: 20px;
            padding-block:5px;
            min-width: 400px;">
            <p style="margin: 0px;"><strong>Subject:</strong> <span id="modalUserSubject"></span></p>
            </div>
            <div style="height:fit-content; width:fit-content;background-color:#595959; padding-inline: 20px;
            padding-block:5px;
            min-width: 400px; margin-top:20px">
            <p><strong>Message:</strong> <span id="modalUserMessage"></span></p>
            </div>
            <div class="modal-buttons">
                <button class="btn btn-mail" onclick="sendUserMail()">Mail User</button>
                <button class="btn btn-delete" onclick="removeReport()">Remove Report</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function showUserModal(reportData) {
        document.getElementById("modalReportId").innerText = reportData.report_id;
        document.getElementById("modalUserId").innerText = reportData.reporter_id;
        document.getElementById("modalUserName").innerText = reportData.name;
        document.getElementById("modalUserType").innerText = reportData.type;
        document.getElementById("modalUserEmail").innerText = reportData.email;
        document.getElementById("modalUserPhone").innerText = reportData.phone;
        document.getElementById("modalUserSubject").innerText = reportData.subject;
        document.getElementById("modalUserMessage").innerText = reportData.message;

        document.getElementById("userModal").style.display = "flex";
    }

    function closeUserModal() {
        document.getElementById("userModal").style.display = "none";
    }

    function sendUserMail() {
        const email = document.getElementById("modalUserEmail").innerText;
        window.location.href = `mailto:${email}`;
    }

    function removeReport() {
        const reportId = document.getElementById("modalReportId").innerText;
        if (confirm("Are you sure you want to delete this report?")) {
            $.ajax({
                url: '../assets/component/delete_report.php', 
                type: 'POST',
                data: { report_id: reportId },
                success: function(response) {
                    alert(response);
                    location.reload(); 
                },
                error: function() {
                    alert("Error deleting report.");
                }
            });
        }
    }
    </script>
</body>
</html>