<?php
include 'main.php';
// Ensure post variable exists
if (isset($_POST['email'])) {
    // Validate email address
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        exit('Please provide a valid email address!');
    }
    // Check if email exists in database
    $stmt = $pdo->prepare('SELECT * FROM subscribers WHERE email = ?');
    $stmt->execute([ $_POST['email'] ]);
    $subscriber = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($subscriber) {
        // The user previously subscribed, so all we need to do is update the status
        if ($subscriber['status'] == 'Unsubscribed') {
            $stmt = $pdo->prepare('UPDATE subscribers SET status = "Subscribed" WHERE id = ?');
            $stmt->execute([ $subscriber['id'] ]);
            exit('Thank you for subscribing!');
        } else {
            exit('You\'re already a subscriber!');
        }
    }
    // Insert email address into the database
    $date = date('Y-m-d\TH:i:s');
    $confirmed = email_confirmation ? 0 : 1;
    $stmt = $pdo->prepare('INSERT INTO subscribers (email,date_subbed,confirmed) VALUES (?,?,?)');
    $stmt->execute([ $_POST['email'], $date, $confirmed ]);
    $id = $pdo->lastInsertId();
    // If subscriber isn't confirmed, send confirmation email
    if (!$confirmed) {
        // Send activation email
        include 'admin/functions.php';
        $response = send_confirmation_email($_POST['email'], sha1($id . $_POST['email']));
        if ($response == 'success') {
            exit('Please confirm your subscription via email!');
        } else {
            exit('Unable to send confirmation email! Please contact the webmaster!');
        }
    } else {
        // Output success response
        exit('Thank you for subscribing!');
    }
} else {
    // No post data specified
    exit('Please provide a valid email address!');
}
?>