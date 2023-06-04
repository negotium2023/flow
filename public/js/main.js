$(function () {
    let pipelineAndPerformanceCanvas = $("#pipeline-performance");

    Chart.defaults.global.defaultFontFamily = "Roboto";
    Chart.defaults.global.defaultFontSize = 18;
    Chart.defaults.global.defaultFontColor = '#09486F';

    let pipelineAndPerformanceData = {
        label: 'Pipeline and Performance',
        data: [10, 5, 5, 11, 3, 8],
        backgroundColor: 'rgba(70, 200, 234, 1)'
    }
    var ppBarChart = new Chart(pipelineAndPerformanceCanvas, {
        type: 'horizontalBar',
        data:{
            labels: ['Prospects', 'Info Gathering', 'Analisys', 'Close', 'Underwriting', 'Cases Issued'],
            datasets: [pipelineAndPerformanceData]
        },
        options:{
            scales: {
                xAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }],
                yAxes:[{
                    gridLines: {
                        display:false
                    } 
                }]
            },
            responsive: true,
            maintainAspectRatio: false,
        }
    });
})