<?php
include 'main.php';
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','last_scheduled','submit_date','sent_items','opens','clicks','unsubscribes'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 20;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (n.title LIKE :search OR n.content LIKE :search) ' : '';
// Retrieve the total number of newsletters
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM newsletters n ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$newsletters_total = $stmt->fetchColumn();
// SQL query to get all newsletters from the "newsletters" table
$stmt = $pdo->prepare('SELECT 
    n.*, 
    (SELECT COUNT(*) FROM campaigns c JOIN campaign_clicks cc ON cc.campaign_id = c.id WHERE c.newsletter_id = n.id) AS clicks,
    (SELECT COUNT(*) FROM campaigns c JOIN campaign_opens co ON co.campaign_id = c.id WHERE c.newsletter_id = n.id) AS opens, 
    (SELECT COUNT(*) FROM campaigns c JOIN campaign_unsubscribes cu ON cu.campaign_id = c.id WHERE c.newsletter_id = n.id) AS unsubscribes,   
    (SELECT COUNT(*) FROM campaigns c JOIN campaign_items ci ON ci.campaign_id = c.id AND ci.status = "Completed" WHERE c.newsletter_id = n.id) AS sent_items, 
    (SELECT COUNT(*) FROM campaigns c JOIN campaign_items ci ON ci.campaign_id = c.id WHERE c.newsletter_id = n.id) AS total_items   
    FROM newsletters n ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results
');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$newsletters = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Newsletter created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Newsletter updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Newsletter deleted successfully!';
    }
}
// Determine the URL
$url = 'newsletters.php?search=' . $search;
?>
<?=template_admin_header('Newsletters', 'newsletters', 'view')?>

<div class="content-title">
    <h2>Newsletters</h2>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="fas fa-check-circle"></i>
    <p><?=$success_msg?></p>
    <i class="fas fa-times"></i>
</div>
<?php endif; ?>


<div class="content-header responsive-flex-column pad-top-5">
    <a href="newsletter.php" class="btn">Create Newsletter</a>
    <form action="" method="get">
        <div class="search">
            <label for="search">
                <input id="search" type="text" name="search" placeholder="Search newsletter..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
                <i class="fas fa-search"></i>
            </label>
        </div>
    </form>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>">#<?php if ($order_by=='id'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>">Title<?php if ($order_by=='title'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=sent_items'?>">Sent<?php if ($order_by=='sent_items'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=opens'?>">Opens<?php if ($order_by=='opens'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=clicks'?>">Clicks<?php if ($order_by=='clicks'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=unsubscribes'?>">Unsubscribes<?php if ($order_by=='unsubscribes'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=last_scheduled'?>">Last Scheduled<?php if ($order_by=='last_scheduled'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=submit_date'?>">Submit Date<?php if ($order_by=='submit_date'): ?><i class="fas fa-level-<?=str_replace(['ASC', 'DESC'], ['up','down'], $order)?>-alt fa-xs"></i><?php endif; ?></a></td>    
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($newsletters)): ?>
                <tr>
                    <td colspan="10" style="text-align:center;">There are no newsletters</td>
                </tr>
                <?php else: ?>
                <?php foreach ($newsletters as $newsletter): ?>
                <tr>
                    <td class="responsive-hidden"><?=$newsletter['id']?></td>
                    <td><?=htmlspecialchars($newsletter['title'], ENT_QUOTES)?></td>
                    <td>
                        <div class="progress">
                            <span class="txt"><?=$newsletter['sent_items']?> of <?=$newsletter['total_items']?></span>
                            <div class="bot">
                                <span class="per"><?=$newsletter['total_items'] ? number_format(($newsletter['sent_items'] * 100) / $newsletter['total_items']) : 0?>%</span>
                                <div class="bar">
                                    <span style="width:<?=$newsletter['total_items'] ? ($newsletter['sent_items'] * 100) / $newsletter['total_items'] : 0?>%"></span>
                                </div>
                            </div>
                        </div>    
                    </td>
                    <td>
                        <div class="progress">
                            <span class="txt"><?=$newsletter['opens']?> of <?=$newsletter['total_items']?></span>
                            <div class="bot">
                                <span class="per"><?=$newsletter['total_items'] ? number_format(($newsletter['opens'] * 100) / $newsletter['total_items']) : 0?>%</span>
                                <div class="bar">
                                    <span style="width:<?=$newsletter['total_items'] ? ($newsletter['opens'] * 100) / $newsletter['total_items'] : 0?>%"></span>
                                </div>
                            </div>
                        </div>  
                    </td>
                    <td>
                        <div class="progress">
                            <span class="txt"><?=$newsletter['clicks']?> of <?=$newsletter['total_items']?></span>
                            <div class="bot">
                                <span class="per"><?=$newsletter['total_items'] ? number_format(($newsletter['clicks'] * 100) / $newsletter['total_items']) : 0?>%</span>
                                <div class="bar">
                                    <span style="width:<?=$newsletter['total_items'] ? ($newsletter['clicks'] * 100) / $newsletter['total_items'] : 0?>%"></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="progress">
                            <span class="txt"><?=$newsletter['unsubscribes']?> of <?=$newsletter['total_items']?></span>
                            <div class="bot">
                                <span class="per"><?=$newsletter['total_items'] ? number_format(($newsletter['unsubscribes'] * 100) / $newsletter['total_items']) : 0?>%</span>
                                <div class="bar">
                                    <span class="red" style="width:<?=$newsletter['total_items'] ? ($newsletter['unsubscribes'] * 100) / $newsletter['total_items'] : 0?>%"></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="responsive-hidden"><?=$newsletter['last_scheduled']==null?'--':date('F j, Y H:ia', strtotime($newsletter['last_scheduled']))?></td>
                    <td class="responsive-hidden"><?=date('F j, Y H:ia', strtotime($newsletter['submit_date']))?></td>
                    <td>
                        <a href="newsletter.php?copy=<?=$newsletter['id']?>" class="link1">Copy</a>
                        <a href="newsletter.php?id=<?=$newsletter['id']?>" class="link1">Edit</a>
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
    <span>Page <?=$pagination_page?> of <?=ceil($newsletters_total / $results_per_page) == 0 ? 1 : ceil($newsletters_total / $results_per_page)?></span>
    <?php if ($pagination_page * $results_per_page < $newsletters_total): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>