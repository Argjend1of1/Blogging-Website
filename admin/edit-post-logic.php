<?php
require 'config/database.php';

$response = ['success' => false, 'message' => ''];

// make sure edit post button was clicked
if (isset($_POST['submit'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = isset($_SESSION['user_is_admin']) ? (isset($_POST['is_featured']) ? 1 : 0) : 0;
    $thumbnail = $_FILES['thumbnail'];

    // check and validate input values
    if (!$title) {
        $response['message'] = "Couldn't update post. Invalid Title.";
    } elseif (!$category_id) {
        $response['message'] = "Couldn't update post. Invalid category.";
    } elseif (!$body) {
        $response['message'] = "Couldn't update post. Invalid text.";
    } else {
        // delete existing thumbnail if new thumbnail is available
        if ($thumbnail['name']) {
            $previous_thumbnail_path = '../images/' . $previous_thumbnail_name;
            if ($previous_thumbnail_path) {
                unlink($previous_thumbnail_path);
            }

            // WORK ON NEW THUMBNAIL
            // Rename image
            $time = time(); // make each image name upload unique using current timestamp
            $thumbnail_name = $time . $thumbnail['name'];
            $thumbnail_tmp_name = $thumbnail['tmp_name'];
            $thumbnail_destination_path = '../images/' . $thumbnail_name;

            // make sure file is an image
            $allowed_files = ['png', 'jpg', 'jpeg'];
            $extension = explode('.', $thumbnail_name);
            $extension = end($extension);
            if (in_array($extension, $allowed_files)) {
                // make sure avatar is not too large (2mb+)
                if ($thumbnail['size'] < 2000000) {
                    // upload avatar
                    move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path);
                } else {
                    $response['message'] = "Couldn't update post. Thumbnail size too big. Should be less than 2mb";
                    echo json_encode($response);
                    die();
                }
            } else {
                $response['message'] = "Couldn't update post. Thumbnail should be png, jpg or jpeg";
                echo json_encode($response);
                die();
            }
        }
    }

    if ($response['message']) {
        echo json_encode($response);
        die();
    } else {
        // set is_featured of all posts to 0 if is_featured for this post is 1
        if ($is_featured == 1) {
            $zero_all_is_featured_query = "UPDATE posts SET is_featured=0";
            $zero_all_is_featured_result = mysqli_query($connection, $zero_all_is_featured_query);
        }

        // set thumbnail name if a new one was uploaded, else keep old thumbnail name
        $thumbnail_to_insert = $thumbnail_name ?? $previous_thumbnail_name;

        $query = "UPDATE posts SET title='$title', body='$body', thumbnail='$thumbnail_to_insert', 
                    category_id=$category_id, is_featured=$is_featured WHERE id=$id LIMIT 1";
        $result = mysqli_query($connection, $query);

        if (!mysqli_errno($connection)) {
            $response['success'] = true;
            $response['message'] = "Post updated successfully";
        } else {
            $response['message'] = "Couldn't update post. Please try again.";
        }
        echo json_encode($response);
        die();
    }
} else {
    $response['message'] = "Invalid request";
    echo json_encode($response);
    die();
}
?>
