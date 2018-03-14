'use strict'

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
    type: 'pie',
    data: {
        labels: [],
        datasets: [{
            label: '# of Uses',
            data: [],
            backgroundColor: [],
            borderWidth: 1
        }]
    },
    options: {
        legend: {
            display: false
        },
        tooltips: {
            displayColors: false
        }
    }
}

request.post('/slack/list/emoji', [], xhr => {
    let emojis = JSON.parse(xhr.responseText)
    emojis = emojis.slice(0, 20)
    let labels = emojis.reduce((labels, value) => {
        labels.push(value[1])
        return labels
    }, [])

    let data = emojis.reduce((data, value) => {
        data.push(value[0])
        return data
    }, [])

    let backgroundColor = palette('tol-dv', data.length).map(hex => {
        return '#' + hex
    })

    chartOptions.data.labels = labels
    chartOptions.data.datasets[0].data = data
    chartOptions.data.datasets[0].backgroundColor = backgroundColor

    console.log(chartOptions)

    const myChart = new Chart(ctx, chartOptions)
})
