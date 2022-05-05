<?php
    function check_path_active($name) {
        if (strpos($_SERVER['REQUEST_URI'], $name) > -1) {
            return "active";
        }
        return "";
    }
?>

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-fw fa-shopping-bag"></i>
        </div>
        <div class="sidebar-brand-text mx-3">POS System</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?php echo check_path_active("index") ?>">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-store"></i>
            <span>POS</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Manament
    </div>

    <li class="nav-item <?php echo check_path_active("product") ?>">
        <a class="nav-link" href="product.php">
            <i class="fas fa-fw fa-shopping-bag"></i>
            <span>Product</span>
        </a>
    </li>

    <?php if(isset($_SESSION['status']) && $_SESSION['status'] === 'manager'): ?>
    <li class="nav-item <?php echo check_path_active("member") ?>">
        <a class="nav-link" href="member.php">
            <i class="fas fa-fw fa-user"></i>
            <span>Member</span>
        </a>
    </li>
    <?php endif; ?>

    <li class="nav-item <?php echo check_path_active("customer") ?>">
        <a class="nav-link" href="customer.php">
            <i class="fas fa-fw fa-user-tie"></i>
            <span>Customer</span>
        </a>
    </li>

    <li class="nav-item <?php echo check_path_active("order") ?>">
        <a class="nav-link" href="order.php">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Order</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->