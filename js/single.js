/**
 * Enhanced JavaScript for Nest Nepal Theme - Single Post Features
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all features
    initFloatingTOC();
    initReadingProgress(); // Assuming this is for the top reading line and is working fine
    initSmoothScrolling();
    initCodeCopyButtons();
    initShareButtons();
    initPostNavigation();
    generateTOC(); // Call the TOC generation
});

/**
 * Global function to toggle TOC visibility (used by HTML button)
 */
function toggleTOC() {
    const floatingTOC = document.getElementById('floating-toc');
    const tocToggle = document.querySelector('.toc-toggle');

    if (!floatingTOC || !tocToggle) return;

    floatingTOC.classList.toggle('toc-visible');
    tocToggle.classList.toggle('active');
    tocToggle.setAttribute('aria-expanded', floatingTOC.classList.contains('toc-visible'));

    // Prevent body scrolling when TOC is open on mobile
    if (floatingTOC.classList.contains('toc-visible') && window.innerWidth <= 768) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}


/**
 * Initialize Floating Table of Contents
 */
function initFloatingTOC() {
    const tocToggle = document.querySelector('.toc-toggle');
    const floatingTOC = document.getElementById('floating-toc');
    const tocClose = document.querySelector('.toc-close');

    if (!tocToggle || !floatingTOC) return;

    // Event listeners
    // The toggleTOC function is now global and called directly from HTML
    if (tocClose) tocClose.addEventListener('click', toggleTOC);

    // Close when clicking outside on mobile (when TOC is modal)
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && floatingTOC.classList.contains('toc-visible')) {
            if (!floatingTOC.contains(e.target) && !tocToggle.contains(e.target)) {
                toggleTOC();
            }
        }
    });

    // Close TOC if window is resized above mobile breakpoint while TOC is open
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 768 && floatingTOC.classList.contains('toc-visible')) {
                toggleTOC(); // Close TOC if it was open on mobile and now is desktop
            }
        }, 200); // Debounce
    });
}

/**
 * Generate Table of Contents dynamically from post content headings.
 */
function generateTOC() {
    const entryContent = document.querySelector('.entry-content');
    const tocList = document.getElementById('toc-list');

    if (!entryContent || !tocList) {
        console.warn("Could not find .entry-content or #toc-list to generate TOC.");
        return;
    }

    tocList.innerHTML = ''; // Clear existing TOC
    const headings = entryContent.querySelectorAll('h2, h3, h4'); // Get all headings

    if (headings.length === 0) {
        // If no headings, hide the TOC element completely
        const floatingTOC = document.getElementById('floating-toc');
        if (floatingTOC) floatingTOC.style.display = 'none';
        const tocToggle = document.querySelector('.toc-toggle');
        if (tocToggle) tocToggle.style.display = 'none';
        return;
    }

    let tocHTML = '';
    let currentH2 = null;
    let currentH3 = null;

    headings.forEach((heading, index) => {
        // Ensure each heading has an ID
        let id = heading.id || `section-${index + 1}`;
        // Sanitize the ID if it was empty, or use existing one.
        // It's good practice to ensure IDs are unique and valid.
        if (!heading.id) {
            id = sanitizeTitle(heading.textContent);
            let uniqueId = id;
            let counter = 1;
            while (document.getElementById(uniqueId)) {
                uniqueId = `${id}-${counter}`;
                counter++;
            }
            heading.id = uniqueId;
            id = uniqueId;
        }

        const level = parseInt(heading.tagName.substring(1)); // H2, H3, H4 -> 2, 3, 4

        if (level === 2) {
            if (currentH2) {
                if (currentH3) {
                    tocHTML += `</ul></li>`; // Close H3 list and H2 list item
                } else {
                    tocHTML += `</li>`; // Close previous H2 list item
                }
            }
            tocHTML += `<li data-level="h2"><a href="#${id}">${heading.textContent}</a>`;
            currentH2 = heading;
            currentH3 = null; // Reset H3 for new H2
        } else if (level === 3) {
            if (!currentH2) { // If an H3 appears before an H2, treat it as an H2 for TOC structure
                 tocHTML += `<li data-level="h2"><a href="#${id}">${heading.textContent}</a>`;
                 currentH2 = heading;
                 currentH3 = null;
            } else if (!currentH3) {
                tocHTML += `<ul class="toc-nested">`; // Start nested list for H3s
                tocHTML += `<li data-level="h3"><a href="#${id}">${heading.textContent}</a></li>`;
                currentH3 = heading;
            } else {
                tocHTML += `<li data-level="h3"><a href="#${id}">${heading.textContent}</a></li>`;
            }
        } else if (level === 4) {
             // For H4, we'll nest under H3 if it exists, otherwise H2.
             // This can be expanded for deeper nesting if needed.
            if (!currentH3 && currentH2) { // If H4 under H2 directly
                if (!currentH3) {
                    tocHTML += `<ul class="toc-nested">`;
                }
                tocHTML += `<li data-level="h4"><a href="#${id}">${heading.textContent}</a></li>`;
            } else if (currentH3) {
                 if (!currentH3.dataset.hasNestedH4) { // Only open nested list once
                    tocHTML += `<ul class="toc-nested">`;
                    currentH3.dataset.hasNestedH4 = true; // Mark that this H3 has nested H4s
                 }
                 tocHTML += `<li data-level="h4"><a href="#${id}">${heading.textContent}</a></li>`;
            } else { // No H2 or H3, treat as H2 for top level
                 tocHTML += `<li data-level="h2"><a href="#${id}">${heading.textContent}</a>`;
                 currentH2 = heading;
                 currentH3 = null;
            }
        }
    });

    // Close any open lists at the end
    if (currentH2) {
        if (currentH3 && currentH3.dataset.hasNestedH4) {
            tocHTML += `</ul></li></ul></li>`;
        } else if (currentH3) {
            tocHTML += `</ul></li>`;
        } else {
            tocHTML += `</li>`;
        }
    }

    tocList.innerHTML = tocHTML;

    // Add scroll listener for active state
    window.addEventListener('scroll', throttle(highlightTOCActive, 100)); // Throttle to prevent performance issues
    highlightTOCActive(); // Call once on load
}

/**
 * Highlight active TOC item based on scroll position.
 */
function highlightTOCActive() {
    const headings = document.querySelectorAll('.entry-content h2, .entry-content h3, .entry-content h4');
    const tocLinks = document.querySelectorAll('#toc-list a');
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;
    const headerOffset = 100; // Adjust this value to account for sticky header/nav

    let activeFound = false;

    for (let i = headings.length - 1; i >= 0; i--) {
        const heading = headings[i];
        if (heading.offsetTop - headerOffset <= scrollY) {
            tocLinks.forEach(link => {
                link.closest('li').classList.remove('active');
            });

            const activeLink = document.querySelector(`#toc-list a[href="#${heading.id}"]`);
            if (activeLink) {
                activeLink.closest('li').classList.add('active');
                // Scroll TOC into view if it's a sidebar and active item is out of view
                if (window.innerWidth > 768) {
                    const tocContainer = document.getElementById('floating-toc');
                    const activeListItem = activeLink.closest('li');
                    if (tocContainer && activeListItem) {
                        const containerRect = tocContainer.getBoundingClientRect();
                        const itemRect = activeListItem.getBoundingClientRect();

                        // If item is above or below visible area of TOC container
                        if (itemRect.top < containerRect.top || itemRect.bottom > containerRect.bottom) {
                            activeListItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }
                }
            }
            activeFound = true;
            break;
        }
    }

    if (!activeFound) {
        // If no heading is active (e.g., at the very top of the page)
        tocLinks.forEach(link => {
            link.closest('li').classList.remove('active');
        });
    }
}

/**
 * Share Post Functionality
 */
function sharePost() {
    const postTitle = document.title;
    const postUrl = window.location.href;

    if (navigator.share) {
        navigator.share({
            title: postTitle,
            url: postUrl
        }).then(() => {
            showNotification('Thanks for sharing!');
        }).catch((error) => {
            console.error('Share failed:', error);
            // Fallback for systems that don't support Web Share API
            copyLinkToClipboard(postUrl);
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        copyLinkToClipboard(postUrl);
    }
}

/**
 * Copy link to clipboard fallback
 */
function copyLinkToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Link copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            // Alert as a last resort
            prompt("Copy this link:", text);
        });
    } else {
        // Deprecated fallback for older browsers
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";  // Avoid scrolling to bottom
        textArea.style.left = "-9999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showNotification('Link copied to clipboard!');
        } catch (err) {
            console.error('Fallback copy failed:', err);
            prompt("Copy this link:", text);
        }
        document.body.removeChild(textArea);
    }
}

/**
 * Show notification (retained from original single.js)
 */
function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    notification.setAttribute('role', 'alert');
    notification.setAttribute('aria-live', 'polite');

    // Add to page
    document.body.appendChild(notification);

    // Show with animation
    setTimeout(() => notification.classList.add('show'), 100);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}

/**
 * Initialize Reading Progress (assuming this is the top line, which is working)
 */
function initReadingProgress() {
    const progressBar = document.querySelector('.reading-progress-bar');
    if (!progressBar) return;

    window.addEventListener('scroll', () => {
        const scrollPx = document.documentElement.scrollTop;
        const winHeightPx = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (scrollPx / winHeightPx) * 100;
        progressBar.style.width = `${scrolled}%`;
    });
}

/**
 * Smooth Scrolling for Anchor Links (existing, but ensure it works with dynamic TOC)
 */
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const hash = this.getAttribute('href');
            if (hash === '#' || hash.length <= 1) return; // Ignore empty or single hash links

            const targetElement = document.querySelector(hash);
            if (targetElement) {
                e.preventDefault(); // Prevent default jump

                // Calculate position considering potential fixed header
                const headerOffset = 80; // Adjust this value to match your sticky header height
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });

                // Update URL hash without jumping
                if (history.pushState) {
                    history.pushState(null, null, hash);
                } else {
                    location.hash = hash;
                }

                // If TOC is open (mobile modal), close it after clicking a link
                const floatingTOC = document.getElementById('floating-toc');
                if (floatingTOC && floatingTOC.classList.contains('toc-visible') && window.innerWidth <= 768) {
                    toggleTOC();
                }
            }
        });
    });
}


/**
 * Code Copy Buttons (retained from original single.js)
 */
function initCodeCopyButtons() {
    document.querySelectorAll('pre').forEach(function(pre) {
        // Check if a copy button already exists
        if (pre.previousElementSibling && pre.previousElementSibling.classList.contains('copy-code-button-wrapper')) {
            return; // Skip if button already added
        }

        const buttonWrapper = document.createElement('div');
        buttonWrapper.className = 'copy-code-button-wrapper';

        const button = document.createElement('button');
        button.className = 'copy-code-button';
        button.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg> <span>Copy Code</span>';

        button.addEventListener('click', function() {
            const code = pre.querySelector('code').textContent;
            navigator.clipboard.writeText(code).then(function() {
                button.querySelector('span').textContent = 'Copied!';
                setTimeout(function() {
                    button.querySelector('span').textContent = 'Copy Code';
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy code: ', err);
            });
        });

        buttonWrapper.appendChild(button);
        pre.parentNode.insertBefore(buttonWrapper, pre);
    });
}


/**
 * Initialize Share Buttons (already existing but enhanced)
 */
function initShareButtons() {
    // Share button click handler moved to sharePost()
}


/**
 * Initialize Post Navigation (retained from original single.js)
 */
function initPostNavigation() {
    // Add any post navigation enhancements here
    // For example, you could add smooth scrolling to the next/previous posts
}


/**
 * Sanitize title for URL (already existing, ensuring it's global if needed)
 */
function sanitizeTitle(title) {
    return title
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '') // Remove non-alphanumeric chars (keep spaces and hyphens)
        .replace(/\s+/g, '-')        // Replace spaces with hyphens
        .replace(/-+/g, '-')         // Replace multiple hyphens with a single hyphen
        .replace(/^-+|-+$/g, '');    // Trim hyphens from start/end
}

/**
 * Throttle function to limit execution rate of a function.
 */
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