'use strict';

import { chartOptions, } from '/chartoptions.js';
import { colors, } from '/colors.js';

export class EmojisChart {
    constructor(canvas) {
        self = this;
        self.canvas = canvas;
    }
    drawFor(n = 5) {
        return self.fetchEmojisTop(n)
            .then(self.processEmojis)
            .then(self.makeChart);
    }
    fetchEmojisTop(n = 5) {
        return fetch(
            `${window.location.origin}/slack/data/emoji/${n}`, {
                method: 'POST',
            }
        )
            .then(r => r.json())
            .catch(error => {
                console.log(error);
            });
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
    dataReduce(datasets, emoji, idx) {
        datasets.push({
            label: emoji.name,
            data: self.makeDataPoints(emoji.data),
            fill: false,
            borderWidth: 1,
            backgroundColor: colors.backgroundColor[idx],
            borderColor: colors.borderColor[idx],
        });
        return datasets;
    }
    processEmojis(emojis) {
        return new Promise(resolve => resolve({
            datasets: emojis.reduce(self.dataReduce, []),
        }));
    }
    makeChart(data) {
        chartOptions.data = data;
        return new Promise(resolve => resolve(
            new Chart(self.canvas.getContext('2d'), chartOptions)));
    }
}
