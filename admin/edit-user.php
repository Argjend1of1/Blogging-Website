<?php
include 'partials/header.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM users WHERE id=$id";
    $result = mysqli_query($connection, $query);
    $user = mysqli_fetch_assoc($result);
} else {
    header('location: ' . ROOT_URL . 'admin/manage-users.php');
    die();
}
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Edit User</h2>
        <?php if (isset($_SESSION['edit-user'])) : ?>
            <div id="alert-message" class="alert__message error">
                <p>
                    <?= $_SESSION['edit-user'];
                    unset($_SESSION['edit-user']);
                    ?>
                </p>
            </div>
        <?php endif ?>
        <form id="edit-user-form" method="POST">
            <input type="hidden" value="<?= $user['id'] ?>" name="id">
            <input type="text" value="<?= $user['firstname'] ?>" name="firstname" placeholder="First Name">
            <input type="text" value="<?= $user['lastname'] ?>" name="lastname" placeholder="Last Name">
            <select name="userrole">
                <option value="0" <?= $user['is_admin'] == 0 ? 'selected' : '' ?>>Author</option>
                <option value="1" <?= $user['is_admin'] == 1 ? 'selected' : '' ?>>Admin</option>
            </select>
            <button type="submit" name="submit" class="btn">Update User</button>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('edit-user-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('submit', 'submit'); // Explicitly add the submit field

            fetch('<?= ROOT_URL ?>admin/edit-user-logic.php', {
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
