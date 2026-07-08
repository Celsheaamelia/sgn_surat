<aside class="sidebar">
    <div class="sidebar-top">
        <div class="logo">
            <div class="logo-badge">
                <i class="bi bi-envelope-paper"></i>
            </div>
            <div>
                <h4>Sistem Surat</h4>
                <small>Panel Admin</small>
            </div>
        </div>

        <ul class="nav-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('tambahsurat') }}" class="nav-link {{ request()->routeIs('tambahsurat') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Manajemen Surat</span>
                </a>
            </li>

            <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-folder2-open"></i>
                    <span>Riwayat Surat</span>
                </a>
            </li>

            <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-database"></i>
                    <span>Data Master</span>
                </a>
            </li>

            <li>
                <a href="#" class="nav-link">
                    <i class="bi bi-person-circle"></i>
                    <span>Profil</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="user-profile">
        <div class="avatar">A</div>
        <div class="user-meta">
            <strong>Administrator</strong>
            <small>Online</small>
        </div>
    </div>
</aside>

<style>
    .sidebar {
        flex: 0 0 280px;
        width: 280px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.5rem 1.1rem;
        background: linear-gradient(145deg, #064e3b 0%, #083d2e 100%);
        color: #f8fafc;
        box-shadow: 18px 0 40px rgba(6, 78, 59, 0.18);
        position: sticky;
        top: 0;
        flex-shrink: 0;
    }

    .sidebar-top {
        display: flex;
        flex-direction: column;
        gap: 1.4rem;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        padding: 0.3rem 0.2rem;
    }

    .logo-badge {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #34d399, #059669);
        color: white;
        font-size: 1.15rem;
        box-shadow: 0 10px 24px rgba(5, 150, 105, 0.22);
    }

    .logo h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .logo small {
        color: rgba(248, 250, 252, 0.7);
    }

    .nav-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        text-decoration: none;
        color: rgba(248, 250, 252, 0.86);
        padding: 0.8rem 0.95rem;
        border-radius: 12px;
        transition: all 0.2s ease;
        font-weight: 600;
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.11);
        color: white;
        transform: translateX(2px);
    }

    .nav-link.active {
        background: linear-gradient(135deg, #10b981, #047857);
        color: white;
        box-shadow: 0 10px 24px rgba(4, 120, 87, 0.18);
    }

    .nav-link i {
        font-size: 1rem;
        width: 18px;
        text-align: center;
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.95rem 1rem;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 16px;
    }

    .avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #f59e0b, #fb923c);
        font-weight: 700;
        color: white;
    }

    .user-meta {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .user-meta small {
        color: rgba(248, 250, 252, 0.7);
    }

    @media (max-width: 767px) {
        .sidebar {
            width: 100%;
            min-height: auto;
            position: relative;
            box-shadow: none;
        }
    }
</style>
