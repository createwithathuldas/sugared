<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../assets/connection/connection.php');

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['add_item'])) {
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $flavors = $_POST['flavors'];
    $variants = $_POST['variants'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $ingrediants = $_POST['ingredients'];
    $preparation_time = $_POST['preparation_time'];
    $special_instructions = $_POST['special_instructions'];
    $available_days = $_POST['available_days'];
    $available_days_str = implode(',', $available_days);
    $lead_time = $_POST['lead_time'];
    $veg=$_POST['veg'];
    $delivery_status=$_POST['delivery'];

    $bakerid = $_SESSION['bid'];

    $insert_item = "INSERT INTO `products`(`userid`, `category`, `subcategory`, `product_name`, `description`, `flavors`, `variants`, `size`, `price`, `ingrediants`, `preparation_time`, `special_instructions`, `available_days`, `lead_time`,`veg`,`delivery_status`) VALUES ($bakerid, '$category', '$subcategory', '$product_name', '$description', '$flavors', '$variants', '$size', '$price', '$ingrediants', '$preparation_time', '$special_instructions', '$available_days_str', '$lead_time','$veg','$delivery_status')";
    $result = $conn->query($insert_item);

    if ($result > 0) {
        echo "<script>alert('New item added successfully');</script>";
        
        // Fetch the inserted product's ID
        $sel_pid = "SELECT product_id FROM products WHERE userid=$bakerid ORDER BY product_id DESC LIMIT 1";
        $pid_result = $conn->query($sel_pid);
        $pid_row = $pid_result->fetch_assoc();
        $product_id = $pid_row['product_id'];

        if (isset($_FILES['files'])) {
            // Define upload directory
            $upload_dir = '../assets/files/baker/';

            // Create the directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_count = count($_FILES['files']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                $file_name = $_FILES['files']['name'][$i];
                $file_tmp = $_FILES['files']['tmp_name'][$i];
                $file_size = $_FILES['files']['size'][$i];
                $file_error = $_FILES['files']['error'][$i];

                $file_path = $upload_dir . basename($file_name);

                if ($file_error === 0) {
                    if (move_uploaded_file($file_tmp, $file_path)) {
                        echo "<script>alert('File $file_name uploaded successfully');</script>";
                        // Insert file info into the database if needed
                        $insert_image = "INSERT INTO `product_images`(`product_id`,`bakerid`, `file_name`, `file_path`) VALUES ($product_id,$bakerid, '$file_name', '$file_path')";
                        $conn->query($insert_image);
                    } else {
                        echo "<script>alert('Failed to upload $file_name');</script>";
                    }
                } else {
                    echo "<script>alert('Error uploading $file_name');</script>";
                }
            }
        } else {
            echo "<script>alert('No files uploaded');</script>";
        }
    } else {
        echo "<script>alert('Failed adding item');</script>";
    }
}

// $conn->close();
?>



<html>
    <head>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
            width: 50vw;
            height: 80vh;
            box-shadow: #ce3434 0px 20px 30px -10px;
            background-color: #171717;
            border-radius: 25px;
            margin-left: 100px;
            margin-top: 100px;
            overflow-y: auto;

        }
        body{
            padding: 0px;
            margin: 0px;
            background-color: #1e1e1e;
        }
        #add_product{
          width: 55px;
          height: 55px;
          background-color: #a40000;border:#a40000;
          font-size: 40px;
          position: absolute;
          left: 38em;
          top: 18em;
          display: flex;
          justify-content: center;
          align-items: center;
        }
        .item-even {
    background-color: #0b0b0b;
}

.item-odd {
    background-color: #313131;
}


        .item{
          width: 50vw;
          height: 65px;
          font-size: 18px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          color: white;
          font-family: Arial, Helvetica, sans-serif;
          overflow: hidden;
          padding-inline: 19px;
        }
        .item img{
          height: 3em;
          width: 3em;
          border-radius: 45px;
          flex-shrink: 0;
          object-fit: fill;
          object-position: center;
        }
        .item button{
          background-color: #171717;
          color: #ce3434;
          border: #ce3434;
        }
.show-product-image {
    width: 100px;
    height: auto;
    object-fit: cover;
}

.modal-product-image {
    height: 400px;
    width: auto;
    margin: 0 auto;
    display: block;
    object-fit: cover;
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
      <style>#products{ color:red;}</style>
    </div>
    <div class="all">
        
        <div class="product">
        <?php  
$bakerid = $_SESSION['bid'];
$item_show_query = "SELECT p.product_id, p.product_name, p.subcategory, p.description, p.flavors, p.variants, p.size, p.price, p.ingrediants, p.preparation_time, p.special_instructions, p.available_days, p.lead_time FROM products p WHERE p.userid = $bakerid;";
$result = $conn->query($item_show_query);

if ($result->num_rows > 0) {
  $index = 0;
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $modal_id = 'modal_' . $product_id; // Unique ID for each modal
        
        // Fetch the first image to display in the item list
        $image_query = "SELECT file_path FROM product_images WHERE product_id = $product_id LIMIT 1";
        $image_result = $conn->query($image_query);
        $image_row = $image_result->fetch_assoc();
        $image_path = $image_row['file_path'];

        $bg_class = ($index % 2 == 0) ? 'item-even' : 'item-odd';

        echo "<div class='item $bg_class'>";
        echo "<img src='$image_path' alt='" . $row['product_name'] . "' class='show-product-image'>";
        echo "<p class='show-product-name'>" . $row['product_name'] . "</p>";
        echo "<p class='show-product-subcategory'>" . $row['subcategory'] . "</p>";
        echo "<p class='show-product-price'>" . $row['price'] . "</p>";
        echo "<button id='more' type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#$modal_id'>more..</button>";
        echo "</div>";

        // Fetch all images related to this product for the modal carousel
        $image_query = "SELECT file_path FROM product_images WHERE product_id = $product_id";
        $image_result = $conn->query($image_query);
        $image_slides = '';
        $indicators = '';
        $active_class = 'active';
        $slide_index = 0;

        while ($image_row = $image_result->fetch_assoc()) {
            $image_slides .= "<div class='carousel-item $active_class'>
                                <img src='" . $image_row['file_path'] . "' class='d-block w-100 modal-product-image' alt='Product Image'>
                              </div>";
            $indicators .= "<button type='button' data-bs-target='#carousel_$modal_id' data-bs-slide-to='$slide_index' class='$active_class' aria-current='true'></button>";
            $active_class = ''; // only first item should be active
            $slide_index++;
        }

        // Modal for each product
        echo "<div class='modal fade' id='$modal_id' tabindex='-1' aria-labelledby='exampleModalLongTitle' aria-hidden='true'>
                <div class='modal-dialog modal-lg' role='document'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='exampleModalWideTitle'>Product Details</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            <!-- Carousel for product images -->
                            <div id='carousel_$modal_id' class='carousel slide' data-bs-ride='carousel'>
                                <div class='carousel-indicators'>
                                    $indicators
                                </div>
                                <div class='carousel-inner'>
                                    $image_slides
                                </div>
                                <button class='carousel-control-prev' type='button' data-bs-target='#carousel_$modal_id' data-bs-slide='prev'>
                                    <span class='carousel-control-prev-icon' aria-hidden='true'></span>
                                    <span class='visually-hidden'>Previous</span>
                                </button>
                                <button class='carousel-control-next' type='button' data-bs-target='#carousel_$modal_id' data-bs-slide='next'>
                                    <span class='carousel-control-next-icon' aria-hidden='true'></span>
                                    <span class='visually-hidden'>Next</span>
                                </button>
                            </div>
                            
                            <!-- Product details -->
                            <p><strong>Product Name:</strong> " . $row['product_name'] . "</p>
                            <p><strong>Subcategory:</strong> " . $row['subcategory'] . "</p>
                            <p><strong>Description:</strong> " . $row['description'] . "</p>
                            <p><strong>Flavors:</strong> " . $row['flavors'] . "</p>
                            <p><strong>Variants:</strong> " . $row['variants'] . "</p>
                            <p><strong>Size Options:</strong> " . $row['size'] . "</p>
                            <p><strong>Price per Size:</strong> " . $row['price'] . "</p>
                            <p><strong>Ingredients:</strong> " . $row['ingrediants'] . "</p>
                            <p><strong>Preparation Time:</strong> " . $row['preparation_time'] . "</p>
                            <p><strong>Special Instructions:</strong> " . $row['special_instructions'] . "</p>
                            <p><strong>Available Days:</strong> " . $row['available_days'] . "</p>
                            <p><strong>Lead Time for Orders:</strong> " . $row['lead_time'] . "</p>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                            <button type='button' class='btn btn-danger' onclick='confirmDelete($product_id)'>Delete</button>
                        </div>
                    </div>
                </div>
            </div>";

            $index++;
    }
}
?>

<script>
function confirmDelete(productId) {
    if (confirm("Are you sure you want to delete this product? This action cannot be undone.")) {
        window.location.href = '/sugared/PROJECT/assets/component/delete_product.php?product_id=' + productId;
    }
}
</script>



             


</div>
        </div>

        <!-- Upload product Button trigger modal -->
    <button id="add_product" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalLong">
      +
  </button>

  <!-- Product upload Modal -->
  <div class="modal fade" id="exampleModalLong" tabindex="-1" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">New Product</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div><form method="post" enctype="multipart/form-data">
              <div class="modal-body">
                  
                  <select name="category" id="pcat" placeholder="Category" class="form-select">
                      <option value="cake" selected>Cake</option>
                      <option value="bread">Bread</option>
                      <option value="pastries">Pastries</option>
                      <option value="cookie">Cookies</option>
                      <option value="muffins">Muffins</option>
                      <option value="pie">Pies</option>
                      <option value="bagels">Bagels</option>
                      <option value="chocolate">Chocolates</option>
                      <option value="candy">Candy</option>
                      <option value="others">Others</option>
                  </select>   <br>
                  <label for="subcategory">Sub-category:</label>
                  <input type="text" id="subcategory" name="subcategory" required placeholder="eg:(plum cakes)">
                <br>
                  <label for="product_name">Product Name:</label>
                  <input type="text" id="product_name" name="product_name" required placeholder="eg:(classic old sweet plum with rum)">
                <br>
                <label for="delivery">Deliverable</label>
                <input type="radio" name="delivery" value="Deliverable">
                <label for="pickup">Pickup</label>
                <input type="radio" name="delivery" value="Pickup">
                
                <br>
                <label for="veg">Vegiterian </label>
                <input type="radio" name="veg" value="veg">
                <label for="veg">Vegiterian </label>
                <input type="radio" name="veg" value="nonveg">
                <br>
                  <label for="description">Description:</label>
                  <textarea id="description" name="description" required placeholder="eg:(baked with love and soaked plum with 90's rum)"></textarea>
                <br>
                  <label for="flavors">Flavors:</label>
                  <input type="text" id="flavors" name="flavors" placeholder="eg:(plum kuchen,plum buckle,plum upside)">
                <br>
                  <label for="variants">Variants:</label>
                  <input type="text" id="variants" name="variants" placeholder="eg:(sweet,no-Sugar,Gluten-Free)">
                <br>
                  <label for="size">Size Options:</label>
                  <input type="text" id="size" name="size" required placeholder="eg:(500g,1kg,2kg,3kg)">
                <br>
                  <label for="price">Price per Size:</label>
                  <input type="text" id="price" name="price" required placeholder="eg:(Rs.130 - Rs.800)">
                <br>
                  <label for="ingredients">Ingredients List:</label>
                  <textarea id="ingredients" name="ingredients" required placeholder="eg:(Wheat,Plum,Cinnamon,Baking Powder,Rum)"></textarea>
                <br>
                  <label for="preparation_time">Preparation Time (hours):</label>
                  <input type="text" id="preparation_time" name="preparation_time" required placeholder="eg:(2:00hr-3:00hr)">
                <br>
                  <label for="shelf_life">Shelf Life (days):</label>
                  <input type="text" id="shelf_life" name="shelf_life" required placeholder="eg:(5 months)">
                <br>
                  <label for="allergens">Allergens:</label>
                  <input type="text" id="allergens" name="allergens" placeholder="eg:(Gluten,Nuts,Milk)">
                <br>
                  <label for="special_instructions">Special Instructions:</label>
                  <textarea id="special_instructions" name="special_instructions" placeholder="eg:(Keep in a dry area)"></textarea>
                <br>
                  <label for="product_images">Product Images:</label>
                  <input type="file" id="product_images" name="files[]" multiple required>
                <br>
                  <label for="available_days">Available Days:</label>
                  <select id="available_days" name="available_days[]" multiple>
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                    <option value="saturday">Saturday</option>
                    <option value="sunday">Sunday</option>
                  </select>
                <br>
                  <label for="lead_time">Lead Time for Orders (hours):</label>
                  <input type="text" id="lead_time" name="lead_time" required placeholder="eg:(Order before atleast 12 hours before)">
                
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary" name="add_item">Add Item</button>
              </div>
              </form>
          </div>
         </div>
       </div>



    </div>
</div>
<?php include('../assets/component/Footer.php'); ?>
</body>
</html>
