'use strict';

import { chartOptions, } from '/chartoptions.js';
import { colors, } from '/colors.js';

let self;

export class EmojisChart {
    constructor(canvas, url) {
        self = this;
        self.canvas = canvas;
        self.url = url;
    }
    drawFor(n = 5) {
        return self.fetchEmojisTop(n)
            .then(self.processEmojis)
            .then(self.makeChart);
    }
    fetchEmojisTop(n = 5) {
        return fetch(`${self.url}/${n}`, {method: 'POST',})
            .then(r => r.json())
            .catch(console.log);
    }
    makeDataPoints(data) {
        const points = [];
        for (let i = 0; i < data[0].length; i += 1) {
            points.push({
                y: data[0][i],
                x: data[1][i],
            });
        }
        return points;
    }
    dataReduce(emoji, idx) {
        return {
            label: emoji.name,
            data: self.makeDataPoints(emoji.data),
            fill: false,
            borderWidth: 1,
            backgroundColor: colors.backgroundColor[idx],
            borderColor: colors.borderColor[idx],
        };
    }
    processEmojis(emojis) {
        return new Promise(resolve => resolve({
            datasets: emojis.map(self.dataReduce),
        }));
    }
    makeChart(data) {
        chartOptions.data = data;
        return new Promise(resolve => resolve(
            new Chart(self.canvas.getContext('2d'), chartOptions))
        );
    }
}
