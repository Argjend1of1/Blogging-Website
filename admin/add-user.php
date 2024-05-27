<?php
include 'partials/header.php';

// get back form data if there was an error
$firstname = $_SESSION['add-user-data']['firstname'] ?? null;
$lastname = $_SESSION['add-user-data']['lastname'] ?? null;
$username = $_SESSION['add-user-data']['username'] ?? null;
$email = $_SESSION['add-user-data']['email'] ?? null;
$createpassword = $_SESSION['add-user-data']['createpassword'] ?? null;
$confirmpassword = $_SESSION['add-user-data']['confirmpassword'] ?? null;

// delete session data
unset($_SESSION['add-user-data']);
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Add User</h2>
        <?php if (isset($_SESSION['add-user'])) : ?>
            <div id="alert-message" class="alert__message error">
                <p>
                    <?= $_SESSION['add-user'];
                    unset($_SESSION['add-user']);
                    ?>
                </p>
            </div>
        <?php endif ?>
        <form id="add-user-form" enctype="multipart/form-data" method="POST">
            <input type="text" name="firstname" value="<?= $firstname ?>" placeholder="First Name">
            <input type="text" name="lastname" value="<?= $lastname ?>" placeholder="Last Name">
            <input type="text" name="username" value="<?= $username ?>" placeholder="Username">
            <input type="email" name="email" value="<?= $email ?>" placeholder="Email">
            <input type="password" name="createpassword" value="<?= $createpassword ?>" placeholder="Create Password">
            <input type="password" name="confirmpassword" value="<?= $confirmpassword ?>" placeholder="Confirm Password">
            <select name="userrole">
                <option value="0">Author</option>
                <option value="1">Admin</option>
            </select>
            <div class="form__control">
                <label for="avatar">User Avatar</label>
                <input type="file" name="avatar" id="avatar">
            </div>
            <button type="submit" name="submit" class="btn">Add User</button>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('add-user-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('submit', 'submit'); // Explicitly add the submit field

            fetch('<?= ROOT_URL ?>admin/add-user-logic.php', {
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
                        window.location.href = '<?= ROOT_URL ?>admin/manage-users.php';
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

<?php
include '../partials/footer.php';
?>
