'use strict';

import {chartOptions,} from './chartoptions.js';
import {colors,} from './colors.js';


const canvas = document.getElementById('chart-emojis');

/**
 * Resize canvas on breakpoints
 * No on an resize event listener because I don't expect users
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
(function initChart() {
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

    function dataReduce(datasets, emoji, idx) {
        datasets.push({
            label: emoji[0],
            data: emoji[2],
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
            labels: emojis[0][3].reduce(labelReduce, []),
        }));
    }

    function makeChart(data) {
        chartOptions.data = data;
        return new Promise(resolve => resolve(
            new Chart(canvas.getContext('2d'), chartOptions)
        ));
    }

    fetchEmojisTop(5)
        .then(processEmojis)
        .then(makeChart);
})();
