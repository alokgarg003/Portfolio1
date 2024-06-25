<?php
// Replace contact@example.com with your real receiving email address
$receiving_email_address = 'alokgarg003@gmail.com';

// Check if PHP Email Form library file exists
$php_email_form = 'vendor/php-email-form/php-email-form.php'; // Adjust path as needed

if (file_exists($php_email_form)) {
    include($php_email_form);
} else {
    die('Unable to load the "PHP Email Form" Library!');
}

// Initialize PHP_Email_Form instance
$contact = new PHP_Email_Form;
$contact->ajax = true;

// Set email details
$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'];
$contact->from_email = $_POST['email'];
$contact->subject = $_POST['subject'];

// SMTP configuration for sending email
$contact->smtp = array(
    'host' => 'smtp.yourmailserver.com', // Replace with your SMTP server hostname
    'username' => 'your_smtp_username',
    'password' => 'your_smtp_password',
    'port' => '587', // Adjust port number as needed (587 for TLS)
    'encryption' => 'tls' // Adjust encryption type (ssl/tls)
);

// Add message parts
$contact->add_message($_POST['name'], 'From');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

// Send email using PHP Email Form library
$email_sent = $contact->send();

// Database connection details
$host = 'localhost'; // MySQL hostname
$username = 'root'; // MySQL username
$password = 'alok003'; // MySQL password
$database = 'resume'; // Replace with your actual database name
$port = '3036'; // Replace with your actual port

// Create MySQL database connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement to insert form data into database
$name = $_POST['name'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

$sql = "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $subject, $message);

// Execute SQL statement
if ($stmt->execute()) {
    echo "Message submitted successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close database connection
$stmt->close();
$conn->close();

// Output email sending result (optional, adjust as needed)
echo $email_sent ? "Email sent successfully." : "Failed to send email.";

?>
