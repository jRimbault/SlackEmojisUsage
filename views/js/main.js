'use strict';

import {EmojisChart} from '{% url "chart_js" %}'

const $ = {
    el: s => document.querySelector(s),
    els: s => document.querySelectorAll(s),
};
const chart = new EmojisChart(
    $.el('#chart'),
    `${window.location.origin}/slack/data/emoji`
);
const listener = f => n => n.addEventListener('click', f);
const draw = f => e => chart.drawFor(f(e));
const emoji = event => event.target.childNodes[3].innerText.slice(1, -1);

Array.from($.els('.emoji')).forEach(listener(draw(emoji)));

draw(x => x)(5);
