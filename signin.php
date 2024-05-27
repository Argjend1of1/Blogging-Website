<?php
require 'config/constants.php';

$username_email = $_SESSION['signin-data']['username_email'] ?? null;
$password = $_SESSION['signin-data']['password'] ?? null;

unset($_SESSION['signin-data']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP & MySQL Blog Application with Admin Panel</title>
    <!-- CUSTOM STYLESHEET -->
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
    <!-- ICONSCOUT CDN -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <!-- GOOGLE FONT (MONTSERRAT) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body>
    <section class="form__section">
        <div class="container form__section-container">
            <h2>Sign In</h2>
            <?php if (isset($_SESSION['signup-success'])) : ?>
                <div class="alert__message success">
                    <p>
                        <?= $_SESSION['signup-success'];
                        unset($_SESSION['signup-success']);
                        ?>
                    </p>
                </div>
            <?php elseif (isset($_SESSION['signin'])) : ?>
                <div id="alert-message" class="alert__message error">
                    <p>
                        <?= $_SESSION['signin'];
                        unset($_SESSION['signin']);
                        ?>
                    </p>
                </div>
            <?php endif ?>
            <form id="signin-form" method="POST">
                <input type="text" name="username_email" value="<?= $username_email ?>" placeholder="Username or Email">
                <input type="password" name="password" value="<?= $password ?>" placeholder="Password">
                <button type="submit" name="submit" class="btn">Sign In</button>
                <small>Don't have an account? <a href="signup.php">Sign Up</a></small>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('signin-form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                formData.append('submit', 'submit'); // Explicitly add the submit field

                fetch('<?= ROOT_URL ?>signin-logic.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response received:', data); // Debugging log
                    let alertMessage = document.getElementById('alert-message');
                    if (!alertMessage) {
                        alertMessage = document.createElement('div');
                        alertMessage.id = 'alert-message';
                        alertMessage.className = 'alert__message';
                        const container = document.querySelector('.form__section-container');
                        container.insertBefore(alertMessage, container.firstChild);
                    }

                    alertMessage.innerHTML = `<p>${data.message}</p>`;

                    if (data.success) {
                        alertMessage.classList.remove('error');
                        alertMessage.classList.add('success');
                        setTimeout(() => {
                            window.location.href = '<?= ROOT_URL ?>admin/';
                        }, 2000);
                    } else {
                        alertMessage.classList.remove('success');
                        alertMessage.classList.add('error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>

</html>
