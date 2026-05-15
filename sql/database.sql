CREATE DATABASE IF NOT EXISTS yic_library;
USE yic_library;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category ENUM('fiction', 'nonfiction', 'academic', 'science') NOT NULL,
    isbn VARCHAR(20),
    available_copies INT DEFAULT 1,
    total_copies INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    fine DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('borrowed', 'returned', 'overdue') DEFAULT 'borrowed',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

INSERT INTO users (first_name, last_name, email, student_id, password, role) VALUES
('Ahmed', 'Ali', 'ahmed@yic.edu.sa', 'YIC-2025-1001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Sara', 'Khaled', 'sara@yic.edu.sa', 'YIC-2025-1002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Omar', 'Ahmed', 'omar@yic.edu.sa', 'YIC-2025-1003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Fatima', 'Mohammed', 'fatima@yic.edu.sa', 'YIC-2025-1004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Yousef', 'Hassan', 'yousef@yic.edu.sa', 'YIC-2025-1005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Admin', 'YIC', 'admin@yic.edu.sa', 'ADMIN-001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO books (title, author, category, isbn, available_copies, total_copies) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 'fiction', '978-0-7432-7356-5', 3, 3),
('1984', 'George Orwell', 'fiction', '978-0-452-28423-4', 3, 3),
('To Kill a Mockingbird', 'Harper Lee', 'fiction', '978-0-06-112008-4', 3, 3),
('Introduction to Algorithms', 'Thomas H. Cormen', 'academic', '978-0-262-03384-8', 3, 3),
('Clean Code', 'Robert C. Martin', 'academic', '978-0-13-235088-4', 3, 3),
('A Brief History of Time', 'Stephen Hawking', 'science', '978-0-553-38016-3', 3, 3),
('Sapiens', 'Yuval Noah Harari', 'nonfiction', '978-0-06-231609-7', 3, 3),
('The Pragmatic Programmer', 'David Thomas', 'academic', '978-0-201-61622-4', 3, 3),
('Dune', 'Frank Herbert', 'fiction', '978-0-441-17271-9', 3, 3),
('The Selfish Gene', 'Richard Dawkins', 'science', '978-0-19-929115-1', 3, 3),
('Thinking Fast and Slow', 'Daniel Kahneman', 'nonfiction', '978-0-374-53355-7', 3, 3),
('The Art of Computer Programming', 'Donald Knuth', 'academic', '978-0-201-89683-1', 3, 3);

INSERT INTO borrowings (user_id, book_id, borrow_date, due_date, return_date, fine, status) VALUES
(1, 5, '2025-03-20', '2025-04-03', NULL, 0.00, 'borrowed'),
(1, 2, '2025-03-25', '2025-04-08', NULL, 0.00, 'borrowed'),
(1, 4, '2025-03-28', '2025-04-11', NULL, 0.00, 'borrowed'),
(1, 1, '2025-01-10', '2025-01-24', '2025-01-24', 0.00, 'returned'),
(1, 7, '2025-01-15', '2025-01-29', '2025-01-29', 0.00, 'returned'),
(1, 9, '2025-02-01', '2025-02-15', '2025-02-15', 0.00, 'returned'),
(1, 8, '2025-02-10', '2025-02-24', '2025-02-24', 0.00, 'returned'),
(1, 6, '2025-02-20', '2025-03-06', '2025-03-10', 5.00, 'returned'),
(1, 3, '2025-03-01', '2025-03-15', '2025-03-15', 0.00, 'returned'),
(2, 1, '2025-03-15', '2025-03-29', NULL, 0.00, 'borrowed'),
(2, 10, '2025-03-20', '2025-04-03', NULL, 0.00, 'borrowed'),
(3, 7, '2025-03-10', '2025-03-24', '2025-03-24', 0.00, 'returned');