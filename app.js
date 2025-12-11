// js/app.js — cart helper + UI touches
function bumpCart() {
  const bubble = document.getElementById('cart-count');
  if (!bubble) return;
  bubble.classList.add('bump');
  setTimeout(() => bubble.classList.remove('bump'), 420);
}

function addToCart(productId) {
  fetch('/marketplace/api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'add', id: productId, qty: 1 })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const counter = document.getElementById('cart-count');
        if (counter) counter.textContent = data.count;
        bumpCart();
        showToast('Added to cart');
      } else {
        showToast('Error: ' + (data.error || 'unknown'), true);
      }
    })
    .catch(err => {
      console.error(err);
      showToast('Network error', true);
    });
}

// Basic toast helper
function showToast(message, isError=false) {
  let el = document.getElementById('me-toast');
  if (!el) {
    el = document.createElement('div');
    el.id = 'me-toast';
    el.style.position = 'fixed';
    el.style.right = '18px';
    el.style.bottom = '18px';
    el.style.padding = '10px 14px';
    el.style.borderRadius = '10px';
    el.style.background = isError ? 'rgba(239,68,68,0.95)' : 'rgba(14,165,164,0.95)';
    el.style.color = '#fff';
    el.style.boxShadow = '0 10px 30px rgba(2,6,23,0.4)';
    el.style.fontWeight = '600';
    el.style.zIndex = 9999;
    document.body.appendChild(el);
  }
  el.textContent = message;
  el.style.background = isError ? 'rgba(239,68,68,0.95)' : 'rgba(14,165,164,0.95)';
  el.style.opacity = '1';
  clearTimeout(el._timeout);
  el._timeout = setTimeout(() => {
    el.style.transition = 'opacity 300ms';
    el.style.opacity = '0';
  }, 1600);
}

// Client-side search filtering
document.addEventListener('DOMContentLoaded', () => {
  const search = document.getElementById('site-search');
  if (!search) return;

  search.addEventListener('input', (e) => {
    const q = e.target.value.trim().toLowerCase();
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
      const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
      const desc = card.querySelector('.small')?.textContent.toLowerCase() || '';
      card.style.display = (name.includes(q) || desc.includes(q)) ? '' : 'none';
    });
  });
});
