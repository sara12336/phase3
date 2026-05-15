<?php
require_once 'includes/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'all';
$availability = $_GET['availability'] ?? 'all';

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
    $p = "%$search%";
    $params = array_merge($params, [$p, $p, $p]);
}
if ($category !== 'all') { $sql .= " AND category = ?"; $params[] = $category; }

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

if ($availability === 'available') $books = array_filter($books, fn($b) => $b['available_copies'] > 0);
elseif ($availability === 'borrowed') $books = array_filter($books, fn($b) => $b['available_copies'] == 0);
$books = array_values($books);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YIC Library | Books</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="container nav-container">
                <div class="logo"><i class="fas fa-book-open"></i><span>YIC Library</span></div><button
                    class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="books.php" class="active">Books</a></li><?php if(isset($_SESSION['user_id'])): ?><li><a
                            href="dashboard.php">Dashboard</a></li>
                    <li><a href="borrow-history.php">History</a></li>
                    <li><a href="logout.php">Logout</a></li><?php else: ?><li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li><?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <section class="page-header">
            <div class="container">
                <h1>Library Collection</h1>
                <p>Browse our extensive collection of books</p>
            </div>
        </section>
        <section class="books-section">
            <div class="container">
                <div class="search-filter-bar">
                    <div class="search-box"><i class="fas fa-search"></i><input type="text" id="searchInput"
                            placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>"></div>
                    <div class="filter-group"><select id="categoryFilter">
                            <option value="all" <?php echo $category==='all'?'selected':''; ?>>All Categories</option>
                            <option value="fiction" <?php echo $category==='fiction'?'selected':''; ?>>Fiction</option>
                            <option value="nonfiction" <?php echo $category==='nonfiction'?'selected':''; ?>>Non-Fiction
                            </option>
                            <option value="academic" <?php echo $category==='academic'?'selected':''; ?>>Academic
                            </option>
                            <option value="science" <?php echo $category==='science'?'selected':''; ?>>Science</option>
                        </select><select id="availabilityFilter">
                            <option value="all" <?php echo $availability==='all'?'selected':''; ?>>All Books</option>
                            <option value="available" <?php echo $availability==='available'?'selected':''; ?>>Available
                                Only</option>
                            <option value="borrowed" <?php echo $availability==='borrowed'?'selected':''; ?>>Borrowed
                                Only</option>
                        </select></div>
                </div>
                <div class="results-info"><span>Showing <?php echo count($books); ?> books</span></div>
                <div class="books-grid">
                    <?php foreach($books as $book): $statusClass = $book['available_copies']>0 ? 'status-available' : 'status-borrowed'; $statusText = $book['available_copies']>0 ? 'Available' : 'Borrowed'; ?>
                    <div class="book-card">
                        <div class="book-info">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p>by <?php echo htmlspecialchars($book['author']); ?></p><span
                                class="book-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                            <div class="book-actions"><?php if(isset($_SESSION['user_id'])): ?><form method="POST"
                                    action="actions.php"><input type="hidden" name="book_id"
                                        value="<?php echo $book['id']; ?>"><button type="submit"
                                        class="btn btn-sm btn-primary"
                                        <?php echo $book['available_copies']<=0 ? 'disabled' : ''; ?>
                                        onclick="return confirm('Borrow?')"><i class="fas fa-book"></i> Borrow</button>
                                </form><?php else: ?><button class="btn btn-sm btn-primary"
                                    <?php echo $book['available_copies']<=0 ? 'disabled' : ''; ?>
                                    onclick="alert('Please login')"><i class="fas fa-book"></i>
                                    Borrow</button><?php endif; ?><button class="btn btn-sm btn-outline"
                                    onclick="alert('<?php echo htmlspecialchars($book['title']); ?>\n<?php echo htmlspecialchars($book['author']); ?>\n<?php echo $statusText; ?>')"><i
                                        class="fas fa-info-circle"></i> Details</button></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
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
    <script>
    document.getElementById('searchInput').addEventListener('input', function() {
        window.location.href = 'books.php?search=' + encodeURIComponent(this.value) + '&category=' +
            encodeURIComponent(document.getElementById('categoryFilter').value) + '&availability=' +
            encodeURIComponent(document.getElementById('availabilityFilter').value);
    });
    document.getElementById('categoryFilter').addEventListener('change', function() {
        window.location.href = 'books.php?search=' + encodeURIComponent(document.getElementById('searchInput')
            .value) + '&category=' + encodeURIComponent(this.value) + '&availability=' + encodeURIComponent(
            document.getElementById('availabilityFilter').value);
    });
    document.getElementById('availabilityFilter').addEventListener('change', function() {
        window.location.href = 'books.php?search=' + encodeURIComponent(document.getElementById('searchInput')
                .value) + '&category=' + encodeURIComponent(document.getElementById('categoryFilter').value) +
            '&availability=' + encodeURIComponent(this.value);
    });
    </script>
</body>

</html>