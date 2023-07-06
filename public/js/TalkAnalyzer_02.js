class TalkAnalyzer {
  constructor() {
    this.OPENCHAT_NAME_PATTERN = /\[LINE\]\s(.*?)のトーク/;

    this.DATELINE_PATTERN = /^(\d{4})\/(\d{1,2})\/(\d{1,2})\((月|火|水|木|金|土|日)\)(\r\n|\r|\n|$)/;
    this.DATE_PATTERN = /(\d{4})\/(\d{1,2})\/(\d{1,2})/;

    this.JOIN_TEXT_PATTERN = /^(?:[01]?\d|2[0-3]):[0-5]\d\t.*(がトークに参加しました。|が参加しました。)(\r\n|\r|\n|$)/;

    this.LIVE_START_USER_PATTERN = /^(?:[01]?\d|2[0-3]):[0-5]\d\t(.*?)\t(\[ライブトークが始まりました。\]|ライブトークが始まりました。)(\r\n|\r|\n|$)/;

    this.LIVE_END_PATTERN = /^(?:[01]?\d|2[0-3]):[0-5]\d\t.*がライブを終了しました。(\r\n|\r|\n|$)/;
    this.LIVE_END_PATTERN2 = /^(?:[01]?\d|2[0-3]):[0-5]\d(\t|\t\t)このライブトークは終了しました。(\r\n|\r|\n|$)/;

    this.currentDate = '';
    this.currentArrayElemNum = 0;
    this.result = {
      openChatName: '',
      date: [],
      joinCount: [],
      liveCount: [],
      liveUser: [],
      elapsedTime: [],
      totalTime: [],
    };
    this.joinCount = 0;
    this.startTime = '';
  }

  analyzeText(text) {
    const lines = text.split('\n');
    this.getOpenChatName(lines[0]);

    for (const line of lines) {
      this.processLine(line);
    }

    this.addCurrentDateElemToResult();

    if (this.result.date.length === 0) {
      throw new Error('データが空です');
    }

    return this.result;
  }

  getOpenChatName(line) {
    this.result.openChatName = line.match(this.OPENCHAT_NAME_PATTERN)[1];
  }

  processLine(line) {
    if (this.isDateLine(line)) {
      this.addCurrentDateElemToResult();
      this.processDateLine(line);
    } else if (!this.currentDate) {
      return;
    } else if (this.isJoinLine(line)) {
      this.joinCount++;
    } else if (!this.startTime && this.isLiveStartLine(line)) {
      this.processLiveStartLine(line);
    } else if (this.startTime && this.isLiveEndLine(line)) {
      this.processLiveEndLine(line);
    }
  }

  isDateLine(line) {
    const match = line.match(this.DATELINE_PATTERN);
    if (match) {
      const [_, year, month, day] = match;
      const date = new Date(`${year}/${month}/${day}`);
      const isAfter20230602 = date >= new Date('2023/6/2');
      return isAfter20230602;
    }
    return false;
  }

  isJoinLine(line) {
    return this.JOIN_TEXT_PATTERN.test(line);
  }

  isLiveStartLine(line) {
    return this.LIVE_START_USER_PATTERN.test(line);
  }

  isLiveEndLine(line) {
    return this.LIVE_END_PATTERN.test(line) || this.LIVE_END_PATTERN2.test(line);
  }

  processDateLine(line) {
    const matches = line.match(this.DATE_PATTERN);
    this.currentDate = this.removeCurrentYearPrefix(matches[0]);

    const n = this.currentArrayElemNum;
    this.result.date[n] = this.currentDate;
    this.result.joinCount[n] = 0;
    this.result.liveCount[n] = 0;
    this.result.liveUser[n] = [];
    this.result.elapsedTime[n] = [];
    this.result.totalTime[n] = 0;
  }

  removeCurrentYearPrefix(dateString) {
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear().toString();

    if (dateString.startsWith(currentYear + "/")) {
      return dateString.slice(currentYear.length + 1);
    }

    return dateString;
  }

  addCurrentDateElemToResult() {
    if (!this.currentDate) {
      return;
    }

    this.addCurrentLiveDataToResult();

    const n = this.currentArrayElemNum;
    this.result.joinCount[n] = this.joinCount;

    this.joinCount = 0;
    this.currentDate = '';
    this.currentArrayElemNum++;
  }

  addCurrentLiveDataToResult() {
    if (this.startTime) {
      const elapsedTime = this.calcElapsedTime(this.startTime, '23:59', 1);
      const n = this.currentArrayElemNum;
      this.result.elapsedTime[n].push(elapsedTime);
      this.startTime = '00:00';
    }

    const n = this.currentArrayElemNum;
    const elapsedTime = this.result.elapsedTime[n];
    if (elapsedTime) {
      const n = this.currentArrayElemNum;
      this.result.liveCount[n] = elapsedTime.length;
      this.result.totalTime[n] = this.calcTotalTime(elapsedTime);
    }
  }

  processLiveStartLine(line) {
    const matches = line.match(/^(\d{1,2}:\d{1,2})/);
    this.startTime = matches[1];
    const liveUserMatches = line.match(this.LIVE_START_USER_PATTERN);
    const n = this.currentArrayElemNum;
    this.result.liveUser[n].push(liveUserMatches[1]);
  }

  processLiveEndLine(line) {
    const matches = line.match(/^(\d{1,2}:\d{1,2})/);
    const endTime = matches[1];
    const elapsedTime = this.calcElapsedTime(this.startTime, endTime);

    const n = this.currentArrayElemNum;
    this.result.elapsedTime[n].push(elapsedTime);
    this.startTime = '';
  }

  calcElapsedTime(startTime, endTime, adjust = 0) {
    const start = new Date(`2020/01/01 ${startTime}`).getTime();
    const end = new Date(`2020/01/01 ${endTime}`).getTime();
    const elapsed = end - start + (adjust * 60000);
    const minutes = Math.floor(elapsed / 60000);
    return minutes;
  }

  calcTotalTime(elapsedTime) {
    const totalMinutes = elapsedTime.reduce((total, time) => total + time, 0);
    return totalMinutes;
  }
}