document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabs = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });

    // Form submission
    const form = document.getElementById('water-quality-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch('predict.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            document.getElementById('prediction-result').innerHTML = `
                <p>The water is predicted to be ${result.prediction ? 'potable' : 'not potable'}.</p>
            `;
        } catch (error) {
            console.error('Error:', error);
        }
    });

    // File upload
    const uploadForm = document.getElementById('file-upload-form');
    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(uploadForm);
        try {
            const response = await fetch('upload.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            document.getElementById('upload-result').innerHTML = `
                <h3>Analysis Results:</h3>
                <p>Total Samples: ${result.totalSamples}</p>
                <p>Potable Samples: ${result.potableSamples}</p>
                <p>Non-Potable Samples: ${result.nonPotableSamples}</p>
                <p>Potability Percentage: ${result.potabilityPercentage.toFixed(2)}%</p>
            `;
        } catch (error) {
            console.error('Error:', error);
        }
    });

    // Data visualization
    fetch('data.php')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('chart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['pH', 'Hardness', 'Solids', 'Chloramines', 'Sulfate', 'Conductivity', 'Organic Carbon', 'Trihalomethanes', 'Turbidity'],
                    datasets: [{
                        label: 'Average Values',
                        data: [
                            data.ph_avg,
                            data.hardness_avg,
                            data.solids_avg,
                            data.chloramines_avg,
                            data.sulfate_avg,
                            data.conductivity_avg,
                            data.organic_carbon_avg,
                            data.trihalomethanes_avg,
                            data.turbidity_avg
                        ],
                        backgroundColor: 'rgba(0, 123, 255, 0.5)'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
});