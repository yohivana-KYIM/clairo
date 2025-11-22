document.querySelectorAll('.nav-link').forEach(function(tab) {
    tab.addEventListener('click', function() {
        const activeTabId = tab.getAttribute('id');
        localStorage.setItem('activeTab', activeTabId);
    });
});

const activeTabId = localStorage.getItem('activeTab');
if (activeTabId) {
    const activeTab = document.querySelector('#' + activeTabId);
    if (activeTab) {
        activeTab.click();
    }
}