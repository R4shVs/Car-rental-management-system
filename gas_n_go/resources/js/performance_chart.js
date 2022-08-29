function plot(data, title) {
    let myChart = document.getElementById('myChart').getContext('2d');
    const monthNames = [
        "gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno",
        "luglio", "agosto", "settembre", "ottobre", "novembre", "dicembre"
    ];

    const d = new Date();

    let rentalsChart = new Chart(myChart, {
        type: 'pie',
        data: {
            labels: data.branches,
            datasets: [{
                data: data.performance,
                lineTension: 0.4,
                backgroundColor: [
                    "#006F3D", "#068D45", "#1AA968",
                    "#5CCC99", "#9EDEC1",
                ],
                borderWidth: 1,
            },]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: true,
                    font: {
                        size: 16
                    },
                    text: title + ' ' + monthNames[d.getMonth()]
                },
                scales: {
                    x: {
                        type: 'linear',
                        min: 1,
                    },
                    y: {
                        type: 'linear',
                        beginAtZero: true
                    },
                }
            }
        }
    });
}