<?php
require 'config/database.php';

$response = ['success' => false, 'message' => ''];

if (isset($_POST['submit'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // validate input
    if (!$title || !$description) {
        $response['message'] = "Invalid form input on edit category page";
    } else {
        $query = "UPDATE categories SET title='$title', description='$description' WHERE id=$id LIMIT 1";
        $result = mysqli_query($connection, $query);

        if (mysqli_errno($connection)) {
            $response['message'] = "Couldn't update category";
        } else {
            $response['success'] = true;
            $response['message'] = "Category $title updated successfully";
        }
    }

    echo json_encode($response);
    die();
} else {
    $response['message'] = "Invalid request";
    echo json_encode($response);
    die();
}
?>
