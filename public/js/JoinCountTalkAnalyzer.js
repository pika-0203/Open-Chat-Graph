export class JoinCountTalkAnalyzer {
  constructor() {
    this.OPENCHAT_NAME_PATTERN = /\[LINE\]\s(.*?)のトーク/

    this.DATELINE_PATTERN = /^(\d{4})\/(\d{1,2})\/(\d{1,2})\((月|火|水|木|金|土|日)\)(\r\n|\r|\n|$)/
    this.DATE_PATTERN = /(\d{4})\/(\d{1,2})\/(\d{1,2})/

    this.JOIN_TEXT_PATTERN =
      /^(?:[01]?\d|2[0-3]):[0-5]\d\t.*(がトークに参加しました。|が参加しました。)(\r\n|\r|\n|$)/

    this.currentDate = ''
    this.currentArrayElemNum = 0
    this.result = {
      date: [],
      joinCount: [],
    }
    this.joinCount = 0
  }

  analyzeText(text, latestDate = '07/15', oldestDate = '06/27') {
    this.oldestDate = this.formatDateString(oldestDate)

    const lines = text.split('\n')

    for (const line of lines) {
      this.processLine(line)
    }

    this.addCurrentDateElemToResult()

    if (this.result.date.length === 0) {
      throw new Error('データが空です')
    }

    return this.trimArrayAndAddZeros(
      this.result.date,
      this.result.joinCount,
      latestDate,
      oldestDate
    )
  }

  formatDateString(inputDate) {
    const dateParts = inputDate.split('/')
    if (dateParts.length === 2) {
      // 日付の形式が "6/3" の場合
      const [month, day] = dateParts
      return `${new Date().getFullYear()}/${month}/${day}`
    } else {
      // 日付の形式が "2023/6/2" の場合
      return inputDate
    }
  }

  processLine(line) {
    if (this.isDateLine(line)) {
      this.addCurrentDateElemToResult()
      this.processDateLine(line)
    } else if (!this.currentDate) {
      return
    } else if (this.isJoinLine(line)) {
      this.joinCount++
    }
  }

  isDateLine(line) {
    const match = line.match(this.DATELINE_PATTERN)
    if (match && this.currentArrayElemNum === 0) {
      const [_, year, month, day] = match
      const date = new Date(`${year}/${month}/${day}`)
      this.after
      const afterOldestDate = date >= new Date(this.oldestDate)
      return afterOldestDate
    } else if (match) {
      return true
    }

    return false
  }

  isJoinLine(line) {
    return this.JOIN_TEXT_PATTERN.test(line)
  }

  processDateLine(line) {
    const matches = line.match(this.DATE_PATTERN)
    this.currentDate = this.removeCurrentYearPrefix(matches[0])

    const n = this.currentArrayElemNum
    this.result.date[n] = this.currentDate
    this.result.joinCount[n] = 0
  }

  removeCurrentYearPrefix(dateString) {
    const currentDate = new Date()
    const currentYear = currentDate.getFullYear().toString()

    if (dateString.startsWith(currentYear + '/')) {
      return dateString.slice(currentYear.length + 1)
    }

    return dateString
  }

  addCurrentDateElemToResult() {
    if (!this.currentDate) {
      return
    }

    const n = this.currentArrayElemNum
    this.result.joinCount[n] = this.joinCount

    this.joinCount = 0
    this.currentDate = ''
    this.currentArrayElemNum++
  }

  trimArrayAndAddZeros(labelArray, dataArray, targetDate, oldestDate) {
    targetDate = new Date(this.formatDateString(targetDate))
    const currentOldestDate = new Date(this.formatDateString(oldestDate))
    const currentDate = new Date(this.formatDateString(labelArray[labelArray.length - 1]))

    // 与えられた日付よりも新しい配列の要素を削除
    while (currentDate > targetDate) {
      dataArray.pop()
      currentDate.setDate(currentDate.getDate() - 1)
    }

    // 与えられた日付が配列の最新の日付よりも新しい場合
    while (currentDate < targetDate) {
      currentDate.setDate(currentDate.getDate() + 1)
      dataArray.push(0)
    }

    // 最古日が配列の一番の要素より前の日付なら、その古い日付まで遡って"0"を追加
    const targetDate2 = new Date(this.formatDateString(labelArray[0]))
    const newDataArray = []
    while (currentOldestDate < targetDate2) {
      newDataArray.push(0)
      currentOldestDate.setDate(currentOldestDate.getDate() + 1)
    }

    dataArray = newDataArray.concat(dataArray)

    return dataArray
  }
}
