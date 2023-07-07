class LiveTalkAnalyzer {
    constructor() {
        this.fileInputElem = document.getElementById('file-input');
        this.buttons = document.getElementById('chart-btn-nav').querySelectorAll('.chart-btn');
        this.ocTitleElem = document.getElementById('octitle');
        this.errorMessageElem = document.getElementById('errorMessage');
        this.graphArea = document.getElementById('graph-area');
        this.graphAreaHiddenClassName = 'hidden-graph';
        this.graphCanvas = document.getElementById('openchat-statistics');
        this.listTable = document.getElementById("live-table-body");
        this.openChatChart = null;
        this.parsedLog = null;
        this.nowLoading = false;
    }

    eventListener() {
        window.addEventListener('pageshow', this.#resetFileInput);
        this.fileInputElem.addEventListener('change', async (e) => await this.#fileEventHandler(e));
        this.buttons.forEach(el => el.addEventListener('click', this.#btnEventHandler));
    }

    #resetFileInput = () => {
        this.fileInputElem.value = '';
    }

    async #fileEventHandler(e) {
        const file = e.target.files[0] ?? null;
        if (!file) {
            return;
        }

        try {
            this.nowLoading = true;
            const text = await this.#readTextFileAsUTF8(file);
            this.#fileInputHandler(text);
        } catch (error) {
            this.nowLoading = false;
            this.#showError(error.message);
            console.error(error);
        }

        this.nowLoading = false;
    }

    async #readTextFileAsUTF8(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = function () {
                const text = reader.result;
                resolve(text);
            };

            reader.onerror = function () {
                reject("ファイルの読み込み中にエラーが発生しました。");
            };

            reader.readAsText(file, "utf-8");
        });
    }

    #fileInputHandler(text) {
        this.#parseLog(text);
        
        if (this.openChatChart !== null) {
            this.openChatChart.chart.destroy();
            this.openChatChart === null;
            this.#switchBtnDisabled(this.buttons[1]);
        }
        
        this.#loadLog()
        this.#errorTextContent('');
        this.#titleTextContent(this.parsedLog.openChatName);
        this.#addRowsToTable(31);
        return;
    }

    #parseLog(text) {
        this.parsedLog = new TalkAnalyzer().analyzeText(text);
        console.log(this.parsedLog);
    }

    #loadLog() {
        this.graphArea.classList.remove(this.graphAreaHiddenClassName);
        
        this.openChatChart = new OpenChatChartFactory({
            date: this.parsedLog.date,
            liveTime: this.parsedLog.totalTime,
            liveUser: this.parsedLog.liveUser,
        },
            this.graphCanvas,
        );
    }

    #btnEventHandler = e => {
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
    }

    #switchBtnDisabled(el) {
        this.buttons.forEach(btn => btn.disabled = false);
        el.disabled = true;
    }

    #addRowsToTable(limit) {
        this.listTable.innerHTML = '';

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
            talkUserCell.textContent = this.#sanitizeString(Array.from(new Set(slicedObj.liveUser[i])).join(', '));
            row.appendChild(talkUserCell);

            this.listTable.appendChild(row);
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

    #sanitizeString(input) {
        const sanitized = input
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

        return sanitized;
    }

    #showError(message) {
        this.graphArea.classList.add(this.graphAreaHiddenClassName);
        this.#resetFileInput();
        this.#errorTextContent(message);
        this.#titleTextContent();
    }

    #errorTextContent(message = '') {
        this.errorMessageElem.textContent = message;
    }

    #titleTextContent(title = 'ライブトーク利用時間分析ツール') {
        this.ocTitleElem.textContent = title;
    }
}