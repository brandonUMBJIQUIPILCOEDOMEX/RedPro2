const labels = ['-', '-', '-', '-']

const dataset1 = {
    label: "Real",
    data: [10, 55, 60, 100],
    borderColor: 'rgba(248, 37, 37, 0.8)',
    fill: false,
    tension: 0.1
};

const dataset2 = {
    label: "Adyancente",
    data: [5, 44, 55, 100],
    borderColor: 'rgba(69, 248, 84, 0.8)',
    fill: false,
    tension: 0.1
};

const dataset3 = {
    label: "Semimedia",
    data: [20, 40, 55, 100],
    borderColor: 'rgba(69, 140, 248, 0.8)',
    fill: false,
    tension: 0.1
};

const dataset4 = {
    label: "Humbral",
    data: [18, 40, 20, 100],
    borderColor: 'rgba(245, 40, 145, 0.8)',
    fill: false,
    tension: 0.1
};

const graph = document.querySelector("#grafica");

const data = {
    labels: labels,
    datasets: [dataset1,dataset2,dataset3,dataset4]
};

const config = {
    type: 'line',
    data: data,
};

new Chart(graph, config);