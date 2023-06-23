class OpenChatChartFactory {
  constructor(data, canvas) {
    Chart.register(ChartDataLabels);
    Chart.register(this.verticalLinePlugin());

    this.data = data;
    this.canvas = canvas;
    this.ctx = canvas.getContext("2d");
    this.limit = 8
    this.animation = true
    this.chart = this.createChart(this.limit);
    this.visibilitychange();
  }

  visibilitychange() {
    document.addEventListener("visibilitychange", () => {
      if (document.visibilityState === "visible") {
        this.ctx.clearRect(0, 0, this.ctx.canvas.clientWidth, this.ctx.canvas.clientHeight);
        this.update();
      }
      if (document.visibilityState === 'hidden') {
        this.chart.destroy();
      }
    });
  }

  update(limit = null) {
    if (limit === null) {
      this.animation = false;
    } else {
      this.chart.destroy();
      this.animation = true;
      this.limit = limit;
    }
    console.log('update');
    this.chart = this.createChart(this.limit);
  }

  createChart() {
    const noLimit = this.limit === 0;
    const config = this.getChartConfig(
      this.limit,
      noLimit ? null : this.getDate(this.limit),
      noLimit ? null : this.getMember(this.limit)
    );

    return new Chart(this.canvas, config);
  }

  getDate(limit) {
    const dates = this.data.date.slice(limit * -1);
    if (limit === 8) {
      return this.formatDate(dates, 1);
    } else {
      return this.formatDate(dates, 2);
    }
  }

  getMember(limit) {
    return this.data.member.slice(limit * -1);
  }

  formatDate(dates, includeWeekday) {
    const weekdays = ['日', '月', '火', '水', '木', '金', '土'];
    return dates.map(date => {
      const [month, day] = date.split('/');
      const weekdayIndex = new Date(new Date().getFullYear(), month - 1, day).getDay();
      const weekday = weekdays[weekdayIndex];
      if (includeWeekday === 1) {
        return [`${month}/${day}`, `(${weekday})`];
      } else if (includeWeekday === 2) {
        return `${month}/${day}(${weekday})`;
      }
    });
  }

  getChartConfig(limit, date = null, mem = null) {
    date = date ?? this.data.date;
    mem = mem ?? this.data.member;

    const isWeekly = limit === 8;
    const isMobile = window.innerWidth <= 375;
    const isPC = window.innerWidth >= 512;
    const aspectRatio = isMobile ? 1.9 / 1 : 2 / 1;
    const ticksFontSize = (limit === 8 || isPC) ? 12 : 11;
    const paddingX = isWeekly ? 10 : 0;
    const paddingY = isWeekly ? 0 : 10;
    const displayY = !isWeekly;
    const verticalLine = !isWeekly;
    const lineTension = 0.4;
    const displayLabel = this.getDisplayLabel(limit);
    const pointRadius = this.getPointRadius(limit);

    const {
      dataMin,
      dataMax,
      stepSize
    } = this.getMemberLabelRange(mem);

    const verticalLinePluginFlag = limit === 8 ? false : {
      color: 'black',
      lineWidth: '1',
      setLineDash: [6, 6],
    }

    return {
      type: 'line',
      data: {
        labels: date,
        datasets: [{
          label: 'メンバー',
          data: mem,
          pointRadius: pointRadius,
          fill: 'start',
          backgroundColor: "rgba(0,0,0,0)",
          borderColor: "rgba(3,199,85,1)",
          borderWidth: 3,
          pointColor: "#fff",
          pointBackgroundColor: "#fff",
          pointStrokeColor: "rgba(3,199,85,1)",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(3,199,85,1)",
          lineTension: lineTension,
          datalabels: {
            align: 'end',
            anchor: 'end',
          },
        }],
      },
      options: {
        animation: this.animation,
        layout: {
          padding: {
            right: 20,
            top: 0
          }
        },
        aspectRatio: aspectRatio,
        scales: {
          x: {
            ticks: {
              padding: paddingX,
              autoSkip: true,
              maxRotation: 90,
              font: {
                size: ticksFontSize,
              },
            },
          },
          y: {
            min: dataMin,
            max: dataMax,
            display: displayY,
            ticks: {
              stepSize: stepSize,
              padding: paddingY,
              font: {
                size: 12,
              },
            },
          },
        },
        plugins: {
          verticalLinePlugin: verticalLinePluginFlag,
          legend: {
            display: false,
          },
          tooltip: {
            enabled: verticalLine,
            mode: 'nearest',
            intersect: false,
            displayColors: false,
            callbacks: {
              label: function (tooltipItem) {
                return tooltipItem.formattedValue;
              }
            }
          },
          datalabels: {
            clip: false,
            borderRadius: 4,
            display: displayLabel,
            color: 'black',
            backgroundColor: "rgba(0,0,0,0)",
            font: {
              size: 12,
              weight: 'bold',
            },
          },
        },
      },
    }
  }

  getMemberLabelRange(mem) {
    let stepSize = 2
    let maxNum = this.incrementIfOdd(Math.max(...mem))
    let minNum = this.decrementIfOdd(Math.min(...mem))
    let dataDiff = this.decrementIfOdd(Math.ceil((maxNum - minNum) * 0.3))
    
    let dataDiffMin = this.decrementIfOdd(Math.ceil(dataDiff * 0.7))
    if (dataDiffMin === 0) dataDiffMin = 2

    if (dataDiff === 0) {
      dataDiff = this.decrementIfOdd(Math.ceil((maxNum) * 0.3))
      if (dataDiff === 0) dataDiff = 2
      dataDiffMin = dataDiff
    }

    if (maxNum - minNum >= 50) {
      stepSize = 2
      maxNum = Math.floor(maxNum / 10) * 10
      minNum = Math.ceil(minNum / 10) * 10
      dataDiff = Math.ceil(dataDiff / 10) * 10
    }

    let dataMin = 0;
    if (this.limit === 8) {
      dataMin = minNum - dataDiffMin
    } else {
      dataMin = minNum - dataDiff
    }

    dataMin = dataMin < 0 ? 0 : dataMin;

    return {
      dataMax: maxNum + dataDiff,
      dataMin: dataMin,
      stepSize: stepSize
    }
  }

  incrementIfOdd(number) {
    if (number % 2 !== 0) {
      return number + 1;
    }
    return number;
  }

  decrementIfOdd(number) {
    if (number % 2 !== 0) {
      return number - 1;
    }
    return number;
  }

  getDisplayLabel(limit) {
    if (limit === 8) {
      return true;
    }

    return function (context) {
      const index = context.dataIndex;
      const dataLength = context.dataset.data.length;

      // 最初と最後のデータポイントのみにラベルを表示する
      if (index === 0 || index === dataLength - 1) {
        return 'displayLabel';
      } else {
        return false;
      }
    }
  }

  getPointRadius(limit) {
    if (limit === 8) {
      return 3;
    }

    return function (context) {
      const index = context.dataIndex;
      const dataLength = context.dataset.data.length;

      // 最初と最後のデータポイントのみにポイントを表示する
      if (index === 0 || index === dataLength - 1) {
        return 3; // ポイントの半径を設定
      } else {
        return 0; // ポイントを非表示にする
      }
    }
  }

  verticalLinePlugin() {
    return {
      id: 'verticalLinePlugin',
      beforeEvent: function (chart, args, options) {
        const e = args.event;
        const chartArea = chart.chartArea;
        const elements = chart.getElementsAtEventForMode(e, 'nearest', { intersect: false }, true);

        if (
          elements.length > 0 &&
          e.x >= chartArea.left &&
          e.x <= chartArea.right &&
          e.y >= chartArea.top &&
          e.y <= chartArea.bottom
        ) {
          options.x = elements[0].element.x;
        } else {
          options.x = NaN;
        }
      },
      afterDraw: function (chart, args, options) {
        const ctx = chart.ctx;
        const chartArea = chart.chartArea;
        const x = options.x;

        if (!isNaN(x)) {
          ctx.save();
          ctx.lineWidth = options.lineWidth || 1;
          ctx.strokeStyle = options.color || 'black';
          ctx.setLineDash(options.setLineDash || []);
          ctx.beginPath();
          ctx.moveTo(x, chartArea.bottom);
          ctx.lineTo(x, chartArea.top);
          ctx.stroke();
          ctx.restore();
        }
      },
      defaults: {
        x: NaN,
        lineWidth: 1,
        color: 'black',
        setLineDash: []
      }
    }
  }
}
