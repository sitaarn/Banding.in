const revealElements = document.querySelectorAll('.story-card, .feature-card, .cta-group');

const observerOptions = {
  threshold: 0.15,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry, index) => {
    if (entry.isIntersecting) {
      setTimeout(() => {
        entry.target.classList.add('visible');
      }, index * 100);
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

revealElements.forEach(el => {
  el.classList.add('hidden');
  observer.observe(el);
});

// =====================
// BUTTON REDIRECT
// =====================
// Tombol Try Now redirect ke halaman search
const btnPrimary = document.querySelector('.btn-primary');
btnPrimary.addEventListener('click', () => {
  btnPrimary.innerText = 'Menuju halaman...';
  btnPrimary.style.opacity = '0.7';
  setTimeout(() => {
    window.location.href = 'search.html';
  }, 800);
});

// Tombol Contact Us redirect ke halaman login
const btnSecondary = document.querySelector('.btn-secondary');
btnSecondary.addEventListener('click', () => {
  window.location.href = 'login.html';
});

// Tombol Login di nav
const navBtns = document.querySelectorAll('.nav-btn');
navBtns[0].addEventListener('click', () => {
  window.location.href = 'login.html';
});
navBtns[1].addEventListener('click', () => {
  window.location.href = 'index.html';
});

// =====================
// FEATURE CARD CLICK EFFECT
// =====================
// Kartu fitur kasih efek "ripple" saat diklik
const featureCards = document.querySelectorAll('.feature-card');
featureCards.forEach(card => {
  card.addEventListener('click', function(e) {
    const ripple = document.createElement('span');
    ripple.classList.add('ripple');
    const rect = card.getBoundingClientRect();
    ripple.style.left = (e.clientX - rect.left) + 'px';
    ripple.style.top = (e.clientY - rect.top) + 'px';
    card.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
  });
});

// =====================
// ORB PARALLAX
// =====================
// Orb background bergerak mengikuti mouse
document.addEventListener('mousemove', (e) => {
  const x = (e.clientX / window.innerWidth - 0.5) * 20;
  const y = (e.clientY / window.innerHeight - 0.5) * 20;

  document.querySelector('.orb-1').style.transform = `translate(${x * 1.2}px, ${y * 1.2}px)`;
  document.querySelector('.orb-2').style.transform = `translate(${-x * 0.8}px, ${-y * 0.8}px)`;
  document.querySelector('.orb-3').style.transform = `translate(${x * 0.5}px, ${y * 0.5}px)`;

});
