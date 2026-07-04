import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// ══════════════════════════════════════════════════════════════
// DARK / LIGHT MODE
// ══════════════════════════════════════════════════════════════

const DARK_KEY = 'pm_theme';

/**
 * Apply theme from localStorage or system preference.
 * Called immediately (before DOM paint) to prevent flash.
 */
(function applyThemeEarly() {
  const saved  = localStorage.getItem(DARK_KEY);
  const system = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const isDark = saved !== null ? saved === 'dark' : system;

  if (isDark) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
})();

/** Toggle dark/light — called from toggle button */
window.toggleTheme = function () {
  const isDark = document.documentElement.classList.toggle('dark');
  localStorage.setItem(DARK_KEY, isDark ? 'dark' : 'light');
  updateThemeIcons(isDark);
};

/** Sync icon visibility with current theme */
function updateThemeIcons(isDark) {
  // Desktop icons
  const sunD  = document.getElementById('icon-sun');
  const moonD = document.getElementById('icon-moon');
  // Mobile icons
  const sunM  = document.getElementById('icon-sun-mob');
  const moonM = document.getElementById('icon-moon-mob');

  if (isDark) {
    sunD?.classList.remove('hidden');
    moonD?.classList.add('hidden');
    sunM?.classList.remove('hidden');
    moonM?.classList.add('hidden');
  } else {
    sunD?.classList.add('hidden');
    moonD?.classList.remove('hidden');
    sunM?.classList.add('hidden');
    moonM?.classList.remove('hidden');
  } 

const adminSun   = document.getElementById('admin-icon-sun');
const adminMoon  = document.getElementById('admin-icon-moon');
const adminLabel = document.getElementById('admin-theme-label');

if (isDark) {
  adminSun?.classList.remove('hidden');
  adminMoon?.classList.add('hidden');
  if (adminLabel) adminLabel.textContent = 'Light';
} else {
  adminSun?.classList.add('hidden');
  adminMoon?.classList.remove('hidden');
  if (adminLabel) adminLabel.textContent = 'Dark';
}

}

// ── On DOM ready ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

  // Sync icons with current stored theme
  const isDark = document.documentElement.classList.contains('dark');
  updateThemeIcons(isDark);

  // Listen for system theme changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    // Only auto-switch if user hasn't manually set a preference
    if (!localStorage.getItem(DARK_KEY)) {
      document.documentElement.classList.toggle('dark', e.matches);
      updateThemeIcons(e.matches);
    }
  });

  // ── Animate on scroll ────────────────────────────────────────
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.classList.add('animate-fade-in-up');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.aos').forEach(el => {
    el.style.opacity = '0';
    observer.observe(el);
  });

  // ── Counter animation ────────────────────────────────────────
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        counterObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('[data-counter]').forEach(el => counterObserver.observe(el));

  function animateCounter(el) {
    const target   = parseFloat(el.dataset.counter);
    const suffix   = el.dataset.suffix || '';
    const duration = 2000;
    const steps    = 60;
    const increment = target / steps;
    let current = 0, step = 0;

    const timer = setInterval(() => {
      step++;
      current = Math.min(current + increment, target);
      el.textContent = current.toFixed(0) + suffix;
      if (step >= steps) {
        el.textContent = target.toFixed(0) + suffix;
        clearInterval(timer);
      }
    }, duration / steps);
  }

});