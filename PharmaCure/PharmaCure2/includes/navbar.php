<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="../public/index.php">PharmaCure</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../public/index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../public/products.php">Products</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../public/about.php">About Us</a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../public/contact.php">Contact Us</a>
            </li>

            <!-- Add Your Cart link -->
            <li class="nav-item">
                <a class="nav-link" href="../public/cart.php">Your Cart</a> <!-- Link to cart page -->
            </li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- User is logged in -->
                <li class='nav-item'>
                    <a class='nav-link' href='../public/user_profile.php'>Profile</a> <!-- Link to user profile -->
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?> <!-- Check if user is admin -->
                    <li class='nav-item'>
                        <a class='nav-link' href='../admin/admin_dashboard.php'>Admin Dashboard</a> <!-- Link to admin dashboard -->
                    </li>
                <?php endif; ?>
                <li class='nav-item'>
                    <span class='navbar-text'>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span> <!-- Display user's name -->
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='../public/user_logout.php'>Logout</a> <!-- Logout link -->
                </li>
            <?php else: ?>
                <!-- User is not logged in -->
                <li class='nav-item'>
                    <a class='nav-link' href='../public/user_register.php'>Register</a> <!-- Register link -->
                </li>
                <li class='nav-item'>
                    <a class='nav-link' href='../public/user_login.php'>Login</a> <!-- Login link -->
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>