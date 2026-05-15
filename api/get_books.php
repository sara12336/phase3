<?php
require_once '../includes/config.php';
header('Content-Type: application/json');
try {
    $stmt = $pdo->query("SELECT id, title, author, available_copies FROM books ORDER BY id LIMIT 8");
    $books = $stmt->fetchAll();
    $booksArray = [];
    foreach($books as $book) {
        $booksArray[] = [
            'id' => $book['id'],
            'title' => $book['title'],
            'author' => $book['author'],
            'available' => $book['available_copies'] > 0,
            'available_copies' => $book['available_copies']
        ];
    }
    echo json_encode(['success' => true, 'books' => $booksArray]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>