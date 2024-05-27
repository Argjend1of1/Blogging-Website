<?php
require 'config/database.php';

$response = ['success' => false, 'message' => ''];

try {
    if (isset($_POST['submit'])) {
        // get form data
        $username_email = filter_var($_POST['username_email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$username_email) {
            throw new Exception("Username or Email required");
        } elseif (!$password) {
            throw new Exception("Password required");
        } else {
            // fetch user from database using prepared statement
            $stmt = $connection->prepare("SELECT * FROM users WHERE username=? OR email=?");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $connection->error);
            }
            $stmt->bind_param('ss', $username_email, $username_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                // convert the record into assoc array
                $user_record = $result->fetch_assoc();
                $db_password = $user_record['password'];
                // compare form password with database password
                if (password_verify($password, $db_password)) {
                    // set session for access control
                    $_SESSION['user-id'] = $user_record['id'];
                    // set session if user is an admin
                    if ($user_record['is_admin'] == 1) {
                        $_SESSION['user_is_admin'] = true;
                    }
                    $response['success'] = true;
                    $response['message'] = "Sign-in successful";
                } else {
                    throw new Exception("Please check your input");
                }
            } else {
                throw new Exception("User not found");
            }
            $stmt->close();
        }

        // Return response if there was any problem
        if ($response['message'] && !$response['success']) {
            $_SESSION['signin-data'] = $_POST;
        }

        echo json_encode($response);
        die();
    } else {
        throw new Exception("Invalid request");
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    if (isset($_POST)) {
        $_SESSION['signin-data'] = $_POST;
    }
    echo json_encode($response);
    die();
}
?>
