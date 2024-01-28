function validateStringNotEmpty(str) {
  const normalizedStr = str.normalize('NFKC')
  const string = normalizedStr.replace(/[\u200B-\u200D\uFEFF]/g, '')
  return string.trim() !== ''
}

;(function () {
  const siteHeader = document.getElementById('site_header')
  const searchInput = document.getElementById('q')
  const searchBtn = document.getElementById('search_button')
  const backdrop = document.getElementById('backdrop')

  searchBtn.addEventListener('click', () => {
    siteHeader.classList.add('is-search-form-open')
    searchInput.focus()
  })
  backdrop.addEventListener('click', () => {
    siteHeader.classList.remove('is-search-form-open')
    searchInput.value = ''
  })

  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' || e.key === 'Tab') {
      siteHeader.classList.remove('is-search-form-open')
      e.key === 'Escape' && searchBtn.focus()
    }
  })

  document.querySelectorAll('.search-form-inner').forEach(function (el) {
    el.addEventListener('submit', (e) => {
      if (!validateStringNotEmpty(e.target.elements['q'].value)) e.preventDefault()
    })
  })
})()
