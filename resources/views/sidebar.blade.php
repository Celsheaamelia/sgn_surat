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
                <a href="{{ route('riwayatsurat') }}" class="nav-link {{ request()->routeIs('riwayatsurat') ? 'active' : '' }}">
                    <i class="bi bi-folder2-open"></i>
                    <span>Riwayat Surat</span>
                </a>
            </li>

            <li>
                <a href="#arsipSppSubmenu" data-bs-toggle="collapse" role="button"
                   aria-expanded="{{ request()->routeIs('arsipkasbon.*') ? 'true' : 'false' }}"
                   aria-controls="arsipSppSubmenu"
                   class="nav-link nav-link-parent {{ request()->routeIs('arsipkasbon.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span class="flex-grow-1">Arsip SPP</span>
                    <i class="bi bi-chevron-down nav-caret"></i>
                </a>
                <div class="collapse {{ request()->routeIs('arsipkasbon.*') ? 'show' : '' }}" id="arsipSppSubmenu">
                    <ul class="nav-submenu">
                        <li>
                            <a href="{{ route('arsipkasbon.create') }}"
                               class="nav-sublink {{ request()->routeIs('arsipkasbon.create') ? 'active' : '' }}">
                                <i class="bi bi-camera"></i>
                                <span>Upload Surat Baru</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('arsipkasbon.index') }}"
                               class="nav-sublink {{ request()->routeIs('arsipkasbon.index') || request()->routeIs('arsipkasbon.show') ? 'active' : '' }}">
                                <i class="bi bi-clock-history"></i>
                                <span>Riwayat Arsip SPP</span>
                            </a>
                        </li>
                    </ul>
                </div>
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
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');

    /* Sidebar diam di tempat, nggak ikut scroll halaman.
       top disamakan dengan tinggi navbar (lihat .navbar-custom di layout). */
    .sidebar {
        position: fixed;
        top: 60px;
        left: 0;
        bottom: 0;
        width: 280px;
        overflow-y: auto;
        z-index: 900;

        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1.5rem 1.1rem;
        background: linear-gradient(145deg, #064e3b 0%, #083d2e 100%);
        color: #f8fafc;
        box-shadow: 18px 0 40px rgba(6, 78, 59, 0.18);
        font-family: 'Inter', -apple-system, sans-serif;
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
        font-family: 'Fraunces', Georgia, serif;
        font-size: 1.05rem;
        font-weight: 600;
        letter-spacing: -0.01em;
    }

    .logo small {
        font-family: 'IBM Plex Mono', ui-monospace, monospace;
        font-size: 0.72rem;
        letter-spacing: 0.03em;
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
        font-family: 'Inter', -apple-system, sans-serif;
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

    .nav-link-parent {
        cursor: pointer;
    }

    .nav-caret {
        font-size: 0.75rem !important;
        width: auto !important;
        margin-left: auto;
        transition: transform 0.2s ease;
    }

    .nav-link-parent[aria-expanded="true"] .nav-caret {
        transform: rotate(180deg);
    }

    .nav-submenu {
        list-style: none;
        margin: 0.35rem 0 0.15rem;
        padding: 0 0 0 1.6rem;
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        border-left: 1px solid rgba(255, 255, 255, 0.14);
        margin-left: 1.15rem;
    }

    .nav-sublink {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        text-decoration: none;
        color: rgba(248, 250, 252, 0.75);
        padding: 0.6rem 0.85rem;
        border-radius: 10px;
        font-family: 'Inter', -apple-system, sans-serif;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .nav-sublink i {
        font-size: 0.85rem;
        width: 16px;
        text-align: center;
    }

    .nav-sublink:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .nav-sublink.active {
        background: linear-gradient(135deg, #10b981, #047857);
        color: white;
        box-shadow: 0 8px 18px rgba(4, 120, 87, 0.18);
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
        font-family: 'Fraunces', Georgia, serif;
        font-weight: 700;
        color: white;
    }

    .user-meta {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
        font-family: 'Inter', -apple-system, sans-serif;
    }

    .user-meta small {
        font-family: 'IBM Plex Mono', ui-monospace, monospace;
        font-size: 0.72rem;
        letter-spacing: 0.03em;
        color: rgba(248, 250, 252, 0.7);
    }

    @media (max-width: 767px) {
        .sidebar {
            position: static;
            width: 100%;
            height: auto;
            box-shadow: none;
        }
    }
</style>