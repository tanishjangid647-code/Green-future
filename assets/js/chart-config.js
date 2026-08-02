/* Green Future - Dashboard Chart.js Configurator */

function initAdminCharts() {
  // Monthly Plantation Line Chart
  const monthlyCanvas = document.getElementById('monthlyPlantationChart');
  if (monthlyCanvas) {
    new Chart(monthlyCanvas, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
        datasets: [{
          label: 'Trees Planted',
          data: [120, 240, 450, 680, 920, 1400, 1850, 2400],
          borderColor: '#2E7D32',
          backgroundColor: 'rgba(46, 125, 50, 0.15)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: true } }
      }
    });
  }

  // Tree Species Distribution Doughnut Chart
  const speciesCanvas = document.getElementById('speciesDistributionChart');
  if (speciesCanvas) {
    new Chart(speciesCanvas, {
      type: 'doughnut',
      data: {
        labels: ['Neem', 'Peepal', 'Mango', 'Gulmohar', 'Jamun', 'Banyan'],
        datasets: [{
          data: [35, 25, 15, 12, 8, 5],
          backgroundColor: ['#2E7D32', '#4CAF50', '#81C784', '#FFC107', '#0284C7', '#6366F1']
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
      }
    });
  }

  // City-wise Plantation Bar Chart
  const cityCanvas = document.getElementById('cityPlantationChart');
  if (cityCanvas) {
    new Chart(cityCanvas, {
      type: 'bar',
      data: {
        labels: ['Mumbai', 'Pune', 'Bangalore', 'Kolkata', 'Delhi', 'Hyderabad'],
        datasets: [{
          label: 'Planted Saplings',
          data: [850, 620, 480, 310, 290, 210],
          backgroundColor: '#4CAF50',
          borderRadius: 8
        }]
      },
      options: { responsive: true }
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (typeof Chart !== 'undefined') {
    initAdminCharts();
  }
});
