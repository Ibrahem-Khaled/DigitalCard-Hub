/* ========================================
   متجر البطاقات الرقمية - JavaScript للداشبورد
   ======================================== */

document.addEventListener('DOMContentLoaded', function() {
    // ========================================
    // إدارة الشريط الجانبي
    // ========================================

    const sidebar = document.getElementById('sidebar');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarExpandBtn = document.getElementById('sidebarExpandBtn');
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
    const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainWrapper = document.querySelector('.main-wrapper');

    // فتح الشريط الجانبي (للشاشات الصغيرة)
    function openSidebar() {
        sidebar.classList.add('open');
        sidebarOverlay.classList.add('show');
        mainWrapper.classList.add('sidebar-open');
        document.body.style.overflow = 'hidden';
    }

    // إغلاق الشريط الجانبي (للشاشات الصغيرة)
    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebarOverlay.classList.remove('show');
        mainWrapper.classList.remove('sidebar-open');
        document.body.style.overflow = '';
    }

    // تبديل حالة الشريط الجانبي (للشاشات الصغيرة)
    function toggleSidebar() {
        if (sidebar.classList.contains('open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    // طي الشريط الجانبي (للشاشات الكبيرة)
    function collapseSidebar() {
        sidebar.classList.add('collapsed');
        mainWrapper.classList.add('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', 'true');
    }

    // توسيع الشريط الجانبي (للشاشات الكبيرة)
    function expandSidebar() {
        sidebar.classList.remove('collapsed');
        mainWrapper.classList.remove('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', 'false');
    }

    // تبديل حالة طي الشريط الجانبي
    function toggleCollapse() {
        if (sidebar.classList.contains('collapsed')) {
            expandSidebar();
        } else {
            collapseSidebar();
        }
    }

    // إضافة مستمعي الأحداث
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', toggleSidebar);
    }

    if (sidebarExpandBtn) {
        sidebarExpandBtn.addEventListener('click', expandSidebar);
    }

    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', toggleCollapse);
    }

    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // إغلاق الشريط الجانبي عند النقر على رابط
    const sidebarLinks = sidebar.querySelectorAll('.nav-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });

    // إدارة حجم الشاشة
    function handleResize() {
        if (window.innerWidth >= 992) {
            closeSidebar();
            // استعادة حالة الشريط الجانبي المحفوظة
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                collapseSidebar();
            } else {
                expandSidebar();
            }
        } else {
            // إزالة حالة الطي للشاشات الصغيرة
            sidebar.classList.remove('collapsed');
            mainWrapper.classList.remove('sidebar-collapsed');
        }
    }

    window.addEventListener('resize', handleResize);

    // تهيئة حالة الشريط الجانبي عند التحميل
    function initializeSidebar() {
        if (window.innerWidth >= 992) {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                collapseSidebar();
            }
        }
    }

    // تشغيل التهيئة
    initializeSidebar();

    // ========================================
    // إدارة الإشعارات
    // ========================================

    const notificationBtn = document.querySelector('.notification-btn');
    const notificationBadge = document.querySelector('.notification-badge');
    const markAllReadBtn = document.querySelector('.mark-all-read');
    const notificationItems = document.querySelectorAll('.notification-item.unread');

    // تعيين جميع الإشعارات كمقروءة
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            notificationItems.forEach(item => {
                item.classList.remove('unread');
            });

            if (notificationBadge) {
                notificationBadge.style.display = 'none';
            }

            // إضافة تأثير بصري
            this.style.opacity = '0.5';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 200);
        });
    }

    // تحديث عداد الإشعارات
    function updateNotificationCount() {
        const unreadCount = document.querySelectorAll('.notification-item.unread').length;
        if (notificationBadge) {
            if (unreadCount > 0) {
                notificationBadge.textContent = unreadCount;
                notificationBadge.style.display = 'flex';
            } else {
                notificationBadge.style.display = 'none';
            }
        }
    }

    // ========================================
    // إدارة شريط البحث
    // ========================================

    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');

    if (searchInput) {
        // البحث عند الضغط على Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch(this.value);
            }
        });

        // البحث عند النقر على زر البحث
        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                performSearch(searchInput.value);
            });
        }

        // تأثير التركيز
        searchInput.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
            this.parentElement.style.boxShadow = '0 8px 25px rgba(139, 92, 246, 0.3)';
        });

        searchInput.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
            this.parentElement.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
        });
    }

    function performSearch(query) {
        if (query.trim()) {
            console.log('البحث عن:', query);
            // هنا يمكن إضافة منطق البحث الفعلي
            showSearchResults(query);
        }
    }

    function showSearchResults(query) {
        // إضافة تأثير بصري للبحث
        const searchBox = document.querySelector('.search-box');
        searchBox.style.background = 'linear-gradient(135deg, #8B5CF6, #6D28D9)';
        searchBox.style.color = 'white';

        setTimeout(() => {
            searchBox.style.background = 'var(--bg-white)';
            searchBox.style.color = 'var(--text-dark)';
        }, 1000);
    }

    // ========================================
    // تأثيرات بصرية متقدمة
    // ========================================

    // تأثير الظهور للعناصر
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // مراقبة العناصر للظهور
    const elementsToObserve = document.querySelectorAll('.card, .stats-card, .table-container');
    elementsToObserve.forEach(element => {
        observer.observe(element);
    });

    // ========================================
    // إدارة القوائم المنسدلة
    // ========================================

    // إضافة تأثيرات للقوائم المنسدلة
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                // إضافة تأثير الارتداد
                menu.classList.add('bounce-in');

                // إزالة التأثير بعد انتهاء الرسوم المتحركة
                setTimeout(() => {
                    menu.classList.remove('bounce-in');
                }, 600);
            });
        }
    });

    // ========================================
    // إدارة الأزرار التفاعلية
    // ========================================

    // تأثيرات الأزرار
    const buttons = document.querySelectorAll('.btn, .notification-btn, .user-btn, .search-btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });

        button.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(0)';
        });

        button.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-2px)';
        });
    });

    // ========================================
    // إدارة النماذج
    // ========================================

    // تأثيرات حقول الإدخال
    const formInputs = document.querySelectorAll('.form-control, .search-input');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // ========================================
    // إدارة الجداول
    // ========================================

    // تأثيرات صفوف الجداول
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(-4px)';
            this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
            this.style.boxShadow = 'none';
        });
    });

    // ========================================
    // إدارة البطاقات
    // ========================================

    // تأثيرات البطاقات
    const cards = document.querySelectorAll('.card, .stats-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
        });
    });

    // ========================================
    // إدارة التنبيهات
    // ========================================

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification-toast notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // إضافة تأثير الظهور
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // إزالة التنبيه بعد 3 ثوان
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // ========================================
    // إدارة المفاتيح السريعة
    // ========================================

    document.addEventListener('keydown', function(e) {
        // Ctrl + K للبحث
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
            }
        }

        // Escape لإغلاق الشريط الجانبي
        if (e.key === 'Escape') {
            closeSidebar();
        }

        // Ctrl + B لتبديل الشريط الجانبي
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            toggleSidebar();
        }
    });

    // ========================================
    // إدارة التحميل
    // ========================================

    function showLoading(element) {
        element.classList.add('loading');
    }

    function hideLoading(element) {
        element.classList.remove('loading');
    }

    // ========================================
    // إدارة الأخطاء
    // ========================================

    window.addEventListener('error', function(e) {
        console.error('خطأ في JavaScript:', e.error);
        showNotification('حدث خطأ غير متوقع', 'error');
    });

    // ========================================
    // تهيئة النظام
    // ========================================

    // تحديث عداد الإشعارات عند التحميل
    updateNotificationCount();

    // إضافة تأثير التحميل للصفحة
    document.body.classList.add('loaded');

    console.log('تم تحميل نظام الداشبورد بنجاح!');
});

// ========================================
// دوال مساعدة عامة
// ========================================

// تنسيق الأرقام
function formatNumber(num) {
    return new Intl.NumberFormat('ar-SA').format(num);
}

// تنسيق العملة
function formatCurrency(amount, currency = 'SAR') {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// تنسيق التاريخ
function formatDate(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    return new Intl.DateTimeFormat('ar-SA', { ...defaultOptions, ...options }).format(new Date(date));
}

// تنسيق الوقت النسبي
function formatRelativeTime(date) {
    const now = new Date();
    const diff = now - new Date(date);
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 60) {
        return `منذ ${minutes} دقيقة`;
    } else if (hours < 24) {
        return `منذ ${hours} ساعة`;
    } else {
        return `منذ ${days} يوم`;
    }
}

// نسخ النص للحافظة
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('تم نسخ النص بنجاح', 'success');
    }).catch(() => {
        showNotification('فشل في نسخ النص', 'error');
    });
}

// تحميل البيانات
async function loadData(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('خطأ في تحميل البيانات:', error);
        showNotification('فشل في تحميل البيانات', 'error');
        throw error;
    }
}

// حفظ البيانات
async function saveData(url, data, options = {}) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                ...options.headers
            },
            body: JSON.stringify(data),
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('خطأ في حفظ البيانات:', error);
        showNotification('فشل في حفظ البيانات', 'error');
        throw error;
    }
}
