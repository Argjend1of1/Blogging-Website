

<?php
require 'config/database.php';

$response = ['success' => false, 'message' => ''];

try {
    // get signup form data if signup button was clicked
    if (isset($_POST['submit'])) {
        $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $avatar = $_FILES['avatar'];

        // validate input values
        if (!$firstname) {
            throw new Exception("Please enter your First Name");
        } elseif (!$lastname) {
            throw new Exception("Please enter your Last Name");
        } elseif (!$username) {
            throw new Exception("Please enter your Username");
        } elseif (!$email) {
            throw new Exception("Please enter a valid email");
        } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
            throw new Exception("Password should be 8+ characters");
        } elseif (!$avatar['name']) {
            throw new Exception("Please add avatar");
        } else {
            // check if passwords don't match
            if ($createpassword !== $confirmpassword) {
                throw new Exception("Passwords do not match");
            } else {
                // hash password
                $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);

                // check if username or email already exist in database
                $stmt = $connection->prepare("SELECT * FROM users WHERE username=? OR email=?");
                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $connection->error);
                }
                $stmt->bind_param('ss', $username, $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    throw new Exception("Username or Email already exist");
                } else {
                    // WORK ON AVATAR
                    // rename avatar
                    $time = time(); // make each image name unique using current timestamp
                    $avatar_name = $time . $avatar['name'];
                    $avatar_tmp_name = $avatar['tmp_name'];
                    $avatar_destination_path = 'images/' . $avatar_name;

                    // make sure file is an image
                    $allowed_files = ['png', 'jpg', 'jpeg'];
                    $extension = explode('.', $avatar_name);
                    $extension = end($extension);
                    if (in_array($extension, $allowed_files)) {
                        // make sure image is not too large (1mb+)
                        if ($avatar['size'] < 1000000) {
                            // upload avatar
                            move_uploaded_file($avatar_tmp_name, $avatar_destination_path);
                            // insert new user into users table
                            $stmt = $connection->prepare("INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) 
                                                                    VALUES (?, ?, ?, ?, ?, ?, 0)");
                            if (!$stmt) {
                                throw new Exception("Prepare statement failed: " . $connection->error);
                            }
                            $stmt->bind_param('ssssss', $firstname, $lastname, $username, $email, $hashed_password, $avatar_name);

                            if ($stmt->execute()) {
                                $response['success'] = true;
                                $response['message'] = "Registration successful. Please log in";
                            } else {
                                throw new Exception("Failed to register user");
                            }
                        } else {
                            throw new Exception("File size too big. Should be less than 1mb");
                        }
                    } else {
                        throw new Exception("File should be png, jpg, or jpeg");
                    }
                }
                $stmt->close();
            }
        }

        // if any problem, return response
        if ($response['message'] && !$response['success']) {
            $_SESSION['signup-data'] = $_POST;
        }

        echo json_encode($response);
        die();
    } else {
        throw new Exception("Invalid request");
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    if (isset($_POST)) {
        $_SESSION['signup-data'] = $_POST;
    }
    echo json_encode($response);
    die();
}
?>

<!-- 
try...catch...throw approach can help you manage exceptions more 
effectively and maintain cleaner code.

try...catch:

Wrap the main logic inside a try block.
Use catch to handle exceptions and set the response message.
throw new Exception:

Whenever a validation fails, use throw new Exception("Error message") 
to throw an exception.
-->