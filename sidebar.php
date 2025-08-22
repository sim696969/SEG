<?php
// Get the current page filename to dynamically highlight the active link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar">
    <div class="sidebar-header">
        <img src="pic/ViTrox.png" alt="Logo" class="sidebar-logo">
        <span class="sidebar-title">ViTrox College</span>
    </div>
    <ul class="nav-links">
        <!-- Home Link -->
        <li class="<?php echo ($current_page == 'student_dashboard.php') ? 'active' : ''; ?>">
            <a href="student_dashboard.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480q0-33 23.5-56.5T240-680l240-180 240 180q23 17 23.5 43.5T720-560v480q0 33-23.5 56.5T640-0H160q-33 0-56.5-23.5T80-80Zm320-420Z"/></svg>
                <span>Home</span>
            </a>
        </li>
        <!-- Calendar Link -->
        <li class="<?php echo ($current_page == 'calendar.php') ? 'active' : ''; ?>">
            <a href="calendar.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
                <span>Calendar</span>
            </a>
        </li>
        <!-- Profile Link -->
        <li class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <a href="profile.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0-66-47-113t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q62 0 126 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-57 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg>
                <span>Profile</span>
            </a>
        </li>
         <!-- Admin Panel (Only shows for admin users) -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <li class="<?php echo ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">
            <a href="admin_dashboard.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-120q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-200v-80h80v80H160Zm0-160v-80h80v80H160Zm0-160v-80h80v80H160Zm160-160v-80h80v80H320Zm0 480v-80h80v80H320Zm160-480v-80h80v80H480Zm0 480v-80h80v80H480Zm160-480v-80h80v80H640Zm0 480v-80h80v80H640Zm160-320v-80h80v80H800Zm0-160v-80h80v80H800Zm0 320v-80h80v80H800ZM80-120v-720q0-33 23.5-56.5T160-920h640q33 0 56.5 23.5T880-840v720q0 33-23.5 56.5T800-40H160q-33 0-56.5-23.5T80-120Zm80-80h640v-720H160v720Zm0 0v-720 720Z"/></svg>
                <span>Admin Panel</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
    <div class="sidebar-footer">
        <a href="logout.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/></svg>
            <span>Logout</span>
        </a>
    </div>
</nav>
