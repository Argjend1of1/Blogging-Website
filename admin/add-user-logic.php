<?php
require 'config/database.php';

$response = ['success' => false, 'message' => ''];

// get form data if submit button was clicked
if (isset($_POST['submit'])) {
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $is_admin = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);
    $avatar = $_FILES['avatar'];

    // validate input values
    if (!$firstname) {
        $response['message'] = "Please enter your First Name";
    } elseif (!$lastname) {
        $response['message'] = "Please enter your Last Name";
    } elseif (!$username) {
        $response['message'] = "Please enter your Username";
    } elseif (!$email) {
        $response['message'] = "Please enter a valid email";
    } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $response['message'] = "Password should be 8+ characters";
    } elseif (!$avatar['name']) {
        $response['message'] = "Please add avatar";
    } else {
        // check if passwords don't match
        if ($createpassword !== $confirmpassword) {
            $response['message'] = "Passwords do not match";
        } else {
            // hash password
            $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

            // check if username or email already exist in database
            $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
            $user_check_result = mysqli_query($connection, $user_check_query);
            if (mysqli_num_rows($user_check_result) > 0) {
                $response['message'] = "Username or Email already exist";
            } else {
                // WORK ON AVATAR
                // rename avatar
                $time = time(); // make each image name unique using current timestamp
                $avatar_name = $time . $avatar['name'];
                $avatar_tmp_name = $avatar['tmp_name'];
                $avatar_destination_path = '../images/' . $avatar_name;

                // make sure file is an image
                $allowed_files = ['png', 'jpg', 'jpeg'];
                $extention = explode('.', $avatar_name);
                $extention = end($extention);
                if (in_array($extention, $allowed_files)) {
                    // make sure image is not too large (1mb+)
                    if ($avatar['size'] < 1000000) {
                        // upload avatar
                        move_uploaded_file($avatar_tmp_name, $avatar_destination_path);
                    } else {
                        $response['message'] = "File size too big. Should be less than 1mb";
                        echo json_encode($response);
                        die();
                    }
                } else {
                    $response['message'] = "File should be png, jpg, or jpeg";
                    echo json_encode($response);
                    die();
                }
            }
        }
    }

    // redirect back to add-user page if there was any problem
    if ($response['message']) {
        echo json_encode($response);
        die();
    } else {
        // insert new user into users table
        $insert_user_query = "INSERT INTO users SET firstname='$firstname', lastname='$lastname', username='$username', email='$email', password='$hashed_password', avatar='$avatar_name', is_admin=$is_admin";
        $insert_user_result = mysqli_query($connection, $insert_user_query);

        if (!mysqli_errno($connection)) {
            $response['success'] = true;
            $response['message'] = "New user $firstname $lastname added successfully.";
            echo json_encode($response);
            die();
        } else {
            $response['message'] = "Failed to add new user";
            echo json_encode($response);
            die();
        }
    }
} else {
    $response['message'] = "Invalid request";
    echo json_encode($response);
    die();
}
?>
