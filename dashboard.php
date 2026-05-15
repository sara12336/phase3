<?php
require_once 'includes/config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$student_id = $_SESSION['student_id'];

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND status = 'borrowed'");
$stmt->execute([$user_id]);
$borrowedCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status = 'borrowed'");
$stmt->execute([$user_id]);
$dueThisWeek = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ?");
$stmt->execute([$user_id]);
$totalBorrowed = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT SUM(fine) FROM borrowings WHERE user_id = ? AND status != 'borrowed'");
$stmt->execute([$user_id]);
$totalFines = $stmt->fetchColumn() ?? 0;

$stmt = $pdo->prepare("SELECT b.title, b.author, br.borrow_date, br.due_date, br.status, br.id as borrowing_id FROM borrowings br JOIN books b ON br.book_id = b.id WHERE br.user_id = ? AND br.status = 'borrowed' ORDER BY br.due_date ASC");
$stmt->execute([$user_id]);
$currentBorrowings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YIC Library | Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="container nav-container">
                <div class="logo"><i class="fas fa-book-open"></i><span>YIC Library</span></div>
                <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="books.php">Books</a></li>
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="borrow-history.php">History</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <section class="dashboard-header">
            <div class="container">
                <div class="user-welcome"><i class="fas fa-user-circle"></i>
                    <div>
                        <h1>Welcome back, <?php echo $user_name; ?>!</h1>
                        <p>ID: <?php echo $student_id; ?> | Member since <?php echo date('M Y'); ?></p>
                    </div>
                </div>
            </div>
        </section>
        <?php if($success): ?><div class="container">
            <div class="alert alert-success"><?php echo $success; ?></div>
        </div><?php endif; ?>
        <?php if($error): ?><div class="container">
            <div class="alert alert-error"><?php echo $error; ?></div>
        </div><?php endif; ?>
        <section class="dashboard-stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card"><i class="fas fa-book"></i>
                        <div>
                            <h3><?php echo $borrowedCount; ?></h3>
                            <p>Currently Borrowed</p>
                        </div>
                    </div>
                    <div class="stat-card"><i class="fas fa-calendar-alt"></i>
                        <div>
                            <h3><?php echo $dueThisWeek; ?></h3>
                            <p>Due This Week</p>
                        </div>
                    </div>
                    <div class="stat-card"><i class="fas fa-history"></i>
                        <div>
                            <h3><?php echo $totalBorrowed; ?></h3>
                            <p>Total Borrowed</p>
                        </div>
                    </div>
                    <div class="stat-card"><i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <h3>$<?php echo number_format($totalFines,2); ?></h3>
                            <p>Fines</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="current-borrowings">
            <div class="container">
                <h2 class="section-title">Currently Borrowed Books</h2>
                <div class="borrowings-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Borrowed</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($currentBorrowings)>0): foreach($currentBorrowings as $b): $today=new DateTime(); $dueDate=new DateTime($b['due_date']); $diff=$today->diff($dueDate)->days; $status=$diff<=3?'Due Soon':'On Time'; $statusClass=$diff<=3?'badge-due-soon':''; ?>
                            <tr>
                                <td><?php echo $b['title']; ?></td>
                                <td><?php echo $b['author']; ?></td>
                                <td><?php echo $b['borrow_date']; ?></td>
                                <td><?php echo $b['due_date']; ?></td>
                                <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $status; ?></span>
                                </td>
                                <td>
                                    <form method="POST" action="actions.php"><input type="hidden" name="borrowing_id"
                                            value="<?php echo $b['borrowing_id']; ?>"><button type="submit"
                                            class="btn btn-sm btn-primary"
                                            onclick="return confirm('Renew?')">Renew</button></form>
                                </td>
                            </tr>
                            <?php endforeach; else: ?><tr>
                                <td colspan="6">No books borrowed</td>
                            </tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
                        <li><a href="index.html">Home</a></li>
                        <li><a href="books.php">Books</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p><i class="fas fa-envelope"></i> library@yic.edu.sa</p>
                    <p><i class="fas fa-phone"></i> +966 14 123 4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 YIC Library System</p>
            </div>
        </div>
    </footer>
    <script src="js/main.js"></script>
</body>

</html>