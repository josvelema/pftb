<?php
include 'main.php';
// Default subscriber values
$subscriber = [
    'email' => '',
    'date_subbed' => date('Y-m-d H:i:s'),
    'confirmed' => 1,
    'status' => 'Subscribed'
];
if (isset($_GET['id'])) {
    // Retrieve the subscriber from the database
    $stmt = $pdo->prepare('SELECT * FROM subscribers WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing subscriber
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the subscriber
        $stmt = $pdo->prepare('UPDATE subscribers SET email = ?, date_subbed = ?, confirmed = ?, status = ? WHERE id = ?');
        $stmt->execute([ $_POST['email'], date('Y-m-d H:i:s', strtotime($_POST['date_subbed'])), $_POST['confirmed'], $_POST['status'], $_GET['id'] ]);
        header('Location: subscribers.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the subscriber
        $stmt = $pdo->prepare('DELETE FROM subscribers WHERE id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: subscribers.php?success_msg=3');
        exit;
    }
} else {
    // Create a new subscriber
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO subscribers (email,date_subbed,confirmed,status) VALUES (?,?,?,?)');
        $stmt->execute([ $_POST['email'], date('Y-m-d H:i:s', strtotime($_POST['date_subbed'])), $_POST['confirmed'], $_POST['status'] ]);
        header('Location: subscribers.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Subscriber', 'subscribers', 'manage')?>

<form action="" method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100"><?=$page?> Subscriber</h2>
        <a href="subscribers.php" class="btn alt mar-right-2">Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn red mar-right-2" onclick="return confirm('Are you sure you want to delete this subscriber?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <div class="content-block">

        <div class="form responsive-width-100">

            <label for="email"><i class="required">*</i> Email</label>
            <input id="email" type="email" name="email" placeholder="Email" value="<?=htmlspecialchars($subscriber['email'], ENT_QUOTES)?>" required>

            <label for="date_subbed"><i class="required">*</i> Date Subbed</label>
            <input id="date_subbed" type="datetime-local" name="date_subbed" value="<?=date('Y-m-d\TH:i', strtotime($subscriber['date_subbed']))?>" required>

            <label for="confirmed"><i class="required">*</i> Confirmed</label>
            <select id="confirmed" name="confirmed" required>
                <option value="1"<?=$subscriber['confirmed']==1?' selected':''?>>Yes</option>
                <option value="0"<?=$subscriber['confirmed']==0?' selected':''?>>No</option>
            </select>

            <label for="status"><i class="required">*</i> Status</label>
            <select id="status" name="status" required>
                <option value="Subscribed"<?=$subscriber['status']=='Subscribed'?' selected':''?>>Subscribed</option>
                <option value="Unsubscribed"<?=$subscriber['status']=='Unsubscribed'?' selected':''?>>Unsubscribed</option>
            </select>

        </div>

    </div>

</form>

<?=template_admin_footer()?>