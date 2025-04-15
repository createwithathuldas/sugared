<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../assets/connection/connection.php');


if (isset($_SESSION['bid'])) {
    $bakerid = $_SESSION['bid'];
    
    $sql = "SELECT 
                b.bakerid,
                b.name,
                b.email,
                b.password,
                b.phone,
                b.fssai_number,
                b.bank_name,
                b.branch_name,
                b.ifsc,
                b.account_number,
                b.upi_id,
                bp.baker_address_id,
                bp.street,
                bp.city,
                bp.district,
                bp.pin,
                bp.lat,
                bp.lon,
                ba.profile_pic_name AS baker_name,
                ba.moto,
                ba.profile_pic
            FROM 
                bakers b
            JOIN 
                baker_profile ba ON b.bakerid = ba.bakerid
            JOIN 
                baker_address bp ON b.bakerid = bp.bakerid
            WHERE 
                b.bakerid = '$bakerid'";
    $result = $conn->query($sql);
    
    
    if ($result && $result->num_rows > 0) {
        $result_row = $result->fetch_assoc();
    } else {
        
        $result_row = null;
    }
} else {
   
    $result_row = null;
    echo "No baker ID found. Please log in.";
    
    exit;
}

?>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            padding: 0px;
            margin: 0px;
            background-color: #1e1e1e;
        }
        .container {
            display: grid;
            grid-template-columns: 1fr 4fr;
            grid-template-areas: 
            "nav body";
            background-color: black;
            padding: 100px;
            height: fit-content;
        }
        .navbar {
            grid-area: nav;
        }
        .all {
            grid-area: body;
            width: 70vw;
            height: 80vh;
            margin-left: 50px;
            padding-left: 20px;
            border-radius: 25px;
            box-shadow: #ce3434 0px 20px 30px -10px;
            margin-top: 50px;
            background-color: #1c1c1c;
            overflow-y: scroll;
            overflow-x: scroll;
        }
        .profile {
            display: grid;
            grid-template-columns: 1fr 4fr;
            grid-template-rows: auto auto;
            grid-template-areas: 
                "pic name"
                "pic fssai"
                "moto moto";
            gap: 20px;
        }

        .profile_pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            align-self: start;
            grid-area: pic;
            margin-top: 10px;
        }

        .profile_name {
            color: white;
            font-size: 50px;
            font-family: monospace;
            align-self: center;
            grid-area: name;
        }

        .profile_certificate {
            color: white;
            font-size: 20px;
            font-family: 'Lucida Sans', Geneva, Verdana, sans-serif;
            align-self: center;
            grid-area: fssai;
            position: relative;
            bottom: 75px;
        }

        .moto {
            color: white;
            font-size: 16px;
            font-family: 'Lucida Sans', Geneva, Verdana, sans-serif;
            align-self: center;
            grid-area: moto;
            text-align: center; 
            position: relative;
            bottom: 75px;
        }
        p {
            color: white;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            font-size: 17px;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
            padding-inline: 20px;
        }

        .details_title {
            font-size: 23px;
            font-weight: 400;
        }

        .details_data {
            font-size: 20px;
            font-weight: 100;
            text-align: right;
        }

        .details_item {
            display: flex;
            justify-content: space-between;
        }

        .address_details {
            grid-column: 1 / -1; /* Span both columns */
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr; /* Adjust the number of columns if needed */
            gap: 20px;
            padding: 10px 0;
            border-top: 1px solid #444;
            margin-top: 20px;
        }

        .address_details p {
            margin: 0;
        }

        /* Modal Styles */
        .modal {
            display: none; 
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
            background-color: #1c1c1c;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 40%; 
            border-radius: 25px;
            color: white;
        }
        .modal-content form{
            display: block;
            width: fit-content;
            padding-inline:80px;
            padding-block: 50px;
            font-size: 150%;
        }
        .modal-content form input{
            width: 500px;
            height: 40px;
            border-radius: 15px;
            color: #000;
            font-size: 18px;
            font-family: monospace;
            margin-block: 20px;
        }

        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        .story{
            background-color: #fff0;
            width: fit-content;
            height: fit-content;
            border:solid  1px #ce3434;
            font-size: 18px;
            color: #ce3434;
            font-family: fantasy;
           
        }
        .story:hover{
            background-color: #600;
        }
        #fileInput {
            display: none;
        }
    .storyMedia{
        width: 100%;
        height: 160px;
    }
    .mediaImg{
        width: 130px;
        height: 130px;
        border-radius: 100%;
        padding-left: 0px;
        border: solid 2px red;
        background-size: contain;
    }
    #storyFile{
        display: none;
    }
.modal2 {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8); /* Gray background with opacity */
    justify-content: center;
    align-items: center;
}

.modal2-content {
    max-width: 90%;
    max-height: 90%;
}

.modal2 img {
    width: 100%;
    height: auto;
}

    </style>
</head>
<body>
<?php 
include('../assets/component/header.php');
?>
<div class="container">
     <div class="navbar">
      <?php include('../assets/component/baker_navbar.php'); ?>
      <style>#dashboard{ color:red;}</style>
    </div>
    <div class="all">
        <div class="profile">

            <?php  $currentDateTime = date('Y-m-d H:i:s');
                  $storyCheckQ="SELECT  story_image_path,upload_time,expiration_time,is_active FROM baker_stories WHERE bakerid=$bakerid";
                  $storyCheckE=$conn->query($storyCheckQ);
                  while($storyCheck = $storyCheckE->fetch_assoc()){
                    $storyPath=$storyCheck['story_image_path'];
                    ?>
             <div class="storyMedia">
              <img src="<?php echo $storyPath; ?>" class="mediaImg" onclick="openModal('<?php echo $storyPath; ?>')">
            </div><?php }
                $deleteExpiredQ = "DELETE FROM baker_stories WHERE bakerid = $bakerid AND expiration_time <= '$currentDateTime'";
                $conn->query($deleteExpiredQ);
            ?>
         <form action="../assets/component/upload_story.php" method="POST" enctype="multipart/form-data" id="storyForm">
             <input type="file" name="story" id="storyFile" style="display:none;" accept="image/*,video/*" required>
             <button type="button" class="story" onclick="selectStoryFile()">+ add story</button>
        </form>
        <script>
    function selectStoryFile() {
        document.getElementById('storyFile').click();
    }
    document.getElementById('storyFile').onchange = function() {
        document.getElementById('storyForm').submit();
    };

    let modal;
let modalImage;
let closeTimeout;

document.addEventListener('DOMContentLoaded', () => {
    modal = document.createElement('div');
    modal.className = 'modal2';
    modal.innerHTML = `
        <div class="modal2-content">
            <img src="" id="modalImage">
        </div>
    `;
    document.body.appendChild(modal);
    modalImage = document.getElementById('modalImage');

    // Close modal when clicking outside the image
    modal.addEventListener('click', closeModal);
});

function openModal(imageSrc) {
    modalImage.src = imageSrc;
    modal.style.display = 'flex';

    // Close the modal automatically after 5 seconds
    clearTimeout(closeTimeout);
    closeTimeout = setTimeout(closeModal, 5000);
}

function closeModal() {
    modal.style.display = 'none';
    modalImage.src = '';
    clearTimeout(closeTimeout); // Clear any remaining timeout
}

</script>


<img src="<?php 
    if ($result_row && !empty($result_row['profile_pic'])) {
        echo $result_row['profile_pic'];
    } else {
        echo '../assets/img/chef_profile.webp'; 
    } ?>" 
    alt="profile_pic" 
    class="profile_pic" id="profile_pic">

<form action="../assets/component/baker_pic_update.php" method="POST" enctype="multipart/form-data" id="profile_pic_form">
    <input type="file" id="fileInput" accept="image/*" name="profile_pic" style="display:none;">
    <input type="hidden" name="bakerid" value="<?php echo $bakerid; ?>">
    <button type="submit" style="position: relative; top:-330px; display:none;" id="upload_pic_button">Upload Profile Pic</button>
</form>

<script>
    var profile_pic = document.getElementById("profile_pic");
    var fileInput = document.getElementById("fileInput");

    profile_pic.onclick = function() {
        fileInput.click();
        document.getElementById('upload_pic_button').style.display = "block";
    };
</script>



            <button id="editProfileBtn" style="position: relative; left:1200px;bottom:300px; width:40px;height:40px;border-radius: 13px; background-color: #1e1e1e; "><i class="fa-solid fa-pencil"></i></button>
            
            <h2 class="profile_name">
                <?php echo $result_row['name'] ?? 'Baker Name'; ?>
            </h2>
            
            <p class="profile_certificate"> +91 
                <?php echo $result_row['phone'] ?? 'Certificate details'; ?>
            </p>
            
            <p class="moto">
                <?php echo $result_row['moto'] ?? 'Baker Motto'; ?>
            </p>
        </div>

        <div class="details">
            <div class="details1">
                <p class="details_title">Email</p>
                <p class="details_data"><?php echo $result_row['email']; ?></p>
                <p class="details_title">fssai_number</p>
                <p class="details_data"><?php echo $result_row['fssai_number']; ?></p>
                
            </div>
            <div class="address_details">
                <p style="font-size: 27px; color:gray;">Address</p>
                <p> </p>
                <p class="details_title">Street</p>
                <p class="details_data"><?php echo $result_row['street']; ?></p>
                <p class="details_title">City</p>
                <p class="details_data"><?php echo $result_row['city']; ?></p>
                <p class="details_title">District</p>
                <p class="details_data"><?php echo $result_row['district']; ?></p>
                <p class="details_title">Pin</p>
                <p class="details_data"><?php echo $result_row['pin']; ?></p>
                <p class="details_title">Latitude</p>
                <p class="details_data"><?php echo $result_row['lat']; ?></p>
                <p class="details_title">Longitude</p>
                <p class="details_data"><?php echo $result_row['lon']; ?></p>
                
            </div>
            <div class="bankDetails">
                <hr>
                <?php if($result_row['account_number']){?>
               <div class="bankDetailsDisplay">
                <p><strong>Bank Name : </strong><?php echo $result_row['bank_name']; ?></p>
                <p><strong>Branch : </strong><?php echo $result_row['branch_name']; ?></p>
                <p><Strong>Bank Account Number : </Strong><?php echo $result_row['account_number']; ?> </p>
                <p><strong>IFSC Code</strong><?php echo $result_row['ifsc']; ?></p>
                <p><strong>UPI PAYMENT ID : </strong><?php echo $result_row['upi_id']; ?></p>
                </div>
                <?php }else{?>
                <div class="addBankDetails">
                    <form method="post" class="BankDetailsForm">
                        <label for="BankName">Bank Name</label>
                        <select name="BankName" id="BankName" required>
                            <option value="sbi">State Bank Of India</option>
                            <option value="icici">ICICI Bank</option>
                            <option value="pnb">Punjab National Bank</option>
                            <option value="federal">Federal Bank</option>
                            <option value="union">Union Bank</option>
                        </select><br>
                        <label for="BranchName">Branch Name</label>
                        <input type="text" name="BranchName" id="BranchName" required><br>
                        <label for="AccountNumber">Account Number</label>
                        <input type="number" name="AccountNumber" id="AccountNumber" required><br>
                        <label for="IFSC">IFSC Code</label>
                        <input type="text" name="IFSC" id="IFSC" maxlength="11" required><br>
                        <label for="UPI">UPI ID</label>
                        <input type="text" name="UPI" id="UPI" required><br>
                        <button type="submit" name="account_submit_btn" >Submit</button><br>
                    </form>
                </div><?php } ?>
            </div>
        </div>
    </div>
</div>
<style>
    .bankDetails{
        width: 1304px;
    }
    .bankDetailsDisplay{
        font-size: 27px;
        display: block;
    }
    /* .addBankDetails{
        display: none;
    } */
    label{
        font-size: 20px;
        color: #fff;
        font-weight: 200;
    }
    .BankDetailsForm input{
        background-color: #888;
        height: 30px;
        width: 500px;
        border-radius: 45px;
        border: none;
        margin-bottom: 10px;
        padding-inline: 20px;
        
    }
    .BankDetailsForm button{
        font-size: 18px;
        font-weight: 900;
        width: 100px;
        height: 40px;
        color:#fff;
        background-color: #950000;
        border-radius: 10px;
        border:1px solid #990000
    }
    .BankDetailsForm select{
        background-color: #1e1e1e;
        color: #fff;
        height: 30px;
        width: fit-content;
        padding-inline: 10px;
        border: 0.3px solid wheat;
        margin-bottom: 20px;
    }

</style>
<!-- Modals -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeProfileModal">&times;</span>
        <h2>Edit Profile</h2>
        <form id="editProfileForm" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="display_name" name="update_name" value="<?php echo $result_row['name'] ?? 'Baker Name'; ?>" required><br>

            <label for="phone">Phone:</label>
            <input type="text" id="display_phone" name="update_phone" value="<?php echo $result_row['phone'] ?? 'Phone Number'; ?>" required><br>

            <label for="motto">Motto:</label>
            <input type="text" id="display_motto" name="update_motto" value="<?php echo $result_row['moto'] ?? 'Baker Motto'; ?>"><br>

            <label for="email">Email:</label>
            <input type="text" id="display_email" name="update_email" value="<?php echo $result_row['email'] ?? 'Baker Email'; ?>"><br>

            <label for="fssai">Fssai Certificate Number:</label>
            <input type="text" id="display_fssai" name="update_fssai" value="<?php echo $result_row['fssai_number'] ?? 'Certificate number'; ?>"><br>

            <label for="city">City:</label>
            <input type="text" id="display_city" name="update_city" value="<?php echo $result_row['city'] ?? 'City name'; ?>"><br>

            <label for="street">Street:</label>
            <input type="text" id="display_street" name="update_street" value="<?php echo $result_row['street'] ?? 'street name'; ?>"><br>

            <label for="district">District:</label>
            <input type="text" id="display_district" name="update_district" value="<?php echo $result_row['district'] ?? 'located District'; ?>"><br>

            <label for="pin">Postal code:</label>
            <input type="text" id="display_pin" name="update_pin" value="<?php echo $result_row['pin'] ?? 'Postal Code'; ?>"><br>

            <input type="submit" value="Update Profile" name="edit_profile_submit"><br>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
   
    $('.BankDetailsForm').on('submit', function(e) {
    e.preventDefault(); // Prevent the form from submitting normally

    // Collect form data
    let bankName = $('#BankName').val();
    let branchName = $('#BranchName').val();
    let accountNumber = $('#AccountNumber').val();
    let ifsc = $('#IFSC').val();
    let upiId = $('#UPI').val();

    // Perform the AJAX request
    $.ajax({
        url: '/sugared/PROJECT/assets/component/baker_account_submit.php', // The PHP file where you handle the form submission
        method: 'POST',
        data: {
            account_submit_btn: true, // Set to true to check in PHP
            BankName: bankName,
            BranchName: branchName,
            AccountNumber: accountNumber,
            IFSC: ifsc,
            UPI: upiId
        },
        success: function(response) {
            // If successful, reload the page
            console.log(response); // Optional: log the server response for debugging
            window.location.reload(); // Reload the page
        },
        error: function(xhr, status, error) {
            console.error("AJAX error: ", error);
            console.log("Response text: ", xhr.responseText);
        }
    });
});


    $('#editProfileForm').on('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    $.ajax({
        url: '/sugared/PROJECT/assets/component/update_baker_profile.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Update the page with new details
                $('#display_name').text(response.name);
                $('#display_phone').text(response.phone);
                $('#display_motto').text(response.motto);
                $('#display_email').text(response.email);
                $('#display_fssai').text(response.fssai); 
                $('#display_city').text(response.city); 
                $('#display_street').text(response.street);
                $('#display_pin').text(response.pin);
                $('#display_district').text(response.district); 
                $('#profileModal').hide();
                alert('Profile updated successfully!');
            } else {
                alert('An error occurred while updating the profile.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ', status, error);
            console.log('Response Text: ', xhr.responseText); // For debugging
        }
    });
});


    $('#closeProfileModal').click(function() {
        $('#profileModal').hide();
    });

    window.onclick = function(event) {
        if (event.target == document.getElementById('profileModal')) {
            document.getElementById('profileModal').style.display = 'none';
        }
    };
});
</script>

<script>
    var profileModal = document.getElementById("profileModal");
    var editProfileBtn = document.getElementById("editProfileBtn");
    var closeProfileModal = document.getElementById("closeProfileModal");
    
    editProfileBtn.onclick = function() {
        profileModal.style.display = "block";
    }
    closeProfileModal.onclick = function() {
        profileModal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == profileModal) {
            profileModal.style.display = "none";
        }
    }

</script>

<?php 
include('../assets/component/Footer.php');
?>

</body>
</html>
