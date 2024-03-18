<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

require_once('env.php');




$allowed_hosts = array('api.devzila.com','localhost', 'chandigarhcaterer.in');
if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
    header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
    exit;
}


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


$first_name = mysqli_real_escape_string($conn, $_REQUEST['first_name']);
$last_name = mysqli_real_escape_string($conn, $_REQUEST['last_name']);
$email = mysqli_real_escape_string($conn, $_REQUEST['email']);
$phone = mysqli_real_escape_string($conn, $_REQUEST['phone']);
$source = mysqli_real_escape_string($conn, $_REQUEST['source']);
$ip = get_client_ip();
$request_data = mysqli_real_escape_string($conn, json_encode($_REQUEST));

print_r($_REQUEST);
$sql = "INSERT INTO leads (first_name, last_name, email, phone, ip, request_data, created_at, updated_at )
VALUES ('$first_name', '$last_name', '$email', '$phone', '$ip', '$request_data', now(), now() )";


if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

//Recipients
$phpmailer->setFrom('mailtrap@demomailtrap.com', 'Mailer');
$phpmailer->addAddress('nilay@devzila.com', 'Devzila');     //Add a recipient
// $phpmailer->addAddress('ellen@example.com');               //Name is optional
//$phpmailer->addReplyTo('info@example.com', 'Information');
//$phpmailer->addCC('cc@example.com');
//$phpmailer->addBCC('bcc@example.com');


//Content
$phpmailer->isHTML(true);                                  //Set email format to HTML
$phpmailer->Subject = "New lead generated on: $source";
$phpmailer->Body    = json_encode($_REQUEST);
$phpmailer->AltBody = json_encode($_REQUEST);

$phpmailer->send();
echo 'Message has been sent';







// Function to get the client IP address
function get_client_ip() {
  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP']))
      $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_X_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
      $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  else if(isset($_SERVER['HTTP_FORWARDED']))
      $ipaddress = $_SERVER['HTTP_FORWARDED'];
  else if(isset($_SERVER['REMOTE_ADDR']))
      $ipaddress = $_SERVER['REMOTE_ADDR'];
  else
      $ipaddress = 'UNKNOWN';
  return $ipaddress;
}
?>