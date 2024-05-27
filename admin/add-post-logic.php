<?php
require 'config/database.php';

$response = ['success' => false, 'message' => ''];

function validateAndRenameThumbnail(&$thumbnail) { // Using function with reference
    $time = time();
    $thumbnail_name = $time . $thumbnail['name'];
    $thumbnail_tmp_name = $thumbnail['tmp_name'];
    $thumbnail_destination_path = '../images/' . $thumbnail_name;

    // make sure file is an image
    $allowed_files = ['png', 'jpg', 'jpeg'];
    $extension = explode('.', $thumbnail_name);
    $extension = end($extension);
    if (in_array($extension, $allowed_files)) {
        // make sure image is not too big. (2mb+)
        if ($thumbnail['size'] < 2000000) {
            // upload thumbnail
            move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
            return $thumbnail_name;
        } else {
            return "File size too big. Should be less than 2mb";
        }
    } else {
        return "File should be png, jpg, or jpeg";
    }
}

if (isset($_POST['submit'])) {
    $author_id = $_SESSION['user-id'];
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = isset($_SESSION['user_is_admin']) ? filter_var($_POST['is_featured'], FILTER_SANITIZE_NUMBER_INT) : 0;
    $thumbnail = $_FILES['thumbnail'];

    // set is_featured to 0 if unchecked
    $is_featured = $is_featured == 1 ? 1 : 0;

    // validate form data
    if (!$title) {
        $response['message'] = "Enter post title";
    } elseif (!$category_id) {
        $response['message'] = "Select post category";
    } elseif (!$body) {
        $response['message'] = "Enter post body";
    } elseif (!$thumbnail['name']) {
        $response['message'] = "Choose post thumbnail";
    } else {
        // WORK ON THUMBNAIL
        $thumbnail_name = validateAndRenameThumbnail($thumbnail); // Function call with reference
        if (strpos($thumbnail_name, 'File') === 0) {
            $response['message'] = $thumbnail_name;
            echo json_encode($response);
            die();
        }
    }

    // redirect back (with form data) to add-post page if there is any problem
    if ($response['message']) {
        echo json_encode($response);
        die();
    } else {
        // set is_featured of all posts to 0 if is_featured for this post is 1
        if ($is_featured == 1) {
            $zero_all_is_featured_query = "UPDATE posts SET is_featured=0";
            $zero_all_is_featured_result = mysqli_query($connection, $zero_all_is_featured_query);
        }

        // insert post into database
        $query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured) 
                  VALUES ('$title', '$body', '$thumbnail_name', $category_id, $author_id, $is_featured)";
        $result = mysqli_query($connection, $query);

        if (!mysqli_errno($connection)) {
            $response['success'] = true;
            $response['message'] = "New post added successfully";
            echo json_encode($response);
            die();
        } else {
            $response['message'] = "Failed to add post";
            echo json_encode($response);
            die();
        }
    }
} else {
    $response['message'] = "Invalid request";
    echo json_encode($response);
}
?>
