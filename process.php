<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    // Recipient email
    $to = 'ellisemputnam@gmail.com'; // Change this to your email address

    // Email subject
    $subject = 'New Message from Contact Form';

    // Email body (HTML version)
    $body = "
        <html>
        <head>
            <title>New Message from Contact Form</title>
        </head>
        <body>
            <p>You have received a new message from your contact form:</p>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Message:</strong><br>$message</p>
        </body>
        </html>
    ";

    // Set headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: $email" . "\r\n"; // Set the "From" email to the user's email

    // Send the email
    if (mail($to, $subject, $body, $headers)) {
        echo "Message has been sent!";
    } else {
        echo "Something went wrong, please try again.";
    }
}
?>
