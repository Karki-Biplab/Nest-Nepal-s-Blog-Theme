const BlogReader = {
    state: {
        tocVisible: false,
        readingProgress: 0,
        currentSection: null,
        isScrolling: false,
        tocData: [],
        estimatedReadingTime: 0
    },
    
    cache: {
        elements: {},
        headings: [],
        tocLinks: []
    },
    
    config: {
        headerOffset: 80,
        scrollThrottle: 16,
        debounceDelay: 300,
        animationDuration: 300
    }
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing Blog Reader...');
    
    cacheElements();
    
    initFloatingTOC();
    initReadingProgress();
    initSmoothScrolling();
    initCodeCopyButtons();
    initShareButtons();
    initPostNavigation();
    initKeyboardNavigation();
    initLazyLoading();
    initScrollToTop();
    
    generateTOC();
    
    calculateReadingTime();
    
    console.log('✅ Blog Reader initialized successfully');
});

function cacheElements() {
    const elements = BlogReader.cache.elements;
    
    elements.floatingTOC = document.getElementById('floating-toc');
    elements.tocToggle = document.querySelector('.toc-toggle');
    elements.tocClose = document.querySelector('.toc-close');
    elements.tocContent = document.querySelector('.toc-content');
    elements.tocList = document.getElementById('toc-list');
    elements.progressBar = document.querySelector('.progress-bar, .reading-progress-bar');
    elements.postContent = document.querySelector('.post-content, .entry-content');
    elements.shareButtons = document.querySelectorAll('.share-btn');
    elements.backButton = document.querySelector('.back-button');
    elements.postTitle = document.querySelector('.post-title');
    elements.postMeta = document.querySelector('.post-meta');
    
    if (!elements.tocList && elements.tocContent) {
        elements.tocList = document.createElement('ul');
        elements.tocList.id = 'toc-list';
        elements.tocContent.appendChild(elements.tocList);
    }
    
    if (!document.querySelector('.scroll-to-top')) {
        const scrollToTopBtn = document.createElement('button');
        scrollToTopBtn.className = 'scroll-to-top';
        scrollToTopBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
            </svg>
        `;
        scrollToTopBtn.setAttribute('aria-label', 'Scroll to top');
        document.body.appendChild(scrollToTopBtn);
        elements.scrollToTop = scrollToTopBtn;
    }
}

function toggleTOC() {
    const { floatingTOC, tocToggle } = BlogReader.cache.elements;
    
    if (!floatingTOC || !tocToggle) return;

    BlogReader.state.tocVisible = !BlogReader.state.tocVisible;
    
    floatingTOC.classList.toggle('toc-visible', BlogReader.state.tocVisible);
    tocToggle.classList.toggle('active', BlogReader.state.tocVisible);
    tocToggle.setAttribute('aria-expanded', BlogReader.state.tocVisible);

    if (window.innerWidth <= 768) {
        document.body.style.overflow = BlogReader.state.tocVisible ? 'hidden' : '';
        
        if (BlogReader.state.tocVisible) {
            createBackdrop();
        } else {
            removeBackdrop();
        }
    }
    
    announceToScreenReader(BlogReader.state.tocVisible ? 'Table of contents opened' : 'Table of contents closed');
}

function createBackdrop() {
    if (document.querySelector('.toc-backdrop')) return;
    
    const backdrop = document.createElement('div');
    backdrop.className = 'toc-backdrop';
    backdrop.addEventListener('click', toggleTOC);
    document.body.appendChild(backdrop);
    
    requestAnimationFrame(() => {
        backdrop.style.opacity = '1';
    });
}

function removeBackdrop() {
    const backdrop = document.querySelector('.toc-backdrop');
    if (backdrop) {
        backdrop.style.opacity = '0';
        setTimeout(() => {
            backdrop.remove();
        }, BlogReader.config.animationDuration);
    }
}

function initFloatingTOC() {
    const { floatingTOC, tocToggle, tocClose } = BlogReader.cache.elements;
    
    if (!tocToggle || !floatingTOC) {
        console.warn('TOC elements not found');
        return;
    }

    if (tocClose) {
        tocClose.addEventListener('click', toggleTOC);
    }

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 768 && BlogReader.state.tocVisible) {
                toggleTOC();
            }
        }, BlogReader.config.debounceDelay);
    });

    floatingTOC.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            toggleTOC();
            tocToggle.focus();
        }
    });
}

function generateTOC() {
    const { postContent, tocList, floatingTOC, tocToggle } = BlogReader.cache.elements;
    
    if (!postContent || !tocList) {
        console.warn('TOC generation failed: Required elements not found');
        return;
    }

    tocList.innerHTML = '';
    BlogReader.cache.headings = [];
    BlogReader.cache.tocLinks = [];
    
    const headings = postContent.querySelectorAll('h1, h2, h3, h4, h5, h6');
    
    if (headings.length === 0) {
        if (floatingTOC) floatingTOC.style.display = 'none';
        if (tocToggle) tocToggle.style.display = 'none';
        return;
    }

    let tocHTML = '';
    let currentLevel = 0;
    let openLists = [];

    headings.forEach((heading, index) => {
        if (!heading.id) {
            heading.id = sanitizeId(heading.textContent) || `heading-${index}`;
        }

        const level = parseInt(heading.tagName.substring(1));
        const title = heading.textContent.trim();
        
        BlogReader.cache.headings.push({
            element: heading,
            id: heading.id,
            title: title,
            level: level
        });

        if (level > currentLevel) {
            for (let i = currentLevel; i < level; i++) {
                tocHTML += '<ul class="toc-nested">';
                openLists.push('</ul>');
            }
        } else if (level < currentLevel) {
            const levelsToClose = currentLevel - level;
            for (let i = 0; i < levelsToClose; i++) {
                tocHTML += openLists.pop() || '';
            }
        }

        tocHTML += `
            <li data-level="h${level}">
                <a href="#${heading.id}" class="toc-link" data-heading-id="${heading.id}">
                    ${escapeHtml(title)}
                </a>
            </li>
        `;

        currentLevel = level;
    });

    while (openLists.length > 0) {
        tocHTML += openLists.pop();
    }

    tocList.innerHTML = tocHTML;
    
    BlogReader.cache.tocLinks = tocList.querySelectorAll('.toc-link');
    
    BlogReader.cache.tocLinks.forEach(link => {
        link.addEventListener('click', handleTOCClick);
    });

    initScrollTracking();
    
    console.log(`📋 Generated TOC with ${headings.length} headings`);
}

function handleTOCClick(e) {
    e.preventDefault();
    
    const targetId = e.target.getAttribute('data-heading-id');
    const targetElement = document.getElementById(targetId);
    
    if (targetElement) {
        scrollToElement(targetElement);
        
        if (window.innerWidth <= 768 && BlogReader.state.tocVisible) {
            setTimeout(() => {
                toggleTOC();
            }, 300);
        }
        
        if (history.pushState) {
            history.pushState(null, null, `#${targetId}`);
        }
        
        announceToScreenReader(`Navigated to ${targetElement.textContent}`);
    }
}

function initScrollTracking() {
    const throttledHighlight = throttle(highlightActiveTOCItem, BlogReader.config.scrollThrottle);
    
    window.addEventListener('scroll', throttledHighlight);
    
    highlightActiveTOCItem();
}

function highlightActiveTOCItem() {
    const headings = BlogReader.cache.headings;
    const tocLinks = BlogReader.cache.tocLinks;
    
    if (!headings.length || !tocLinks.length) return;
    
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;
    const headerOffset = BlogReader.config.headerOffset;
    
    let activeHeading = null;
    
    for (let i = headings.length - 1; i >= 0; i--) {
        const heading = headings[i];
        if (heading.element.offsetTop - headerOffset <= scrollY + 10) {
            activeHeading = heading;
            break;
        }
    }
    
    tocLinks.forEach(link => {
        const listItem = link.closest('li');
        if (listItem) {
            listItem.classList.remove('active');
        }
    });
    
    if (activeHeading) {
        const activeLink = document.querySelector(`[data-heading-id="${activeHeading.id}"]`);
        if (activeLink) {
            const listItem = activeLink.closest('li');
            if (listItem) {
                listItem.classList.add('active');
                scrollTOCToActiveItem(activeLink);
            }
        }
        
        BlogReader.state.currentSection = activeHeading.id;
    }
}

function scrollTOCToActiveItem(activeLink) {
    const { floatingTOC } = BlogReader.cache.elements;
    
    if (!floatingTOC || window.innerWidth <= 768) return;
    
    const tocContainer = floatingTOC.querySelector('.toc-content');
    if (!tocContainer) return;
    
    const containerRect = tocContainer.getBoundingClientRect();
    const itemRect = activeLink.getBoundingClientRect();
    
    if (itemRect.top < containerRect.top || itemRect.bottom > containerRect.bottom) {
        activeLink.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }
}

function initReadingProgress() {
    const { progressBar } = BlogReader.cache.elements;
    
    if (!progressBar) {
        console.warn('Progress bar element not found');
        return;
    }
    
    const updateProgress = throttle(() => {
        const scrollPx = document.documentElement.scrollTop;
        const winHeightPx = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = Math.min((scrollPx / winHeightPx) * 100, 100);
        
        BlogReader.state.readingProgress = scrolled;
        progressBar.style.width = `${scrolled}%`;
        
        const { scrollToTop } = BlogReader.cache.elements;
        if (scrollToTop) {
            scrollToTop.classList.toggle('visible', scrolled > 20);
        }
    }, BlogReader.config.scrollThrottle);
    
    window.addEventListener('scroll', updateProgress);
    updateProgress();
}

function initSmoothScrolling() {
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href^="#"]');
        if (!link) return;
        
        const hash = link.getAttribute('href');
        if (hash === '#' || hash.length <= 1) return;
        
        const targetElement = document.querySelector(hash);
        if (targetElement) {
            e.preventDefault();
            scrollToElement(targetElement);
            
            if (history.pushState) {
                history.pushState(null, null, hash);
            }
        }
    });
}

function scrollToElement(element) {
    const headerOffset = BlogReader.config.headerOffset;
    const elementPosition = element.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
    
    BlogReader.state.isScrolling = true;
    
    window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
    });
    
    setTimeout(() => {
        BlogReader.state.isScrolling = false;
    }, 1000);
}

function initCodeCopyButtons() {
    const codeBlocks = document.querySelectorAll('pre');
    
    codeBlocks.forEach((pre, index) => {
        if (pre.querySelector('.copy-code-button')) return;
        
        const code = pre.querySelector('code');
        if (!code) return;
        
        const button = document.createElement('button');
        button.className = 'copy-code-button';
        button.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
            </svg>
            <span>Copy</span>
        `;
        button.setAttribute('aria-label', `Copy code block ${index + 1}`);
        
        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(code.textContent);
                
                const span = button.querySelector('span');
                const originalText = span.textContent;
                span.textContent = 'Copied!';
                button.classList.add('copied');
                
                setTimeout(() => {
                    span.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
                
                showNotification('Code copied to clipboard!');
            } catch (err) {
                console.error('Failed to copy code:', err);
                fallbackCopyTextToClipboard(code.textContent);
            }
        });
        
        pre.appendChild(button);
    });
    
    console.log(`📋 Added copy buttons to ${codeBlocks.length} code blocks`);
}

function initShareButtons() {
    const shareButtons = document.querySelectorAll('.share-btn');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            const platform = button.classList.contains('facebook') ? 'facebook' :
                            button.classList.contains('twitter') ? 'twitter' :
                            button.classList.contains('linkedin') ? 'linkedin' :
                            'copy';
            
            sharePost(platform);
        });
    });
    
    const mainShareButton = document.querySelector('.share-button');
    if (mainShareButton) {
        mainShareButton.addEventListener('click', () => sharePost('native'));
    }
}

function sharePost(platform = 'native') {
    const postTitle = document.title;
    const postUrl = window.location.href;
    const postDescription = document.querySelector('meta[name="description"]')?.content || '';
    
    switch (platform) {
        case 'native':
            if (navigator.share) {
                navigator.share({
                    title: postTitle,
                    text: postDescription,
                    url: postUrl
                }).then(() => {
                    showNotification('Thanks for sharing!');
                }).catch(err => {
                    console.error('Share failed:', err);
                    sharePost('copy');
                });
            } else {
                sharePost('copy');
            }
            break;
            
        case 'facebook':
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(postUrl)}`, '_blank', 'width=600,height=400');
            break;
            
        case 'twitter':
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(postUrl)}&text=${encodeURIComponent(postTitle)}`, '_blank', 'width=600,height=400');
            break;
            
        case 'linkedin':
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(postUrl)}`, '_blank', 'width=600,height=400');
            break;
            
        case 'copy':
        default:
            copyToClipboard(postUrl);
            break;
    }
}

async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showNotification('Link copied to clipboard!');
    } catch (err) {
        console.error('Failed to copy:', err);
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    textArea.style.top = '-9999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Link copied to clipboard!');
    } catch (err) {
        console.error('Fallback copy failed:', err);
        showNotification('Copy failed. Please copy manually: ' + text, 'error');
    }
    
    document.body.removeChild(textArea);
}

function initPostNavigation() {
    const prevButton = document.querySelector('.nav-previous a');
    const nextButton = document.querySelector('.nav-next a');
    
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        
        switch (e.key) {
            case 'ArrowLeft':
                if (prevButton && (e.ctrlKey || e.metaKey)) {
                    e.preventDefault();
                    prevButton.click();
                }
                break;
            case 'ArrowRight':
                if (nextButton && (e.ctrlKey || e.metaKey)) {
                    e.preventDefault();
                    nextButton.click();
                }
                break;
        }
    });
}

function initKeyboardNavigation() {
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        
        switch (e.key) {
            case 't':
            case 'T':
                if (!e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                    toggleTOC();
                }
                break;
                
            case 'Escape':
                if (BlogReader.state.tocVisible) {
                    toggleTOC();
                }
                break;
                
            case 'Home':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
                break;
                
            case 'End':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
                }
                break;
        }
    });
}

function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    } else {
        images.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove('lazy');
        });
    }
}

function initScrollToTop() {
    const { scrollToTop } = BlogReader.cache.elements;
    
    if (scrollToTop) {
        scrollToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

function calculateReadingTime() {
    const { postContent } = BlogReader.cache.elements;
    if (!postContent) return;
    
    const text = postContent.textContent || '';
    const wordsPerMinute = 200;
    const words = text.trim().split(/\s+/).length;
    const readingTime = Math.ceil(words / wordsPerMinute);
    
    BlogReader.state.estimatedReadingTime = readingTime;
    
    const readingTimeElement = document.querySelector('.reading-time');
    if (readingTimeElement && !readingTimeElement.textContent.includes('min')) {
        readingTimeElement.textContent = `${readingTime} min read`;
    }
}

function showNotification(message, type = 'success') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.setAttribute('role', 'alert');
    notification.setAttribute('aria-live', 'polite');
    
    document.body.appendChild(notification);
    
    requestAnimationFrame(() => {
        notification.classList.add('show');
    });
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, BlogReader.config.animationDuration);
    }, type === 'error' ? 5000 : 3000);
}

function announceToScreenReader(message) {
    const announcement = document.createElement('div');
    announcement.setAttribute('aria-live', 'polite');
    announcement.setAttribute('aria-atomic', 'true');
    announcement.className = 'sr-only';
    announcement.textContent = message;
    
    document.body.appendChild(announcement);
    
    setTimeout(() => {
        document.body.removeChild(announcement);
    }, 1000);
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

function sanitizeId(text) {
    return text
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
// Add this to your existing JavaScript
function initReadingProgress() {
    const { progressBar } = BlogReader.cache.elements;
    
    if (!progressBar) return;
    
    const updateProgress = throttle(() => {
        const scrollPx = document.documentElement.scrollTop;
        const winHeightPx = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = Math.min((scrollPx / winHeightPx) * 100, 100);
        
        BlogReader.state.readingProgress = scrolled;
        progressBar.style.width = `${scrolled}%`;
        
        // Add color change based on progress
        if (scrolled > 80) {
            progressBar.style.background = 'linear-gradient(90deg, #3182ce, #805ad5)';
        } else if (scrolled > 50) {
            progressBar.style.background = 'linear-gradient(90deg, #3182ce, #4299e1)';
        } else {
            progressBar.style.background = 'linear-gradient(90deg, #3182ce, #63b3ed)';
        }
    }, BlogReader.config.scrollThrottle);
    
    window.addEventListener('scroll', updateProgress);
    updateProgress();
}

window.toggleTOC = toggleTOC;
window.sharePost = sharePost;
window.shareOnFacebook = () => sharePost('facebook');
window.shareOnTwitter = () => sharePost('twitter');
window.shareOnLinkedIn = () => sharePost('linkedin');
window.copyToClipboard = () => sharePost('copy');

if (typeof module !== 'undefined' && module.exports) {
    module.exports = BlogReader;
}