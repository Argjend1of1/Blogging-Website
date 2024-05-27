<?php
require 'config/database.php';

$response = ['success' => false, 'message' => ''];

if (isset($_POST['submit'])) {
    // get updated form data
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $is_admin = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);

    // check for valid input
    if (!$firstname || !$lastname) {
        $response['message'] = "Invalid form input on edit page.";
    } else {
        // update user
        $query = "UPDATE users SET firstname='$firstname', lastname='$lastname', is_admin=$is_admin WHERE id=$id LIMIT 1";
        $result = mysqli_query($connection, $query);

        if (mysqli_errno($connection)) {
            $response['message'] = "Failed to update user.";
        } else {
            $response['success'] = true;
            $response['message'] = "User $firstname $lastname updated successfully";
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
