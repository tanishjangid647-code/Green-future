/* Green Future - AI Plantation Assistant Chatbot */

function initChatbot() {
  const toggleBtn = document.getElementById('chat-toggle-btn');
  const chatBox = document.getElementById('chat-box-container');
  const closeBtn = document.getElementById('chat-close-btn');
  const sendBtn = document.getElementById('chat-send-btn');
  const inputElem = document.getElementById('chat-input');
  const bodyElem = document.getElementById('chat-messages');

  if (!toggleBtn || !chatBox) return;

  toggleBtn.addEventListener('click', () => {
    chatBox.classList.toggle('d-none');
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', () => {
      chatBox.classList.add('d-none');
    });
  }

  function appendMessage(text, sender = 'bot') {
    const msgDiv = document.createElement('div');
    msgDiv.className = `chat-msg ${sender}`;
    msgDiv.innerText = text;
    bodyElem.appendChild(msgDiv);
    bodyElem.scrollTop = bodyElem.scrollHeight;
  }

  function processUserQuery(query) {
    const q = query.toLowerCase();
    let reply = "I'm EcoBot! I can help you with tree species selection, campaign registration, watering routines, and CO2 offset calculations. Ask me anything!";

    if (q.includes('plant') || q.includes('how to')) {
      reply = "To plant a tree: 1) Dig a pit twice as wide as the root ball. 2) Place sapling straight. 3) Backfill with 50% soil and 50% compost. 4) Water thoroughly immediately.";
    } else if (q.includes('water') || q.includes('schedule')) {
      reply = "Newly planted saplings require 5-10 liters of water every 2-3 days for the first 6 months. Reduce frequency during monsoon seasons.";
    } else if (q.includes('certificate') || q.includes('qr')) {
      reply = "Certificates are generated automatically after attending a campaign or sponsoring a tree. You can download or print them anytime from your User Dashboard.";
    } else if (q.includes('campaign') || q.includes('event')) {
      reply = "Check out our 'Campaigns' page to find active plantation drives near your city (Mumbai, Pune, Bangalore, Kolkata, Delhi).";
    } else if (q.includes('volunteer')) {
      reply = "You can register as a Volunteer during signup! Volunteers help monitor assigned trees, log growth photos, and verify plantation status.";
    }

    setTimeout(() => appendMessage(reply, 'bot'), 600);
  }

  function handleSend() {
    const val = inputElem.value.trim();
    if (!val) return;
    appendMessage(val, 'user');
    inputElem.value = '';
    processUserQuery(val);
  }

  if (sendBtn) sendBtn.addEventListener('click', handleSend);
  if (inputElem) {
    inputElem.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') handleSend();
    });
  }
}

document.addEventListener('DOMContentLoaded', initChatbot);
