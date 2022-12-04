<?php
include 'main.php';
// Default campaign values
$campaign = [
    'title' => '',
    'status' => 'Active',
    'submit_date' => date('Y-m-d H:i:s'),
    'newsletter_id' => 0 
];
// Add campaign items to the database
function addCampaignItems($pdo, $campaign_id) {
    if (isset($_POST['recipients']) && is_array($_POST['recipients']) && count($_POST['recipients']) > 0) {
        $in  = str_repeat('?,', count($_POST['recipients']) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM campaign_items WHERE campaign_id = ? AND subscriber_id NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $campaign_id ], $_POST['recipients']));
        foreach ($_POST['recipients'] as $r) {
            $stmt = $pdo->prepare('INSERT IGNORE INTO campaign_items (campaign_id,subscriber_id,status,update_date) VALUES (?,?,"Queued",NULL)');
            $stmt->execute([ $campaign_id, $r ]);
        }
    } else {
        $stmt = $pdo->prepare('DELETE FROM campaign_items WHERE campaign_id = ?');
        $stmt->execute([ $campaign_id ]);       
    }
}
// Retrieve subscribers from the database
$stmt = $pdo->prepare('SELECT * FROM subscribers WHERE status = "Subscribed" AND confirmed = 1 ORDER BY email ASC');
$stmt->execute();
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Retrieve newsletters from the database
$stmt = $pdo->prepare('SELECT * FROM newsletters ORDER BY submit_date ASC');
$stmt->execute();
$newsletters = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['id'])) {
    // Retrieve the campaign from the database
    $stmt = $pdo->prepare('SELECT * FROM campaigns WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $campaign = $stmt->fetch(PDO::FETCH_ASSOC);
    // Retrieve campaign items
    $stmt = $pdo->prepare('SELECT subscriber_id FROM campaign_items WHERE campaign_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $campaign_items = $stmt->fetchAll(PDO::FETCH_COLUMN);
    // ID param exists, edit an existing campaign
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the campaign
        $stmt = $pdo->prepare('UPDATE campaigns SET title = ?, submit_date = ?, newsletter_id = ? WHERE id = ?');
        $stmt->execute([ $_POST['title'], date('Y-m-d H:i:s', strtotime($_POST['start_date'])), $_POST['newsletter_id'], $_GET['id'] ]);
        addCampaignItems($pdo, $_GET['id']); 
        header('Location: campaigns.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the campaign
        $stmt = $pdo->prepare('DELETE c, cc, ci, co, cu FROM campaigns c LEFT JOIN campaign_clicks cc ON cc.campaign_id = c.id LEFT JOIN campaign_items ci ON ci.campaign_id = c.id LEFT JOIN campaign_opens co ON co.campaign_id = c.id LEFT JOIN campaign_unsubscribes cu ON cu.campaign_id = c.id WHERE c.id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: campaigns.php?success_msg=3');
        exit;
    }
} else {
    // Create a new campaign
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO campaigns (title,status,submit_date,newsletter_id) VALUES (?,?,?,?)');
        $stmt->execute([ $_POST['title'], 'Active', date('Y-m-d H:i:s', strtotime($_POST['start_date'])), $_POST['newsletter_id'] ]);
        addCampaignItems($pdo, $pdo->lastInsertId()); 
        header('Location: campaigns.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Campaign', 'campaigns', 'manage')?>

<form action="" method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100"><?=$page?> Campaign</h2>
        <a href="campaigns.php" class="btn alt mar-right-2">Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn red mar-right-2" onclick="return confirm('Are you sure you want to delete this campaign?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <div class="content-block">

        <div class="form responsive-width-100">

            <label for="title"><i class="required">*</i> Title</label>
            <input id="title" type="text" name="title" placeholder="Title" value="<?=htmlspecialchars($campaign['title'], ENT_QUOTES)?>" required>

            <label for="start_date"><i class="required">*</i> Start Date</label>
            <input id="start_date" type="datetime-local" name="start_date" placeholder="Date" value="<?=date('Y-m-d\TH:i', strtotime($campaign['submit_date']))?>" required>

            <label for="newsletter_id"><i class="required">*</i> Newsletter</label>
            <select id="newsletter_id" name="newsletter_id" required>
                <option value="" disabled>(select newsletter)</option>
                <?php foreach ($newsletters as $newsletter): ?>
                <option value="<?=$newsletter['id']?>"<?=$campaign['newsletter_id']==$newsletter['id']?' selected':''?>><?=$newsletter['id']?> - <?=$newsletter['title']?></option>
                <?php endforeach; ?>
            </select>

            <label for="recipients"><i class="required">*</i> Recipients</label>
            <div class="multi-checkbox">
                <div class="item check-all">
                    <input id="check-all" type="checkbox">
                    <input type="text" placeholder="Search...">
                </div>
                <div class="con">
                    <?php foreach ($subscribers as $subscriber): ?>
                    <div class="item">
                        <input id="checkbox-<?=$subscriber['id']?>" type="checkbox" name="recipients[]" value="<?=$subscriber['id']?>"<?=isset($campaign_items) && in_array($subscriber['id'], $campaign_items)?' checked':''?>>
                        <label for="checkbox-<?=$subscriber['id']?>"><?=$subscriber['email']?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    </div>

</form>

<?=template_admin_footer()?>