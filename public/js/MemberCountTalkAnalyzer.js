class TalkTextAnalyzer {
  #DATE_LINE_PATTERN = /^(\d{4})\/(\d{1,2})\/(\d{1,2})\((月|火|水|木|金|土|日)\)$/
  #DATE_PATTERN = /(\d{4})\/(\d{1,2})\/(\d{1,2})/
  #JOIN_TEXT_LINE_PATTERN = /^(?:[01]?\d|2[0-3]):[0-5]\d\t.*(がトークに参加しました。|が参加しました。)$/
  #LEAVE_TEXT_LINE_PATTERN = /^(?:[01]?\d|2[0-3]):[0-5]\d\t.*(がトークを退出しました。|が退出しました。)$/

  #currentYear = ''
  #currentDate = ''
  #currentIndex = 0
  #currentJoinCount = 0
  #currentLeaveCount = 0
  #result = { date: [], memberIncrease: [] }

  constructor() {
    this.#currentYear = new Date().getFullYear().toString()
  }

  /**
   * @param {string} text
   * @returns {{ date: number[], memberIncrease: number[]}}
   * @throws if data is empty
   */
  parseText(text) {
    for (const line of text.split(/\r\n|\r|\n/)) {
      this.#processLine(line)
    }

    this.#currentDate && this.#addCurrentValueToResult()

    if (this.#result.date.length === 0) {
      throw new Error('data is empty')
    }

    return this.#result
  }

  #processLine(line) {
    if (this.#DATE_LINE_PATTERN.test(line)) {
      this.#currentDate && this.#addCurrentValueToResult()
      this.#currentDate = this.#removeCurrentYearPrefix(line)
    } else if (this.#currentDate) {
      this.#processLogLine(line)
    }
  }

  #removeCurrentYearPrefix(line) {
    const date = line.match(this.#DATE_PATTERN)[0]
    return date.startsWith(this.#currentYear + '/') ? date.slice(this.#currentYear.length + 1) : date
  }

  #addCurrentValueToResult() {
    this.#result.date[this.#currentIndex] = this.#currentDate
    this.#result.memberIncrease[this.#currentIndex] = this.#currentJoinCount - this.#currentLeaveCount

    this.#currentJoinCount = 0
    this.#currentLeaveCount = 0

    this.#currentIndex++
  }

  #processLogLine(line) {
    if (this.#JOIN_TEXT_LINE_PATTERN.test(line)) {
      this.#currentJoinCount++
    } else if (this.#LEAVE_TEXT_LINE_PATTERN.test(line)) {
      this.#currentLeaveCount++
    }
  }
}

/**
 * @param {string} text
 * @param {int} latestMemberCount
 * @returns {{ date: number[], member: number[]}}
 * @throws if data is empty
 */
function talkTextAnalyzerWrapper(text, latestMemberCount = 0) {
  const result = new TalkTextAnalyzer().parseText(text)
  return result
}

const fs = require('fs')
var text = fs.readFileSync('/var/www/html/public/js/test.txt', 'utf8')

console.log(talkTextAnalyzerWrapper(text))
