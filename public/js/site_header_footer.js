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
  ;(function (el) {
    if (!el) return
    const outer = document.getElementById('copy-btn-outer')
    let flag = true

    const copyUrl = async () => {
      if (!flag) return
      flag = false

      const location = new URL(document.location)
      const params = location.searchParams
      const tag = params.get('tag')

      let url = ''
      if (tag) {
        url = location.href
      } else {
        url = `https://${location.hostname}${location.pathname}`
      }

      const text = document.title + '\n' + url

      try {
        await navigator.clipboard.writeText(text)
      } catch {
        console.error('コピーできませんでした')
        return false
      }

      document.getElementById('copy-btn-title').textContent = document.title
      document.getElementById('copy-btn-url').textContent = url

      outer.classList.remove('fade-out-copy')
      outer.classList.add('copy-btn-copied')
      outer.classList.add('fade-in-copy')

      // 1秒後にフェードアウトアニメーションを適用し、非表示（display:none）に変更
      setTimeout(function () {
        outer.classList.remove('fade-in-copy')
        outer.classList.add('fade-out-copy')
        setTimeout(function () {
          outer.classList.remove('copy-btn-copied')
          flag = true
        }, 250) // フェードアウトにかかる時間（ミリ秒）
      }, 1500) // 表示されてからフェードアウトまでの待機時間（ミリ秒）

      return true
    }

    el.addEventListener('click', () => copyUrl())

    document.getElementById('copy-description').addEventListener('click', () => {
      outer.classList.remove('fade-in')
      outer.classList.add('fade-out')
      outer.classList.remove('copy-btn-copied')
    })
  })(document.getElementById('copy-btn'))
})()

const setHeaderShow = (header, hidden, show) => {
  // 現在の位置を保持
  let currentPosition = 0

  window.addEventListener('scroll', () => {
    // スクロール位置を保持
    let scrollPosition = document.documentElement.scrollTop

    // スクロールに合わせて要素をヘッダーの高さ分だけ移動（表示域から隠したり表示したり）
    if (scrollPosition <= 0) {
      header.style.transform = `translate(0, ${show})`
    } else if (currentPosition <= scrollPosition) {
      header.style.transform = 'translate(0,' + hidden + 'px)'
    } else if (currentPosition > scrollPosition) {
      header.style.transform = `translate(0, ${show})`
    }

    currentPosition = document.documentElement.scrollTop
  })
}

;(() => {
  const header = document.querySelector('.site_header_outer')
  setHeaderShow(header, -48, 0)
})()

const setAnchorPosition = () => {
  let count = 0
  const pcAdBarDetector = setInterval(() => {
    const pcAdBar = document.querySelector('.adsbygoogle-noablate[data-anchor-status]')
    if (pcAdBar !== null) {
      clearInterval(pcAdBarDetector)

      pcAdBar.style.top = '0px'
      setHeaderShow(pcAdBar, 0, '47px')

      pcAdBar.style.transition = 'transform 0.3s'

      if (
        document.querySelector('.site_header_outer').style.transform === 'translate(0px, 0px)' ||
        document.documentElement.scrollTop === 0
      ) {
        pcAdBar.style.transform = 'translate(0px, 47px)'
      }

      const height = pcAdBar.clientHeight
      if (height > 110) {
        document.body.style.padding = `${height}px 0px 0px 0px`
      }
    }
    count++
    console.log(count)

    if (count > 300) {
      clearInterval(pcAdBarDetector)
    }
  }, 100)
}

setAnchorPosition()
