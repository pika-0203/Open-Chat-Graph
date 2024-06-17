export class UrlValidator {
  #urlPattern = /^https?:\/\/[\w/:%#\$&\?\(\)~\.=\+\-]+$/g

  constructor(input, message, submitBtn) {
    this.input = input
    this.message = message
    this.submitBtn = submitBtn
  }

  handle() {
    const matchURL = this.input.value.match(this.#urlPattern)
    if (matchURL) {
      this.#toggleErrorMessage(false)
      this.#toggleBtnDisabled(false)
    } else {
      this.#toggleErrorMessage(!!this.input.value)
      this.#toggleBtnDisabled(true)
    }
  }

  #toggleErrorMessage(bool) {
    if (bool) {
      this.message.style.display = 'block'
    } else {
      this.message.style.display = 'none'
    }
  }

  #toggleBtnDisabled(bool) {
    this.submitBtn.disabled = bool
  }
}
