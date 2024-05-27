<!-- NOT USED
    The reason for not using, is cause we didnt find 
    a way to implement it in our project and keep
    the logic intact.
    But we still just gave an idea on how should be done since it was
    a request, if it counts for anything.
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Email</title>
</head>
<body>
    <form action="send_email.php" method="post">
        <label for="to">To:</label>
        <input type="email" name="to" id="to" required><br>
        <label for="subject">Subject:</label>
        <input type="text" name="subject" id="subject" required><br>
        <label for="message">Message:</label>
        <textarea name="message" id="message" required></textarea><br>
        <button type="submit">Send Email</button>
    </form>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = filter_var($_POST['to'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject']);
    $message = filter_var($_POST['message']);

    $headers = "From: yourname@example.com\r\n";
    $headers .= "Reply-To: yourname@example.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $message, $headers)) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email.";
    }
} else {
    echo "Invalid request.";
}
?>