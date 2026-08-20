/* Green Future - AI Plantation Assistant Chatbot */

function initChatbot() {

  const toggleBtn = document.getElementById('chat-toggle-btn');
  const chatBox = document.getElementById('chat-box-container');
  const closeBtn = document.getElementById('chat-close-btn');
  const sendBtn = document.getElementById('chat-send-btn');
  const inputElem = document.getElementById('chat-input');
  const bodyElem = document.getElementById('chat-messages');

  if (!toggleBtn || !chatBox || !inputElem || !bodyElem) return;


  // ---------------------------------------------------------
  // Open / Close Chatbot
  // ---------------------------------------------------------

  toggleBtn.addEventListener('click', () => {
    chatBox.classList.toggle('d-none');
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', () => {
      chatBox.classList.add('d-none');
    });
  }


  // ---------------------------------------------------------
  // Add Message
  // ---------------------------------------------------------

  function appendMessage(text, sender = 'bot') {

    const msgDiv = document.createElement('div');

    msgDiv.className = `chat-msg ${sender}`;

    msgDiv.innerText = text;

    bodyElem.appendChild(msgDiv);

    bodyElem.scrollTop = bodyElem.scrollHeight;
  }


  // ---------------------------------------------------------
  // Typing Indicator
  // ---------------------------------------------------------

  function showTyping() {

    const typing = document.createElement('div');

    typing.className = 'chat-msg bot';
    typing.id = 'chat-typing';

    typing.innerText = 'EcoBot is typing...';

    bodyElem.appendChild(typing);

    bodyElem.scrollTop = bodyElem.scrollHeight;
  }


  function removeTyping() {

    const typing = document.getElementById('chat-typing');

    if (typing) {
      typing.remove();
    }
  }


  // ---------------------------------------------------------
  // Chatbot Knowledge Base
  // ---------------------------------------------------------

  function processUserQuery(query) {

    const q = query
      .toLowerCase()
      .trim();

    let reply = '';


    // -------------------------------------------------------
    // Greeting
    // -------------------------------------------------------

    if (
      q.includes('hello') ||
      q.includes('hi') ||
      q.includes('hey') ||
      q.includes('namaste') ||
      q.includes('good morning') ||
      q.includes('good evening')
    ) {

      reply =
        "Hello! 🌱 I'm EcoBot, your Green Future assistant. I can help you with trees, campaigns, tree tracking, volunteering, certificates, weather, your profile, and more.";

    }


    // -------------------------------------------------------
    // About Green Future
    // -------------------------------------------------------

    else if (
      q.includes('what is green future') ||
      q.includes('about green future') ||
      q.includes('what is this website') ||
      q.includes('green future')
    ) {

      reply =
        "Green Future is a tree plantation and environmental monitoring platform. 🌳 You can join plantation campaigns, adopt trees, track their growth, view GPS locations, upload progress, volunteer for drives, and earn certificates.";

    }


    // -------------------------------------------------------
    // Planting Trees
    // -------------------------------------------------------

    else if (
      q.includes('plant a tree') ||
      q.includes('plant tree') ||
      q.includes('how to plant') ||
      q.includes('planting a tree') ||
      q.includes('plantation')
    ) {

      reply =
        "To plant a tree 🌱: dig a suitable pit, place the sapling upright, mix soil with compost, fill the pit carefully, water it thoroughly, and continue regular watering during the establishment period.";

    }


    // -------------------------------------------------------
    // Tree Care
    // -------------------------------------------------------

    else if (
      q.includes('tree care') ||
      q.includes('take care') ||
      q.includes('care for tree') ||
      q.includes('maintain tree')
    ) {

      reply =
        "Good tree care includes regular watering, removing weeds, protecting the trunk, checking for pests or disease, providing suitable sunlight, and monitoring growth regularly. 🌳";

    }


    // -------------------------------------------------------
    // Watering
    // -------------------------------------------------------

    else if (
      q.includes('water') ||
      q.includes('watering') ||
      q.includes('how often') ||
      q.includes('water schedule')
    ) {

      reply =
        "Newly planted trees generally need regular watering while their roots establish. The exact amount depends on the species, soil and weather. During heavy monsoon rainfall, watering may need to be reduced. 💧";

    }


    // -------------------------------------------------------
    // Campaigns
    // -------------------------------------------------------

    else if (
      q.includes('campaign') ||
      q.includes('plantation drive') ||
      q.includes('plantation drive') ||
      q.includes('event') ||
      q.includes('tree drive')
    ) {

      reply =
        "You can find available plantation drives on the Campaigns page. 🌱 Open a campaign to see its date, location, organizer, tree species and available volunteer slots.";

    }


    // -------------------------------------------------------
    // Join Campaign
    // -------------------------------------------------------

    else if (
      q.includes('join campaign') ||
      q.includes('join drive') ||
      q.includes('register campaign') ||
      q.includes('participate') ||
      q.includes('join plantation')
    ) {

      reply =
        "To join a plantation drive, open the Campaigns page, select a campaign, open its details and click 'Join Plantation Drive'. Your registration will then be stored in your account.";

    }


    // -------------------------------------------------------
    // Volunteer
    // -------------------------------------------------------

    else if (
      q.includes('volunteer') ||
      q.includes('become volunteer') ||
      q.includes('volunteer registration') ||
      q.includes('volunteer work')
    ) {

      reply =
        "Volunteers help Green Future monitor trees, verify plantation activities, upload growth updates and participate in plantation drives. You can register with the Volunteer role during signup.";

    }


    // -------------------------------------------------------
    // My Trees
    // -------------------------------------------------------

    else if (
      q.includes('my trees') ||
      q.includes('my tree') ||
      q.includes('adopted trees') ||
      q.includes('where are my trees') ||
      q.includes('see my trees')
    ) {

      reply =
        "You can see your adopted and planted trees from your User Dashboard or the 'My Trees' section. 🌳 There you can view tree health, height, plantation date and tracking information.";

    }


    // -------------------------------------------------------
    // Tree Tracking
    // -------------------------------------------------------

    else if (
      q.includes('track tree') ||
      q.includes('tree tracking') ||
      q.includes('track my tree') ||
      q.includes('gps') ||
      q.includes('tree location')
    ) {

      reply =
        "Use the Tree Tracking page to search for a tree using its official Tree Tag Code. You can view its GPS location, health status, current height and growth history.";

    }


    // -------------------------------------------------------
    // QR Code
    // -------------------------------------------------------

    else if (
      q.includes('qr') ||
      q.includes('qr code') ||
      q.includes('scan tree') ||
      q.includes('scan code')
    ) {

      reply =
        "Each tracked tree can have an official QR code. 📱 After deployment, scanning the QR code will open that tree's public tracking page, where you can view its information and growth history.";

    }


    // -------------------------------------------------------
    // Tree Progress
    // -------------------------------------------------------

    else if (
      q.includes('growth') ||
      q.includes('tree progress') ||
      q.includes('growth progress') ||
      q.includes('tree update') ||
      q.includes('inspection')
    ) {

      reply =
        "Tree growth updates are recorded through inspection logs. They can include measured height, uploaded images, notes and inspection dates. 🌱";

    }


    // -------------------------------------------------------
    // Tree Health
    // -------------------------------------------------------

    else if (
      q.includes('tree health') ||
      q.includes('healthy tree') ||
      q.includes('needs water') ||
      q.includes('damaged tree')
    ) {

      reply =
        "Green Future tracks tree health using statuses such as Healthy, Needs Water, Damaged and Dead. Volunteers can inspect trees and update their condition.";

    }


    // -------------------------------------------------------
    // Weather
    // -------------------------------------------------------

    else if (
      q.includes('weather') ||
      q.includes('temperature') ||
      q.includes('rain') ||
      q.includes('humidity')
    ) {

      reply =
        "The User Dashboard displays live weather based on the city saved in your profile. 🌦️ The weather information is retrieved from a real weather API.";

    }


    // -------------------------------------------------------
    // Wishlist
    // -------------------------------------------------------

    else if (
      q.includes('wishlist') ||
      q.includes('wish list') ||
      q.includes('saved campaign') ||
      q.includes('save campaign')
    ) {

      reply =
        "Your Wishlist lets you save campaigns that you're interested in. ❤️ You can open your Wishlist from your user navigation or dashboard.";

    }


    // -------------------------------------------------------
    // Profile
    // -------------------------------------------------------

    else if (
      q.includes('profile') ||
      q.includes('edit profile') ||
      q.includes('change name') ||
      q.includes('change city') ||
      q.includes('update profile')
    ) {

      reply =
        "You can update your profile information from Profile & Settings. You can change your name, phone number, city and state, while your registered email remains read-only.";

    }


    // -------------------------------------------------------
    // Certificate
    // -------------------------------------------------------

    else if (
      q.includes('certificate') ||
      q.includes('certificates') ||
      q.includes('download certificate') ||
      q.includes('my certificate')
    ) {

      reply =
        "Your certificates are available through the Certificates section of your User Dashboard. 📜 Depending on your participation, you can view or download your Green Future certificates.";

    }


    // -------------------------------------------------------
    // CO2
    // -------------------------------------------------------

    else if (
      q.includes('co2') ||
      q.includes('carbon') ||
      q.includes('carbon offset') ||
      q.includes('carbon dioxide')
    ) {

      reply =
        "Green Future tracks estimated CO₂ offset for planted trees. 🌍 The value is associated with each tree and can be displayed in your tree tracking and dashboard information.";

    }


    // -------------------------------------------------------
    // Login
    // -------------------------------------------------------

    else if (
      q.includes('login') ||
      q.includes('log in') ||
      q.includes('cannot login') ||
      q.includes('login problem')
    ) {

      reply =
        "If you're having trouble logging in, make sure your email and password are correct and that your account is active. You can also use the Forgot Password option on the login page.";

    }


    // -------------------------------------------------------
    // Forgot Password
    // -------------------------------------------------------

    else if (
      q.includes('forgot password') ||
      q.includes('reset password') ||
      q.includes('forgot my password')
    ) {

      reply =
        "Use the 'Forgot Password' option on the login page to request a password reset link. The reset link is time-limited for security.";

    }


    // -------------------------------------------------------
    // Signup
    // -------------------------------------------------------

    else if (
      q.includes('signup') ||
      q.includes('sign up') ||
      q.includes('register') ||
      q.includes('create account')
    ) {

      reply =
        "You can create a Green Future account using the Register page. During registration, you can choose the appropriate role available to you.";

    }


    // -------------------------------------------------------
    // Contact
    // -------------------------------------------------------

    else if (
      q.includes('contact') ||
      q.includes('support') ||
      q.includes('help')
    ) {

      reply =
        "For assistance, use the Contact page to send a message to the Green Future team. You can also ask me about campaigns, trees, volunteering, tracking or your account.";

    }


    // -------------------------------------------------------
    // Thanks
    // -------------------------------------------------------

    else if (
      q.includes('thank') ||
      q.includes('thanks')
    ) {

      reply =
        "You're welcome! 🌱 I'm always happy to help. Keep growing a greener future! 🌳";

    }


    // -------------------------------------------------------
    // Goodbye
    // -------------------------------------------------------

    else if (
      q === 'bye' ||
      q.includes('goodbye')
    ) {

      reply =
        "Goodbye! 🌱 See you again at Green Future!";

    }


    // -------------------------------------------------------
    // Unknown Question
    // -------------------------------------------------------

    else {

      reply =
        "I'm not sure about that yet. 🌱 Try asking me about tree planting, tree care, campaigns, volunteering, tree tracking, QR codes, weather, certificates, CO₂, your profile, wishlist or login help.";

    }


    showTyping();

    setTimeout(() => {

      removeTyping();

      appendMessage(reply, 'bot');

    }, 600);
  }


  // ---------------------------------------------------------
  // Send Message
  // ---------------------------------------------------------

  function handleSend() {

    const val = inputElem.value.trim();

    if (!val) return;

    appendMessage(val, 'user');

    inputElem.value = '';

    processUserQuery(val);
  }


  if (sendBtn) {

    sendBtn.addEventListener(
      'click',
      handleSend
    );

  }


  if (inputElem) {

    inputElem.addEventListener(
      'keypress',
      (e) => {

        if (e.key === 'Enter') {

          e.preventDefault();

          handleSend();

        }

      }
    );

  }

}


document.addEventListener(
  'DOMContentLoaded',
  initChatbot
);