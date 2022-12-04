<?php
include 'main.php';
// Default newsletter values
$newsletter = [
    'title' => '',
    'content' => '',
    'submit_date' => date('Y-m-d H:i:s')
];
if (isset($_GET['id'])) {
    // Retrieve the newsletter from the database
    $stmt = $pdo->prepare('SELECT * FROM newsletters WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $newsletter = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing newsletter
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the newsletter
        $stmt = $pdo->prepare('UPDATE newsletters SET title = ?, content = ? WHERE id = ?');
        $stmt->execute([ $_POST['title'], $_POST['content'], $_GET['id'] ]);
        header('Location: newsletters.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the newsletter
        $stmt = $pdo->prepare('DELETE FROM newsletters WHERE id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: newsletters.php?success_msg=3');
        exit;
    }
} else {
    // Create a new newsletter
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO newsletters (title,content,submit_date) VALUES (?,?,?)');
        $stmt->execute([ $_POST['title'], $_POST['content'], date('Y-m-d H:i:s') ]);
        header('Location: newsletters.php?success_msg=1');
        exit;
    }
}
// If copying an existing newsletter
if (isset($_GET['copy'])) {
    // Retrieve the newsletter from the database
    $stmt = $pdo->prepare('SELECT * FROM newsletters WHERE id = ?');
    $stmt->execute([ $_GET['copy'] ]);
    $newsletter = $stmt->fetch(PDO::FETCH_ASSOC);
    // Update title
    if ($newsletter) {
        $newsletter['title'] .= ' Copy';
    }
}
?>
<?=template_admin_header($page . ' Newsletter', 'newsletters', 'manage')?>

<form action="" method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100"><?=$page?> Newsletter</h2>
        <a href="newsletters.php" class="btn alt mar-right-2">Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn red mar-right-2" onclick="return confirm('Are you sure you want to delete this newsletter?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <div class="content-block">

        <div class="form responsive-width-100">

            <label for="title"><i class="required">*</i> Title</label>
            <input id="title" type="text" name="title" placeholder="Title" value="<?=htmlspecialchars($newsletter['title'], ENT_QUOTES)?>" required>

            <label for="content"><i class="required">*</i> HTML Template</label>
        </div>

        <div class="newsletter-editor">
            <div class="header">
                <div class="format-btns">
                    <span>Insert Tag</span>
                    <a href="#" class="format-btn div">Div</a>
                    <a href="#" class="format-btn heading">Heading</a>
                    <a href="#" class="format-btn paragraph">Paragraph</a>
                    <a href="#" class="format-btn strong">Strong</a>
                    <a href="#" class="format-btn italic">Italic</a>
                    <a href="#" class="format-btn image">Image</a>
                </div>
                <div class="preview-btn">
                    <a href="#"><i class="fa-solid fa-eye"></i>Preview</a>
                </div>
            </div>
            <textarea id="content" name="content" placeholder="Enter your HTML template..." wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?=htmlspecialchars($newsletter['content'], ENT_QUOTES)?></textarea>
        </div>

    </div>

</form>

<?=template_admin_footer()?>