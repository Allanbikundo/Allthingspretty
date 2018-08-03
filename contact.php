<?php

session_start();

require 'phpmailer/PHPMailerAutoload.php';

$errors = [] ;

if (isset($_POST['name'], $_POST['email'], $_POST['message'])) {

  $fields = [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'message' =>$_POST['message']
  ];

   foreach ($fields as $field => $data) {
     if (empty($data)) {
         $errors[] = 'the '  . $field . ' field is required.';
     }
   }
   if(empty($errors)){
     $mail = new PHPMailer;

     //$mail->SMTPDebug = 3;                               // Enable verbose debug output

     $mail->isSMTP();                                      // Set mailer to use SMTP
     $mail->Host = 'smtp.gmail.com;';  // Specify main and backup SMTP servers
     $mail->SMTPAuth = true;                               // Enable SMTP authentication
     $mail->Username = 'allanbmageto@gmail.com';                 // SMTP username
     $mail->Password = 'fsrflrtdhqhfqfns';                           // SMTP password
     $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
     $mail->Port = 587;

     $mail->Subject = 'contact form submitted';
     $mail->Body    = 'From: ' .$fields['name'].' ('.$fields['email'].')<p>'.$fields['message'].'</p>';
     $mail->AltBody =

     $mail->setFrom('from@example.com', 'All things Pretty');

     $mail->addAddress('allanbmageto@gmail.com', 'allan User');

     if(!$mail->send()) {
         echo 'Message could not be sent.';
         echo 'Mailer Error: ' . $mail->ErrorInfo;
     } else {
         echo 'Message has been sent';
     }
   }
} else {
  $errors[] = 'something went wrong';
}
$_SESSION['errors'] = $errors;
$_SESSION['fields'] =  $fields;

header('location: index.php');

 ?>
