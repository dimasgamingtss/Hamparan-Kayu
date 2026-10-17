// Hamburger menu functionality
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });
}

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-menu a').forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
    });
});

// AGGRESSIVE SCROLL RESTORATION - FIXES SCROLL LOCK
function forceRestoreScroll() {
    // Remove zoom-open class from body and html
    document.body.classList.remove('zoom-open');
    document.documentElement.classList.remove('zoom-open');
    
    // Force remove all scroll lock properties from body
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('position');
    document.body.style.removeProperty('width');
    document.body.style.removeProperty('height');
    document.body.style.removeProperty('top');
    document.body.style.removeProperty('left');
    document.body.style.removeProperty('right');
    document.body.style.removeProperty('bottom');
    
    // Force remove all scroll lock properties from html
    document.documentElement.style.removeProperty('overflow');
    document.documentElement.style.removeProperty('position');
    document.documentElement.style.removeProperty('width');
    document.documentElement.style.removeProperty('height');
    document.documentElement.style.removeProperty('top');
    document.documentElement.style.removeProperty('left');
    document.documentElement.style.removeProperty('right');
    document.documentElement.style.removeProperty('bottom');
    
    // Force enable scroll
    document.body.style.overflow = 'auto';
    document.documentElement.style.overflow = 'auto';
    
    // Remove any inline styles that might lock scroll
    document.body.removeAttribute('style');
    document.documentElement.removeAttribute('style');
    
    // Force scroll to current position to refresh scroll
    window.scrollTo(window.scrollX, window.scrollY);
    
    console.log('Scroll forcefully restored!');
}

// Auto-restore scroll when zoom overlay is removed
function watchForZoomRemoval() {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.removedNodes.forEach(function(node) {
                    if (node.classList && node.classList.contains('zoom-overlay')) {
                        console.log('Zoom overlay removed, restoring scroll...');
                        setTimeout(forceRestoreScroll, 100);
                    }
                });
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

// Initialize scroll restoration watcher
document.addEventListener('DOMContentLoaded', function() {
    watchForZoomRemoval();
    
    // Also try to restore scroll every 2 seconds if zoom-open class exists
    setInterval(function() {
        if (document.body.classList.contains('zoom-open') && !document.querySelector('.zoom-overlay')) {
            console.log('Detected orphaned zoom-open class, restoring scroll...');
            forceRestoreScroll();
        }
    }, 2000);
});

// Note: The zoom system is already implemented in products.php
// This file handles the hamburger menu functionality and AGGRESSIVE scroll restoration
