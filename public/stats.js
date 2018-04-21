'use strict';

const ctx = document.getElementById('chart-emojis').getContext('2d');

const chartOptions = {
    type: 'line',
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
        elements: {
            point: {
                radius: 0,
                hitRadius: 5,
                hoverRadius: 1
            }
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

const colors = [{
    backgroundColor: ['rgba(255, 99, 132, 0.2)'],
    borderColor: ['rgba(255,99,132,1)']
}, {
    backgroundColor: ['rgba(54, 162, 235, 0.2)', ],
    borderColor: ['rgba(54, 162, 235, 1)']
}, {
    backgroundColor: ['rgba(255, 206, 86, 0.2)'],
    borderColor: ['rgba(255, 206, 86, 1)']
}, {
    backgroundColor: ['rgba(75, 192, 192, 0.2)'],
    borderColor: ['rgba(75, 192, 192, 1)']
}, {
    backgroundColor: ['rgba(153, 102, 255, 0.2)'],
    borderColor: ['rgba(153, 102, 255, 1)']
}];

const fetchEmojisTop = (n = 5) =>
    fetch(`${window.location.origin}/slack/data/emoji/${n}`, {method: 'POST'})
    .then(response => response.json())
    .catch(error => { console.log(error); });

fetchEmojisTop(5).then(emojis => {
    const data = {
        labels: [],
        datasets: []
    };
    for (let i = 0; i < emojis.length; i += 1) {
        data.datasets.push({
            label: emojis[i][0],
            data: emojis[i][2],
            fill: false,
            borderWidth: 1,
            backgroundColor: colors[i].backgroundColor,
            borderColor: colors[i].borderColor
        })
    }
    // initialize empty labels
    let size = emojis[0][2].length;
    while (size--) data.labels[size] = '';
    chartOptions.data = data;
    const myChart = new Chart(ctx, chartOptions);
});


/**
 * Resize canvas on breakpoints:
 * - 1200px
 * - 991px
 * - 768px
 * No on an resize event listener because I don't expect users
 * to resize their browser on their phone.
 */
(function responsiveCanvas() {
    const canvas = document.getElementById('chart-emojis');
    if (window.innerWidth < 768) {
        canvas.height = 70;
    } else if (window.innerWidth < 991) {
        canvas.height = 50;
    } else if (window.innerWidth > 991) {
        canvas.height = 40;
    }
})();
