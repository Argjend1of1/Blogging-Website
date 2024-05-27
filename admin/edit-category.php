<?php
include 'partials/header.php';

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // fetch category from database
    $query = "SELECT * FROM categories WHERE id=$id";
    $result = mysqli_query($connection, $query);
    if (mysqli_num_rows($result) == 1) {
        $category = mysqli_fetch_assoc($result);
    }
} else {
    header('location: ' . ROOT_URL . 'admin/manage-categories');
    die();
}
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Edit Category</h2>
        <?php if (isset($_SESSION['edit-category'])) : ?>
            <div id="alert-message" class="alert__message error">
                <p>
                    <?= $_SESSION['edit-category'];
                    unset($_SESSION['edit-category']);
                    ?>
                </p>
            </div>
        <?php endif ?>
        <form id="edit-category-form" method="POST">
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
            <input type="text" name="title" value="<?= $category['title'] ?>" placeholder="Title">
            <textarea rows="4" name="description" placeholder="Description"><?= $category['description'] ?></textarea>
            <button type="submit" name="submit" class="btn">Update Category</button>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('edit-category-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('submit', 'submit'); // Explicitly add the submit field

            fetch('<?= ROOT_URL ?>admin/edit-category-logic.php', {
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
                        window.location.href = '<?= ROOT_URL ?>admin/manage-categories.php';
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
