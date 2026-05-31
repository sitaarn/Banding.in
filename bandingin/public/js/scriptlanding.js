function goToSearch() {
  window.location.href = '/bandingin/list';
}

function goToLogin() {
  localStorage.setItem('redirectAfterLogin', window.location.href);
  window.location.href = '/bandingin/login';
}

window.addEventListener('click', function(e) {
  const wrap = document.getElementById('userChipWrap');
  if (wrap && !wrap.contains(e.target)) {
    wrap.classList.remove('open');
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const pfLabels = document.querySelectorAll('.landing-pf-label');
  pfLabels.forEach(label => {
    label.addEventListener('click', () => {
      pfLabels.forEach(l => l.classList.remove('active'));
      label.classList.add('active');
    });
  });
});

function toggleDropdown() {
  const wrap = document.getElementById('userChipWrap');
  if (wrap) wrap.classList.toggle('open');
}

function doLogout() {
  localStorage.removeItem('loggedIn');
  localStorage.removeItem('userName');
  localStorage.removeItem('userEmail');
  window.location.href = '/bandingin/logout';
}


function getInitials(name) {
  return name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() || '').join('');
}

document.addEventListener("DOMContentLoaded", function () {
  const avatarEl = document.getElementById('userAvatar');
  if (avatarEl) {
    let name = avatarEl.getAttribute('data-avatar');
    if (name) {
      avatarEl.innerHTML = getInitials(name);
    }
  }
});