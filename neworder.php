<!DOCTYPE html>
<html lang="en-us">
    <head>
        <meta charset="utf-8">
        <title>Meme Mugs | New Order</title>
        <link href="https://fonts.googleapis.com/css?family=Righteous|Roboto+Mono" rel="stylesheet">
        <link href="css/main.css" rel="stylesheet">
        <link rel="icon" href="images/mug.ico" type="image/x-icon">
    </head>
        <?php include("essential/header.php");?>
        <body>
            <div class="content">
                <h2>New Order</h2>
                <?php
                    require_once("database.php");
                    
                    // Check connection
                    if ($db->connect_error) {
                        die("Connection failed: " . $db->connect_error);
                    } 
                    
                    //define variables as empty values
                    
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $customer = $product = $quantity = $entry = $stockErr = "";
                        
                        $customer = $_POST['customer'];
                        $product = $_POST['product'];
                        $quantity = $_POST['quantity'];
                        
                        $sql = "SELECT CONCAT(IFNULL(CONCAT(title, ' '), ''), first_name, ' ', IFNULL(CONCAT(middle_name, ' '),''), last_name, ' ', IFNULL(suffix, '')) AS name FROM Customers
                            WHERE customer_id = {$customer}";
                        
                        $sql2 = "SELECT name, size FROM Inventory
                            where product_id = {$product}";
                        
                        $sql3 = "INSERT INTO Orders (customer_id, product_id, quantity)
                            VALUES ('$customer', '$product', '$quantity')";
                        
                        $productname = $db->query($sql2);
                        $customername = $db->query($sql);
                        while($row = $productname->fetch_assoc()) {
                            $productNameEcho = $row["name"];
                            $productSizeEcho = $row["size"];
                        }
                         while($row2 = $customername->fetch_assoc()) {
                            $customerNameEcho = $row2["name"];
                        }
                        
                        $productStock = $db->query("SELECT in_stock FROM Inventory
                                WHERE product_id = '$product'");
                        while ($row3 = $productStock->fetch_assoc()) {
                            $productStock2 = $row3["in_stock"];
                        }
                                
                        $newStock = $productStock2 - $quantity;
                        if ($newStock < 0) {
                            $stockErr = "<b><small class='asterisk'>*</small>Item is out of stock</b><br>";
                        } else {
                            $updateStock = "UPDATE Inventory
                                            SET in_stock = '$newStock'
                                            WHERE product_id = '$product'";
                        }
                        
                        if (empty($stockErr)) {
                            $db->query($sql3);
                            $db->query($updateStock);
                            $entry = "<b>New order: {$quantity} of {$productNameEcho} ({$productSizeEcho}) for {$customerNameEcho}</b></br>";
                        } else {
                            $entry = "<b>Order could not be placed</b></br>";
                        }
                    }
                ?>
                <!-- New Customer Form -->
                <form class="data-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <label for="customer">Customer<small class="asterisk">*</small></label>
                    <select class="customerDropdown" name="customer" id="customer" required>
                        <option value="" disabled selected hidden>----------------------------</option>
                        <?php
                            $query = "SELECT customer_id, CONCAT(IFNULL(CONCAT(title, ' '), ''), first_name, ' ', IFNULL(CONCAT(middle_name, ' '),''), last_name, ' ', IFNULL(suffix, '')) AS name FROM Customers
                                ORDER BY last_name";
                             $res = $db->query($query); 
                        
                           while ($row = $res->fetch_assoc()) 
                           {
                             echo '<option value=" '.$row['customer_id'].' "> '.$row['name'].' </option>';
                           }
                        ?>
                    </select>
                    <br>
                    <label for="product">Product<small class="asterisk">*</small></label>
                    <select class="productDropdown" name="product" id="product" required>
                        <option value="" disabled selected hidden>--------------------------------</option>
                        <?php
                            $query = "SELECT product_id, name, size FROM Inventory
                                ORDER BY name";
                             $res = $db->query($query); 
                        
                           while ($row = $res->fetch_assoc()) 
                           {
                             echo '<option value=" '.$row['product_id'].' "> '.$row['name'].' ('.$row['size'].') </option>';
                           }
                        ?>
                    </select>
                    <br>
                    <label for="quantity">Quantity<small class="asterisk">*</small></label>
                    <input type="number" name="quantity" id="quantity" min="0" step="1" oninput="validity.valid||(value='');" required>
                    <br>
                    <input style="margin-bottom:0.25em" type="submit" name="Submit" value="Submit"><br>
                    <input style="margin-top:0.25em" type="reset" name="Reset"><br><br>
                    <?php echo $stockErr;?><br>
                    <?php echo $entry;?><br>
                    <b style="font-size:0.8em;cursor:default"><small class="asterisk">*</small>Required Fields</b>
                </form>
                <?php $db->close();?>
            </div>
            <script src="js/nav.js"></script>
            <script src="js/formreset.js"></script>
        </body>
        <?php include("essential/footer.php");?>
</html>