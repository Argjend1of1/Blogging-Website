<?php
include 'partials/header.php';

// fetch categories from database
$query = "SELECT * FROM categories";
$categories = mysqli_query($connection, $query);

// get back form data if form was invalid
$title = &$_SESSION['add-post-data']['title'] ?? null;  // Reference
$body = &$_SESSION['add-post-data']['body'] ?? null;    // Reference

// delete form data session
unset($_SESSION['add-post-data']);
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Add Post</h2>
        <?php if (isset($_SESSION['add-post'])) : ?>
            <div id="alert-message" class="alert__message error">
                <p>
                    <?= $_SESSION['add-post'];
                    unset($_SESSION['add-post']);
                    ?>
                </p>
            </div>
        <?php endif ?>
        <form id="add-post-form" action="<?= ROOT_URL ?>admin/add-post-logic.php" enctype="multipart/form-data" method="POST">
            <input type="text" name="title" value="<?= $title ?>" placeholder="Title">
            <select name="category">
                <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                    <option value="<?= $category['id'] ?>"><?= $category['title'] ?></option>
                <?php endwhile ?>
            </select>
            <textarea rows="10" name="body" placeholder="Body"><?= $body ?></textarea>
            <?php if (isset($_SESSION['user_is_admin'])) : ?>
                <div class="form__control inline">
                    <input type="checkbox" name="is_featured" value="1" id="is_featured" checked>
                    <label for="is_featured">Featured</label>
                </div>
            <?php endif ?>
            <div class="form__control">
                <label for="thumbnail">Add Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail">
            </div>
            <button type="submit" name="submit" class="btn">Add Post</button>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('add-post-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('submit', 'submit'); // Explicitly add the submit field

            fetch('<?= ROOT_URL ?>admin/add-post-logic.php', {
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

<?php
include '../partials/footer.php';
?>
