/* Green Future - Interactive Carbon Footprint Calculator */

function initCarbonCalculator() {
  const calcForm = document.getElementById('carbon-calc-form');
  const calcResult = document.getElementById('carbon-calc-result');
  if (!calcForm || !calcResult) return;

  calcForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const kmDriven = parseFloat(document.getElementById('calc-km').value) || 0;
    const electricityKwh = parseFloat(document.getElementById('calc-kwh').value) || 0;
    const flightsYear = parseFloat(document.getElementById('calc-flights').value) || 0;

    // Standard IPCC Emission Factors
    // Car: ~0.192 kg CO2 per km
    // Electricity: ~0.82 kg CO2 per kWh
    // Short Flight: ~250 kg CO2 per flight
    const carEmission = kmDriven * 52 * 0.192;
    const elecEmission = electricityKwh * 12 * 0.82;
    const flightEmission = flightsYear * 250;

    const totalAnnualCO2Kg = Math.round(carEmission + elecEmission + flightEmission);
    const treesNeeded = Math.ceil(totalAnnualCO2Kg / 22); // 1 mature tree absorbs ~22kg CO2/yr

    calcResult.innerHTML = `
      <div class="card border-0 bg-success text-white rounded-4 p-4 shadow mt-3">
        <h5 class="fw-bold mb-3"><i class="fas fa-smog me-2"></i> Your Annual Carbon Footprint Result</h5>
        <div class="row align-items-center text-center">
          <div class="col-md-6 border-end border-light mb-3 mb-md-0">
            <span class="fs-1 fw-extrabold">${(totalAnnualCO2Kg / 1000).toFixed(2)}</span>
            <span class="d-block small">Tons of CO₂ / Year</span>
          </div>
          <div class="col-md-6">
            <span class="fs-1 fw-extrabold text-warning">${treesNeeded}</span>
            <span class="d-block small">Trees Needed to Offset Your Footprint</span>
          </div>
        </div>
        <div class="mt-4 pt-3 border-top border-white-50 text-center">
          <p class="small mb-2">Planting just <strong>${Math.ceil(treesNeeded / 12)} trees per month</strong> completely neutralizes your impact!</p>
          <a href="campaigns.php" class="btn btn-accent px-4 py-2 rounded-pill font-weight-bold">
            <i class="fas fa-leaf me-1"></i> Sponsor Plant Plantation Drives
          </a>
        </div>
      </div>
    `;
  });
}

document.addEventListener('DOMContentLoaded', initCarbonCalculator);
