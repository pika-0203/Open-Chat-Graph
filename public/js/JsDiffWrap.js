class JsDiffWrap {
  diffType = 'diffChars'

  /**
   * @param {HTMLElement} a
   * @param {HTMLElement} b
   * @param {HTMLElement} result
   */
  constructor(a, b, result) {
    this.a = a
    this.b = b
    this.result = result
  }

  changed() {
    const diff = JsDiff[this.diffType](this.a.textContent, this.b.textContent)
    const fragment = document.createDocumentFragment()

    for (let i = 0; i < diff.length; i++) {
      if (diff[i].added && diff[i + 1] && diff[i + 1].removed) {
        const swap = diff[i]
        diff[i] = diff[i + 1]
        diff[i + 1] = swap
      }

      let node
      if (diff[i].removed) {
        node = document.createElement('del')
        node.appendChild(document.createTextNode(diff[i].value))
      } else if (diff[i].added) {
        node = document.createElement('ins')
        node.appendChild(document.createTextNode(diff[i].value))
      } else {
        node = document.createTextNode(diff[i].value)
      }

      fragment.appendChild(node)
    }

    this.result.textContent = ''
    this.result.appendChild(fragment)
  }
}
