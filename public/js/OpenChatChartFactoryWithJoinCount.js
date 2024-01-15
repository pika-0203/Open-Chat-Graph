export class OpenChatChartFactoryWithJoinCount {
  constructor(data, canvas) {
    Chart.register(ChartDataLabels);
    Chart.register(this.verticalLinePlugin());

    this.data = data;
    this.canvas = canvas;
    this.ctx = canvas.getContext("2d");
    this.limit = 8
    this.animation = true
    this.chart = this.createChart(this.limit);
  }

  update(limit = null) {
    if (limit === null) {
      this.animation = false;
    } else {
      this.chart.destroy();
      this.animation = true;
      this.limit = limit;
    }

    this.chart = this.createChart(this.limit);
  }

  createChart() {
    const noLimit = this.limit === 0;
    const config = this.getChartConfig(
      this.limit,
      noLimit ? null : this.getDate(this.limit),
      noLimit ? null : this.getMember(this.limit),
      noLimit ? null : this.getJoinCount(this.limit),
    );

    return new Chart(this.canvas, config);
  }

  getDate(limit) {
    const dates = this.data.date.slice(limit * -1);
    if (limit === 8) {
      return this.formatDate(dates);
    } else {
      return this.formatDate(dates, true);
    }
  }

  getMember(limit) {
    return this.data.member.slice(limit * -1);
  }

  getJoinCount(limit) {
    return this.data.joinCount.slice(limit * -1);
  }

  formatDate(dates, includeWeekday = false) {
    const weekdays = ['日', '月', '火', '水', '木', '金', '土'];

    return dates.map(date => {
      const dateArray = date.split('/');

      if (dateArray.length === 2) {
        const [month, day] = dateArray;
        const weekdayIndex = new Date(new Date().getFullYear(), month - 1, day).getDay();
        const weekday = weekdays[weekdayIndex];

        if (!includeWeekday) return [`${month}/${day}`, `(${weekday})`];
        return `${month}/${day}(${weekday})`;
      } else {
        const [year, month, day] = dateArray;
        const weekdayIndex = new Date(year, month - 1, day).getDay();
        const weekday = weekdays[weekdayIndex];

        if (!includeWeekday) return [`${year}/${month}/${day}`, `(${weekday})`];
        return `${year}/${month}/${day}(${weekday})`;
      }
    });
  }

  getChartConfig(limit, date = null, mem = null, joinCount = null) {
    date = date ?? this.data.date;
    mem = mem ?? this.data.member;
    joinCount = joinCount ?? this.data.joinCount;

    const isWeekly = limit === 8;
    const windowWidth = window.innerWidth;
    const isMiddleMobile = windowWidth <= 375;
    const isMiniMobile = windowWidth <= 360;
    
    this.isPC = windowWidth >= 512;
    const aspectRatio = () => window.innerWidth <= 375 ? 1.5 / 1 : this.isPC ? 1.8 / 1 : 1.6 / 1;
    document.querySelector('.chart-canvas-section').style.aspectRatio = aspectRatio()

    const ticksFontSizeMobile = isMiniMobile ? 11 : 12
    
    const ticksFontSize =
    (limit === 8)
        ? this.isPC
        ? 13
          : ticksFontSizeMobile
          : (limit === 31)
          ? this.isPC
          ? 11
          : 10.5
          : this.isPC
          ? 13
          : 10.5;
          
          const datalabelFontSize =
          (limit === 8)
          ? this.isPC
          ? 13
          : ticksFontSizeMobile
          : (limit === 31)
          ? 10.5
          : this.isPC
          ? 11
          : 10.5;
          
          const dataFontSize = this.isPC ? 13 : ticksFontSizeMobile;
          const paddingX = isWeekly ? 10 : 0;
          const paddingY = isWeekly ? 0 : 5;
          const displayY = !isWeekly;
          const verticalLine = !isWeekly;
          
          const displayLabel = this.getDisplayLabel(limit);
          const pointRadius = this.getPointRadius(limit);
          const lineTension = 0.4;

          const {
      dataMin,
      dataMax,
      stepSize
    } = this.getMemberLabelRange(mem, this.isPC);

    const verticalLinePluginFlag = limit === 8 ? false : {
      color: 'black',
      lineWidth: '1',
      setLineDash: [6, 6],
    }

    return {
      data: {
        labels: date,
        datasets: [{
          type: 'line',
          label: 'メンバー数',
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
          clip: false,
          datalabels: {
            align: 'end',
            anchor: 'end',
          },
          yAxisID: "rainChart",
        }, {
          type: 'bar',
          label: '新規参加数',
          data: joinCount,
          backgroundColor: "rgb(199,3,117, 0.2)",
          borderColor: "rgb(199,3,117, 0.2)",
          borderWidth: 3,
          pointRadius: pointRadius,
          pointBackgroundColor: "#fff",
          clip: false,
          pointHighlightFill: "#fff",
          datalabels: {
            align: 'start',
            anchor: 'start',
          },
          yAxisID: "temperatureChart",
        }],
      },
      options: {
        animation: this.animation,
        layout: {
          padding: {
            left: -5,
            top: 0
          }
        },
        onResize: chart => {
          chart.options.aspectRatio = aspectRatio();
          chart.resize();
        },
        aspectRatio: aspectRatio(),
        scales: {
          x: {
            ticks: {
              color: this.labelFrontColorCallback,
              padding: paddingX,
              autoSkip: true,
              maxRotation: 90,
              padding: 17,
              font: {
                size: ticksFontSize,
              },
            },
          },
          "rainChart": {
            position: "left",
            min: dataMin,
            max: dataMax,
            display: displayY,
            ticks: {
              stepSize: stepSize,
              padding: paddingY,
              font: {
                size: dataFontSize,
              },
            },
          },
          "temperatureChart": {
            position: "right",
            display: displayY,
            grid: {
              display: false, // 縦軸のグリッドを非表示
            },
            ticks: {
              font: {
                size: dataFontSize,
              },
            },
          },
        },
        plugins: {
          verticalLinePlugin: verticalLinePluginFlag,
          legend: {
            display: true,
          },
          tooltip: {
            titleFont: {
              size: dataFontSize
            },
            bodyFont: {
              size: dataFontSize
            },
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
              size: datalabelFontSize,
              weight: 'bold',
            },
          },
        },
      },
    }
  }

  getMemberLabelRange(mem) {
    const fullSizeWindow = window.innerWidth >= 652;
    const diffMaxConst = fullSizeWindow ? 0.25 : 0.31;
    const diffMinConst = fullSizeWindow ? 0.1 : 0.15;
    const diff8Const = fullSizeWindow ? 0.25 : 0.5;

    let stepSize = 2
    let maxNum = this.incrementIfOdd(Math.max(...mem))
    let minNum = this.decrementIfOdd(Math.min(...mem))

    let dataDiffMax = this.incrementIfOdd(Math.ceil((maxNum - minNum) * diffMaxConst))
    let dataDiffMin = this.decrementIfOdd(Math.ceil((maxNum - minNum) * diffMinConst))
    let dataDiff8 = this.decrementIfOdd(Math.ceil(dataDiffMax * diff8Const))

    if (dataDiffMax === 0) {
      dataDiffMax = 2
      dataDiff8 = 2
    } else if (dataDiff8 === 0) {
      dataDiff8 = 2
    }

    if (dataDiffMin === 0) dataDiffMin = 2

    const trueDiff = maxNum - minNum
    if (trueDiff >= 50 && this.limit !== 8) {
      maxNum = Math.floor(maxNum / 10) * 10;
      minNum = Math.ceil(minNum / 10) * 10;
      dataDiffMax = Math.floor(dataDiffMax / 10) * 10;
      dataDiffMin = Math.ceil(dataDiffMin / 10) * 10;
      if (trueDiff >= 100) stepSize = 10;
    }

    let dataMin = 0;
    if (this.limit === 8) {
      dataMin = minNum - dataDiff8
    } else {
      dataMin = minNum - dataDiffMin
    }

    dataMin = dataMin < 0 ? 0 : dataMin;

    return {
      dataMax: maxNum + dataDiffMax,
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

    return (context) => {
      if (context.dataset.type === 'bar') {
        return (this.isPC && this.limit === 31 && !context.dataset.data.some(item => item >= 100)) ? true : 'auto';
      }

      const index = context.dataIndex;
      const dataLength = context.dataset.data.length;

      // 最初と最後のデータポイントのみにラベルを表示する
      if (index === 0 || index === dataLength - 1) {
        return 'auto';
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

  labelFrontColorCallback(context) {
    let label = context.tick.label;
    if (Array.isArray(label)) {
      label = label[1];
    }

    if (label.includes('土')) {
      return '#44617B';
    } else if (label.includes('日')) {
      return '#9C3848';
    } else {
      return 'black';
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
