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
                mobileMenuModal.classList.add('closing');
                setTimeout(() => {
                    mobileMenuModal.classList.remove('active');
                    mobileMenuModal.classList.remove('closing');
                    document.body.style.overflow = '';
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
                let hasActiveSublink = false;

                if (content) {
                    const activeSublinks = content.querySelectorAll('.mobile-menu-sublink.active');
                    hasActiveSublink = activeSublinks.length > 0;
                }

                if (!hasActiveSublink) {
                    header.classList.remove('active');
                    if (content) {
                        content.classList.remove('expanded');
                        content.style.maxHeight = null;
                    }
                }
            });
        }

        /**
         * Open specific accordion
         */
        function openAccordion(accordionHeader) {
            const content = accordionHeader.nextElementSibling;
            if (content && content.classList.contains('mobile-menu-accordion-content')) {
                accordionHeader.classList.add('active');
                content.classList.add('expanded');
                setTimeout(() => {
                    content.style.maxHeight = content.scrollHeight + 'px';
                }, 10);
            }
        }

        /**
         * Close specific accordion
         */
        function closeAccordion(accordionHeader) {
            const content = accordionHeader.nextElementSibling;
            if (content && content.classList.contains('mobile-menu-accordion-content')) {
                accordionHeader.classList.remove('active');
                content.classList.remove('expanded');
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

            // Remove existing listeners by cloning
            accordionHeaders.forEach(header => {
                const newHeader = header.cloneNode(true);
                header.parentNode.replaceChild(newHeader, header);
            });

            // Add fresh listeners
            const freshHeaders = document.querySelectorAll('.mobile-menu-accordion-header');
            freshHeaders.forEach(header => {
                header.addEventListener('click', function(e) {
                    if (window.innerWidth > 768) return;
                    e.preventDefault();
                    e.stopPropagation();
                    toggleAccordion(this);
                });
            });

            // Auto-open accordions with active links
            setTimeout(() => {
                autoOpenAccordionsWithActiveLinks();
            }, 100);
        }

        /**
         * Auto-open accordions that contain active links
         */
        function autoOpenAccordionsWithActiveLinks() {
            const accordions = document.querySelectorAll('.mobile-menu-accordion');

            accordions.forEach(accordion => {
                const content = accordion.querySelector('.mobile-menu-accordion-content');
                if (content) {
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

            if (mobileMenuTrigger) {
                mobileMenuTrigger.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (mobileMenuModal) {
                            mobileMenuModal.classList.add('active');
                            document.body.style.overflow = 'hidden';
                            setTimeout(() => {
                                initMobileAccordions();
                                updateActiveStates();
                            }, 100);
                        }
                    }
                });
            }

            if (closeMobileMenu) {
                closeMobileMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                    closeMobileMenuModalWithAnimation();
                });
            }

            if (mobileMenuModal) {
                mobileMenuModal.addEventListener('click', function(e) {
                    if (e.target === mobileMenuModal) {
                        closeMobileMenuModalWithAnimation();
                    }
                });
            }

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

            updateBottomNavActiveState(currentPath);
            updateMobileMenuActiveState(currentPath, fullPath);

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
                    if ((href.includes('/dashboard') && currentPath.includes('/dashboard')) ||
                        (href.includes('/tickets') && currentPath.includes('/tickets')) ||
                        (href.includes('/notifications') && currentPath.includes('/notifications')) ||
                        (href.includes('/calendar') && currentPath.includes('/calendar'))) {
                        item.classList.add('active');
                    }
                }
            });
        }

        /**
         * Update mobile menu active state
         */
        function updateMobileMenuActiveState(currentPath, fullPath) {
            const menuLinks = document.querySelectorAll('.mobile-menu-link, .mobile-menu-sublink');

            menuLinks.forEach(link => {
                link.classList.remove('active');
                const href = link.getAttribute('href');
                if (!href || href.startsWith('javascript:')) return;

                try {
                    const linkUrl = new URL(href, window.location.origin);
                    const currentUrl = new URL(window.location.href);

                    if (linkUrl.pathname === currentUrl.pathname) {
                        if (!href.includes('?')) {
                            link.classList.add('active');
                        } else {
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
            const menuLinks = document.querySelectorAll('.mobile-menu-link, .mobile-menu-sublink');

            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.classList.contains('mobile-menu-accordion-header')) {
                        return;
                    }
                    const href = this.getAttribute('href');
                    if (href && !href.startsWith('javascript:')) {
                        updateActiveStates();
                        setTimeout(() => {
                            closeMobileMenuModalWithAnimation();
                        }, 300);
                    }
                });
            });

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
            toggleMobileDesktopView();
            initMobileMenuModal();

            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    initMobileAccordions();
                    updateActiveStates();
                }, 200);
            }

            initLinkHandling();
            window.addEventListener('resize', handleResize);
            isInitialized = true;
            console.log('Sidebar initialized successfully');
        }

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
            confirmButtonColor: '#ff6600',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-sign-out-alt me-2"></i>Yes, Logout',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
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
                    form.submit();
                    resolve();
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    }
</script>
