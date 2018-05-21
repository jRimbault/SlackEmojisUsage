'use strict'

import { EmojisChart } from '/chart.js'

const canvas = document.querySelector('#chart-emojis')
const emojis = document.querySelectorAll('.list-group-item')
const chart = new EmojisChart(canvas, `${window.location.origin}/slack/data/emoji`)

/**
 * Resize canvas on breakpoints
 * Not on an resize event listener because I don't expect users
 * to resize their browser on their phone.
 */
function responsiveCanvas () {
  if (window.innerWidth < 600) {
    return canvas.height = 80
  }
  if (window.innerWidth < 768) {
    return canvas.height = 70
  }
  if (window.innerWidth < 991) {
    return canvas.height = 50
  }
  return canvas.height
}

function getDetails (node) {
  const name = node.childNodes[3].innerText.slice(1, -1)
  node.addEventListener('click', () => chart.drawFor(name))
}

responsiveCanvas()
chart.drawFor(5)
Array.from(emojis).forEach(getDetails)
