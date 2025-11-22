document.addEventListener('DOMContentLoaded', function () {
    const activeTab = localStorage.getItem('activeTab');

    const tabButtons = document.querySelectorAll('.nav-link');

    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            const tabId = button.getAttribute('data-tab-id');
            localStorage.setItem('activeTab', tabId);
        });
    });

    if (activeTab) {
        const tabToActivate = document.querySelector(`[data-tab-id="${activeTab}"]`);
        if (tabToActivate) {
            tabToActivate.click();
        }
    }
});