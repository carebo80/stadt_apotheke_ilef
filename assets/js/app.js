// Theme Switcher (system | light | dark) – funktioniert auf allen Seiten via Delegation
import '../css/app.css';
import '../js/dark-mode.js';
(() => {
  const KEY = 'theme'; // 'system' | 'light' | 'dark'
  const root = document.documentElement;
  const mq   = window.matchMedia('(prefers-color-scheme: dark)');

  function computeDark(mode) {
    if (mode === 'dark')  return true;
    if (mode === 'light') return false;
    // system:
    return mq.matches;
  }

  function syncUI(mode) {
    document.querySelectorAll('[data-theme]').forEach(btn => {
      btn.setAttribute('aria-current', String(btn.dataset.theme === mode));
    });
  }

  function apply(mode) {
    const dark = computeDark(mode);
    root.classList.toggle('dark', dark);
    root.setAttribute('data-theme', dark ? 'dark' : 'light');
    localStorage.setItem(KEY, mode);
    syncUI(mode);
  }

  // Initial
  const initial = localStorage.getItem(KEY) || 'system';
  apply(initial);

// Click-Delegation: nur echte Theme-Buttons!
// (Keine A-Links, keine anderen Elemente)
document.addEventListener('click', (e) => {
  const btn = e.target.closest('button[data-theme]');
  if (!btn) return;            // alles andere normal durchlassen
  e.preventDefault();          // nur beim Theme-Button
  e.stopPropagation();         // sicherheitshalber nicht weiterbubblen
  apply(btn.dataset.theme);
});

  // Reagieren, wenn die System-Einstellung wechselt und wir im "system"-Modus sind
  const onSystemChange = () => {
    if ((localStorage.getItem(KEY) || 'system') === 'system') apply('system');
  };
  mq.addEventListener?.('change', onSystemChange);
  mq.addListener?.(onSystemChange); // älterer Safari

  // Tab-Wechsel/anderes Tab ändert das Theme
  window.addEventListener('storage', (ev) => {
    if (ev.key === KEY) apply(ev.newValue || 'system');
  });
})();
(() => {
  function bindTouchDropdown(navId) {
    const nav = document.getElementById(navId);
    if (!nav) return;

    const isTouch = window.matchMedia('(hover: none), (pointer: coarse)').matches;

    // ARIA vorbereiten
    nav.querySelectorAll('li.menu-item-has-children > a').forEach(a => {
      a.setAttribute('aria-haspopup', 'true');
      a.setAttribute('aria-expanded', 'false');
    });

    function closeAll() {
      nav.querySelectorAll('li.menu-item-has-children[data-open="true"]').forEach(li => {
        li.removeAttribute('data-open');
        const a = li.querySelector(':scope > a');
        if (a) a.setAttribute('aria-expanded', 'false');
      });
    }

    nav.addEventListener('click', (e) => {
      const link = e.target.closest('li.menu-item-has-children > a');
      if (!link) return;
      if (!isTouch) return; // Desktop: Hover/CSS regelt das

      const li = link.parentElement;
      if (li.dataset.open === 'true') return; // schon offen → normal navigieren

      e.preventDefault();           // erster Tap: nur öffnen
      closeAll();
      li.dataset.open = 'true';
      link.setAttribute('aria-expanded', 'true');
    }, true);

    document.addEventListener('click', (e) => {
      if (!nav.contains(e.target)) closeAll();
    });
    window.addEventListener('resize', closeAll);
  }

  // Desktop-Navi + (falls vorhanden) Mobile-Navi anbinden
  ['primary-navigation', 'primary-navigation-mobile'].forEach(bindTouchDropdown);
})();
// app.js
(function (w) {
  if (!w.wp || !wp.customize) return; // nur im Customizer-Preview aktiv

  (function (api) {
    const hex2rgb = (hex) => {
      if (!hex) return '0 0 0';
      hex = hex.replace('#','');
      if (hex.length === 3) hex = hex.split('').map(x => x + x).join('');
      const r = parseInt(hex.slice(0,2),16);
      const g = parseInt(hex.slice(2,4),16);
      const b = parseInt(hex.slice(4,6),16);
      return `${r} ${g} ${b}`;
    };

    const apply = () => {
      const css = `
        :root{
          --c-primary: ${hex2rgb(api('sa_primary')())};
          --c-surface: ${hex2rgb(api('sa_surface')())};
          --c-on-surface: ${hex2rgb(api('sa_on_surface')())};
          --c-border: ${hex2rgb(api('sa_border')())};
        }
        html.dark{
          --c-primary: ${hex2rgb(api('sa_primary_dark')())};
          --c-surface: ${hex2rgb(api('sa_surface_dark')())};
          --c-on-surface: ${hex2rgb(api('sa_on_surface_dark')())};
          --c-border: ${hex2rgb(api('sa_border_dark')())};
        }`;
      let tag = document.getElementById('sa-custom-colors-live');
      if (!tag) {
        tag = document.createElement('style');
        tag.id = 'sa-custom-colors-live';
        document.head.appendChild(tag);
      }
      tag.textContent = css;
    };

    [
      'sa_primary','sa_surface','sa_on_surface','sa_border',
      'sa_primary_dark','sa_surface_dark','sa_on_surface_dark','sa_border_dark'
    ].forEach(key => api(key, setting => setting.bind(apply)));

    apply();
  })(wp.customize);
})(window);
