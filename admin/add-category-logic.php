<?php
require 'config/database.php';

$response = ['success' => false, 'message' => ''];

if (isset($_POST['submit'])) {
    // get form data
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$title) {
        $response['message'] = "Enter title";
    } elseif (!$description) {
        $response['message'] = "Enter description";
    }

    // Return response if there was invalid input
    if ($response['message']) {
        echo json_encode($response);
        die();
    } else {
        // insert category into database
        $query = "INSERT INTO categories (title, description) VALUES ('$title', '$description')";
        $result = mysqli_query($connection, $query);
        if (mysqli_errno($connection)) {
            $response['message'] = "Couldn't add category";
        } else {
            $response['success'] = true;
            $response['message'] = "$title category added successfully";
        }
        echo json_encode($response);
        die();
    }
} else {
    $response['message'] = "Invalid request";
    echo json_encode($response);
}
?>
