'use strict';

import { EmojisChart, } from '/chart.js';

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

const chart = new EmojisChart(canvas);
chart.drawFor(5);
