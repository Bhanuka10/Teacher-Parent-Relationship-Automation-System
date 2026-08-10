import { Chart } from 'chart.js/auto';

document.querySelectorAll('[data-trend-chart]').forEach((canvas) => {
    const labels = JSON.parse(canvas.dataset.labels);
    const values = JSON.parse(canvas.dataset.values);

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data: values,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,.12)',
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#6366f1',
                pointRadius: 4,
            }],
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } },
        },
    });
});

document.querySelectorAll('[data-subject-chart]').forEach((canvas) => {
    const labels = JSON.parse(canvas.dataset.labels);
    const studentValues = JSON.parse(canvas.dataset.studentValues);
    const classAverages = JSON.parse(canvas.dataset.classAverages);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'This student', data: studentValues, backgroundColor: '#6366f1', borderRadius: 4 },
                { label: 'Class average', data: classAverages, backgroundColor: '#c7d2fe', borderRadius: 4 },
            ],
        },
        options: {
            plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
            scales: { y: { beginAtZero: true } },
        },
    });
});
