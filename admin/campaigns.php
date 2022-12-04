<?php
include 'main.php';
// Delete campaign
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE c, ci, cc, co, cu FROM campaigns c LEFT JOIN campaign_items ci ON ci.campaign_id = c.id LEFT JOIN campaign_clicks cc ON cc.campaign_id = c.id LEFT JOIN campaign_opens co ON co.campaign_id = c.id LEFT JOIN campaign_unsubscribes cu ON cu.campaign_id = c.id WHERE c.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: campaigns.php?success_msg=3');
    exit;
}
// Update campaign
if (isset($_GET['update'], $_GET['status'])) {
    $stmt = $pdo->prepare('UPDATE campaigns SET status = ? WHERE id = ?');
    $stmt->execute([ $_GET['status'], $_GET['update'] ]);
    exit('success');  
}
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','total_items','total_completed_items','total_failed_items','total_clicks','total_unsubscribes','total_opens','status','submit_date'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 20;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (title LIKE :search) ' : '';
// Retrieve the total number of campaigns
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM campaigns ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$campaigns_total = $stmt->fetchColumn();
// SQL query to get all campaigns from the "campaigns" table
$stmt = $pdo->prepare('SELECT 
    c.*, 
    (SELECT COUNT(*) FROM campaign_items ci WHERE ci.campaign_id = c.id) AS total_items,
    (SELECT COUNT(*) FROM campaign_items ci WHERE ci.campaign_id = c.id AND (ci.status = "Completed" OR ci.status = "Cancelled")) AS total_completed_items,
    (SELECT COUNT(*) FROM campaign_items ci WHERE ci.campaign_id = c.id AND ci.status = "Failed") AS total_failed_items,
    (SELECT COUNT(*) FROM campaign_clicks cc WHERE cc.campaign_id = c.id) AS total_clicks,
    (SELECT COUNT(*) FROM campaign_unsubscribes cu WHERE cu.campaign_id = c.id) AS total_unsubscribes,
    (SELECT COUNT(*) FROM campaign_opens co WHERE co.campaign_id = c.id) AS total_opens  
    FROM campaigns c ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results
');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Campaign created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Campaign updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Campaign deleted successfully!';
    }
}
// Determine the URL
$url = 'campaigns.php?search=' . $search . (isset($_GET['page_id']) ? '&page_id=' . $_GET['page_id'] : '');
?>
<?=template_admin_header('Campaigns', 'campaigns', 'view')?>

<div class="content-title">
    <h2>Campaigns</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>


<div class="content-header responsive-flex-column pad-top-5">
    <a href="campaign.php" class="btn">Create Campaign</a>
    <form action="" method="get">
        <div class="search">
            <label for="search">
                <input id="search" type="text" name="search" placeholder="Search campaigns..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
                <i class="fas fa-search"></i>
            </label>
        </div>
    </form>
</div>

<div class="content-block">
    <div class="table">
        <table class="ajax-update">
            <thead>
                <tr>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>">Title<?php if ($order_by=='title'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=total_completed_items'?>">Sent<?php if ($order_by=='total_completed_items'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=total_opens'?>">Opens<?php if ($order_by=='total_opens'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=total_clicks'?>">Clicks<?php if ($order_by=='total_clicks'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=total_failed_items'?>">Fails<?php if ($order_by=='total_failed_items'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=total_unsubscribes'?>">Unsubscribes<?php if ($order_by=='total_unsubscribes'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=status'?>">Status<?php if ($order_by=='status'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=submit_date'?>">Date Scheduled<?php if ($order_by=='submit_date'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($campaigns)): ?>
                <tr>
                    <td colspan="10" style="text-align:center;">There are no campaigns</td>
                </tr>
                <?php else: ?>
                <?php foreach ($campaigns as $campaign): ?>
                <tr>
                    <td><?=htmlspecialchars($campaign['title'], ENT_QUOTES)?></td>
                    <td>
                        <div class="progress">
                            <span class="txt"><?=$campaign['total_completed_items']?> of <?=$campaign['total_items']?></span>
                            <div class="bot">
                                <span class="per"><?=$campaign['total_items'] ? number_format(($campaign['total_completed_items'] * 100) / $campaign['total_items']) : 0?>%</span>
                                <div class="bar">
                                    <span style="width:<?=$campaign['total_items'] ? ($campaign['total_completed_items'] * 100) / $campaign['total_items'] : 0?>%"></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="responsive-hidden">
                        <div class="progress">
                            <span class="txt"><?=$campaign['total_opens']?> of <?=$campaign['total_items']?></span>
                            <div class="bot">
                                <span class="per"><?=$campaign['total_items'] ? number_format(($campaign['total_opens'] * 100) / $campaign['total_items']) : 0?>%</span>
                                <div class="bar">
                                    <span style="width:<?=$campaign['total_items'] ? ($campaign['total_opens'] * 100) / $campaign['total_items'] : 0?>%"></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="responsive-hidden">
                        <div class="progress">
                            <span class="txt"><?=$campaign['total_clicks']?> of <?=$campaign['total_items']?></span>
                            <div class="bot">
                                <span class="per"><?=$campaign['total_items'] ? number_format(($campaign['total_clicks'] * 100) / $campaign['total_items']) : 0?>%</span>
                                <div class="bar">
                                    <span style="width:<?=$campaign['total_items'] ? ($campaign['total_clicks'] * 100) / $campaign['total_items'] : 0?>%"></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="responsive-hidden">
                        <div class="progress">
                            <span class="txt"><?=$campaign['total_failed_items']?> of <?=$campaign['total_items']?></span>
                            <div class="bot">
                                <span class="per"><?=$campaign['total_items'] ? number_format(($campaign['total_failed_items'] * 100) / $campaign['total_items']) : 0?>%</span>
                                <div class="bar">
                                    <span class="red" style="width:<?=$campaign['total_items'] ? ($campaign['total_failed_items'] * 100) / $campaign['total_items'] : 0?>%"></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="responsive-hidden">
                        <div class="progress">
                            <span class="txt"><?=$campaign['total_unsubscribes']?> of <?=$campaign['total_items']?></span>
                            <div class="bot">
                                <span class="per"><?=$campaign['total_items'] ? number_format(($campaign['total_unsubscribes'] * 100) / $campaign['total_items']) : 0?>%</span>
                                <div class="bar">
                                    <span class="red" style="width:<?=$campaign['total_items'] ? ($campaign['total_unsubscribes'] * 100) / $campaign['total_items'] : 0?>%"></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="status-container">
                        <div class="status" data-id="<?=$campaign['id']?>">
                            <span title="<?=htmlspecialchars($campaign['status'], ENT_QUOTES)?>" class="<?=strtolower(htmlspecialchars($campaign['status'], ENT_QUOTES))?>"></span>
                            <i class="fa-solid fa-caret-down"></i>
                            <div class="dropdown">
                                <a href="#" data-value="Active">Active</a>
                                <a href="#" data-value="Inactive">Inactive</a>
                                <a href="#" data-value="Paused">Pause</a>
                                <a href="#" data-value="Cancelled">Cancel</a>
                            </div>
                        </div>
                    </td>
                    <td><?=date('F j, Y H:ia', strtotime($campaign['submit_date']))?></td>
                    <td>
                        <a href="campaign_view.php?id=<?=$campaign['id']?>" class="link1">View</a>
                        <a href="campaign.php?id=<?=$campaign['id']?>" class="link1">Edit</a>
                        <a href="campaigns.php?delete=<?=$campaign['id']?>" class="link1" onclick="return confirm('Are you sure you want to delete this campaign?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    <?php if ($pagination_page > 1): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Prev</a>
    <?php endif; ?>
    <span>Page <?=$pagination_page?> of <?=ceil($campaigns_total / $results_per_page) == 0 ? 1 : ceil($campaigns_total / $results_per_page)?></span>
    <?php if ($pagination_page * $results_per_page < $campaigns_total): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>