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
  resize(990, 50)
  resize(768, 70)
  resize(600, 80)

  return canvas.height
}

function resize (limit, size) {
  if (window.innerWidth > limit) return canvas.height
  canvas.height = size

  return canvas.height
}

function getDetails (node) {
  const name = node.childNodes[3].innerText.slice(1, -1)
  node.addEventListener('click', () => chart.drawFor(name))
}

responsiveCanvas()
chart.drawFor(5)
Array.from(emojis).forEach(getDetails)
