<?php
include 'partials/header.php';

// get back form data if invalid
$title = $_SESSION['add-category-data']['title'] ?? null;
$description = $_SESSION['add-category-data']['description'] ?? null;

unset($_SESSION['add-category-data']);
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Add Category</h2>
        <?php if (isset($_SESSION['add-category'])) : ?>
            <div id="alert-message" class="alert__message error">
                <p>
                    <?= $_SESSION['add-category'];
                    unset($_SESSION['add-category']) ?>
                </p>
            </div>
        <?php endif ?>
        <form id="add-category-form" method="POST">
            <input type="text" value="<?= $title ?>" name="title" placeholder="Title">
            <textarea rows="4" name="description" placeholder="Description"><?= $description ?></textarea>
            <button type="submit" name="submit" class="btn">Add Category</button>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('add-category-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('submit', 'submit'); // Explicitly add the submit field

            fetch('<?= ROOT_URL ?>admin/add-category-logic.php', {
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
