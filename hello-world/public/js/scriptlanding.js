function goToSearch() {
  window.location.href = 'http://localhost/hello-world/list';
}

function goToLogin() {
  localStorage.setItem('redirectAfterLogin', window.location.href);
  window.location.href = 'http://localhost/hello-world/login';
}

function toggleDropdown() {
  const wrap = document.getElementById('userChipWrap');
  if (wrap) wrap.classList.toggle('open');
}

function doLogout() {
  localStorage.removeItem('loggedIn');
  localStorage.removeItem('userName');
  localStorage.removeItem('userEmail');
  window.location.href = 'http://localhost/hello-world/logout';
}


function getInitials(name) {
  return name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() || '').join('');
}

document.getElementById('userAvatar').addEventListener("DOMContentLoaded", function (e) {
  let avatar = e.getAttribute('data-avatar');
  avatar = getInitials(avatar);

  document.getElementById('userAvatar').innerHTML = avatar;
});