/* Green Future - Core JavaScript Controller */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Initialize AOS Scroll Animation
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 800,
      once: true,
      easing: 'ease-in-out'
    });
  }

  // 2. Dark Mode Toggle Logic
  const darkModeToggle = document.getElementById('dark-mode-toggle');
  const storedTheme = localStorage.getItem('theme');
  if (storedTheme === 'dark') {
    document.body.classList.add('dark-mode');
    if (darkModeToggle) darkModeToggle.checked = true;
  }

  if (darkModeToggle) {
    darkModeToggle.addEventListener('change', () => {
      if (darkModeToggle.checked) {
        document.body.classList.add('dark-mode');
        localStorage.setItem('theme', 'dark');
      } else {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('theme', 'light');
      }
    });
  }

  // 3. Multi-language (English / Hindi) Switcher
  const langToggle = document.getElementById('lang-switch');
  const translations = {
    hi: {
      'nav-home': 'मुख्य पृष्ठ',
      'nav-campaigns': 'अभियान',
      'nav-trees': 'वृक्ष ट्रैकिंग',
      'nav-leaderboard': 'लीडरबोर्ड',
      'nav-gallery': 'गैलरी',
      'nav-reports': 'रिपोर्ट',
      'nav-blog': 'ब्लॉग',
      'nav-contact': 'संपर्क करें',
      'hero-title': 'स्मार्ट वृक्षारोपण और पर्यावरण संरक्षण',
      'hero-subtitle': 'आधुनिक डिजिटल तकनीक के साथ पेड़ लगाएं, उनके विकास को ट्रैक करें और CO₂ उत्सर्जन को कम करें।',
      'btn-join': 'अभियान में शामिल हों',
      'btn-explore': 'अधिक जानें',
      'stat-planted': 'पेड़ लगाए गए',
      'stat-co2': 'किग्रा CO₂ बचाया',
      'stat-volunteers': 'सक्रिय स्वयंसेवक',
      'stat-campaigns': 'सफल अभियान'
    },
    en: {
      'nav-home': 'Home',
      'nav-campaigns': 'Campaigns',
      'nav-trees': 'Tree Tracking',
      'nav-leaderboard': 'Leaderboard',
      'nav-gallery': 'Gallery',
      'nav-reports': 'Reports',
      'nav-blog': 'Blog',
      'nav-contact': 'Contact',
      'hero-title': 'Planting Trees, Securing Tomorrow',
      'hero-subtitle': 'Join India\'s leading digital tree plantation movement. Track growth with real-time GPS & QR codes.',
      'btn-join': 'Join Campaign',
      'btn-explore': 'Explore More',
      'stat-planted': 'Trees Planted',
      'stat-co2': 'Kg CO₂ Saved',
      'stat-volunteers': 'Volunteers',
      'stat-campaigns': 'Campaigns'
    }
  };

  const currentLang = localStorage.getItem('lang') || 'en';
  applyLanguage(currentLang);

  if (langToggle) {
    langToggle.value = currentLang;
    langToggle.addEventListener('change', (e) => {
      const selected = e.target.value;
      localStorage.setItem('lang', selected);
      applyLanguage(selected);
    });
  }

  function applyLanguage(lang) {
    if (!translations[lang]) return;
    document.querySelectorAll('[data-i18n]').forEach(elem => {
      const key = elem.getAttribute('data-i18n');
      if (translations[lang][key]) {
        elem.innerText = translations[lang][key];
      }
    });
  }

  // 4. Floating Leaf Particle Effect in Hero
  const heroContainer = document.querySelector('.hero-section');
  if (heroContainer) {
    for (let i = 0; i < 15; i++) {
      const leaf = document.createElement('i');
      leaf.className = 'fas fa-leaf floating-leaf';
      leaf.style.left = Math.random() * 100 + '%';
      leaf.style.animationDuration = (8 + Math.random() * 8) + 's';
      leaf.style.animationDelay = (Math.random() * 5) + 's';
      leaf.style.fontSize = (14 + Math.random() * 16) + 'px';
      leaf.style.color = Math.random() > 0.5 ? '#81C784' : '#FFC107';
      heroContainer.appendChild(leaf);
    }
  }

  // 5. Animated Number Counter
  const counters = document.querySelectorAll('.counter-number');
  counters.forEach(counter => {
    const target = +counter.getAttribute('data-target');
    let count = 0;
    const speed = Math.ceil(target / 80);
    const updateCount = () => {
      count += speed;
      if (count < target) {
        counter.innerText = count.toLocaleString();
        setTimeout(updateCount, 25);
      } else {
        counter.innerText = target.toLocaleString();
      }
    };
    updateCount();
  });
});
