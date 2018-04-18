'use strict';

const data = new Promise(resolve => {
    const r = fetch(
        window.location.origin + '/slack/data/emoji', {
            method: 'POST'
        }
    );
    r.then(response => {
        return resolve(response.json());
    });
    r.catch(error => {
        console.log(error);
    });
});

data.then(json => {
    const top = 5;
    const emojis = json.slice(0, top);
    for (let i = 0; i < top; i += 1) {
        chartOptions.data.datasets[i].label = emojis[i][0];
        chartOptions.data.datasets[i].data = emojis[i][2];
        chartOptions.data.datasets[i].fill = false;
    }
    for (let i = 0; i < emojis[0][2].length; i += 1) {
        chartOptions.data.labels.push(emojis[0][3][i].substr(0, 16));
    }
    const myChart = new Chart(ctx, chartOptions);
});


const ctx = document.getElementById('chart-emojis').getContext('2d')

const chartOptions = {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            backgroundColor: ['rgba(255, 99, 132, 0.2)'],
            borderColor: ['rgba(255,99,132,1)'],
            borderWidth: 1
        }, {
            backgroundColor: ['rgba(54, 162, 235, 0.2)', ],
            borderColor: ['rgba(54, 162, 235, 1)'],
            borderWidth: 1
        }, {
            backgroundColor: ['rgba(255, 206, 86, 0.2)'],
            borderColor: ['rgba(255, 206, 86, 1)'],
            borderWidth: 1
        }, {
            backgroundColor: ['rgba(75, 192, 192, 0.2)'],
            borderColor: ['rgba(75, 192, 192, 1)'],
            borderWidth: 1
        }, {
            backgroundColor: ['rgba(153, 102, 255, 0.2)'],
            borderColor: ['rgba(153, 102, 255, 1)'],
            borderWidth: 1
        }]
    },
    options: {
        legend: {
            display: true
        },
        tooltips: {
            displayColors: false
        },
        title: {
            text: 'Top 5 Custom Emojis',
            display: true
        },
    }
};
