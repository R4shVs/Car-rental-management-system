function plot(data, title) {
    let myChart = document.getElementById('myChart').getContext('2d');
    const year = new Date().getFullYear();

    let rentalsChart = new Chart(myChart, {
        type: 'bar',
        data: {
            labels: data.months,
            datasets: [{
                data: data.rentals,
                lineTension: 0.4,
                backgroundColor: 'rgba(5, 150, 105, 0.5)',
                borderColor: '#059669',
                borderWidth: 4,
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
                    text: title + ' ' + year
                }
            },
            scales: {
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