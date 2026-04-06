<!-- sidebar-script.blade.php -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ========================================
        // VARIABLES & STATE MANAGEMENT
        // ========================================
        let isInitialized = false;
        let resizeTimeout;

        // ========================================
        // CORE FUNCTIONS
        // ========================================

        /**
         * Toggle between mobile and desktop sidebar view
         */
        function toggleMobileDesktopView() {
            const isMobile = window.innerWidth <= 768;
            const desktopSidebar = document.querySelector('.deznav.desktop-sidebar');
            const bottomNav = document.querySelector('.mobile-bottom-nav');
            const navControl = document.querySelector('.nav-control');
            const overlay = document.querySelector('.deznav-overlay');

            if (isMobile) {
                // MOBILE VIEW
                if (desktopSidebar) {
                    desktopSidebar.style.display = 'none';
                    desktopSidebar.classList.remove('is-active');
                }
                if (bottomNav) bottomNav.style.display = 'block';
                if (navControl) navControl.style.display = 'none';
                if (overlay) overlay.style.display = 'none';
                document.body.classList.remove('sidebar-open');
            } else {
                // DESKTOP VIEW - CLOSE MOBILE MODAL
                if (desktopSidebar) desktopSidebar.style.display = 'block';
                if (bottomNav) bottomNav.style.display = 'none';
                if (navControl) navControl.style.display = 'flex';

                closeMobileMenuModalWithAnimation();
            }
        }

        /**
         * Close mobile menu modal with fade animation
         */
        function closeMobileMenuModalWithAnimation() {
            const mobileMenuModal = document.getElementById('mobileMenuModal');
            if (mobileMenuModal && mobileMenuModal.classList.contains('active')) {
                // Add closing animation class
                mobileMenuModal.classList.add('closing');

                // Wait for animation to complete
                setTimeout(() => {
                    mobileMenuModal.classList.remove('active');
                    mobileMenuModal.classList.remove('closing');
                    document.body.style.overflow = '';

                    // JANGAN close accordions yang punya sublink aktif
                    closeOnlyEmptyAccordions();
                }, 300);
            }
        }

        /**
         * Close only accordions that don't have active sublinks
         */
        function closeOnlyEmptyAccordions() {
            const accordionHeaders = document.querySelectorAll('.mobile-menu-accordion-header');

            accordionHeaders.forEach(header => {
                const accordion = header.parentElement;
                const content = accordion.querySelector('.mobile-menu-accordion-content');

                // Cek apakah ada sublink aktif dalam accordion ini
                let hasActiveSublink = false;
                if (content) {
                    const activeSublinks = content.querySelectorAll('.mobile-menu-sublink.active');
                    hasActiveSublink = activeSublinks.length > 0;
                }

                // Jika TIDAK ada sublink aktif, tutup accordion
                if (!hasActiveSublink) {
                    header.classList.remove('active');
                    if (content) {
                        content.classList.remove('expanded');
                        // Hapus inline style max-height
                        content.style.maxHeight = null;
                    }
                }
            });
        }

        /**
         * Open specific accordion - FIXED VERSION
         */
        function openAccordion(accordionHeader) {
            const accordion = accordionHeader.parentElement;
            const content = accordion.querySelector('.mobile-menu-accordion-content');

            if (content) {
                accordionHeader.classList.add('active');
                content.classList.add('expanded');

                // FIX: Set height setelah delay kecil untuk memastikan content sudah render
                setTimeout(() => {
                    content.style.maxHeight = content.scrollHeight + 'px';
                }, 10);
            }
        }

        /**
         * Close specific accordion
         */
        function closeAccordion(accordionHeader) {
            const accordion = accordionHeader.parentElement;
            const content = accordion.querySelector('.mobile-menu-accordion-content');

            if (content) {
                accordionHeader.classList.remove('active');
                content.classList.remove('expanded');
                // Set height ke 0 dengan transition
                content.style.maxHeight = '0';
            }
        }

        /**
         * Toggle accordion (open/close)
         */
        function toggleAccordion(accordionHeader) {
            const isActive = accordionHeader.classList.contains('active');

            if (isActive) {
                closeAccordion(accordionHeader);
            } else {
                openAccordion(accordionHeader);
            }
        }

        /**
         * Initialize mobile menu accordions - FIXED VERSION
         */
        function initMobileAccordions() {
            const accordionHeaders = document.querySelectorAll('.mobile-menu-accordion-header');

            accordionHeaders.forEach(header => {
                // Remove existing click listeners
                const newHeader = header.cloneNode(true);
                header.parentNode.replaceChild(newHeader, header);
            });

            // Get fresh references
            const freshHeaders = document.querySelectorAll('.mobile-menu-accordion-header');

            freshHeaders.forEach(header => {
                header.addEventListener('click', function(e) {
                    if (window.innerWidth > 768) return;

                    e.preventDefault();
                    e.stopPropagation();

                    toggleAccordion(this);
                });
            });

            // Auto-open accordions with active sublinks - FIXED
            setTimeout(() => {
                autoOpenAccordionsWithActiveLinks();
            }, 100); // Delay untuk memastikan DOM sudah selesai render
        }

        /**
         * Auto-open accordions that contain active links - FIXED VERSION
         */
        function autoOpenAccordionsWithActiveLinks() {
            // Cari semua accordion
            const accordions = document.querySelectorAll('.mobile-menu-accordion');

            accordions.forEach(accordion => {
                const content = accordion.querySelector('.mobile-menu-accordion-content');
                if (content) {
                    // Cek apakah ada sublink aktif dalam accordion ini
                    const activeSublinks = content.querySelectorAll('.mobile-menu-sublink.active');
                    const hasActiveSublink = activeSublinks.length > 0;

                    if (hasActiveSublink) {
                        const accordionHeader = accordion.querySelector(
                            '.mobile-menu-accordion-header');
                        if (accordionHeader && !accordionHeader.classList.contains('active')) {
                            openAccordion(accordionHeader);
                        }
                    }
                }
            });
        }

        /**
         * Initialize mobile menu modal functionality
         */
        function initMobileMenuModal() {
            const mobileMenuTrigger = document.getElementById('mobileMenuTrigger');
            const mobileMenuModal = document.getElementById('mobileMenuModal');
            const closeMobileMenu = document.getElementById('closeMobileMenu');

            // Open mobile menu
            if (mobileMenuTrigger) {
                mobileMenuTrigger.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        e.stopPropagation();

                        if (mobileMenuModal) {
                            mobileMenuModal.classList.add('active');
                            document.body.style.overflow = 'hidden';

                            // Re-initialize accordions when menu opens - FIXED
                            setTimeout(() => {
                                initMobileAccordions();
                                updateActiveStates();
                            }, 100); // Delay untuk memastikan modal sudah terbuka
                        }
                    }
                });
            }

            // Close mobile menu button
            if (closeMobileMenu) {
                closeMobileMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                    closeMobileMenuModalWithAnimation();
                });
            }

            // Close on outside click
            if (mobileMenuModal) {
                mobileMenuModal.addEventListener('click', function(e) {
                    if (e.target === mobileMenuModal) {
                        closeMobileMenuModalWithAnimation();
                    }
                });
            }

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileMenuModalWithAnimation();
                }
            });
        }

        /**
         * Update active states for navigation items
         */
        function updateActiveStates() {
            const currentPath = window.location.pathname;
            const currentSearch = window.location.search;
            const fullPath = currentPath + currentSearch;

            // 1. Update bottom navigation
            updateBottomNavActiveState(currentPath);

            // 2. Update mobile menu links
            updateMobileMenuActiveState(currentPath, fullPath);

            // 3. Auto-open accordions with active links
            setTimeout(() => {
                autoOpenAccordionsWithActiveLinks();
            }, 150);
        }

        /**
         * Update bottom navigation active state
         */
        function updateBottomNavActiveState(currentPath) {
            const bottomNavItems = document.querySelectorAll('.mobile-nav-item');

            bottomNavItems.forEach(item => {
                item.classList.remove('active');
                const href = item.getAttribute('href');

                if (href) {
                    // Check for exact or partial matches
                    if (
                        (href.includes('/dashboard') && currentPath.includes('/dashboard')) ||
                        (href.includes('/tickets') && currentPath.includes('/tickets')) ||
                        (href.includes('/notifications') && currentPath.includes('/notifications'))
                    ) {
                        item.classList.add('active');
                    }
                }
            });
        }

        /**
         * Update mobile menu active state
         */
        function updateMobileMenuActiveState(currentPath, fullPath) {
            // Update mobile menu links
            const menuLinks = document.querySelectorAll('.mobile-menu-link, .mobile-menu-sublink');

            menuLinks.forEach(link => {
                // Remove active class first
                link.classList.remove('active');

                const href = link.getAttribute('href');
                if (!href || href.startsWith('javascript:')) return;

                try {
                    // Create URL objects for comparison
                    const linkUrl = new URL(href, window.location.origin);
                    const currentUrl = new URL(window.location.href);

                    // Check pathname match
                    if (linkUrl.pathname === currentUrl.pathname) {
                        // For links without query params
                        if (!href.includes('?')) {
                            link.classList.add('active');
                        }
                        // For links with query params
                        else {
                            const linkParams = new URLSearchParams(linkUrl.search);
                            const currentParams = new URLSearchParams(currentUrl.search);

                            let allParamsMatch = true;
                            for (const [key, value] of linkParams) {
                                if (currentParams.get(key) !== value) {
                                    allParamsMatch = false;
                                    break;
                                }
                            }

                            if (allParamsMatch) {
                                link.classList.add('active');
                            }
                        }
                    }
                } catch (e) {
                    console.error('Error parsing URL:', e);
                }
            });
        }

        /**
         * Initialize link click handling
         */
        function initLinkHandling() {
            // Handle all menu link clicks in mobile modal
            const menuLinks = document.querySelectorAll('.mobile-menu-link, .mobile-menu-sublink');

            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Skip if it's an accordion header
                    if (this.classList.contains('mobile-menu-accordion-header')) {
                        return;
                    }

                    const href = this.getAttribute('href');

                    // Only handle real links
                    if (href && !href.startsWith('javascript:')) {
                        // Update active state immediately
                        updateActiveStates();

                        // Close mobile menu with animation after a delay
                        setTimeout(() => {
                            closeMobileMenuModalWithAnimation();
                        }, 300);
                    }
                });
            });

            // Handle bottom nav clicks
            const bottomNavItems = document.querySelectorAll('.mobile-nav-item');
            bottomNavItems.forEach(item => {
                item.addEventListener('click', function() {
                    const href = this.getAttribute('href');
                    if (href && !href.startsWith('javascript:')) {
                        setTimeout(updateActiveStates, 100);
                    }
                });
            });
        }

        /**
         * Handle window resize with debouncing
         */
        function handleResize() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                toggleMobileDesktopView();

                // Re-initialize if switching to mobile
                if (window.innerWidth <= 768) {
                    setTimeout(() => {
                        initMobileAccordions();
                        updateActiveStates();
                    }, 100);
                }
            }, 250);
        }

        // ========================================
        // MAIN INITIALIZATION
        // ========================================
        function initSidebar() {
            // 1. Initial view setup
            toggleMobileDesktopView();

            // 2. Initialize mobile menu modal
            initMobileMenuModal();

            // 3. Initialize accordions (only on mobile)
            if (window.innerWidth <= 768) {
                // Delay initialization untuk memastikan DOM ready
                setTimeout(() => {
                    initMobileAccordions();
                    updateActiveStates();
                }, 200);
            }

            // 4. Initialize link handling
            initLinkHandling();

            // 5. Set up resize handler
            window.addEventListener('resize', handleResize);

            // 6. Mark as initialized
            isInitialized = true;

            console.log('Sidebar initialized successfully');
        }

        // Start initialization when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSidebar);
        } else {
            initSidebar();
        }
    });

    function confirmLogout() {
        Swal.fire({
            title: 'Logout?',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ff6600', // Warna orange sesuai tema
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-sign-out-alt me-2"></i>Yes, Logout',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    // Buat form logout secara dinamis
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('logout') }}';
                    form.style.display = 'none';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    form.appendChild(csrfToken);
                    document.body.appendChild(form);

                    // Submit form
                    form.submit();

                    // Resolve promise (tidak akan pernah sampai sini karena redirect)
                    resolve();
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    }
</script>
