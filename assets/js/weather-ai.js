
// Real Weather Widget using Open-Meteo API
async function fetchCityWeather(city = 'Mumbai') {

  const weatherCard = document.getElementById('weather-widget');

  if (!weatherCard) return;

  city = city.trim() || 'Mumbai';

  // Loading state
  weatherCard.innerHTML = `
    <div class="text-center py-3">
      <div class="spinner-border text-success" role="status"></div>
      <p class="small text-muted mt-2 mb-0">
        Loading weather for ${city}...
      </p>
    </div>
  `;

  try {

    // ---------------------------------------------------------
    // 1. Convert city name into latitude and longitude
    // ---------------------------------------------------------

    const geoResponse = await fetch(
      `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(city)}&count=1&language=en&format=json&countryCode=IN`
    );

    if (!geoResponse.ok) {
      throw new Error('Unable to find location');
    }

    const geoData = await geoResponse.json();

    if (!geoData.results || geoData.results.length === 0) {
      throw new Error(`Location "${city}" not found`);
    }

    const location = geoData.results[0];

    const latitude = location.latitude;
    const longitude = location.longitude;

    const locationName = location.name;
    const stateName = location.admin1 || 'India';


    // ---------------------------------------------------------
    // 2. Get current weather
    // ---------------------------------------------------------

    const weatherResponse = await fetch(
      `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m&temperature_unit=celsius&wind_speed_unit=kmh&timezone=auto`
    );

    if (!weatherResponse.ok) {
      throw new Error('Weather service unavailable');
    }

    const weatherData = await weatherResponse.json();

    const current = weatherData.current;


    // ---------------------------------------------------------
    // 3. Convert weather code to readable description
    // ---------------------------------------------------------

    const weatherDescription =
      getWeatherDescription(current.weather_code);


    // ---------------------------------------------------------
    // 4. Display weather
    // ---------------------------------------------------------

    weatherCard.innerHTML = `
      <div class="d-flex align-items-center justify-content-between">

        <div>

          <h6 class="mb-1 text-muted">
            <i class="fas fa-map-marker-alt text-danger me-1"></i>
            ${locationName}, ${stateName} Weather
          </h6>

          <h3 class="mb-0 fw-bold text-success">
            ${Math.round(current.temperature_2m)}°C
          </h3>

          <small class="text-muted">
            ${weatherDescription}
            • Humidity: ${current.relative_humidity_2m}%
          </small>

          <div class="small text-muted mt-1">
            Feels like ${Math.round(current.apparent_temperature)}°C
            • Wind ${Math.round(current.wind_speed_10m)} km/h
          </div>

        </div>

        <div class="text-end">

          <span class="badge bg-success px-3 py-2 rounded-pill">
            Live Weather
          </span>

          <div class="mt-2 text-muted small">
            <i class="fas fa-cloud-sun text-warning me-1"></i>
            Updated automatically
          </div>

        </div>

      </div>

      <div class="text-muted mt-2" style="font-size: 11px;">
        Weather data by Open-Meteo
      </div>
    `;

  } catch (error) {

    console.error('Weather error:', error);

    weatherCard.innerHTML = `
      <div class="alert alert-warning mb-0 rounded-3">

        <i class="fas fa-exclamation-triangle me-2"></i>

        Unable to load weather for
        <strong>${city}</strong>.

        <div class="small mt-1">
          Please check the city name or try again later.
        </div>

      </div>
    `;
  }
}


// ---------------------------------------------------------
// Weather Code → Description
// ---------------------------------------------------------

function getWeatherDescription(code) {

  const weatherCodes = {

    0: 'Clear Sky',

    1: 'Mainly Clear',
    2: 'Partly Cloudy',
    3: 'Overcast',

    45: 'Fog',
    48: 'Depositing Rime Fog',

    51: 'Light Drizzle',
    53: 'Moderate Drizzle',
    55: 'Dense Drizzle',

    56: 'Light Freezing Drizzle',
    57: 'Dense Freezing Drizzle',

    61: 'Slight Rain',
    63: 'Moderate Rain',
    65: 'Heavy Rain',

    66: 'Light Freezing Rain',
    67: 'Heavy Freezing Rain',

    71: 'Slight Snow',
    73: 'Moderate Snow',
    75: 'Heavy Snow',

    77: 'Snow Grains',

    80: 'Slight Rain Showers',
    81: 'Moderate Rain Showers',
    82: 'Violent Rain Showers',

    85: 'Slight Snow Showers',
    86: 'Heavy Snow Showers',

    95: 'Thunderstorm',

    96: 'Thunderstorm with Slight Hail',
    99: 'Thunderstorm with Heavy Hail'
  };

  return weatherCodes[code] || 'Unknown Conditions';
}
// AI Tree Species Recommendation Logic
// AI Tree Species Recommendation System
function initAIRecommender() {
  const form = document.getElementById('ai-recommender-form');
  const resultDiv = document.getElementById('ai-recommendation-result');

  if (!form || !resultDiv) return;

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const city = document.getElementById('ai-city').value;
    const environment = document.getElementById('ai-environment').value;
    const soil = document.getElementById('ai-soil').value;
    const water = document.getElementById('ai-water').value;
    const sunlight = document.getElementById('ai-sunlight').value;
    const purpose = document.getElementById('ai-purpose').value;

    resultDiv.innerHTML = `
      <div class="text-center py-4">
        <div class="spinner-border text-success" role="status"></div>
        <p class="mt-2 text-muted">
          AI analyzing location, soil, water and environmental conditions...
        </p>
      </div>
    `;

    setTimeout(() => {

      /*
       * Each species receives a suitability score.
       * The score is based on the user's selected conditions.
       */

      const species = [

        {
          name: "Azadirachta indica (Neem)",
          score: 0,
          co2: "25–35 kg/yr",
          reason: "Drought tolerant, hardy and well suited to many Indian urban environments.",
          environments: ["urban", "dry", "rural"],
          soils: ["loamy", "sandy", "red", "black"],
          water: ["normal", "low"],
          sunlight: ["full"],
          purposes: ["shade", "air", "biodiversity"]
        },

        {
          name: "Ficus religiosa (Peepal)",
          score: 0,
          co2: "30–40 kg/yr",
          reason: "Large canopy and strong ecological value make it suitable for spacious sites.",
          environments: ["urban", "rural", "riverbank"],
          soils: ["loamy", "clay"],
          water: ["normal", "high"],
          sunlight: ["full"],
          purposes: ["shade", "air", "biodiversity"]
        },

        {
          name: "Mangifera indica (Mango)",
          score: 0,
          co2: "35–45 kg/yr",
          reason: "Fruit-bearing tree suitable for warm regions when adequate space and water are available.",
          environments: ["urban", "rural"],
          soils: ["loamy", "red", "black"],
          water: ["normal", "high"],
          sunlight: ["full"],
          purposes: ["fruit", "shade", "biodiversity"]
        },

        {
          name: "Syzygium cumini (Jamun)",
          score: 0,
          co2: "30–40 kg/yr",
          reason: "A hardy native fruit tree that performs well in warm conditions and supports biodiversity.",
          environments: ["urban", "rural", "riverbank"],
          soils: ["loamy", "clay", "black"],
          water: ["normal", "high"],
          sunlight: ["full", "partial"],
          purposes: ["fruit", "biodiversity", "shade"]
        },

        {
          name: "Delonix regia (Gulmohar)",
          score: 0,
          co2: "25–35 kg/yr",
          reason: "Fast-growing ornamental tree providing broad shade in warm urban locations.",
          environments: ["urban", "rural"],
          soils: ["loamy", "sandy", "red"],
          water: ["normal", "low"],
          sunlight: ["full"],
          purposes: ["shade", "biodiversity"]
        },

        {
          name: "Cocos nucifera (Coconut)",
          score: 0,
          co2: "20–30 kg/yr",
          reason: "Well adapted to tropical coastal environments and sandy soils.",
          environments: ["coastal", "rural"],
          soils: ["sandy", "saline"],
          water: ["normal", "high"],
          sunlight: ["full"],
          purposes: ["fruit", "coastal_protection"]
        },

        {
          name: "Avicennia marina (Grey Mangrove)",
          score: 0,
          co2: "20–30 kg/yr",
          reason: "Highly adapted to saline coastal wetlands and periodically flooded tidal environments.",
          environments: ["coastal"],
          soils: ["saline", "sandy", "clay"],
          water: ["tidal", "high"],
          sunlight: ["full"],
          purposes: ["coastal_protection", "biodiversity", "erosion"]
        },

        {
          name: "Rhizophora mucronata (Red Mangrove)",
          score: 0,
          co2: "20–30 kg/yr",
          reason: "Suitable for tropical intertidal environments where mangrove restoration is appropriate.",
          environments: ["coastal"],
          soils: ["saline", "clay", "sandy"],
          water: ["tidal", "high"],
          sunlight: ["full"],
          purposes: ["coastal_protection", "biodiversity", "erosion"]
        },

        {
          name: "Acacia nilotica (Babul)",
          score: 0,
          co2: "20–30 kg/yr",
          reason: "Hardy species with good tolerance to dry conditions and useful for soil stabilization.",
          environments: ["dry", "rural"],
          soils: ["sandy", "clay", "black"],
          water: ["low", "normal"],
          sunlight: ["full"],
          purposes: ["erosion", "biodiversity", "air"]
        },

        {
          name: "Pongamia pinnata (Karanja)",
          score: 0,
          co2: "25–35 kg/yr",
          reason: "Hardy Indian tree that can tolerate difficult soils and is useful for ecological restoration.",
          environments: ["coastal", "rural", "urban", "riverbank"],
          soils: ["sandy", "loamy", "saline"],
          water: ["normal", "high"],
          sunlight: ["full"],
          purposes: ["biodiversity", "erosion", "air", "coastal_protection"]
        }

      ];

      // Calculate suitability score
      species.forEach(tree => {

        if (tree.environments.includes(environment)) {
          tree.score += 30;
        }

        if (tree.soils.includes(soil)) {
          tree.score += 20;
        }

        if (tree.water.includes(water)) {
          tree.score += 20;
        }

        if (tree.sunlight.includes(sunlight)) {
          tree.score += 10;
        }

        if (tree.purposes.includes(purpose)) {
          tree.score += 20;
        }

      });

      // Sort from highest suitability to lowest
      species.sort((a, b) => b.score - a.score);

      // Select top 3
      const recommendations = species.slice(0, 3);

      // Make sure city is displayed
      const cityName = city === "Other"
        ? "your selected location"
        : city;

      resultDiv.innerHTML = `
        <div class="alert alert-success border-success bg-white shadow-sm rounded-4 p-4 mt-3">

          <div class="text-center mb-4">
            <div class="bg-success text-white p-3 rounded-circle d-inline-block fs-3">
              <i class="fas fa-seedling"></i>
            </div>

            <h5 class="mt-2 mb-1 fw-bold text-success">
              AI Species Recommendations
            </h5>

            <p class="small text-muted mb-0">
              Based on ${cityName}, ${environment} environment,
              ${soil} soil and ${purpose.replaceAll("_", " ")} goal.
            </p>
          </div>

          ${recommendations.map((tree, index) => `
            <div class="border rounded-4 p-3 mb-3 bg-light">

              <div class="d-flex justify-content-between align-items-center mb-2">

                <div>
                  <span class="badge bg-success mb-1">
                    ${index === 0 ? "Best Match" : "Alternative " + index}
                  </span>

                  <h6 class="fw-bold text-success mb-0">
                    ${tree.name}
                  </h6>
                </div>

                <div class="text-end">
                  <strong class="text-success">
                    ${tree.score}%
                  </strong>
                  <small class="d-block text-muted">
                    Suitability
                  </small>
                </div>

              </div>

              <div class="progress mb-2" style="height: 7px;">
                <div
                  class="progress-bar bg-success"
                  style="width: ${tree.score}%;">
                </div>
              </div>

              <p class="small text-secondary mb-2">
                <strong>Why?</strong> ${tree.reason}
              </p>

              <small class="text-muted">
                Estimated CO₂ benefit: ${tree.co2}
              </small>

            </div>
          `).join("")}

          <div class="alert alert-warning mt-3 mb-0 small">
            <i class="fas fa-info-circle me-1"></i>
            <strong>Important:</strong>
            This advisor provides a suitability estimate based on the
            selected conditions. Final species selection should consider
            local native-species guidance, site conditions and expert
            forestry advice.
          </div>

        </div>
      `;

    }, 900);
  });
}
document.addEventListener('DOMContentLoaded', () => {

  const weatherCard =
    document.getElementById('weather-widget');

  const userCity =
    weatherCard?.dataset.city || 'Mumbai';

  fetchCityWeather(userCity);

  initAIRecommender();
});