<style>
.navbar-custom{
    height:60px;
    width:100%;
    background:#fff;
    border-bottom:1px solid #e9ecef;
    position:fixed;
    top:0;
    left:0;
    z-index:1000;
    padding:0 20px;
}

.top-navbar{
    height:70px;
    background:#fff;
    border-bottom:1px solid #dee2e6;

    display:flex;
    align-items:center;
    justify-content:space-between;

    padding:0 25px;
}

.navbar-custom img{
    height:40px;
}

.profile-btn{
    border:none;
    background:none;
    display:flex;
    align-items:center;
    gap:8px;
}

.profile-btn i{
    color:#033689;
}

.profile-btn .bi-person-circle{
    font-size:34px;
}

.profile-btn .bi-chevron-down{
    font-size:18px;
}

.dropdown-menu{
    border:none;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,.1);
}

.dropdown-item{
    padding:10px 15px;
}

.dropdown-item:hover{
    background:#f5f7fb;
}

.logo{
    display:flex;
    align-items:center;
    gap:15px;
}

.logo img{
    height:60px;   /* jangan terlalu besar */
    width:auto;
    object-fit:contain;
}

</style>

<nav class="navbar navbar-expand-lg navbar-custom">

    <div class="container-fluid">

        <div class="logo d-flex justify-content-center align-items-center gap-3 py-3">
            <img src="{{ asset('images/logosgn.png') }}" class="logo-img">
            <img src="{{ asset('images/pglogo.png') }}" class="logo-img">
        </div>

        <div class="dropdown ms-auto">

            <button class="profile-btn" data-bs-toggle="dropdown">

                <i class="bi bi-person-circle"></i>

                <i class="bi bi-chevron-down"></i>

            </button>

            <ul class="dropdown-menu dropdown-menu-end">

                <li class="dropdown-header">
                    <strong>Administrator</strong><br>
                    <small>{{ session('user_email') }}</small>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </button>
                    </form>
                </li>

            </ul>

        </div>

    </div>

</nav>
