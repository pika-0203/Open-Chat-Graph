class OpenChatChartFactory {
    constructor(data, canvas, gradientHeight) {
        Chart.register(ChartDataLabels);
        this.data = data;
        this.canvas = canvas;
        this.gradient = canvas.getContext("2d").createLinearGradient(0, 0, 0, gradientHeight);
        this.gradient.addColorStop(0, 'rgba(3,199,85,1)');
        this.gradient.addColorStop(1, 'rgba(3,199,85,0)');
        this.chart = this.#create(8);
    }

    update(limit) {
        this.chart.destroy();
        this.chart = this.#create(limit);
    }

    #create(limit) {
        return new Chart(
            this.canvas,
            {
                type: 'line',
                data: {
                    labels: this.data.date.slice(limit * -1),
                    datasets: [{
                        label: 'メンバー',
                        data: this.data.member.slice(limit * -1),
                        pointRadius: 0,
                        fill: 'start',
                        backgroundColor: this.gradient,
                        borderColor: "rgba(3,199,85,1)",
                        borderWidth: 2,
                        pointColor: "#fff",
                        pointStrokeColor: "rgba(3,199,85,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(3,199,85,1)",
                        datalabels: {
                            align: 'end',
                            anchor: 'end',
                        },
                    }],
                },
                options: {
                    aspectRatio: 2 / 1,
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: true,
                                padding: 2,
                            },
                        },
                        y: {
                            grace: '0.2%',
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1,
                                padding: 12,
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            enabled: false,
                        },
                        datalabels: {
                            clip: false,
                            borderRadius: 4,
                            color: 'white',
                            display: 'auto',
                            backgroundColor: 'rgb(3,199,85)',
                            font: {
                                size: 11,
                                weight: 'bold',
                            },
                        },
                    },
                },
            }
        );
    }
}