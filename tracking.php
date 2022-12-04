<?php
include 'main.php';
// Ensure the action and ID GET parameters exist
if (isset($_GET['action'], $_GET['id'])) {
    // Verify the subscriber
    $stmt = $pdo->prepare('SELECT c.*, ci.*, s.* FROM campaigns c JOIN campaign_items ci ON ci.campaign_id = c.id JOIN subscribers s ON s.id = ci.subscriber_id WHERE SHA1(CONCAT(c.id, ci.id, s.email)) = ?');
    $stmt->execute([ $_GET['id'] ]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    // If verification successful...
    if ($item) {
        // Handle click events
        if ($_GET['action'] == 'click') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO campaign_clicks (campaign_id,subscriber_id,submit_date) VALUES (?,?,?)');
            $stmt->execute([ $item['campaign_id'], $item['subscriber_id'], date('Y-m-d H:i:s') ]);   
            header('Location: ' . (isset($_GET['url']) ? $_GET['url'] : website_url));
            exit;
        }   
        // Handle open message events
        if ($_GET['action'] == 'open') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO campaign_opens (campaign_id,subscriber_id,submit_date) VALUES (?,?,?)');
            $stmt->execute([ $item['campaign_id'], $item['subscriber_id'], date('Y-m-d H:i:s') ]);  
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Disposition: attachment; filename="' . $_GET['id']  . '.gif"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize('message_open_tracking.gif'));
            readfile('message_open_tracking.gif');
            exit;
        }
        // Handle unsubscribe events
        if ($_GET['action'] == 'unsubscribe') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO campaign_unsubscribes (campaign_id,subscriber_id,submit_date) VALUES (?,?,?)');
            $stmt->execute([ $item['campaign_id'], $item['subscriber_id'], date('Y-m-d H:i:s') ]);   
            $stmt = $pdo->prepare('UPDATE subscribers SET status = "Unsubscribed" WHERE id = ?');
            $stmt->execute([ $item['subscriber_id'] ]);   
            exit('You\'ve successfully unsubscribed! You will no longer receive emails from us.');
        }
    } else {
        exit('Invalid ID!');
    }
}
?>