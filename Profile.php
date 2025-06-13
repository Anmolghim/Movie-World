<?php
session_start();

$server = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "gshare";
$port = "3307";

// Create connection
$conn = new mysqli($server, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = "";
// Get the logged-in user's email from session
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];

    // Fetch only that user's info
    $stmt = $conn->prepare("SELECT * FROM googleUserInfo WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $id = $user['id'];
        $username = $user['name'];
        $email = $user['email'];
        $picture = $user['picture'];
    } else {
        echo "User not found.";
    }
} else {
    echo "You are not logged in.";
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($user['id']) && isset($_POST['delete_account'])) {
    $googleId = $user['id'];
    $stmt = $conn->prepare("DELETE FROM googleUserInfo WHERE id = ?");
    $stmt->bind_param('i', $googleId);

    if ($stmt->execute()) {
        echo "<script>alert('Account deleted successfully.');</script>";
        session_destroy();
        echo "<script>window.location.href='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting account.');</script>";
    }
}

// Handle logout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - CinemaStream</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .profile-container {
            width: 50rem;
            margin: 80px auto 20px auto; /* Added top margin to account for fixed header */
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .profile-header {
            background: linear-gradient(135deg, #e50914 0%, #b20710 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .profile-image-container {
            position: relative;
            z-index: 2;
            margin-bottom: 20px;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            object-fit: cover;
            transition: transform 0.3s ease;
            background: linear-gradient(45deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: #fff;
            margin: 0 auto;
        }

        .profile-image:hover {
            transform: scale(1.05);
        }

        .profile-name {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .profile-email {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
            position: relative;
            z-index: 2;
        }

        .profile-content {
            padding: 40px 30px;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #e50914;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .info-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: black;
            line-height: 1.4;
        }

        .account-actions {
            background: rgba(255, 255, 255, 0.03);
            padding: 30px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 30px;
        }

        .danger-zone {
            border-top: 1px solid rgba(229, 9, 20, 0.3);
            padding-top: 25px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-right: 15px;
            margin-bottom: 10px;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: black;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: transparent;
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .btn-danger:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
        }

        /* Profile dropdown styles */
        .profile_user {
            position: fixed;
            top: 80px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1000;
            min-width: 280px;
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .profile_user img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e50914;
        }

        .profile_user p {
            color: #333;
            margin: 5px 0;
        }

        .profile_user p:first-of-type {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .button-2 {
            background: #e50914;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .button-2:hover {
            background: #b20710;
            transform: translateY(-2px);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .profile-container {
                margin: 70px 10px 20px 10px;
            }

            .profile-header {
                padding: 30px 20px;
            }

            .profile-content {
                padding: 30px 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .btn {
                width: 100%;
                justify-content: center;
                margin-right: 0;
            }

            .profile_user {
                right: 10px;
                left: 10px;
                min-width: auto;
            }
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <!-- Profile Dropdown -->
    <div class="profile_user" id="profileDropdown">
        <?php if ($user): ?>
            <?php if (!empty($user['picture'])): ?>
                <img src="<?php echo htmlspecialchars($user['picture']); ?>" alt="Profile Picture"
                    onerror="this.src='default-avatar.png'">
            <?php endif; ?>
            <p><?php echo htmlspecialchars($user['name']); ?></p>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div style="display: flex; justify-content: center; margin-top: 1rem;">
                    <input class="button-2" type="submit" name="logout" value="Sign Out">
                </div>
            </form>
        <?php else: ?>
            <div style="text-align: center; padding: 20px;">
                <i class="fa-solid fa-user-circle" style="font-size: 4rem; color: #ccc; margin-bottom: 15px;"></i>
                <p style="font-size: 1.125rem; font-weight: 600; color: #333;">Welcome, Guest!</p>
                <p style="color: #666; margin-bottom: 20px;">Sign in to unlock all features</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Header Section -->
    <header>
        <div class="header-left">
            <button class="toggle-btn" onclick="toggleMenu()">
                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M3 18h18v-2H3zm0-5h18v-2H3zm0-7v2h18V6z" />
                </svg>
            </button>
            <h1>Movie World</h1>
            <nav class="nav-links">
                <a href="#" onclick="scrollToSection('explore')">Explore</a>
                <a href="#" onclick="navigateToSection('trending')">Trending</a>
                <a href="#" onclick="navigateToSection('categories')">Categories</a>
            </nav>
        </div>

        <div class="header-center">
            <div class="search-container">
                <i class="fa-solid fa-search search-icon"></i>
                <input type="text" id="input_text" placeholder="Search amazing videos..." />
            </div>
        </div>

        <div class="header-right">
            <button class="action-button notification" onclick="showNotifications()" aria-label="Notifications">
                <i class="fa-solid fa-bell"></i>
            </button>

            <button class="action-button" onclick="toggleProfile()" aria-label="Profile Menu">
                <i class="fa-solid fa-user-circle"></i>
            </button>

            <?php if (!$user): ?>
                <a href="users_login.php" class="login_users">Sign In</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="container1" id="mainContainer">
        <!-- Sidebar Navigation -->
        <div class="container2">
            <div class="sidebar-item" onclick="navigateToSection('home')">
                <i class="fa-solid fa-house"></i>
                <label>Home</label>
            </div>
            <div class="sidebar-item" onclick="navigateToSection('shorts')">
                <i class="fa-solid fa-play"></i>
                <label>Shorts</label>
            </div>
            <div class="sidebar-item" onclick="navigateToSection('subscriptions')">
                <i class="fa-solid fa-heart"></i>
                <label>Subscriptions</label>
            </div>
            <div class="sidebar-item" onclick="navigateToSection('library')">
                <i class="fa-solid fa-bookmark"></i>
                <label>Library</label>
            </div>
            <div class="sidebar-item" onclick="navigateToSection('history')">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <label>History</label>
            </div>
            <div class="sidebar-item active" onclick="navigateToSection('profile')">
                <i class="fa-solid fa-user"></i>
                <label>Profile</label>
            </div>
        </div>

        <!-- Profile Container -->
        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-image-container">
                    <?php if ($user && !empty($user['picture'])): ?>
                        <img src="<?php echo htmlspecialchars($user['picture']); ?>" alt="Profile Picture" class="profile-image" style="font-size: inherit;">
                    <?php else: ?>
                        <div class="profile-image">🎬</div>
                    <?php endif; ?>
                </div>
                <h1 class="profile-name"><?php echo $user ? htmlspecialchars($username) : 'Guest User'; ?></h1>
                <p class="profile-email"><?php echo $user ? htmlspecialchars($email) : 'Please sign in'; ?></p>
            </div>

            <!-- Profile Content -->
            <div class="profile-content">
                <!-- Account Information -->
                <h2 class="section-title">👤 Account Information</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo $user ? htmlspecialchars($username) : 'N/A'; ?></div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?php echo $user ? htmlspecialchars($email) : 'N/A'; ?></div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">Account ID</div>
                        <div class="info-value"><?php echo $user ? htmlspecialchars($id) : 'N/A'; ?></div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">Account Status</div>
                        <div class="info-value"><?php echo $user ? 'Active' : 'Not Logged In'; ?></div>
                    </div>
                </div>

                <!-- Account Actions -->
                <?php if ($user): ?>
                <div class="account-actions">
                    <h2 class="section-title">⚙️ Account Settings</h2>

                    <button class="btn btn-secondary" onclick="alert('Feature coming soon!')">
                        🔒 Change Password
                    </button>

                    <button class="btn btn-secondary" onclick="alert('Feature coming soon!')">
                        📧 Update Email
                    </button>

                    <button class="btn btn-secondary" onclick="alert('Feature coming soon!')">
                        🔔 Notification Settings
                    </button>

                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                        <div class="danger-zone">
                            <h3 style="color: #ef4444; font-size: 1.1rem; margin-bottom: 15px;">⚠️ Danger Zone</h3>
                            <p style="color: #888; margin-bottom: 20px; font-size: 0.9rem;">
                                Once you delete your account, there is no going back. Please be certain.
                            </p>
                            <input type="hidden" name="delete_account" value="1">
                            <button type="submit" class="btn btn-danger">
                                🗑️ Delete Account
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Profile dropdown functionality
        function toggleProfile() {
            const profileDropdown = document.getElementById('profileDropdown');
            const mainContainer = document.getElementById('mainContainer');

            if (profileDropdown.style.display === 'none' || profileDropdown.style.display === '') {
                profileDropdown.style.display = 'flex';
                mainContainer.style.filter = 'blur(8px)';
                mainContainer.style.opacity = '0.7';

                setTimeout(() => {
                    document.addEventListener('click', closeProfileOnOutsideClick);
                }, 100);
            } else {
                closeProfile();
            }
        }

        function closeProfile() {
            const profileDropdown = document.getElementById('profileDropdown');
            const mainContainer = document.getElementById('mainContainer');

            profileDropdown.style.display = 'none';
            mainContainer.style.filter = 'none';
            mainContainer.style.opacity = '1';

            document.removeEventListener('click', closeProfileOnOutsideClick);
        }

        function closeProfileOnOutsideClick(event) {
            const profileDropdown = document.getElementById('profileDropdown');
            const profileButton = document.querySelector('.action-button[onclick="toggleProfile()"]');

            if (!profileDropdown.contains(event.target) && !profileButton.contains(event.target)) {
                closeProfile();
            }
        }

        // Navigation functions
        function navigateToSection(section) {
            console.log('Navigating to:', section);
            
            const sectionToPageMap = {
                home: 'index.php',
                shorts: 'Display_shorts.php',
                subscriptions: 'my_subscriptions.php',
                library: 'video_library.php',
                history: 'watch_history.php',
                profile: 'Profile.php'
            };

            const targetPage = sectionToPageMap[section];
            if (targetPage && section !== 'profile') {
                window.location.href = targetPage;
            }
        }

        function scrollToSection(section) {
            console.log('Scrolling to:', section);
        }

        function showNotifications() {
            alert('Notifications feature coming soon!');
        }

        function toggleMenu() {
            const container1 = document.querySelector('.container1');
            const container2 = document.querySelector('.container2');
            container1.classList.toggle('sidebar-hidden');
            container2.classList.toggle('hidden');
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeProfile();
            }
        });
    </script>
</body>
</html>