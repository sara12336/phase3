<?php
require_once 'includes/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit(); }

$user_id = $_SESSION['user_id'];

// Borrow Book
if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    
    // Check if user already has this book borrowed
    $stmt = $pdo->prepare("SELECT id FROM borrowings WHERE user_id = ? AND book_id = ? AND status = 'borrowed'");
    $stmt->execute([$user_id, $book_id]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "You already have this book! Return it first.";
        header('Location: dashboard.php');
        exit();
    }
    
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT available_copies FROM books WHERE id = ? FOR UPDATE");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();
        if ($book && $book['available_copies'] > 0) {
            $dueDate = date('Y-m-d', strtotime('+14 days'));
            $stmt = $pdo->prepare("INSERT INTO borrowings (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'borrowed')");
            $stmt->execute([$user_id, $book_id, date('Y-m-d'), $dueDate]);
            $stmt = $pdo->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?");
            $stmt->execute([$book_id]);
            $pdo->commit();
            $_SESSION['success'] = "Book borrowed! Due: $dueDate";
        } else $_SESSION['error'] = "Book not available";
    } catch (Exception $e) { $pdo->rollBack(); $_SESSION['error'] = "Failed to borrow"; }
}

// Renew Book
if (isset($_POST['borrowing_id'])) {
    $borrowing_id = $_POST['borrowing_id'];
    $stmt = $pdo->prepare("SELECT id FROM borrowings WHERE id = ? AND user_id = ? AND status = 'borrowed'");
    $stmt->execute([$borrowing_id, $user_id]);
    if ($stmt->fetch()) {
        $newDueDate = date('Y-m-d', strtotime('+14 days'));
        $stmt = $pdo->prepare("UPDATE borrowings SET due_date = ? WHERE id = ?");
        $stmt->execute([$newDueDate, $borrowing_id]);
        $_SESSION['success'] = "Book renewed! New due: $newDueDate";
    } else $_SESSION['error'] = "Cannot renew this book";
}

header('Location: dashboard.php');
exit();
?>