'use strict';

import {chartOptions} from '{% url "chartoptions_js" %}'
import {colors} from '{% url "colors_js" %}'

/** Helper functions */
const url = base => n => `${base}/${n}`;
const json = r => r.json();
const fetch = options => url => window.fetch(url, options);
const postFetch = fetch({method: 'POST'});
const chart = (canvas, options) => new window.Chart(canvas, options);
const push = array => any => array.push(any);

/** Data functions */
const makeDataPoints = data => {
    const points = [];
    const ppush = push(points);
    for (let i = 0; i < data[0].length; i += 1) {
        ppush({
            y: data[0][i],
            x: data[1][i]
        })
    }
    return points
};
const dataReduce = (emoji, idx) => {
    return {
        label: emoji.name,
        data: makeDataPoints(emoji.data),
        fill: false,
        borderWidth: 1,
        backgroundColor: colors.backgroundColor[idx],
        borderColor: colors.borderColor[idx]
    }
};
const processEmojis = emojis => {
    return {
        datasets: emojis.map(dataReduce)
    }
};

const makeChart = canvas => data => {
    chartOptions.data = data;
    chart(canvas.getContext('2d'), chartOptions)
};

export class EmojisChart {
    constructor(canvas, base) {
        this.draw = makeChart(canvas);
        this.url = url(base)
    }

    drawFor(n = 5) {
        return window.fetch(this.url(n))
            .then(json)
            .then(processEmojis)
            .then(this.draw)
    }
}
