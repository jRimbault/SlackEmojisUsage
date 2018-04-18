<canvas id="chart-emojis" width="100" height="100"></canvas>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script>
'use strict';

const data = new Promise(resolve => {
    const r = fetch(
        window.location.origin + '/api.php',
        {method: 'POST'}
    );
    r.then(response => {
        return resolve(response.json());
    });
    r.catch(error => {
        console.log(error);
    });
});

data.then(json => {
    const emojis = json.slice(0, 5);
    for (let i = 0; i < 5; i += 1) {
        chartOptions.data.datasets[i].label = emojis[i][0];
        chartOptions.data.datasets[i].data = emojis[i][2];
        chartOptions.data.datasets[i].fill = false;
    }
    const myChart = new Chart(ctx, chartOptions)
});


const ctx = document.getElementById('chart-emojis').getContext('2d')

const chartOptions = {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: '# of Uses',
            data: [],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)'
            ],
            borderWidth: 1
        },{
            label: '# of Uses',
            data: [],
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)'
            ],
            borderWidth: 1
        },{
            label: '# of Uses',
            data: [],
            backgroundColor: [
                'rgba(255, 206, 86, 0.2)'
            ],
            borderColor: [
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
        },{
            label: '# of Uses',
            data: [],
            backgroundColor: [
                'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
        },{
            label: '# of Uses',
            data: [],
            backgroundColor: [
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        fill: false,
        legend: {
            display: false
        },
        tooltips: {
            displayColors: false
        },
        title: {
            text: 'Top 5 Custom Emojis',
            display: true
        },
    }
}

</script>
