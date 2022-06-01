const maleArray = Object.keys(data).map((val) => data[val]["male"].length);
const femaleArray = Object.keys(data).map((val) => data[val]["female"].length);
const ctx = document.getElementById('chart');
const chart = new Chart('chart', {
    type: 'bar',
    data: {
        labels: Object.keys(data),
        datasets: [{
                label: 'Male',
                data: maleArray,
                backgroundColor: '#FFEE63',
                borderWidth: 1
            },
            {
                label: 'Female',
                data: femaleArray,
                backgroundColor: '#FF5B00',
                borderWidth: 1
            }
        ],
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Poll Results'
            },
        },
        responsive: true,
        scales: {
            x: {
                stacked: true,
            },
            y: {
                stacked: true
            }
        }
    }
});