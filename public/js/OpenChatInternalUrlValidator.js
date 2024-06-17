export class OpenChatUrlValidator {
  #urlPattern =
    /((https:\/\/line.me\/ti\/g2\/[a-zA-Z0-9.\-_@:/~?%&;=+#',()*!]+)(.*)(?=\?)|(https:\/\/line.me\/ti\/g2\/[a-zA-Z0-9.\-_@:/~?%&;=+#',()*!]+))/g

  constructor(input, message, submitBtn) {
    this.input = input
    this.message = message
    this.submitBtn = submitBtn
  }

  handle() {
    if (!this.input.value) {
      this.#toggleErrorMessage(false)
      this.#toggleBtnDisabled(false)
      return
    }

    const matchURL = this.input.value.match(this.#urlPattern)
    if (matchURL) {
      this.#insertTextAndFocusOnStart(matchURL[0])
      this.#toggleErrorMessage(false)
      this.#toggleBtnDisabled(false)
    } else {
      this.#toggleErrorMessage(true)
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

  #insertTextAndFocusOnStart(string) {
    this.input.value = string
    this.input.focus()
    this.input.setSelectionRange(1, 0)
  }
}
