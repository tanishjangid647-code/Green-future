/* Green Future - AI Tree Species Recommender & Live Weather Widget */

// Weather Widget Simulator / API Call
function fetchCityWeather(city = 'Mumbai') {
  const weatherCard = document.getElementById('weather-widget');
  if (!weatherCard) return;

  const weatherData = {
    'Mumbai': { temp: 29, desc: 'Partly Cloudy', humidity: 78, aqi: 62, status: 'Good' },
    'Pune': { temp: 26, desc: 'Light Rain', humidity: 82, aqi: 45, status: 'Excellent' },
    'Bangalore': { temp: 24, desc: 'Pleasant Breeze', humidity: 65, aqi: 38, status: 'Excellent' },
    'Delhi': { temp: 33, desc: 'Hazy Sun', humidity: 55, aqi: 142, status: 'Moderate' },
    'Kolkata': { temp: 30, desc: 'Humid', humidity: 85, aqi: 88, status: 'Moderate' }
  };

  const data = weatherData[city] || weatherData['Mumbai'];
  weatherCard.innerHTML = `
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h6 class="mb-1 text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i> ${city} Weather</h6>
        <h3 class="mb-0 fw-bold text-success">${data.temp}°C</h3>
        <small class="text-muted">${data.desc} • Humidity: ${data.humidity}%</small>
      </div>
      <div class="text-end">
        <span class="badge ${data.aqi < 50 ? 'bg-success' : 'bg-warning text-dark'} px-3 py-2 rounded-pill">
          AQI ${data.aqi} (${data.status})
        </span>
        <div class="mt-2 text-muted small"><i class="fas fa-cloud-sun text-warning me-1"></i> Optimal Planting Condition</div>
      </div>
    </div>
  `;
}

// AI Tree Species Recommendation Logic
function initAIRecommender() {
  const form = document.getElementById('ai-recommender-form');
  const resultDiv = document.getElementById('ai-recommendation-result');
  if (!form || !resultDiv) return;

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const city = document.getElementById('ai-city').value;
    const soil = document.getElementById('ai-soil').value;
    const purpose = document.getElementById('ai-purpose').value;

    resultDiv.innerHTML = `
      <div class="text-center py-4">
        <div class="spinner-border text-success" role="status"></div>
        <p class="mt-2 text-muted">AI Analyzing Soil Composition & Micro-climate for ${city}...</p>
      </div>
    `;

    setTimeout(() => {
      let treeName = 'Azadirachta indica (Neem)';
      let co2 = '25.5 kg/yr';
      let survival = '96%';
      let reason = 'High drought resistance, excellent air purifying capacity, and thrives in all soil types.';

      if (purpose === 'fruit') {
        treeName = 'Mangifera indica (Mango / Alphonso)';
        co2 = '42.0 kg/yr';
        survival = '92%';
        reason = 'Deep taproot system, high fruit yield, and strong canopy shade ideal for urban gardens.';
      } else if (purpose === 'shade') {
        treeName = 'Ficus religiosa (Peepal)';
        co2 = '34.8 kg/yr';
        survival = '98%';
        reason = '24-hour oxygen release cycle, vast shade canopy, and rapid growth rate.';
      } else if (soil === 'sandy') {
        treeName = 'Red Mangrove / Coconut Palm';
        co2 = '30.0 kg/yr';
        survival = '94%';
        reason = 'High salinity tolerance and coastal soil stabilization properties.';
      }

      resultDiv.innerHTML = `
        <div class="alert alert-success border-success bg-white shadow-sm rounded-4 p-4 mt-3">
          <div class="d-flex align-items-center gap-3 mb-3">
            <div class="bg-success text-white p-3 rounded-circle fs-3">
              <i class="fas fa-seedling"></i>
            </div>
            <div>
              <span class="badge bg-success mb-1">Recommended Species</span>
              <h5 class="mb-0 fw-bold text-success">${treeName}</h5>
            </div>
          </div>
          <div class="row text-center mb-3">
            <div class="col-6">
              <div class="p-2 bg-light rounded-3">
                <small class="text-muted d-block">Est. CO₂ Offset</small>
                <strong class="text-dark fs-6">${co2}</strong>
              </div>
            </div>
            <div class="col-6">
              <div class="p-2 bg-light rounded-3">
                <small class="text-muted d-block">Survival Rate</small>
                <strong class="text-success fs-6">${survival}</strong>
              </div>
            </div>
          </div>
          <p class="small text-secondary mb-0"><strong>Why this tree?</strong> ${reason}</p>
        </div>
      `;
    }, 1000);
  });
}

document.addEventListener('DOMContentLoaded', () => {
  fetchCityWeather('Mumbai');
  initAIRecommender();
});
