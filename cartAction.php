<?php
// initialize shopping cart class
include 'Cart.php';
$cart = new Cart;

// include database configuration file
include 'dbConfig.php';
if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
    if($_REQUEST['action'] == 'addToCart' && !empty($_REQUEST['id'])){
        $productID = $_REQUEST['id'];
        // get product details
        $query = $db->query("SELECT * FROM products WHERE id = ".$productID);
        $row = $query->fetch_assoc();
        $itemData = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'qty' => 1
        );

        $insertItem = $cart->insert($itemData);
        $redirectLoc = $insertItem?'viewCart.php':'index.php';
        header("Location: ".$redirectLoc);
    }elseif($_REQUEST['action'] == 'updateCartItem' && !empty($_REQUEST['id'])){
        $itemData = array(
            'rowid' => $_REQUEST['id'],
            'qty' => $_REQUEST['qty']
        );
        $updateItem = $cart->update($itemData);
        echo $updateItem?'ok':'err';die;
    }elseif($_REQUEST['action'] == 'removeCartItem' && !empty($_REQUEST['id'])){
        $deleteItem = $cart->remove($_REQUEST['id']);
        header("Location: viewCart.php");
    }elseif($_REQUEST['action'] == 'placeOrder' && $cart->total_items() > 0 && !empty($_SESSION['username'])){
        // insert order details into database
        $insertOrder = $db->query("INSERT INTO orders (customer_id, total_price, created, modified) VALUES ('".$_SESSION['username']."', '".$cart->total()."', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."')");

        if($insertOrder){
            $orderID = $db->insert_id;
            $sql = '';
            // get cart items
            $cartItems = $cart->contents();
            foreach($cartItems as $item){
                $sql .= "INSERT INTO order_items (order_id, product_id, quantity) VALUES ('".$orderID."', '".$item['id']."', '".$item['qty']."');";
            }

              //this is what ive added
              // ill be coming later in the afternoon to link the ids to relevant items
            require 'phpmailer/PHPMailerAutoload.php';

            $errors = [] ;

                 $mail = new PHPMailer;

                 //$mail->SMTPDebug = 3;                               // Enable verbose debug output

                 $mail->isSMTP();                                      // Set mailer to use SMTP
                 $mail->Host = 'smtp.gmail.com;';  // Specify main and backup SMTP servers
                 $mail->SMTPAuth = true;                               // Enable SMTP authentication
                 $mail->Username = 'allanbmageto@gmail.com';                 // SMTP username
                 $mail->Password = 'fsrflrtdhqhfqfns';                           // SMTP password
                 $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                 $mail->Port = 587;


                 $mail->Subject = 'Order Details';
                 $mail->Body    =  '<h1>Order Details</h1>
                  </br>
                  <tr>
                      <th>Product</th>
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Subtotal</th>
                  </tr>
                  <tr>
                  <td>'.$item["name"].'</td>
                  <td>' .$item["price"].'</td>
                  <td>' .$item["qty"]. ' </td>
                  <td>'.$item["subtotal"]. '</td>
                 ';
                 $mail->AltBody = 'From: ' .$item['id'].' ('.$item['qty'].')<p>'.$orderID.' '.$itemData.''.$item["name"].' '.$item["price"].' </p>' ;



                 $mail->setFrom('from@example.com', 'All things Pretty');

                 $mail->addAddress('gjasline@gmail.com', 'Jasline User');
                 $mail->addBCC("shirowwinnie@gmail.com","Joe Tailer");

                 if(!$mail->send()) {
                     echo 'Message could not be sent.';
                     echo 'Mailer Error: ' . $mail->ErrorInfo;
                 } else {
                     echo 'Message has been sent';
                 }
            header('location: index.php');



            // insert order items into database
            $insertOrderItems = $db->multi_query($sql);

            if($insertOrderItems){
                $cart->destroy();
                header("Location: orderSuccess.php?id=$orderID");
            }else{
                header("Location: checkout.php");
            }
        }else{
            header("Location: checkout.php");
        }
    }else{
        header("Location: index.php");
    }
}else{
    header("Location: index.php");
}
?>
