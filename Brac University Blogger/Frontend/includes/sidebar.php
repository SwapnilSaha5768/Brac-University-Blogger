<?php
$page = basename($_SERVER['PHP_SELF']);
$basePath = isset($basePath) ? $basePath : '';
?>
<!-- Mobile Menu Button -->
<div class="mobile-menu-btn">
    <i class='bx bx-menu'></i>
</div>

<!-- Overlay -->
<div class="sidebar-overlay"></div>

<div class="sidebar">
    <div class="sidebar-header">
        <h3>Blog</h3>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo $basePath; ?>index.php" class="<?php echo ($page == 'index.php') ? 'active' : ''; ?>">
                <i class="bx bx-home"></i>
                <span class="nav-item">Home</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $basePath; ?>profiles/profile.php" class="<?php echo ($page == 'profile.php') ? 'active' : ''; ?>">
                <i class="bx bxs-face-mask"></i>
                <span class="nav-item">Profile</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $basePath; ?>search.php" class="<?php echo ($page == 'search.php') ? 'active' : ''; ?>">
                <i class="bx bx-search"></i>
                <span class="nav-item">Search</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $basePath; ?>posts/create.php" class="<?php echo ($page == 'create.php') ? 'active' : ''; ?>">
                <i class="bx bx-pencil"></i>
                <span class="nav-item">Write Post</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $basePath; ?>auth/logout.php">
                <i class="bx bx-log-out"></i>
                <span class="nav-item">Sign Out</span>
            </a>
        </li>
    </ul>
    
    <div class="user-info">
            <img src="<?php echo $basePath; ?>uploads/default.png" class="profile-pic-small">
            <div>
            <?php
            if(isset($_SESSION["fullname"])) {
                echo "<strong>" . $_SESSION["fullname"] . "</strong><br>";
                echo "<small>@" . $_SESSION["username"] . "</small>";
            }
            ?>
            </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if(mobileBtn) {
        mobileBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
});
</script>
