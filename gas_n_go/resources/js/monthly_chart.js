function plot(data, title) {
    let myChart = document.getElementById('myChart').getContext('2d');
    const monthNames = [
        "gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno",
        "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre"
    ];

    const d = new Date();

    let rentalsChart = new Chart(myChart, {
        type: 'line',
        data: {
            labels: data.days,
            datasets: [{
                data: data.rentals,
                lineTension: 0.4,
                fillColor: 'rgba(159,159,159,0)',
                borderColor: '#DCDCDC',
                borderWidth: 4,
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 1.5,
                pointRadius: 5,
                pointHoverRadius: 5,
                pointBackgroundColor: '#DCDCDC',
                fill: true,
            },]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    font: {
                        size: 16
                    },
                    text: title + ' ' + monthNames[d.getMonth()]
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    min: 1,
                    max: d.getDate(),

                    ticks: {
                        stepSize: 1
                    }
                },
                y: {
                    type: 'linear',
                    beginAtZero: true,                    
                    ticks: {
                        stepSize: 1
                    }
                },
            }
        }
    });
}