<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$receiving_email_address = getenv('CONTACT_RECEIVING_EMAIL') ?: 'alokgarg003@gmail.com';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    http_response_code(400);
    exit('All fields are required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Invalid email address.');
}

$emailSent = false;

if (filter_var($receiving_email_address, FILTER_VALIDATE_EMAIL)) {
    $mailSubject = "Portfolio Contact: " . $subject;
    $mailBody = "From: {$name}\nEmail: {$email}\n\n{$message}";
    $headers = "From: {$email}\r\nReply-To: {$email}\r\n";
    $emailSent = @mail($receiving_email_address, $mailSubject, $mailBody, $headers);
}

$host = getenv('DB_HOST');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME');
$port = getenv('DB_PORT') ?: '3306';

$dbSaved = false;

if ($host && $username && $database) {
    $conn = new mysqli($host, $username, $password, $database, (int) $port);

    if (!$conn->connect_error) {
        $sql = "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $subject, $message);
            $dbSaved = $stmt->execute();
            $stmt->close();
        }

        $conn->close();
    }
}

if ($emailSent || $dbSaved) {
    echo "Message submitted successfully.";
    exit;
}

echo "Message received, but no delivery backend is configured.";
