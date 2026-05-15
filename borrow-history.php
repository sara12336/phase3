<?php
require_once 'includes/config.php';
requireLogin();
$user_id = $_SESSION['user_id'];
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$year = $_GET['year'] ?? 'all';
$limit = 5;
$offset = ($page - 1) * $limit;
$sql = "SELECT b.title, b.author, br.borrow_date, br.return_date, br.status, br.fine FROM borrowings br JOIN books b ON br.book_id = b.id WHERE br.user_id = ?";
$params = [$user_id];
if ($year !== 'all') {
    $sql .= " AND YEAR(br.borrow_date) = ?";
    $params[] = $year;
}
$countSql = str_replace("SELECT b.title, b.author, br.borrow_date, br.return_date, br.status, br.fine", "SELECT COUNT(*)", $sql);
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalRecords = $stmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);
$sql .= " ORDER BY br.borrow_date DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$history = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YIC Library | History</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="container nav-container">
                <div class="logo">
                    <i class="fas fa-book-open">
                    </i>
                    <span>YIC Library</span>
                </div>
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars">
                    </i>
                </button>
                <ul class="nav-menu" id="navMenu">
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="books.php">Books</a>
                    </li>
                    <li>
                        <a href="dashboard.php">Dashboard</a>
                    </li>
                    <li>
                        <a href="borrow-history.php" class="active">History</a>
                    </li>
                    <li>
                        <a href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <section class="page-header">
            <div class="container">
                <h1>Borrowing History</h1>
                <p>View all books you've borrowed</p>
            </div>
        </section>
        <section class="history-section">
            <div class="container">
                <div class="history-filters">
                    <div class="filter-tabs">
                        <a href="?year=all&page=1" class="filter-tab <?php echo $year === 'all' ? 'active' : ''; ?>">All
                            Time</a>
                        <a href="?year=2025&page=1"
                            class="filter-tab <?php echo $year === '2025' ? 'active' : ''; ?>">2025</a>
                        <a href="?year=2024&page=1"
                            class="filter-tab <?php echo $year === '2024' ? 'active' : ''; ?>">2024</a>
                    </div>
                </div>
                <div class="history-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Borrowed</th>
                                <th>Returned</th>
                                <th>Status</th>
                                <th>Fine</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($history) > 0):
                                foreach ($history as $r): ?>
                                    <tr>
                                        <td>
                                            <?php echo escape($r['title']); ?>
                                        </td>
                                        <td>
                                            <?php echo escape($r['author']); ?>
                                        </td>
                                        <td>
                                            <?php echo escape($r['borrow_date']); ?>
                                        </td>
                                        <td>
                                            <?php echo $r['return_date'] ? escape($r['return_date']) : '—'; ?>
                                        </td>
                                        <td>
                                            <span
                                                class="status-badge <?php echo $r['status'] === 'returned' ? 'status-available' : 'badge-due-soon'; ?>">
                                                <?php echo ucfirst(escape($r['status'])); ?>
                                            </span>
                                        </td>
                                        <td>$<?php echo number_format($r['fine'], 2); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="6">No history</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?year=<?php echo $year; ?>&page=<?php echo $i; ?>"
                                class="<?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>YIC Library</h4>
                    <p>Supporting education and research at Yanbu Industrial College.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li>
                            <a href="index.php">Home</a>
                        </li>
                        <li>
                            <a href="books.php">Books</a>
                        </li>
                        <li>
                            <a href="#">FAQs</a>
                        </li>
                        <li>
                            <a href="#">Contact</a>
                        </li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p>
                        <i class="fas fa-envelope">
                        </i> library@yic.edu.sa
                    </p>
                    <p>
                        <i class="fas fa-phone">
                        </i> +966 14 123 4567
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 YIC Library System</p>
            </div>
        </div>
    </footer>
    <script src="js/main.js">
    </script>
</body>

</html>