document.addEventListener('DOMContentLoaded', () => {
    const btnYes = document.getElementById('btnYes');
    const btnNo = document.getElementById('btnNo');
    const ageQuestion = document.getElementById('ageQuestion');
    const accessDenied = document.getElementById('accessDenied');

    if (!btnYes || !btnNo || !ageQuestion || !accessDenied || !window.BrewAgeGate) return;

    btnYes.addEventListener('click', () => {
        window.BrewAgeGate.confirm();
        window.location.replace(window.BrewAgeGate.destination());
    });

    btnNo.addEventListener('click', () => {
        ageQuestion.classList.add('hidden');
        accessDenied.classList.remove('hidden');
        accessDenied.querySelector('h1')?.focus();
    });
});
