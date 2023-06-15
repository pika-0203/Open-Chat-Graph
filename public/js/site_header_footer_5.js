const siteHeader = document.getElementById('site_header')
const searchInput = document.getElementById('q')
const searchBtn = document.getElementById('search_button');
const backdrop = document.getElementById('backdrop');

(el => {
  if (!el) return;


  el.addEventListener('click', () => {
    siteHeader.classList.toggle('is-user-menu-open')
  })

  backdrop.addEventListener('click', () => {
    siteHeader.classList.remove('is-user-menu-open')
  })
})(document.getElementById('menu_button'));

(el => {
  if (!el) return;

  el.addEventListener('click', e => {
    if (e.target.closest('.modal_inner') && e.target.closest('#login-modal-close-btn') === null) return;
    el.classList.remove('is-login-modal-open');
  })
})(document.getElementById('login-modal'));

searchBtn.addEventListener('click', () => {
  siteHeader.classList.add('is-search-form-open')
  searchInput.focus()
})
backdrop.addEventListener('click', () => {
  siteHeader.classList.remove('is-search-form-open')
});

searchInput.addEventListener("keydown", e => {
  if (e.key === "Escape" || e.key === "Tab") {
    siteHeader.classList.remove('is-search-form-open')
    e.key === "Escape" && searchBtn.focus()
  }
})

function validateStringNotEmpty(str) {
  const normalizedStr = str.normalize('NFKC')
  const string = normalizedStr.replace(/[\u200B-\u200D\uFEFF]/g, '')
  return string.trim() !== ''
}

document.querySelectorAll('.search-form-inner').forEach(el => {
  el.addEventListener('submit', e => {
    if (!validateStringNotEmpty(e.target.elements['q'].value))
      e.preventDefault()
  })
});

(el => {
  if (!el) return;

  const copyUrl = async () => {
    try {
      await navigator.clipboard.writeText(location.href);
      return true;
    } catch {
      console.error('コピーできませんでした')
      return false;
    }
  }

  el.addEventListener('click', (e) => {
    if (!copyUrl()) return;
    el.classList.add('copy-btn-copied')
    document.getElementById('copy-btn-text').textContent = 'コピーしました'
  })
})(document.getElementById('copy-btn'));