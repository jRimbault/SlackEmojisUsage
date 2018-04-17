'use strict'

function shuffle(array) {
    var currentIndex = array.length,
        temporaryValue, randomIndex

    // While there remain elements to shuffle...
    while (0 !== currentIndex) {

        // Pick a remaining element...
        randomIndex = Math.floor(Math.random() * currentIndex)
        currentIndex -= 1

        // And swap it with the current element.
        temporaryValue = array[currentIndex]
        array[currentIndex] = array[randomIndex]
        array[randomIndex] = temporaryValue
    }

    return array
}

const request = {
    _objectToURI: object => {
        return Object.keys(object).map(key => {
            return encodeURIComponent(key) + '=' + encodeURIComponent(object[key])
        }).join('&')
    },
    serialize: data => {
        return typeof data == 'string' ? data : request._objectToURI(data)
    },
    get: (url, callback) => {
        const xhr = new XMLHttpRequest()
        xhr.open('GET', url)
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) callback(xhr)
        }
        xhr.send()
    },
    post: (url, data, callback) => {
        const xhr = new XMLHttpRequest()
        xhr.open('POST', url)
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) callback(xhr)
        }
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
        xhr.send(request.serialize(data))
    }
}

const ctx = document.getElementById('chart-emojis').getContext('2d')

const chartOptions = {
    type: 'doughnut',
    data: {
        labels: [],
        datasets: [{
            label: '# of Uses',
            data: [],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        legend: {
            display: false
        },
        tooltips: {
            displayColors: false
        },
        title: {
            text: 'Top 5 Custom Emojis',
            display: true
        },
    }
}

// https://github.com/chartjs/Chart.js/issues/815
request.post('/slack/list/emoji', [], xhr => {
    let emojis = JSON.parse(xhr.responseText).slice(0, 5)
    let labels = emojis.reduce((labels, value) => {
        labels.push(value[1])
        return labels
    }, [])

    let data = emojis.reduce((data, value) => {
        data.push(value[0])
        return data
    }, [])

    let backgroundColor = palette('tol-rainbow', data.length).map(hex => {
        return '#' + hex
    })

    chartOptions.data.labels = labels
    chartOptions.data.datasets[0].data = data

    console.log(chartOptions)

    const myChart = new Chart(ctx, chartOptions)
})
