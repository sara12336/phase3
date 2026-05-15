document.addEventListener('DOMContentLoaded', function() {
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const navMenu = document.getElementById('navMenu');
    if (mobileBtn && navMenu) {
        mobileBtn.addEventListener('click', () => navMenu.classList.toggle('active'));
        navMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => navMenu.classList.remove('active')));
    }
    if (document.getElementById('loginForm')) initLoginValidation();
    if (document.getElementById('registerForm')) initRegisterValidation();
    if (document.getElementById('popularBooksGrid')) loadPopularBooksFromDB();
});

function initLoginValidation() {
    const form = document.getElementById('loginForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        let ok = true;
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            document.getElementById('emailError').textContent = 'Valid email required';
            ok = false;
        } else document.getElementById('emailError').textContent = '';
        if (!password.value || password.value.length < 6) {
            document.getElementById('passwordError').textContent = 'Password (min 6 chars) required';
            ok = false;
        } else document.getElementById('passwordError').textContent = '';
        if (!ok) e.preventDefault();
    });
}

function initRegisterValidation() {
    const form = document.getElementById('registerForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        let ok = true;
        const fname = document.getElementById('first_name');
        const lname = document.getElementById('last_name');
        const email = document.getElementById('email');
        const sid = document.getElementById('student_id');
        const pwd = document.getElementById('password');
        const cpwd = document.getElementById('confirm_password');
        const terms = document.getElementById('termsCheckbox');
        
        if (!fname.value.trim()) { document.getElementById('firstNameError').textContent = 'Required'; ok = false; } else document.getElementById('firstNameError').textContent = '';
        if (!lname.value.trim()) { document.getElementById('lastNameError').textContent = 'Required'; ok = false; } else document.getElementById('lastNameError').textContent = '';
        if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) { document.getElementById('regEmailError').textContent = 'Valid email required'; ok = false; } else document.getElementById('regEmailError').textContent = '';
        if (!sid.value.trim() || sid.value.trim().length < 5) { document.getElementById('studentIdError').textContent = 'Valid ID required'; ok = false; } else document.getElementById('studentIdError').textContent = '';
        if (!pwd.value || pwd.value.length < 6) { document.getElementById('regPasswordError').textContent = 'Min 6 characters'; ok = false; } else document.getElementById('regPasswordError').textContent = '';
        if (pwd.value !== cpwd.value) { document.getElementById('confirmPasswordError').textContent = 'Passwords do not match'; ok = false; } else document.getElementById('confirmPasswordError').textContent = '';
        if (!terms.checked) { document.getElementById('termsError').textContent = 'Agree to terms'; ok = false; } else document.getElementById('termsError').textContent = '';
        if (!ok) e.preventDefault();
    });
}

function loadPopularBooksFromDB() {
    const grid = document.getElementById('popularBooksGrid');
    if (!grid) return;
    fetch('api/get_books.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) grid.innerHTML = data.books.slice(0,4).map(book => createBookCard(book)).join('');
            else grid.innerHTML = '<p>No books</p>';
        })
        .catch(() => grid.innerHTML = '<p>Failed to load</p>');
}

function createBookCard(book) {
    const loggedIn = typeof isLoggedIn !== 'undefined' && isLoggedIn;
    const statusClass = book.available ? 'status-available' : 'status-borrowed';
    const statusText = book.available ? 'Available' : 'Borrowed';
    return `
        <div class="book-card">
            <div class="book-info">
                <h3>${escapeHtml(book.title)}</h3>
                <p>by ${escapeHtml(book.author)}</p>
                <span class="book-status ${statusClass}">${statusText}</span>
                <div class="book-actions">
                    ${loggedIn ? 
                        `<form method="POST" action="actions.php"><input type="hidden" name="book_id" value="${book.id}"><button type="submit" class="btn btn-sm btn-primary" ${!book.available ? 'disabled' : ''}>Borrow</button></form>` :
                        `<button class="btn btn-sm btn-primary" ${!book.available ? 'disabled' : ''} onclick="alert('Please login')">Borrow</button>`
                    }
                    <button class="btn btn-sm btn-outline" onclick="alert('${escapeHtml(book.title)}\\n${escapeHtml(book.author)}\\n${statusText}')">Details</button>
                </div>
            </div>
        </div>
    `;
}

function escapeHtml(str) {
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}