// JavaScript لتحسين تفاعل صفحة الاتصال والفئات

document.addEventListener('DOMContentLoaded', function() {
    // تحسين تفاعل التبويبات
    initCategoryTabs();

    // تحسين نموذج الاتصال
    initContactForm();

    // تحسين الصور المتحركة
    initHeroCarousel();

    // تحسين التمرير السلس
    initSmoothScrolling();
});

// تهيئة التبويبات
function initCategoryTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const collectionCards = document.querySelectorAll('.collection-card');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');

            // إزالة الكلاس النشط من جميع الأزرار
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // إضافة الكلاس النشط للزر المحدد
            this.classList.add('active');

            // تصفية البطاقات
            collectionCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');

                if (category === 'all' || cardCategory === category) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// تهيئة نموذج الاتصال
function initContactForm() {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('.form-submit');
            const originalText = submitButton.textContent;

            // تعطيل الزر وإظهار حالة التحميل
            submitButton.disabled = true;
            submitButton.textContent = 'جاري الإرسال...';
            submitButton.style.opacity = '0.7';

            // إعادة تفعيل الزر بعد 3 ثوان (في حالة عدم إعادة التوجيه)
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                submitButton.style.opacity = '1';
            }, 3000);
        });

        // تحسين التحقق من صحة البيانات
        const inputs = contactForm.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        });
    }
}

// التحقق من صحة حقل واحد
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.getAttribute('name');
    let isValid = true;
    let errorMessage = '';

    // إزالة رسائل الخطأ السابقة
    removeFieldError(field);

    // التحقق من الحقول المطلوبة
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'هذا الحقل مطلوب';
    }

    // التحقق من البريد الإلكتروني
    if (fieldName === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'البريد الإلكتروني غير صحيح';
        }
    }

    // التحقق من رقم الهاتف
    if (fieldName === 'phone' && value) {
        const phoneRegex = /^[0-9+\-\s()]+$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = 'رقم الهاتف غير صحيح';
        }
    }

    // التحقق من طول الرسالة
    if (fieldName === 'message' && value && value.length < 10) {
        isValid = false;
        errorMessage = 'الرسالة يجب أن تكون أكثر من 10 أحرف';
    }

    if (!isValid) {
        showFieldError(field, errorMessage);
    }

    return isValid;
}

// إظهار خطأ في حقل
function showFieldError(field, message) {
    field.classList.add('error');
    field.style.borderColor = '#e53e3e';

    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#e53e3e';
    errorDiv.style.fontSize = '14px';
    errorDiv.style.marginTop = '5px';

    field.parentNode.appendChild(errorDiv);
}

// إزالة خطأ من حقل
function removeFieldError(field) {
    field.classList.remove('error');
    field.style.borderColor = '';

    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// تهيئة الصور المتحركة
function initHeroCarousel() {
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    let currentSlide = 0;

    if (slides.length === 0) return;

    function showSlide(index) {
        // إخفاء جميع الشرائح
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));

        // إظهار الشريحة المحددة
        slides[index].classList.add('active');
        indicators[index].classList.add('active');

        currentSlide = index;
    }

    function nextSlide() {
        const nextIndex = (currentSlide + 1) % slides.length;
        showSlide(nextIndex);
    }

    // إضافة مستمعي الأحداث للعلامات
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => showSlide(index));
    });

    // تبديل تلقائي كل 5 ثوان
    setInterval(nextSlide, 5000);
}

// تهيئة التمرير السلس
function initSmoothScrolling() {
    const links = document.querySelectorAll('a[href^="#"]');

    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                const headerHeight = document.querySelector('header')?.offsetHeight || 0;
                const targetPosition = targetElement.offsetTop - headerHeight - 20;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// تحسين تجربة المستخدم للهواتف المحمولة
function initMobileOptimizations() {
    // تحسين حجم الخط للهواتف المحمولة
    if (window.innerWidth <= 768) {
        document.body.style.fontSize = '16px';
    }

    // تحسين النماذج للهواتف المحمولة
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            // منع التكبير التلقائي في iOS
            if (this.tagName === 'INPUT' && this.type !== 'range') {
                this.style.fontSize = '16px';
            }
        });
    });
}

// تهيئة التحسينات للهواتف المحمولة
initMobileOptimizations();

// إضافة تأثيرات الحركة
function addScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
            }
        });
    }, observerOptions);

    // مراقبة العناصر المراد تحريكها
    const animatedElements = document.querySelectorAll('.collection-card, .info-item, .testimonial-card');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        observer.observe(el);
    });
}

// تهيئة تأثيرات الحركة
addScrollAnimations();

// إضافة CSS للحركات
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .error {
        border-color: #e53e3e !important;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1) !important;
    }

    .field-error {
        animation: fadeInUp 0.3s ease forwards;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem !important;
        }

        .section-title {
            font-size: 2rem !important;
        }

        .hero-stats {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }

        .cta-group {
            flex-direction: column !important;
        }

        .cta-button {
            width: 100% !important;
            text-align: center !important;
        }
    }
`;
document.head.appendChild(style);
