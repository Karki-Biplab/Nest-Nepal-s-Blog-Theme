const BlogReader = {
    state: {
        // Removed tocVisible
        readingProgress: 0,
        currentSection: null,
        isScrolling: false,
        // Removed tocData
        estimatedReadingTime: 0
    },
    
    cache: {
        elements: {},
        // Removed headings and tocLinks
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
    
    // Removed initFloatingTOC();
    initReadingProgress(); // This will now act as the loading bar
    initSmoothScrolling();
    initCodeCopyButtons();
    initShareButtons();
    initPostNavigation();
    initKeyboardNavigation();
    initLazyLoading();
    initScrollToTop();
    
    // Removed generateTOC();
    
    calculateReadingTime();
    
    console.log('✅ Blog Reader initialized successfully');
});

function cacheElements() {
    const elements = BlogReader.cache.elements;
    
    // Removed floatingTOC, tocToggle, tocClose, tocContent, tocList
    elements.readingProgress = document.querySelector('.reading-progress');
    elements.postContent = document.querySelector('.entry-content');
    elements.shareButtons = document.querySelectorAll('.share-button');
    elements.codeBlocks = document.querySelectorAll('pre code');
    elements.postNavigationLinks = document.querySelectorAll('.post-navigation a');
    elements.commentsArea = document.getElementById('comments');
    elements.scrollToTop = document.querySelector('.scroll-to-top');
    elements.allImages = document.querySelectorAll('.entry-content img[data-src]');
}

// Removed function initFloatingTOC() { ... }
// Removed function toggleTOC() { ... }
// Removed function closeTOC() { ... }
// Removed function handleTocClick(event) { ... }
// Removed function generateTOC() { ... }
// Removed function updateActiveTOCLink() { ... }

function initReadingProgress() {
    const { readingProgress, postContent } = BlogReader.cache.elements;
    if (!readingProgress || !postContent) return;

    function updateProgress() {
        if (!postContent) return; // Add a check in case postContent is null

        const contentHeight = postContent.offsetHeight;
        const scrollPosition = window.scrollY;
        const viewportHeight = window.innerHeight;

        let progress = 0;
        if (contentHeight > viewportHeight) {
            // Calculate progress based on scrolling through the content
            const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
            progress = (scrollPosition / scrollableHeight) * 100;
        } else {
            // If content is smaller than viewport, consider it loaded quickly
            progress = (scrollPosition > 0) ? 100 : 0;
        }
        
        progress = Math.min(100, Math.max(0, progress)); // Clamp between 0 and 100
        readingProgress.style.transform = `scaleX(${progress / 100})`;
        BlogReader.state.readingProgress = progress;
    }

    // Update progress on scroll and resize
    let scrollTimeout;
    const throttledUpdateProgress = () => {
        if (!BlogReader.state.isScrolling) {
            requestAnimationFrame(() => {
                updateProgress();
                BlogReader.state.isScrolling = false;
            });
            BlogReader.state.isScrolling = true;
        }
    };

    window.addEventListener('scroll', throttledUpdateProgress);
    window.addEventListener('resize', updateProgress); // Update on resize

    // Initial update
    updateProgress();
}


function initSmoothScrolling() {
    // This function can remain as is if you still want smooth scrolling to anchor links within the content.
    // As there's no TOC, internal anchor links (e.g., from generated headings if you ever add them back)
    // would still benefit from this.
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerOffset = BlogReader.config.headerOffset; // Adjust if you have a fixed header
                const elementPosition = targetElement.getBoundingClientRect().top + window.scrollY;
                const offsetPosition = elementPosition - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Update URL hash without jumping
                if (history.pushState) {
                    history.pushState(null, null, targetId);
                } else {
                    location.hash = targetId;
                }
            }
        });
    });
}

function initCodeCopyButtons() {
    const { codeBlocks } = BlogReader.cache.elements;

    codeBlocks.forEach(codeBlock => {
        const container = document.createElement('div');
        container.classList.add('code-block-container');
        codeBlock.parentNode.insertBefore(container, codeBlock);
        container.appendChild(codeBlock);

        const copyButton = document.createElement('button');
        copyButton.classList.add('copy-code-button');
        copyButton.textContent = 'Copy';
        container.appendChild(copyButton);

        copyButton.addEventListener('click', () => {
            const code = codeBlock.textContent;
            navigator.clipboard.writeText(code).then(() => {
                copyButton.textContent = 'Copied!';
                setTimeout(() => {
                    copyButton.textContent = 'Copy';
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy code: ', err);
            });
        });
    });
}

function initShareButtons() {
    const { shareButtons } = BlogReader.cache.elements;
    shareButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    text: 'Check out this post!',
                    url: window.location.href,
                }).then(() => {
                    console.log('Successful share');
                }).catch((error) => {
                    console.log('Error sharing', error);
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                alert('Web Share API is not supported in your browser. You can manually copy the URL.');
                // Optionally, provide a custom share dialog or copy link to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    const tooltip = button.querySelector('.tooltip');
                    if (tooltip) {
                        tooltip.textContent = 'Link Copied!';
                        setTimeout(() => {
                            tooltip.textContent = 'Share';
                        }, 2000);
                    }
                }).catch(err => {
                    console.error('Failed to copy link: ', err);
                });
            }
        });
    });
}

function initPostNavigation() {
    const { postNavigationLinks } = BlogReader.cache.elements;
    postNavigationLinks.forEach(link => {
        link.addEventListener('focus', function() {
            // Add focus styling if needed
        });
        link.addEventListener('blur', function() {
            // Remove focus styling if needed
        });
    });
}

function initKeyboardNavigation() {
    document.addEventListener('keydown', (event) => {
        // Example: 'Escape' key to close TOC (no longer needed, but as an example)
        // if (event.key === 'Escape' && BlogReader.state.tocVisible) {
        //     closeTOC();
        // }
    });
}

function calculateReadingTime() {
    const { postContent } = BlogReader.cache.elements;
    const readingTimeElement = document.getElementById('reading-time');
    if (!postContent || !readingTimeElement) return;

    const text = postContent.textContent;
    const wordsPerMinute = 200; // Average reading speed
    const words = text.trim().split(/\s+/).length;
    const minutes = Math.ceil(words / wordsPerMinute);

    BlogReader.state.estimatedReadingTime = minutes;
    readingTimeElement.textContent = minutes;
}

function initLazyLoading() {
    const { allImages } = BlogReader.cache.elements;
    if (allImages.length === 0) return;

    if ('IntersectionObserver' in window) {
        let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    let lazyImage = entry.target;
                    lazyImage.src = lazyImage.dataset.src;
                    if (lazyImage.dataset.srcset) {
                        lazyImage.srcset = lazyImage.dataset.srcset;
                    }
                    lazyImage.classList.remove('lazy');
                    lazyImageObserver.unobserve(lazyImage);
                }
            });
        });

        allImages.forEach(function(lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });
    } else {
        // Fallback for older browsers
        let lazyImages = [].slice.call(allImages);
        let lazyLoad = function() {
            if (lazyImages.length === 0) return;
            lazyImages = lazyImages.filter(function(img) {
                const rect = img.getBoundingClientRect();
                const isInViewport = (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) + 300 && // Add some buffer
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                );
                if (isInViewport) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    return false; // Remove from array
                }
                return true; // Keep in array
            });
            if (lazyImages.length === 0) {
                document.removeEventListener('scroll', lazyLoad);
                window.removeEventListener('resize', lazyLoad);
                window.removeEventListener('orientationchange', lazyLoad);
            }
        };
        document.addEventListener('scroll', lazyLoad);
        window.addEventListener('resize', lazyLoad);
        window.addEventListener('orientationchange', lazyLoad);
        lazyLoad(); // Initial check
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

function announceToScreenReader(message) {
    let ariaLiveRegion = document.getElementById('screen-reader-announcement');
    if (!ariaLiveRegion) {
        ariaLiveRegion = document.createElement('div');
        ariaLiveRegion.setAttribute('id', 'screen-reader-announcement');
        ariaLiveRegion.setAttribute('aria-live', 'polite');
        ariaLiveRegion.setAttribute('aria-atomic', 'true');
        ariaLiveRegion.classList.add('screen-reader-only');
        document.body.appendChild(ariaLiveRegion);
    }
    ariaLiveRegion.textContent = message;
}