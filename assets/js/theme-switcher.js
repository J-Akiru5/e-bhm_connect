/**
 * Theme Switcher for E-BHM Connect
 * 
 * Handles dark/light mode switching with localStorage persistence
 * and system preference detection.
 */

(function() {
    'use strict';

    const STORAGE_KEY = 'ebhm_theme';
    const THEME_ATTRIBUTE = 'data-theme';
    const THEMES = {
        LIGHT: 'light',
        DARK: 'dark',
        SYSTEM: 'system'
    };

    /**
     * Get system color scheme preference
     */
    function getSystemTheme() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return THEMES.DARK;
        }
        return THEMES.LIGHT;
    }

    /**
     * Get saved theme from localStorage
     */
    function getSavedTheme() {
        try {
            return localStorage.getItem(STORAGE_KEY) || THEMES.SYSTEM;
        } catch (e) {
            return THEMES.SYSTEM;
        }
    }

    /**
     * Save theme to localStorage
     */
    function saveTheme(theme) {
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {
            console.warn('Could not save theme preference:', e);
        }
    }

    /**
     * Get the effective theme (resolves 'system' to actual theme)
     */
    function getEffectiveTheme(theme) {
        if (theme === THEMES.SYSTEM) {
            return getSystemTheme();
        }
        return theme;
    }

    /**
     * Apply theme to document
     */
    function applyTheme(theme) {
        const effectiveTheme = getEffectiveTheme(theme);
        document.documentElement.setAttribute(THEME_ATTRIBUTE, effectiveTheme);
        document.body.setAttribute(THEME_ATTRIBUTE, effectiveTheme);
        
        // Update meta theme-color for mobile browsers
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', effectiveTheme === THEMES.DARK ? '#0f172a' : '#f8fafc');
        }

        // Dispatch custom event for other scripts to react
        window.dispatchEvent(new CustomEvent('themechange', {
            detail: { theme: effectiveTheme, preference: theme }
        }));

        // Update any theme toggle buttons
        updateToggleButtons(theme);
    }

    /**
     * Update theme toggle button states
     */
    function updateToggleButtons(currentTheme) {
        // Update dropdown selects
        document.querySelectorAll('[data-theme-select]').forEach(select => {
            select.value = currentTheme;
        });

        // Update radio buttons
        document.querySelectorAll('[data-theme-radio]').forEach(radio => {
            radio.checked = radio.value === currentTheme;
        });

        // Update toggle buttons with active class
        document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
            const btnTheme = btn.getAttribute('data-theme-toggle');
            btn.classList.toggle('active', btnTheme === currentTheme);
        });

        // Update icons in toggle buttons
        const effectiveTheme = getEffectiveTheme(currentTheme);
        document.querySelectorAll('[data-theme-icon]').forEach(icon => {
            const lightIcon = icon.querySelector('.icon-light');
            const darkIcon = icon.querySelector('.icon-dark');
            const systemIcon = icon.querySelector('.icon-system');

            if (lightIcon) lightIcon.style.display = effectiveTheme === THEMES.LIGHT ? 'block' : 'none';
            if (darkIcon) darkIcon.style.display = effectiveTheme === THEMES.DARK ? 'block' : 'none';
            if (systemIcon) systemIcon.style.display = currentTheme === THEMES.SYSTEM ? 'block' : 'none';
        });
    }

    /**
     * Toggle between light and dark (skips system)
     */
    function toggleTheme() {
        const currentTheme = getSavedTheme();
        const effectiveTheme = getEffectiveTheme(currentTheme);
        const newTheme = effectiveTheme === THEMES.DARK ? THEMES.LIGHT : THEMES.DARK;
        setTheme(newTheme);
    }

    /**
     * Cycle through themes: light -> dark -> system -> light
     */
    function cycleTheme() {
        const currentTheme = getSavedTheme();
        let newTheme;
        
        switch (currentTheme) {
            case THEMES.LIGHT:
                newTheme = THEMES.DARK;
                break;
            case THEMES.DARK:
                newTheme = THEMES.SYSTEM;
                break;
            case THEMES.SYSTEM:
            default:
                newTheme = THEMES.LIGHT;
                break;
        }
        
        setTheme(newTheme);
    }

    /**
     * Set theme explicitly
     */
    function setTheme(theme) {
        if (!Object.values(THEMES).includes(theme)) {
            theme = THEMES.SYSTEM;
        }
        saveTheme(theme);
        applyTheme(theme);
    }

    /**
     * Initialize theme on page load
     */
    function initTheme() {
        const savedTheme = getSavedTheme();
        applyTheme(savedTheme);

        // Listen for system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                const currentSetting = getSavedTheme();
                if (currentSetting === THEMES.SYSTEM) {
                    applyTheme(THEMES.SYSTEM);
                }
            });
        }

        // Set up event listeners for theme controls
        setupEventListeners();
    }

    /**
     * Set up event listeners for theme controls
     */
    function setupEventListeners() {
        // Theme toggle buttons (click to toggle between light/dark)
        document.querySelectorAll('[data-theme-switch]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                toggleTheme();
            });
        });

        // Theme cycle buttons (click to cycle through all themes)
        document.querySelectorAll('[data-theme-cycle]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                cycleTheme();
            });
        });

        // Theme select dropdowns
        document.querySelectorAll('[data-theme-select]').forEach(select => {
            select.addEventListener('change', (e) => {
                setTheme(e.target.value);
            });
        });

        // Theme radio buttons
        document.querySelectorAll('[data-theme-radio]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.checked) {
                    setTheme(e.target.value);
                }
            });
        });

        // Specific theme toggle buttons
        document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const theme = btn.getAttribute('data-theme-toggle');
                setTheme(theme);
            });
        });
    }

    // Apply theme immediately (before DOM ready) to prevent flash
    (function() {
        const savedTheme = getSavedTheme();
        const effectiveTheme = getEffectiveTheme(savedTheme);
        document.documentElement.setAttribute(THEME_ATTRIBUTE, effectiveTheme);
    })();

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }

    // Re-run setup when content is dynamically added (for AJAX/SPA)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.addedNodes.length) {
                setupEventListeners();
            }
        });
    });

    // Start observing when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            observer.observe(document.body, { childList: true, subtree: true });
        });
    } else {
        observer.observe(document.body, { childList: true, subtree: true });
    }

    // Expose API globally
    window.ThemeSwitcher = {
        getTheme: getSavedTheme,
        getEffectiveTheme: () => getEffectiveTheme(getSavedTheme()),
        setTheme: setTheme,
        toggleTheme: toggleTheme,
        cycleTheme: cycleTheme,
        THEMES: THEMES
    };

})();
