<!-- sidebar-style.blade.php -->
<style>
    /* ========================================
       VARIABLES & GLOBAL STYLES
    ======================================== */
    :root {
        --primary-color: #FF7B00;
        --primary-dark: #CC6200;
        --primary-light: #FFA347;
        --primary-gradient: linear-gradient(135deg, #FF7B00 0%, #FF5500 100%);
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --info-color: #17a2b8;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --border-color: #e9ecef;
        --text-dark: #333;
        --text-light: #6c757d;
        --white: #fff;
        --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 4px 20px rgba(0, 0, 0, 0.15);
        --transition: all 0.3s ease;
    }

    /* ========================================
       DESKTOP OVERRIDES
    ======================================== */
    .deznav .metismenu>li>a.active {
        color: var(--primary-color) !important;
        background: rgba(255, 123, 0, 0.1) !important;
    }

    .deznav .metismenu>li>a:hover {
        color: var(--primary-color) !important;
        background: rgba(255, 123, 0, 0.05) !important;
    }

    .add-menu-sidebar {
        background: var(--primary-gradient) !important;
    }

    .add-menu-sidebar:hover {
        background: linear-gradient(135deg, #FF5500 0%, #CC6200 100%) !important;
    }

    .badge-primary {
        background-color: var(--primary-color) !important;
    }

    /* ========================================
       MOBILE BOTTOM NAVIGATION BAR
    ======================================== */
    .mobile-bottom-nav {
        display: none;
    }

    /* Mobile Menu Modal - Hidden by default */
    .mobile-menu-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--white);
        z-index: 9999;
        overflow-y: auto;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .mobile-menu-modal.active {
        display: block;
        opacity: 1;
        animation: slideInFromBottom 0.3s ease-out;
    }

    .mobile-menu-modal.closing {
        animation: slideOutToBottom 0.3s ease-in;
    }

    @keyframes slideInFromBottom {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideOutToBottom {
        from {
            transform: translateY(0);
            opacity: 1;
        }

        to {
            transform: translateY(100%);
            opacity: 0;
        }
    }

    /* ========================================
       RESPONSIVE SIDEBAR SWITCH
    ======================================== */
    .deznav.desktop-sidebar {
        display: block;
    }

    .mobile-bottom-nav {
        display: none;
    }

    .nav-control {
        display: flex;
    }

    @media screen and (max-width: 768px) {
        .deznav.desktop-sidebar {
            display: none !important;
            transform: translateX(-100%) !important;
        }

        .deznav-overlay {
            display: none !important;
        }

        .nav-control {
            display: none !important;
        }

        .mobile-bottom-nav {
            display: block !important;
        }

        body {
            overflow-x: hidden !important;
        }
    }

    @media screen and (min-width: 769px) {
        .deznav.desktop-sidebar {
            display: block !important;
        }

        .mobile-bottom-nav {
            display: none !important;
        }

        .nav-control {
            display: flex !important;
        }
    }

    /* ========================================
       MOBILE BOTTOM NAV STYLES
    ======================================== */
    @media screen and (max-width: 768px) {
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--white);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            height: 65px;
            padding-bottom: env(safe-area-inset-bottom);
            border-top: 1px solid var(--border-color);
        }

        .mobile-nav-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 100%;
            padding: 0 10px;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--text-light);
            font-size: 11px;
            transition: var(--transition);
            position: relative;
            flex: 1;
            padding: 5px;
            min-width: 50px;
        }

        .mobile-nav-item i {
            font-size: 22px;
            margin-bottom: 3px;
            transition: var(--transition);
        }

        .mobile-nav-item span {
            font-size: 10px;
            font-weight: 600;
            text-align: center;
            display: block;
        }

        .mobile-nav-item:hover,
        .mobile-nav-item.active {
            color: var(--primary-color);
        }

        .mobile-nav-item.active i {
            transform: scale(1.1);
            color: var(--primary-color);
        }

        /* Floating Action Button (FAB) */
        .mobile-nav-item.mobile-fab {
            position: relative;
            top: -20px;
        }

        .fab-button {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(255, 123, 0, 0.4);
            transition: var(--transition);
        }

        .fab-button i {
            color: var(--white);
            font-size: 24px;
            margin-bottom: 0;
        }

        .fab-button:active {
            transform: scale(0.95);
            box-shadow: 0 2px 8px rgba(255, 123, 0, 0.4);
        }

        /* Mobile Badge */
        .mobile-badge {
            position: absolute;
            top: 2px;
            right: 15%;
            background: var(--danger-color);
            color: var(--white);
            font-size: 9px;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 10px;
            min-width: 16px;
            text-align: center;
            line-height: 1.2;
            border: 2px solid var(--white);
        }
    }

    /* ========================================
       MOBILE MENU MODAL STYLES
    ======================================== */
    @media screen and (max-width: 768px) {
        .mobile-menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: var(--primary-gradient);
            color: var(--white);
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .mobile-menu-header h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--white);
        }

        .close-mobile-menu {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: var(--white);
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
            transition: var(--transition);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-mobile-menu:active {
            transform: scale(0.9);
            background: rgba(255, 255, 255, 0.3);
        }

        .mobile-menu-content {
            padding: 20px;
            padding-bottom: 100px;
        }

        .mobile-menu-user-info {
            display: flex;
            align-items: center;
            padding: 20px;
            background: var(--light-color);
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .user-avatar i {
            font-size: 28px;
            color: var(--white);
        }

        .user-details h5 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .user-details p {
            margin: 0;
            font-size: 13px;
            color: var(--text-light);
        }

        /* Accordion Menu Styles - FIXED */
        .mobile-menu-accordion {
            background: var(--white);
            border-radius: 10px;
            margin-bottom: 8px;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .mobile-menu-accordion-header {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: var(--text-dark);
            font-size: 15px;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
            user-select: none;
        }

        .mobile-menu-accordion-header i {
            font-size: 20px;
            margin-right: 15px;
            color: var(--primary-color);
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .mobile-menu-accordion-header .accordion-icon {
            margin-left: auto;
            transition: var(--transition);
            font-size: 14px;
            color: var(--text-light);
        }

        .mobile-menu-accordion-header.active .accordion-icon {
            transform: rotate(90deg);
            color: var(--primary-color);
        }

        .mobile-menu-accordion-header:active {
            background: rgba(255, 123, 0, 0.1);
        }

        .mobile-menu-accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: var(--light-color);
        }

        /* PERBAIKAN UTAMA: Kalau expanded, langsung set height auto */
        .mobile-menu-accordion-content.expanded {
            max-height: 1000px !important;
            /* Important untuk override inline style */
        }

        .mobile-menu-sublink {
            display: flex;
            align-items: center;
            padding: 12px 20px 12px 55px;
            text-decoration: none;
            color: var(--text-light);
            font-size: 14px;
            font-weight: 500;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .mobile-menu-sublink i {
            font-size: 16px;
            margin-right: 10px;
            color: var(--secondary-color);
            width: 20px;
            text-align: center;
        }

        .mobile-menu-sublink .mobile-badge {
            position: static;
            margin-left: auto;
            transform: none;
            font-size: 11px;
            padding: 2px 6px;
            min-width: 18px;
        }

        .mobile-menu-sublink:active {
            background: rgba(255, 123, 0, 0.05);
            color: var(--primary-color);
        }

        /* Regular Menu Link */
        .mobile-menu-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            margin-bottom: 8px;
            background: var(--white);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-dark);
            font-size: 15px;
            font-weight: 600;
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .mobile-menu-link i {
            font-size: 20px;
            margin-right: 15px;
            color: var(--primary-color);
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .mobile-menu-link .mobile-badge {
            position: static;
            margin-left: auto;
            transform: none;
            font-size: 11px;
            padding: 2px 6px;
            min-width: 18px;
        }

        .mobile-menu-link:active {
            background: rgba(255, 123, 0, 0.1);
            border-color: var(--primary-color);
            transform: scale(0.98);
        }

        .mobile-menu-link.logout-link {
            margin-top: 20px;
            border-color: var(--danger-color);
            background: rgba(220, 53, 69, 0.1);
        }

        .mobile-menu-link.logout-link i {
            color: var(--danger-color);
        }

        .mobile-menu-link.logout-link:active {
            background: rgba(220, 53, 69, 0.2);
        }

        /* ACTIVE STATE STYLES */
        .mobile-menu-link.active,
        .mobile-menu-sublink.active {
            background: rgba(255, 123, 0, 0.15) !important;
            border-color: var(--primary-color) !important;
            color: var(--primary-color) !important;
        }

        .mobile-menu-link.active i,
        .mobile-menu-sublink.active i {
            color: var(--primary-color) !important;
        }

        .mobile-menu-sublink.active {
            background: rgba(255, 123, 0, 0.1) !important;
        }

        /* Section Styles */
        .mobile-menu-divider {
            height: 1px;
            background: var(--border-color);
            margin: 20px 0;
        }

        .mobile-menu-section-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 15px 0 10px 0;
            padding: 0 5px;
        }
    }

    /* ========================================
       FOOTER MOBILE ADJUSTMENT
    ======================================== */
    @media screen and (max-width: 768px) {
        .footer {
            padding-bottom: 70px !important;
        }

        .content-body {
            padding-bottom: 80px !important;
        }
    }

    /* ========================================
       EXTRA SMALL DEVICES
    ======================================== */
    @media screen and (max-width: 375px) {
        .mobile-nav-item {
            font-size: 10px;
            padding: 3px;
        }

        .mobile-nav-item i {
            font-size: 20px;
        }

        .mobile-nav-item span {
            font-size: 9px;
        }

        .fab-button {
            width: 50px;
            height: 50px;
        }

        .fab-button i {
            font-size: 22px;
        }

        .mobile-menu-link,
        .mobile-menu-accordion-header {
            padding: 12px 15px;
            font-size: 14px;
        }

        .mobile-menu-sublink {
            padding: 10px 15px 10px 50px;
            font-size: 13px;
        }

        .mobile-menu-user-info {
            padding: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
        }

        .user-avatar i {
            font-size: 24px;
        }
    }

    /* ========================================
       LANDSCAPE MODE
    ======================================== */
    @media screen and (max-width: 768px) and (orientation: landscape) {
        .mobile-bottom-nav {
            height: 55px;
        }

        .mobile-nav-item i {
            font-size: 20px;
        }

        .mobile-nav-item span {
            font-size: 9px;
        }

        .fab-button {
            width: 48px;
            height: 48px;
        }

        .mobile-nav-item.mobile-fab {
            top: -15px;
        }

        .mobile-menu-modal {
            overflow-y: scroll;
        }
    }
</style>
