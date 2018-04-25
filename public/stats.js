'use strict';


const canvas = document.getElementById('chart-emojis');

/**
 * Resize canvas on breakpoints
 * Not on an resize event listener because I don't expect users
 * to resize their browser on their phone.
 */
(function responsiveCanvas(cvs) {
    if (window.innerWidth < 600) {
        return cvs.height = 80;
    }
    if (window.innerWidth < 768) {
        return cvs.height = 70;
    }
    if (window.innerWidth < 991) {
        return cvs.height = 50;
    }
    return cvs.height;
})(canvas);

/**
 * Lower scope for all chart related things
 */
function initChart() {
    function fetchEmojisTop(n = 5) {
        return fetch(
            `${window.location.origin}/slack/data/emoji/${n}`, {
                method: 'POST',
            }
        )
            .then(response => response.json())
            .catch(error => {
                console.log(error);
            });
    }

    function makeDataPoints(data) {
        const points = [];
        for (let i = 0; i < data[0].length; i += 1) {
            points.push({
                y: data[0][i],
                x: data[1][i],
            });
        }
        return points;
    }

    function dataReduce(datasets, emoji, idx) {
        datasets.push({
            label: emoji.name,
            data: makeDataPoints(emoji.data),
            fill: false,
            borderWidth: 1,
            backgroundColor: colors.backgroundColor[idx],
            borderColor: colors.borderColor[idx],
        });
        return datasets;
    }

    function labelReduce(labels, date, idx) {
        labels.push('');
        return labels;
    }

    function processEmojis(emojis) {
        return new Promise(resolve => resolve({
            datasets: emojis.reduce(dataReduce, []),
        }));
    }

    function makeChart(data) {
        chartOptions.data = data;
        return new Promise(resolve => resolve(
            new Chart(canvas.getContext('2d'), chartOptions)
        ));
    }

    return (n = 5) => {
        return fetchEmojisTop(n)
            .then(processEmojis)
            .then(makeChart);
    };
};

const fetchEmojisTop = initChart();
fetchEmojisTop(5);
