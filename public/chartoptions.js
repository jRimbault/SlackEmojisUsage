'use strict';


export const chartOptions = {
    type: 'line',
    options: {
        elements: {
            point: {
                radius: 0,
                hitRadius: 5,
                hoverRadius: 1
            }
        },
        legend: {
            display: true
        },
        tooltips: {
            displayColors: false
        },
        title: {
            text: 'Top 5 Custom Emojis',
            display: false
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false
                }
            }],
            yAxes: [{
                gridLines: {
                    display: false
                }
            }]
        }
    }
};
