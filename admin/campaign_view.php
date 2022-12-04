<?php
include 'main.php';
// Retrieve the campaign from the database
$stmt = $pdo->prepare('SELECT 
    c.*, 
    (SELECT COUNT(*) FROM campaign_items ci WHERE ci.campaign_id = c.id) AS total_items,
    (SELECT COUNT(*) FROM campaign_items ci WHERE ci.campaign_id = c.id AND (ci.status = "Completed" OR ci.status = "Cancelled")) AS total_completed_items,
    (SELECT COUNT(*) FROM campaign_items ci WHERE ci.campaign_id = c.id AND ci.status = "Failed") AS total_failed_items,
    (SELECT COUNT(*) FROM campaign_clicks cc WHERE cc.campaign_id = c.id) AS total_clicks,
    (SELECT COUNT(*) FROM campaign_unsubscribes cu WHERE cu.campaign_id = c.id) AS total_unsubscribes,
    (SELECT COUNT(*) FROM campaign_opens co WHERE co.campaign_id = c.id) AS total_opens  
    FROM campaigns c WHERE c.id = ?
');
$stmt->execute([ $_GET['id'] ]);
$campaign = $stmt->fetch(PDO::FETCH_ASSOC); 
if (!$campaign) {
    exit('Invalid campaign ID!');
}
// Delete campaign
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM campaign_items WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: campaign_view.php?id=' . $_GET['id'] . '&success_msg=1');
    exit;
}
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['email','clicked','unsubscribed','is_read','status','update_date'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
$stmt = $pdo->prepare('SELECT 
    ci.*, 
    s.email AS email,
    (SELECT COUNT(*) FROM campaign_clicks cc WHERE cc.campaign_id = ci.campaign_id AND cc.subscriber_id = ci.subscriber_id) AS clicked,
    (SELECT COUNT(*) FROM campaign_unsubscribes cu WHERE cu.campaign_id = ci.campaign_id AND cu.subscriber_id = ci.subscriber_id) AS unsubscribed,
    (SELECT COUNT(*) FROM campaign_opens co WHERE co.campaign_id = ci.campaign_id AND co.subscriber_id = ci.subscriber_id) AS is_read 
    FROM campaign_items ci 
    LEFT JOIN subscribers s ON ci.subscriber_id = s.id 
    WHERE ci.campaign_id = ? 
    ORDER BY ' . $order_by . ' ' . $order . '
');
$stmt->execute([ $campaign['id'] ]);
$recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Send newsletter
if (isset($_GET['send'])) {
    include 'functions.php';
    foreach ($recipients as $recipient) {
        if ($_GET['send'] == $recipient['id']) {
            $date = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare('SELECT * FROM newsletters WHERE id = ?');
            $stmt->execute([ $campaign['newsletter_id'] ]);
            $newsletter = $stmt->fetch(PDO::FETCH_ASSOC); 
            if ($newsletter) {
                // Generate the unqiue code
                $code = sha1($campaign['id'] . $recipient['id'] . $recipient['email']);
                // Replace the template placeholders with the respective code
                $content = str_replace([
                    '%open_tracking_code%',
                    '%unsubscribe_link%',
                    '%click_link%'
                ], [
                    '<img src="' . website_url . 'tracking.php?action=open&id=' . $code . '" width="1" height="1" alt="">',
                    website_url . 'tracking.php?action=unsubscribe&id=' . $code,
                    website_url . 'tracking.php?action=click&id=' . $code . '&url='
                ], $newsletter['content']);
                $response = send_newsletter($recipient['email'], $newsletter['title'], $content);
                if ($response == 'success') {
                    $stmt = $pdo->prepare('UPDATE campaign_items ci SET ci.update_date = ?, ci.status = "Completed", ci.fail_text = "" WHERE ci.id = ?');
                    $stmt->execute([ $date, $recipient['id'] ]);
                    $stmt = $pdo->prepare('UPDATE newsletters SET last_scheduled = ? WHERE id = ?');
                    $stmt->execute([ $date, $newsletter['id'] ]);
                    header('Location: campaign_view.php?id=' . $_GET['id'] . '&success_msg=2');
                } else {
                    $stmt = $pdo->prepare('UPDATE campaign_items ci SET ci.update_date = ?, ci.status = "Failed", ci.fail_text = ? WHERE ci.id = ?');
                    $stmt->execute([ $date, $response, $recipient['id'] ]);        
                    header('Location: campaign_view.php?id=' . $_GET['id'] . '&success_msg=3');   
                }
            }
        }
    }
    exit;
}
// Determine the URL
$url = 'campaign_view.php?id=' . $campaign['id'];
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Recipient deleted successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Newsletter sent successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Failed to send newsletter! Please check the error log associated with the subscriber!';
    }
}
?>
<?=template_admin_header(htmlspecialchars($campaign['title'], ENT_QUOTES) . ' - Campaign', 'campaigns', 'view')?>

<div class="content-title responsive-flex-wrap responsive-pad-bot-3">
    <h2 class="responsive-width-100"><a href="campaigns.php">Campaigns<i class="fa-solid fa-angles-right"></i></a><?=htmlspecialchars($campaign['title'], ENT_QUOTES)?></h2>
    <a href="campaigns.php" class="btn alt mar-right-2">Cancel</a>
    <a href="campaigns.php?delete=<?=$campaign['id']?>" class="btn red mar-right-2" onclick="return confirm('Are you sure you want to delete this campaign?')">Delete</a>
    <a href="campaign.php?id=<?=$campaign['id']?>" class="btn">Edit</a>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg <?=$_GET['success_msg']==3?'error':'success'?>">
    <?php if ($_GET['success_msg']==3): ?>
    <i class="fa-solid fa-circle-exclamation"></i>
    <?php else: ?>
    <i class="fas fa-check-circle"></i>
    <?php endif; ?>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>

<div class="campaign-stats ajax-update">
    <div class="content-block stat">
        <div class="ratio" style="--ratio: <?=$campaign['total_items'] ? 1-((($campaign['total_completed_items'] * 100) / $campaign['total_items']) / 100) : 0?>;">
            <span class="percentage"><?=$campaign['total_items'] ? number_format(($campaign['total_completed_items'] * 100) / $campaign['total_items'], 1) : 0?>%</span>
        </div>
        <div class="data">
            <span class="title">Sent</span>
            <span class="val"><?=number_format($campaign['total_completed_items'])?> <span>/ <?=number_format($campaign['total_items'])?></span></span>
        </div>
    </div>
    <div class="content-block stat">
        <div class="ratio" style="--ratio: <?=$campaign['total_items'] ? 1-((($campaign['total_opens'] * 100) / $campaign['total_items']) / 100) : 0?>;">
            <span class="percentage"><?=$campaign['total_items'] ? number_format(($campaign['total_opens'] * 100) / $campaign['total_items'], 1) : 0?>%</span>
        </div>
        <div class="data">
            <span class="title">Opens</span>
            <span class="val"><?=number_format($campaign['total_opens'])?> <span>/ <?=number_format($campaign['total_items'])?></span></span>
        </div>
    </div>
    <div class="content-block stat">
        <div class="ratio" style="--ratio: <?=$campaign['total_items'] ? 1-((($campaign['total_clicks'] * 100) / $campaign['total_items']) / 100) : 0?>;">
            <span class="percentage"><?=$campaign['total_items'] ? number_format(($campaign['total_clicks'] * 100) / $campaign['total_items'], 1) : 0?>%</span>
        </div>
        <div class="data">
            <span class="title">Clicks</span>
            <span class="val"><?=number_format($campaign['total_clicks'])?> <span>/ <?=number_format($campaign['total_items'])?></span></span>
        </div>
    </div>
    <div class="content-block stat">
        <div class="ratio red" style="--ratio: <?=$campaign['total_items'] ? 1-((($campaign['total_failed_items'] * 100) / $campaign['total_items']) / 100) : 0?>;">
            <span class="percentage"><?=$campaign['total_items'] ? number_format(($campaign['total_failed_items'] * 100) / $campaign['total_items'], 1) : 0?>%</span>
        </div>
        <div class="data">
            <span class="title">Fails</span>
            <span class="val"><?=number_format($campaign['total_failed_items'])?> <span>/ <?=number_format($campaign['total_items'])?></span></span>
        </div>
    </div>
    <div class="content-block stat">
        <div class="ratio red" style="--ratio: <?=$campaign['total_items'] ? 1-((($campaign['total_unsubscribes'] * 100) / $campaign['total_items']) / 100) : 0?>;">
            <span class="percentage"><?=$campaign['total_items'] ? number_format(($campaign['total_unsubscribes'] * 100) / $campaign['total_items'], 1) : 0?>%</span>
        </div>
        <div class="data">
            <span class="title">Unsubscribes</span>
            <span class="val"><?=number_format($campaign['total_unsubscribes'])?> <span>/ <?=number_format($campaign['total_items'])?></span></span>
        </div>
    </div>     
</div>

<div class="content-title">
    <h2>Recipients</h2>
</div>

<div class="content-block">
    <div class="table ajax-update">
        <table>
            <thead>
                <tr>
                    <td style="padding-right:15px">#</td>
                    <td colspan="2"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=email'?>">Email<?php if ($order_by=='email'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=is_read'?>">Read<?php if ($order_by=='is_read'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=clicked'?>">Clicked<?php if ($order_by=='clicked'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=unsubscribed'?>">Unsubscribed<?php if ($order_by=='unsubscribed'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=status'?>">Status<?php if ($order_by=='status'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=update_date'?>">Date Updated<?php if ($order_by=='update_date'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recipients)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no recipients</td>
                </tr>
                <?php else: ?>
                <?php foreach ($recipients as $k => $recipient): ?>
                <tr>
                    <td><?=$k+1?></td>
                    <td class="img">
                        <span style="background-color:<?=color_from_string($recipient['email'])?>"><?=strtoupper(substr($recipient['email'], 0, 1))?></span>
                    </td>
                    <td><?=htmlspecialchars($recipient['email'], ENT_QUOTES)?></td>
                    <td><?=$recipient['is_read']?'<span class="dot" title="Yes"></span>':'<span class="dot red" title="No"></span>'?></td>
                    <td><?=$recipient['clicked']?'<span class="dot" title="Yes"></span>':'<span class="dot red" title="No"></span>'?></td>
                    <td><?=$recipient['unsubscribed']?'<span class="dot" title="Yes"></span>':'<span class="dot red" title="No"></span>'?></td>
                    <td><?=$recipient['status']?><?=$recipient['fail_text']?' <a href="#" class="link1 recipient-error" title="' . $recipient['fail_text'] . '">[?]</a>':''?></td>
                    <td><?=$recipient['update_date']==null?'--':date('F j, Y H:ia', strtotime($recipient['update_date']))?></td>
                    <td>
                        <a href="campaign_view.php?id=<?=$_GET['id']?>&send=<?=$recipient['id']?>" class="link1" onclick="return confirm('Are you sure you want to perform this action?')">Send</a>
                        <a href="campaign_view.php?id=<?=$_GET['id']?>&delete=<?=$recipient['id']?>" class="link1" onclick="return confirm('Are you sure you want to delete this recipient?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>