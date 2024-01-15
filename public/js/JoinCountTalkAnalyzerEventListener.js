export class JoinCountTalkAnalyzerEventListener {
  constructor(
    latestDate = '07/15',
    oldestDate = '06/27',
    date,
    member,
    OpenChatChartFactoryWithJoinCount,
    JoinCountTalkAnalyzer
  ) {
    this.fileInputElem = document.getElementById('file-input')

    this.errorMessageElem = document.getElementById('errorMessage')

    this.OpenChatChartFactoryWithJoinCount = OpenChatChartFactoryWithJoinCount
    this.JoinCountTalkAnalyzer = JoinCountTalkAnalyzer
    
    this.parsedLog = null
    this.nowLoading = false

    this.latestDate = latestDate
    this.oldestDate = oldestDate

    this.date = date
    this.member = member

    this.fileInputElem.addEventListener('change', async (e) => await this.#fileEventHandler(e))
  }

  async #fileEventHandler(e) {
    const file = e.target.files[0] ?? null
    if (!file) {
      return
    }

    try {
      this.nowLoading = true
      const text = await this.#readTextFileAsUTF8(file)
      this.#fileInputHandler(text)
    } catch (error) {
      this.nowLoading = false
      this.#errorTextContent(error.message)
      console.error(error)
    }

    this.nowLoading = false
  }

  async #readTextFileAsUTF8(file) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader()

      reader.onload = function () {
        const text = reader.result
        resolve(text)
      }

      reader.onerror = function () {
        reject('ファイルの読み込み中にエラーが発生しました。')
      }

      reader.readAsText(file, 'utf-8')
    })
  }

  #fileInputHandler(text) {
    this.#parseLog(text)
    this.#loadLog()
    this.#errorTextContent('')

    return
  }

  #parseLog(text) {
    this.parsedLog = new this.JoinCountTalkAnalyzer().analyzeText(
      text,
      this.latestDate,
      this.oldestDate
    )
    console.log(this.parsedLog)
  }

  #loadLog() {
    if (openChatChartInstance.chart instanceof Chart) {
      openChatChartInstance.chart.destroy()
    }

    openChatChartInstance = new this.OpenChatChartFactoryWithJoinCount(
      {
        date: this.date,
        member: this.member,
        joinCount: this.parsedLog,
      },
      document.getElementById('openchat-statistics')
    )
  }

  #errorTextContent(message = '') {
    this.errorMessageElem.textContent = message
  }
}
