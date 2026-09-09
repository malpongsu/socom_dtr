// Live clock on the home page
function updateClock() {
    const clockEl = document.getElementById('clock');
    if (!clockEl) return;
    const now = new Date();
    clockEl.textContent = now.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
        + ' - ' + now.toLocaleTimeString();
}
setInterval(updateClock, 1000);
updateClock();

// RFID readers act as keyboard input followed by Enter; auto-submit the scan form.
(function () {
    const input = document.getElementById('rfid_tag');
    const form = document.getElementById('scanForm');
    if (!input || !form) return;

    input.focus();

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (input.value.trim() !== '') {
                form.submit();
            }
        }
    });

    // Refocus the scan input whenever the user clicks elsewhere on the page.
    document.addEventListener('click', function () {
        if (document.getElementById('rfid_tag')) {
            input.focus();
        }
    });
})();
