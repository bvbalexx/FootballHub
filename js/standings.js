document.addEventListener("DOMContentLoaded", function () {
    const standingsBtn = document.getElementById('standingsBtn');
    const scorersBtn = document.getElementById('scorersBtn');
    const standingsSection = document.getElementById('standingsSection');
    const scorersSection = document.getElementById('scorersSection');

    if (standingsBtn && scorersBtn && standingsSection && scorersSection) {
        standingsBtn.addEventListener('click', () => {
            standingsBtn.classList.add('active');
            scorersBtn.classList.remove('active');
            standingsSection.style.display = 'block';
            scorersSection.style.display = 'none';
        });

        scorersBtn.addEventListener('click', () => {
            scorersBtn.classList.add('active');
            standingsBtn.classList.remove('active');
            standingsSection.style.display = 'none';
            scorersSection.style.display = 'block';
        });
    }
});
