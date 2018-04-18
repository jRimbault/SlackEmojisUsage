'use strict';


const openColors = {
    grey: ['#e9ecef', '#dee2e6', '#ced4da', '#adb5bd', '#868e96', '#495057', '#343a40', '#212529'],
    red: ['#ffc9c9', '#ffa8a8', '#ff8787', '#ff6b6b', '#fa5252', '#f03e3e', '#e03131', '#c92a2a'],
    pink: ['#fcc2d7', '#faa2c1', '#f783ac', '#f06595', '#e64980', '#d6336c', '#c2255c', '#a61e4d'],
    grape: ['#eebefa', '#e599f7', '#da77f2', '#cc5de8', '#be4bdb', '#ae3ec9', '#9c36b5', '#862e9c'],
    violet: ['#d0bfff', '#b197fc', '#9775fa', '#845ef7', '#7950f2', '#7048e8', '#6741d9', '#5f3dc4'],
    indigo: ['#bac8ff', '#91a7ff', '#748ffc', '#5c7cfa', '#4c6ef5', '#4263eb', '#3b5bdb', '#364fc7'],
    blue: ['#a5d8ff', '#74c0fc', '#4dabf7', '#339af0', '#228be6', '#1c7ed6', '#1971c2', '#1864ab'],
    cyan: ['#99e9f2', '#66d9e8', '#3bc9db', '#22b8cf', '#15aabf', '#1098ad', '#0c8599', '#0b7285'],
    teal: ['#96f2d7', '#63e6be', '#38d9a9', '#20c997', '#12b886', '#0ca678', '#099268', '#087f5b'],
    green: ['#b2f2bb', '#8ce99a', '#69db7c', '#51cf66', '#40c057', '#37b24d', '#2f9e44', '#2b8a3e'],
    lime: ['#d8f5a2', '#c0eb75', '#a9e34b', '#94d82d', '#82c91e', '#74b816', '#66a80f', '#5c940d'],
    yellow: ['#ffec99', '#ffe066', '#ffd43b', '#fcc419', '#fab005', '#f59f00', '#f08c00', '#e67700'],
    orange: ['#ffd8a8', '#ffc078', '#ffa94d', '#ff922b', '#fd7e14', '#f76707', '#e8590c', '#d9480f']
};

const colorPicker = () => {
    const colors = Object.keys(openColors);
    const rdmColor = Math.floor(Math.random() * colors.length);
    const rdmHue = Math.floor(Math.random() * openColors[colors[rdmColor]].length);
    return openColors[colors[rdmColor]][rdmHue];
};

const ctx = document.getElementById('chart-emojis').getContext('2d')

const chartOptions = {
    type: 'line',
    data: {
        labels: [],
        datasets: []
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
        const color = colorPicker();
        chartOptions.data.datasets.push({
            borderColor: color,
            backgroundColor: color,
            label: emojis[i][0],
            data: emojis[i][2],
            fill: false,
        });
    }
    for (let i = 0; i < emojis[0][2].length; i += 1) {
        chartOptions.data.labels.push(i + 1);
    }
    const myChart = new Chart(ctx, chartOptions);
});
