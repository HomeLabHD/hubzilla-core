/**
 * redbasic theme specific JavaScript
 */

const redbasic_dark_mode = localStorage.getItem('redbasic_dark_mode');
const redbasic_theme_color = localStorage.getItem('redbasic_theme_color');

if (redbasic_dark_mode == 1) {
    document.documentElement.setAttribute('data-bs-theme', 'dark');
}

if (redbasic_dark_mode == 0) {
    document.documentElement.setAttribute('data-bs-theme', 'light');
}

if (redbasic_theme_color) {
    document.querySelector('meta[name=theme-color]').setAttribute('content', redbasic_theme_color);
}

document.addEventListener('DOMContentLoaded', function () {
    // provide a fake progress bar for pwa standalone mode
    if (window.matchMedia('(display-mode: standalone)').matches) {
        window.addEventListener('beforeunload', function () {
            if (!document.querySelector('.page-loader')) {
                let loader = document.createElement('div');
                loader.classList.add('bg-primary', 'page-loader');
                document.body.prepend(loader);
            }
        });
    }

    if (redbasic_dark_mode == 1) {
        document.getElementById('theme-switch-icon').classList.remove('bi-moon');
        document.getElementById('theme-switch-icon').classList.add('bi-sun');
        document.querySelector('[data-bs-theme="light"]').setAttribute('data-bs-theme', 'dark');
    }
    if (redbasic_dark_mode == 0) {
        document.getElementById('theme-switch-icon').classList.remove('bi-sun');
        document.getElementById('theme-switch-icon').classList.add('bi-moon');
        let darkElements = document.querySelectorAll('[data-bs-theme="dark"]:not(nav)');
        darkElements.forEach(el => el.setAttribute('data-bs-theme', 'light'));
    }

    const navBackgroundColor = document.querySelector('nav').style.backgroundColor;
    if (redbasic_theme_color !== navBackgroundColor) {
        document.querySelector('meta[name=theme-color]').setAttribute('content', navBackgroundColor);
        localStorage.setItem('redbasic_theme_color', navBackgroundColor);
    }

    // CSS3 calc() fallback (for unsupported browsers)
    const testElem = document.createElement('div');
    testElem.style.width = 'calc(10px + 10px)';
    testElem.style.display = 'none';
    testElem.id = 'css3-calc';
    document.body.appendChild(testElem);
    
    if (testElem.offsetWidth === 10) {
        window.addEventListener('resize', function () {
            if (window.innerWidth < 992) {
                document.querySelector('main').style.width = `${window.innerWidth + document.querySelector('aside').offsetWidth}px`;
            } else {
                document.querySelector('main').style.width = '100%';
            }
        });
    }
    testElem.remove(); // Remove the test element

    if (window.innerWidth < 1200) {
        const rightAsideWrapper = document.getElementById("right_aside_wrapper");
        const leftAsideWrapper = document.getElementById("left_aside_wrapper");
        const notificationsWrapper = document.getElementById("notifications_wrapper");

        if (leftAsideWrapper && rightAsideWrapper) {
            const children = rightAsideWrapper.children;
            if (children.length) {
                leftAsideWrapper.append(...children);
            }
        }

        if (notificationsWrapper) {
            notificationsWrapper.classList.add('d-none');
        }
    }

    if (document.getElementById('region_1')) {
        stickyScroll('.aside_spacer_left', '.aside_spacer_top_left', 'section', parseFloat(document.querySelector('main').getBoundingClientRect().top), 20);
    }

    if (document.getElementById('region_3')) {
        stickyScroll('.aside_spacer_right', '.aside_spacer_top_right', 'section', parseFloat(document.querySelector('main').getBoundingClientRect().top), 20);
    }

    document.querySelectorAll('.usermenu').forEach(function (element) {
        element.addEventListener('click', function () {
            let navCollapse = document.querySelectorAll('#navbar-collapse-1, #navbar-collapse-2');
            navCollapse.forEach(function (nav) {
                if (nav.classList.contains('show')) {
                    nav.classList.remove('show');
                }
            });
        });
    });

    document.getElementById('theme-switch').addEventListener('click', function () {
        const html = document.documentElement;
        if (html.getAttribute('data-bs-theme') === 'dark') {
            let nav = document.querySelector('nav');
            if (nav.dataset.bsTheme === 'dark') {
                document.querySelectorAll('[data-bs-theme="dark"]:not(nav)').forEach(function (el) {
                    el.setAttribute('data-bs-theme', 'light');
                });
            } else {
                document.querySelector('[data-bs-theme="dark"]').setAttribute('data-bs-theme', 'light');
            }
            localStorage.setItem('redbasic_dark_mode', 0);
            document.getElementById('theme-switch-icon').classList.remove('bi-sun');
            document.getElementById('theme-switch-icon').classList.add('bi-moon');
        } else {
            document.querySelectorAll('[data-bs-theme="light"]').forEach(function (el) {
                el.setAttribute('data-bs-theme', 'dark');
            });
            localStorage.setItem('redbasic_dark_mode', 1);
            document.getElementById('theme-switch-icon').classList.remove('bi-moon');
            document.getElementById('theme-switch-icon').classList.add('bi-sun');
        }
        document.querySelector('meta[name=theme-color]').setAttribute('content', document.querySelector('nav').style.backgroundColor);
        localStorage.setItem('redbasic_theme_color', document.querySelector('nav').style.backgroundColor);
    });

    document.getElementById('menu-btn').addEventListener('click', function () {
        const navCollapse = document.getElementById('navbar-collapse-1');
        if (navCollapse && navCollapse.classList.contains('show')) {
            navCollapse.classList.remove('show');
        }
    });

    document.querySelectorAll('.notifications-btn').forEach(function (element) {
        element.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            let navCollapse = document.getElementById('navbar-collapse-2');
            if (navCollapse && navCollapse.classList.contains('show')) {
                navCollapse.classList.remove('show');
            }
        });
    });

    $("input[data-role=cat-tagsinput]").tagsinput({
        tagClass: 'badge rounded-pill bg-warning text-dark'
    });

    document.querySelectorAll('a.disabled').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    const doctitle = document.title;
    function checkNotify() {
        let notifyUpdateElem = document.getElementById('notify-update');
        if (notifyUpdateElem && notifyUpdateElem.innerHTML !== '') {
            document.title = '(' + notifyUpdateElem.innerHTML + ') ' + doctitle;
        } else {
            document.title = doctitle;
        }
    }

    setInterval(checkNotify, 10 * 1000);

    let touch_start = null;
    const touch_max = window.innerWidth / 10;

    window.addEventListener('touchstart', function (e) {
        if (e.touches.length === 1) {
            touch_start = e.touches[0].clientX;
            if (touch_start < touch_max) {
                document.body.style.overflowY = 'hidden';
            }
        } else {
            touch_start = null;
        }
    });

    window.addEventListener('touchend', function (e) {
        document.body.style.overflowY = '';
        let touch_offset = 30; // at least 30px are a swipe
        if (touch_start) {
            let touch_end = e.changedTouches[0].clientX;
            if (touch_end > (touch_start + touch_offset)) {
                if (touch_start < touch_max) {
                    toggleAside();
                }
            }
            if (touch_end < (touch_start - touch_offset)) {
                // a right -> left swipe
            }
        }
    });
});

function setStyle(element, cssProperty) {
    for (let property in cssProperty) {
        element.style[property] = cssProperty[property];
    }
}

function stickyScroll(sticky, stickyTop, container, topOffset, bottomOffset) {
    let lastScrollTop = 0;
    let stickyElement = document.querySelector(sticky);

    if (!stickyElement) {
        return;
    }

    let stickyHeight = stickyElement.getBoundingClientRect().height;
    const stickyTopElement = document.querySelector(stickyTop);
    const content = document.querySelector(container);
    let diff = window.innerHeight - stickyHeight;

    let h = 0;
    let lasth = 0;
    let st = window.pageYOffset || document.documentElement.scrollTop;

    let resizeObserver = new ResizeObserver(function () {
        stickyHeight = stickyElement.getBoundingClientRect().height;
        st = window.pageYOffset || document.documentElement.scrollTop;
        diff = window.innerHeight - stickyHeight;
    });

    resizeObserver.observe(stickyElement);
    resizeObserver.observe(content);

    window.addEventListener('scroll', function () {
        if (window.innerHeight > stickyHeight + topOffset) {
            setStyle(stickyTopElement, { height: '0px' });
            setStyle(stickyElement, { position: 'sticky', top: `${topOffset}px` });
        } else {
            st = window.pageYOffset || document.documentElement.scrollTop;
            if (st > lastScrollTop) {
                setStyle(stickyTopElement, { height: `${lasth}px` });
                setStyle(stickyElement, { position: 'sticky', top: `${Math.round(diff) - bottomOffset}px`, bottom: '' });
            } else {
                h = stickyElement.getBoundingClientRect().top - content.getBoundingClientRect().top;
                if (Math.round(stickyTopElement.getBoundingClientRect().height) === lasth) {
                    setStyle(stickyTopElement, { height: `${Math.round(h)}px` });
                }
                lasth = Math.round(h);
                setStyle(stickyElement, { position: 'sticky', top: '', bottom: `${Math.round(diff) - topOffset}px` });
            }
            lastScrollTop = st <= 0 ? 0 : st;
        }
    }, false);
}

function makeFullScreen(full) {
    if (typeof full === 'undefined' || full === true) {
        document.querySelector('main').classList.add('fullscreen');
        document.getElementById('fullscreen-btn').style.display = 'none';
        document.getElementById('inline-btn').style.display = 'inline-block';
    } else {
        document.querySelector('main').classList.remove('fullscreen');
        document.getElementById('fullscreen-btn').style.display = '';
        document.getElementById('inline-btn').style.display = 'none';
    }
}
