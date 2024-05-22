document.addEventListener('DOMContentLoaded', () => {
  class ChartManager {
    constructor(containerId, chartData) {
      this.container = document.getElementById(containerId);
      this.chartData = {
        dates: chartData.dates.map(date => new Date(`${date.replace(' ', 'T')}T12:00:00-05:00`)),
        points: chartData.points
      };
      this.initChart();
      this.setupEventListeners();
    }

    initChart() {
      const options = {
        chart: {
          height: "320px",
          type: "area",
          fontFamily: "Rubick, sans-serif",
          dropShadow: {
            enabled: false
          },
          toolbar: {
            show: false
          },
        },
        tooltip: {
          enabled: true,
          x: {
            show: false
          }
        },
        fill: {
          type: "gradient",
          gradient: {
            opacityFrom: 0.55,
            opacityTo: 0,
            shade: "#EFB900",
            gradientToColors: ["#EFB900"]
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          width: 6
        },
        grid: {
          show: false,
          strokeDashArray: 4,
          padding: {
            left: 2,
            right: 2,
            top: 0
          }
        },
        series: [{
          name: "Nuevos registros",
          data: this.chartData.points,
          color: "#EFB900"
        }],
        xaxis: {
          categories: this.chartData.dates.map(date => date.toLocaleDateString('es-PE', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
          })),
          labels: {
            show: false
          },
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
        },
        yaxis: {
          show: false
        },
      };

      this.chart = new ApexCharts(this.container.querySelector('.graphic'), options);
      this.chart.render();
      console.log(this.chartData);
    }

    setupEventListeners() {
      const dropdownButton = this.container.querySelector('.dropdown-button');
      const dropdownMenu = this.container.querySelector('.dropdown-content');

      dropdownButton.addEventListener('click', () => {
        // dropdownMenu.classList.toggle('hidden');
      });

      this.container.querySelectorAll('.timeframe-option').forEach(option => {
        option.addEventListener('click', (e) => {
          e.preventDefault();
          const timeframe = option.getAttribute('data-timeframe');
          const newData = this.filterDataByTimeframe(timeframe);
          this.updateChart(newData);
        });
      });
    }

    filterDataByTimeframe(timeframe) {
      const endDate = new Date();
      let startDate = new Date(endDate.getTime()); // Copia para evitar mutaciones
     
      switch (timeframe) {
        case "yesterday":
          startDate.setDate(startDate.getDate() - 1);
          break;
        case "today":
          console.log(startDate.getDate() );
          startDate.setDate(startDate.getDate()  );
          break;
        case "last7days":
          startDate.setDate(endDate.getDate() - 7);
          break;
        case "last30days":
          startDate.setDate(endDate.getDate() - 30);
          break;
        case "last90days":
          startDate.setDate(endDate.getDate() - 90);
          break;
        default:
          return {
            dates: this.chartData.dates, points: this.chartData.points
          };
      }

      const filteredDates = [];
      const filteredPoints = [];
      console.log(this.chartData.dates);

      
      this.chartData.dates.forEach((date, index) => {
        // console.log(date);
        if (date >= startDate ) {
          filteredDates.push(date.toLocaleString('es-PE', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric'
          }));
          filteredPoints.push(this.chartData.points[index]);
        }
      });

      return {
        dates: filteredDates,
        points: filteredPoints
      };
    }

    updateChart(newData) {
      console.log(newData);
      this.chart.updateOptions({
        series: [{
          data: newData.points
        }],
        xaxis: {
          categories: newData.dates
        }
      });
    }
  }

  // Instancia para cada gráfico
  console.log(chartData);
  new ChartManager('chart-container1', chartData);
  new ChartManager('chart-container2', chartData);
  new ChartManager('chart-container3', chartData);
});
