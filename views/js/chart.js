'use strict';

import {chartOptions} from '{% url "chartoptions_js" %}'
import {colors} from '{% url "colors_js" %}'

/** Helper functions */
const url = base => n => `${base}/${n}`;
const chart = (canvas, options) => new window.Chart(canvas, options);

/** Data functions */
const makeDataPoints = data => {
    const points = [];
    for (let i = 0; i < data[0].length; i += 1) {
        points.push({
            y: data[0][i],
            x: data[1][i]
        });
    }
    return points;
};

const graphData = (emoji, idx) => Object.create({
    label: emoji.name,
    data: makeDataPoints(emoji.data),
    fill: false,
    borderWidth: 1,
    backgroundColor: colors.background(idx),
    borderColor: colors.border(idx),
});

const jsonToGrapData = emojis => Object.create({
    datasets: emojis.map(graphData),
});

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
            .then(r => r.json())
            .then(jsonToGrapData)
            .then(this.draw);
    }
}
