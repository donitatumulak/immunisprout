<body>
    <div id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo-grn.png') }}" alt="ImmuniSprout Logo">
    </div>

    <ul class="nav-list">
        <li>
            <a href="{{ route('dashboard') }}" data-bs-toggle="tooltip"
               data-bs-placement="right"
               title="Dashboard"><i class="fa-solid fa-house"></i><span>Dashboard</span></a>
        </li>
        <li>
            <a href="{{ route('children.index') }}" data-bs-toggle="tooltip"
               data-bs-placement="right"
               title="Child Records"><i class="fa-solid fa-children"></i></i><span>Child Records</span></a>
        </li>
        <li>
            <a href="{{ route('vaccinations.index') }}" data-bs-toggle="tooltip"
               data-bs-placement="right"
               title="Immunization Records"><i class="fa-solid fa-shield-virus"></i><span>Immunization Records</span></a>
        </li>
        <li>
            <a href="{{ route('inventory.index') }}" data-bs-toggle="tooltip"
               data-bs-placement="right"
               title="Vaccine Inventory"><i class="fa-solid fa-boxes-stacked"></i></i><span>Vaccine Inventory</span></a>
        </li>
        <li>
            <a href="{{ route('notifications.index') }}" data-bs-toggle="tooltip"
               data-bs-placement="right"
               title="Notifications"><i class="fa-solid fa-bell"></i></i><span>Notifications</span></a>
        </li>
        @if(auth()->user()->canDelete())
            <li>
                <a href="{{ route('user-management.index') }}" data-bs-toggle="tooltip"
                data-bs-placement="right"
                title="Users"><i class="fa-solid fa-users"></i></i><span>Users</span></a>
            </li>
        @endif
        <li>
            <a href="{{ route('profile.index') }}" data-bs-toggle="tooltip"
               data-bs-placement="right"
               title="Profile"><i class="fa-solid fa-circle-user"></i></i><span>Profile</span></a>
        </li>
        <li>
            <a href="#" 
            data-bs-toggle="tooltip"
            data-bs-placement="right"
            title="Logout"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>

    <div class="sidebar-toggle" id="toggle-btn">
        <i class="fa-solid fa-chevron-left"></i>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector(".sidebar");
    const toggleBtn = document.getElementById("toggle-btn");
    const body = document.body;

    // Desktop Toggle (Chevron)
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            if (window.innerWidth > 991) {
                sidebar.classList.toggle("collapsed");
                body.classList.toggle("collapsed-active");
            } else {
                // If clicked on mobile, just hide it
                sidebar.classList.remove("mobile-active");
            }
        });
    }

    // Handle Mobile Hamburger (Add this button to your Top Navbar)
    const mobileBtn = document.querySelector(".mobile-nav-toggle");
    if (mobileBtn) {
        mobileBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            sidebar.classList.toggle("mobile-active");
        });
    }

    // Close mobile sidebar when clicking outside
    document.addEventListener("click", function (e) {
        if (window.innerWidth <= 991) {
            if (!sidebar.contains(e.target) && sidebar.classList.contains("mobile-active")) {
                sidebar.classList.remove("mobile-active");
            }
        }
    });
});
</script>
</body>