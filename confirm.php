<?php
include 'main.php';
// Ensure GET ID parameter exists
if (isset($_GET['id'])) {
    // Attempt tgo retrieve the subscriber based on the ID param
    $stmt = $pdo->prepare('SELECT * FROM subscribers WHERE SHA1(CONCAT(id,email)) = ?');
    $stmt->execute([ $_GET['id'] ]);
    $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);
    // If subscriber exists
    if ($subscriber) {
        // Subscriber confirmed! Update the confirm column
        $stmt = $pdo->prepare('UPDATE subscribers SET confirmed = 1 WHERE id = ?');
        $stmt->execute([ $subscriber['id'] ]); 
        // Output response
        exit('Thank you for confirming your subscription!');
    } else {
        exit('Invalid ID!');
    }
} else {
    exit('Invalid ID!');
}
?>