(function(){
  const STORAGE_KEY = 'theme';
  const root = document.documentElement;

  function getPreferredTheme(){
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored === 'light' || stored === 'dark') return stored;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function applyTheme(theme){
    if (theme === 'dark') {
      root.classList.add('dark');
      root.setAttribute('data-theme', 'dark');
    } else {
      root.classList.remove('dark');
      root.setAttribute('data-theme', 'light');
    }
    window.dispatchEvent(new CustomEvent('themechange', { detail: { theme } }));
  }

  // Init
  const initial = getPreferredTheme();
  applyTheme(initial);

  // Reagieren auf Systemwechsel (nur wenn User nichts gespeichert hat)
  const media = window.matchMedia('(prefers-color-scheme: dark)');
  media.addEventListener('change', e=>{
    const stored = localStorage.getItem(STORAGE_KEY);
    if (!stored) applyTheme(e.matches ? 'dark' : 'light');
  });

  // Toggle-Funktion global
  window.__toggleTheme = function(){
    const current = root.classList.contains('dark') ? 'dark' : 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next);
  }
})();
