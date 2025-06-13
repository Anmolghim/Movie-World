<?php
// Initialize variables
$videos = [];
$database_error = false;
$error_message = '';

try {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "gshare", 3307);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Query to get shorts videos
    $sql = "SELECT * FROM files WHERE file_type = 'video' AND category = 'shorts' ORDER BY uploaded_date DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $videos[] = $row;
        }
    }

    $conn->close();

} catch (Exception $e) {
    $database_error = true;
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie World - Professional Video Platform</title>

    <!-- External CSS Libraries -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="//cdn.datatables.net/2.0.6/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/css/lightgallery.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="index.css">

</head>

<body>
    <!-- ========================================
         6. USER PROFILE DROPDOWN
         ======================================== -->
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
            <div style="text-align: center; padding: var(--space-lg);">
                <i class="fa-solid fa-user-circle"
                    style="font-size: 4rem; color: var(--gray-400); margin-bottom: var(--space-md);"></i>
                <p style="font-size: 1.125rem; font-weight: 600; color: var(--gray-700);">Welcome, Guest!</p>
                <p style="color: var(--gray-500); margin-bottom: var(--space-lg);">Sign in to unlock all features</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========================================
         7. HEADER SECTION
         ======================================== -->
    <header>
        <div class="header-left">
            <!-- Toggle Button with Image -->
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

    <!-- ========================================
         8. MAIN CONTENT AREA
         ======================================== -->
    <div class="container1" id="mainContainer">
        <!-- Enhanced Sidebar Navigation -->
        <div class="container2">
            <div class="sidebar-item active" onclick="navigateToSection('home')">
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
            <div class="sidebar-item" onclick="navigateToSection('profile')">
                <i class="fa-solid fa-user"></i>
                <label>Profile</label>
            </div>
        </div>

        <!-- Main Gallery Area -->
        <!-- Main Gallery Area -->
        <div class="shorts-section">
            <h2 class="shorts-title">🎬 Enjoy the shorts video</h2>
            <div class="shorts-gallery" id="shortsGallery">
                <?php if ($database_error): ?>
                    <div class="shorts-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Connection Error</h3>
                        <p>Unable to load reels. Please try again later.</p>
                    </div>
                <?php elseif (!empty($videos)): ?>
                    <?php foreach ($videos as $index => $video): ?>
                        <div class="shorts-card"
                            onclick="openShortsModal('<?= htmlspecialchars($video['file_path']) ?>', <?= $index ?>)">
                            <div class="shorts-video-wrapper">
                                <video preload="metadata" muted>
                                    <source src="<?= htmlspecialchars($video['file_path']) ?>" type="video/mp4">
                                </video>
                                <div class="play-overlay">
                                    <i class="fas fa-play"></i>
                                </div>
                            </div>
                            <div class="shorts-info">
                                <h3><?= htmlspecialchars(pathinfo($video['file_name'], PATHINFO_FILENAME)) ?></h3>
                                <small><?= number_format($video['file_size'] / (1024 * 1024), 2) ?> MB</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="shorts-empty">
                        <i class="fa-solid fa-video"></i>
                        <h3>No Reels Available</h3>
                        <p>Upload your first reel to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal -->
        <div id="shortsModal" class="shorts-modal">
            <div class="modal-container">
                <button class="close-modal" onclick="closeShortsModal()">
                    <i class="fas fa-times"></i>
                </button>

                <div class="reel-viewer">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>

                    <video id="modalVideo" class="reel-video" controls loop>
                        <source src="" type="video/mp4">
                    </video>

                    <div class="reel-controls">
                        <button class="control-btn" onclick="toggleLike()">
                            <i class="far fa-heart" id="likeIcon"></i>
                        </button>
                        <button class="control-btn" onclick="shareReel()">
                            <i class="fas fa-share"></i>
                        </button>
                        <button class="control-btn" onclick="downloadReel()">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>

                    <div class="reel-info">
                        <div class="reel-title" id="reelTitle">Reel Title</div>
                        <div class="reel-meta" id="reelMeta">File size • Upload date</div>
                    </div>
                </div>

                <button class="navigation-btn prev-btn" onclick="previousReel()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="navigation-btn next-btn" onclick="nextReel()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

    </div>

    <!-- ========================================
         9. JAVASCRIPT LIBRARIES
         ======================================== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/lightgallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/thumbnail/lg-thumbnail.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/zoom/lg-zoom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.5.0/plugins/video/lg-video.min.js"></script>

    <!-- ========================================
         10. ENHANCED JAVASCRIPT
         ======================================== -->
    <script>
        // ========================================
        // Gallery Initialization with Enhanced Settings
        // ========================================
        $(document).ready(function () {
            $('#gallery').lightGallery({
                selector: '.gallery-item a',
                mode: 'lg-fade',
                download: true,
                counter: true,
                zoom: true,
                enableSwipe: true,
                autoplay: false,
                fullScreen: true,
                thumbnail: true,
                animateThumb: true,
                showThumbByDefault: false,
                toogleThumb: true,
                mousewheel: true,
                getCaptionFromTitleOrAlt: false,
                appendSubHtmlTo: '.lg-sub-html',
                subHtmlSelectorRelative: true,
                preload: 2,
                speed: 400,
                hideBarsDelay: 6000,
                videoMaxWidth: '855px',
            });

            // Initialize page
            initializePage();
        });

        // ========================================
        // Page Initialization
        // ========================================
        function initializePage() {
            // Add loading animation to gallery items
            const galleryItems = document.querySelectorAll('.gallery-item');
            galleryItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.6s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Set active sidebar item based on current section
            setActiveSidebarItem('home');
        }

        // ========================================
        // Enhanced Profile Dropdown
        // ========================================
        function toggleProfile() {
            const profileDropdown = document.getElementById('profileDropdown');
            const mainContainer = document.getElementById('mainContainer');

            if (profileDropdown.style.display === 'none' || profileDropdown.style.display === '') {
                profileDropdown.style.display = 'flex';
                mainContainer.style.filter = 'blur(8px)';
                mainContainer.style.opacity = '0.7';

                // Add click outside to close
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
            const profileButton = document.querySelector('.action-button');

            if (!profileDropdown.contains(event.target) && !profileButton.contains(event.target)) {
                closeProfile();
            }
        }

        // ========================================
        // Enhanced Navigation Functions
        // ========================================
        function navigateToSection(section) {
            console.log('Navigating to:', section);

            // Sidebar UI update
            setActiveSidebarItem(section);

            // Show loading state
            showLoadingState();

            // Define mapping of section keys to actual page URLs
            const sectionToPageMap = {
                home: 'index.php',
                shorts: 'Display_shorts.php',
                subscriptions: 'my_subscriptions.php',
                library: 'video_library.php',
                history: 'watch_history.php',
                profile: 'Profile.php'
            };

            // Simulate navigation delay
            setTimeout(() => {
                hideLoadingState();

                // Get target page from map
                const targetPage = sectionToPageMap[section];

                if (targetPage) {
                    window.location.href = targetPage;
                } else {
                    console.warn('No page mapped for section:', section);
                }
            }, 500);
        }
        /*    =================================
        javascript for the reels to display in the proper format
        ======================================================
        */
        let currentReelIndex = 0;
        let reelsData = <?= json_encode($videos) ?>;
        let isLiked = false;

        function openShortsModal(videoPath, index) {
            currentReelIndex = index;
            const modal = document.getElementById('shortsModal');
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            loadReel(index);
            
            // Add keyboard navigation
            document.addEventListener('keydown', handleKeyPress);
        }

        function closeShortsModal() {
            const modal = document.getElementById('shortsModal');
            const modalVideo = document.getElementById('modalVideo');
            
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            modalVideo.pause();
            
            // Remove keyboard navigation
            document.removeEventListener('keydown', handleKeyPress);
        }

        function loadReel(index) {
            if (index < 0 || index >= reelsData.length) return;
            
            const reel = reelsData[index];
            const modalVideo = document.getElementById('modalVideo');
            const reelTitle = document.getElementById('reelTitle');
            const reelMeta = document.getElementById('reelMeta');
            
            modalVideo.src = reel.file_path;
            reelTitle.textContent = reel.file_name.replace(/\.[^/.]+$/, "");
            reelMeta.textContent = `${(reel.file_size / (1024 * 1024)).toFixed(2)} MB • ${formatDate(reel.uploaded_date)}`;
            
            modalVideo.load();
            modalVideo.play();
            
            // Update progress bar
            modalVideo.addEventListener('timeupdate', updateProgress);
        }

        function previousReel() {
            if (currentReelIndex > 0) {
                currentReelIndex--;
                loadReel(currentReelIndex);
            }
        }

        function nextReel() {
            if (currentReelIndex < reelsData.length - 1) {
                currentReelIndex++;
                loadReel(currentReelIndex);
            }
        }

        function handleKeyPress(e) {
            switch(e.key) {
                case 'Escape':
                    closeShortsModal();
                    break;
                case 'ArrowLeft':
                    previousReel();
                    break;
                case 'ArrowRight':
                    nextReel();
                    break;
                case ' ':
                    e.preventDefault();
                    togglePlayPause();
                    break;
            }
        }

        function togglePlayPause() {
            const modalVideo = document.getElementById('modalVideo');
            if (modalVideo.paused) {
                modalVideo.play();
            } else {
                modalVideo.pause();
            }
        }

        function toggleLike() {
            const likeIcon = document.getElementById('likeIcon');
            isLiked = !isLiked;
            
            if (isLiked) {
                likeIcon.className = 'fas fa-heart';
                likeIcon.style.color = '#ff3040';
            } else {
                likeIcon.className = 'far fa-heart';
                likeIcon.style.color = 'white';
            }
        }

        function shareReel() {
            if (navigator.share) {
                navigator.share({
                    title: 'Check out this reel!',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Link copied to clipboard!');
            }
        }

        function downloadReel() {
            const reel = reelsData[currentReelIndex];
            const link = document.createElement('a');
            link.href = reel.file_path;
            link.download = reel.file_name;
            link.click();
        }

        function updateProgress() {
            const modalVideo = document.getElementById('modalVideo');
            const progressFill = document.getElementById('progressFill');
            
            if (modalVideo.duration) {
                const progress = (modalVideo.currentTime / modalVideo.duration) * 100;
                progressFill.style.width = progress + '%';
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        // Close modal when clicking outside
        document.getElementById('shortsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeShortsModal();
            }
        });

        // Auto-play next reel when current ends
        document.addEventListener('DOMContentLoaded', function() {
            const modalVideo = document.getElementById('modalVideo');
            modalVideo.addEventListener('ended', function() {
                if (currentReelIndex < reelsData.length - 1) {
                    nextReel();
                }
            });
        });
        // ===============================================

        function setActiveSidebarItem(section) {
            // Remove active class from all items
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.classList.remove('active');
            });

            // Add active class to current section
            const activeItem = document.querySelector(`[onclick*="${section}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
            }
        }

        function updateGalleryHeader(section) {
            const titleElement = document.querySelector('.gallery-title');
            const subtitleElement = document.querySelector('.gallery-subtitle');

            const headers = {
                'home': {
                    title: 'Discover Amazing Videos',
                    subtitle: 'Explore our curated collection of premium content'
                },
                'shorts': {
                    title: 'Short & Sweet',
                    subtitle: 'Quick entertainment for your busy lifestyle'
                },
                'subscriptions': {
                    title: 'Your Subscriptions',
                    subtitle: 'Latest from channels you love'
                },
                'library': {
                    title: 'Your Library',
                    subtitle: 'Your saved and favorite videos'
                },
                'history': {
                    title: 'Watch History',
                    subtitle: 'Continue where you left off'
                },
                'profile': {
                    title: 'Your Profile',
                    subtitle: 'Manage your account and content'
                }
            };

            if (headers[section]) {
                titleElement.textContent = headers[section].title;
                subtitleElement.textContent = headers[section].subtitle;
            }
        }

        function filterContentBySection(section) {
            // Add section-specific filtering logic here
            console.log('Filtering content for section:', section);
        }

        function scrollToSection(section) {
            console.log('Scrolling to:', section);
            document.querySelector('.gallery-container').scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function showNotifications() {
            // Enhanced notification system
            const notification = document.createElement('div');
            notification.innerHTML = `
                <div style="position: fixed; top: 100px; right: 20px; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.15); z-index: 1001; max-width: 300px; border-left: 4px solid var(--secondary-600);">
                    <h4 style="margin: 0 0 10px 0; color: var(--gray-800);">
                        <i class="fa-solid fa-bell" style="color: var(--secondary-600); margin-right: 8px;"></i>
                        Notifications
                    </h4>
                    <ul style="margin: 0; padding-left: 20px; color: var(--gray-600); font-size: 0.9rem;">
                      <?= $videoListHtml ?> 
                    </ui>
                </div>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        function toggleMenu() {
            const container1 = document.querySelector('.container1');
            const container2 = document.querySelector('.container2');
            // Toggle the 'hidden' class to show/hide the sidebar
            container1.classList.toggle('sidebar-hidden');
            container2.classList.toggle('hidden');
        }

        // ========================================
        // Enhanced Search Functionality
        // ========================================
        let searchTimeout;
        document.getElementById('input_text').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            // Clear previous timeout
            clearTimeout(searchTimeout);

            // Add loading state to search
            this.style.background = 'url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEwIDNDNi4xMzQwMSAzIDMgNi4xMzQwMSAzIDEwQzMgMTMuODY2IDYuMTM0MDEgMTcgMTAgMTdDMTMuODY2IDE3IDE3IDEzLjg2NiAxNyAxMEMxNyA2LjEzNDAxIDEzLjg2NiAzIDEwIDNaTTEwIDVDMTIuNzYxNCA1IDE1IDcuMjM4NTggMTUgMTBDMTUgMTIuNzYxNCAxMi43NjE0IDE1IDEwIDE1QzcuMjM4NTggMTUgNSAxMi43NjE0IDUgMTBDNSA3LjIzODU4IDcuMjM4NTggNSAxMCA1WiIgZmlsbD0iIzk0YTNiOCIvPgo8L3N2Zz4K") no-repeat right 12px center, white';

            // Debounced search
            searchTimeout = setTimeout(() => {
                performSearch(searchTerm);
                this.style.background = 'white';
            }, 300);
        });

        function performSearch(searchTerm) {
            const galleryItems = document.querySelectorAll('.gallery-item');
            let visibleCount = 0;

            galleryItems.forEach(item => {
                const videoTitle = item.querySelector('.video-title');
                const title = videoTitle ? videoTitle.textContent.toLowerCase() : '';
                const videoElement = item.querySelector('video source');
                const videoSrc = videoElement ? videoElement.src.toLowerCase() : '';

                if (title.includes(searchTerm) || videoSrc.includes(searchTerm)) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.3s ease';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show "no results" message if needed
            const existingNoResults = document.querySelector('.no-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }

            if (visibleCount === 0 && searchTerm.trim() !== '') {
                const noResults = document.createElement('div');
                noResults.className = 'no-results no-content';
                noResults.innerHTML = `
                    <i class="fa-solid fa-search" style="font-size: 4rem; color: var(--gray-400); margin-bottom: var(--space-lg);"></i>
                    <h3>No Results Found</h3>
                    <p>We couldn't find any videos matching "<strong>${searchTerm}</strong>"</p>
                    <p>Try adjusting your search terms or browse our categories.</p>
                `;
                document.getElementById('gallery').appendChild(noResults);
            }
        }

        // ========================================
        // Loading States
        // ========================================
        function showLoadingState() {
            const gallery = document.getElementById('gallery');
            const loader = document.createElement('div');
            loader.className = 'loading-overlay';
            loader.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(248, 250, 252, 0.8); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 999;">
                    <div class="loading" style="font-size: 1.125rem; font-weight: 500;">
                        Loading content...
                    </div>
                </div>
            `;
            document.body.appendChild(loader);
        }

        function hideLoadingState() {
            const loader = document.querySelector('.loading-overlay');
            if (loader) {
                loader.remove();
            }
        }




        // ========================================
        // Enhanced Keyboard Shortcuts
        // ========================================
        document.addEventListener('keydown', function (event) {
            // ESC key to close profile dropdown
            if (event.key === 'Escape') {
                closeProfile();
            }

            // Ctrl/Cmd + K to focus search
            if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
                event.preventDefault();
                const searchInput = document.getElementById('input_text');
                searchInput.focus();
                searchInput.select();
            }

            // Arrow keys for navigation
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                const sidebarItems = document.querySelectorAll('.sidebar-item');
                const activeItem = document.querySelector('.sidebar-item.active');
                const currentIndex = Array.from(sidebarItems).indexOf(activeItem);

                if (event.key === 'ArrowDown' && currentIndex < sidebarItems.length - 1) {
                    sidebarItems[currentIndex + 1].click();
                } else if (event.key === 'ArrowUp' && currentIndex > 0) {
                    sidebarItems[currentIndex - 1].click();
                }
            }
        });

        // ========================================
        // Intersection Observer for Animations
        // ========================================
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe gallery items for scroll animations
        setTimeout(() => {
            document.querySelectorAll('.gallery-item').forEach(item => {
                observer.observe(item);
            });
        }, 100);

        // ========================================
        // Performance Optimizations
        // ========================================

        // Lazy load videos
        document.addEventListener('DOMContentLoaded', function () {
            const videos = document.querySelectorAll('video');
            videos.forEach(video => {
                video.setAttribute('loading', 'lazy');
                video.setAttribute('preload', 'none');
            });
        });

        // Smooth scrolling for gallery container
        const galleryContainer = document.querySelector('.gallery-container');
        if (galleryContainer) {
            galleryContainer.style.scrollBehavior = 'smooth';
        }

        // Add fade-in animation class
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>