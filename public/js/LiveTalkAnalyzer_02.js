class LiveTalkAnalyzer {
    constructor() {
        this.openChatChart = null;
        this.parsedLog = null;
        this.fileInputElem = document.getElementById('file-input');
        this.ocTitleElem = document.getElementById('octitle');
        this.buttons = document.getElementById('chart-btn-nav').querySelectorAll('.chart-btn');
        this.nowLoading = false;
    }

    addEventListener() {
        this.fileInputElem.addEventListener('change', e => {
            // 選択されたファイルを取得
            const file = e.target.files[0];

            // FileReaderオブジェクトを作成
            const reader = new FileReader();
            // ファイルの読み込み完了時のイベントリスナーを設定
            reader.addEventListener('load', e => this.#fileInputHandler(e.target.result), { once: true });

            // ファイルをテキストとして読み込む
            reader.readAsText(file, 'utf-8');
        });
    }

    #fileInputHandler(text) {
        if (this.openChatChart === null) {
            document.getElementById('graph-area').classList.remove('hidden-graph');
            this.#btnEventListener();
        } else {
            this.openChatChart.chart.destroy();
            this.#switchBtnDisabled(this.buttons[1]);
        }

        if (!this.#loadLog(text)) {
            document.getElementById('graph-area').classList.add('hidden-graph');
            this.ocTitleElem.textContent = 'ファイルを読み込めませんでした';
            this.openChatChart = null;
            return;
        }

        this.#addRowsToTable(31);
        console.log(this.parsedLog);
    }

    #btnEventListener() {
        this.buttons.forEach(el => el.addEventListener('click', e => {
            if (this.nowLoading) {
                return
            } else if (e.target.id === "btn-week") {
                this.openChatChart.update(8);
                this.#addRowsToTable(8)
            } else if (e.target.id === "btn-month") {
                this.openChatChart.update(31);
                this.#addRowsToTable(31)
            } else if (e.target.id === "btn-all") {
                this.openChatChart.update(0);
                this.#addRowsToTable(0)
            }

            this.#switchBtnDisabled(e.target);
        }));
    }

    #switchBtnDisabled(el) {
        this.buttons.forEach(btn => btn.disabled = false);
        el.disabled = true;
    }

    #loadLog(text) {
        this.nowLoading = true;
        try {
            this.parsedLog = new TalkAnalyzer().analyzeText(text);
            this.openChatChart = new OpenChatChartFactory({
                date: this.parsedLog.date,
                liveTime: this.parsedLog.totalTime,
                liveUser: this.parsedLog.liveUser,
            },
                document.getElementById('openchat-statistics'),
            );
        } catch (error) {
            console.error(error);
            this.nowLoading = false;
            return false;
        }

        this.ocTitleElem.textContent = this.parsedLog.openChatName;
        this.nowLoading = false;
        return true;
    }

    #addRowsToTable(limit) {
        const tb = document.getElementById("live-table-body");
        tb.innerHTML = '';

        const slicedObj = this.#applySlice(this.parsedLog, limit);

        slicedObj.date.forEach((item, i) => {
            const row = document.createElement("tr");

            const dateCell = document.createElement("td");
            const date = OpenChatChartFactory.dateFormatter(item, true);
            const dateColor = OpenChatChartFactory.dateFrontColor(date);
            dateCell.textContent = date;
            dateCell.style.color = dateColor;
            row.appendChild(dateCell);

            const talkCountCell = document.createElement("td");
            talkCountCell.textContent = slicedObj.liveCount[i];
            row.appendChild(talkCountCell);

            const talkTimeCell = document.createElement("td");
            talkTimeCell.textContent = OpenChatChartFactory.formatTimeCallback2(slicedObj.totalTime[i]);
            row.appendChild(talkTimeCell);

            const talkUserCell = document.createElement("td");
            talkUserCell.textContent = Array.from(new Set(slicedObj.liveUser[i])).join(', ');
            row.appendChild(talkUserCell);

            tb.appendChild(row);
        });
    }

    #applySlice(obj, limit) {
        const newObj = {};
        Object.keys(obj).forEach(key => {
            if (Array.isArray(obj[key])) {
                newObj[key] = obj[key].slice(-limit);
            } else {
                newObj[key] = obj[key];
            }
        });

        return newObj;
    }
}