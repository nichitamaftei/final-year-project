$(document).ready(initialise); // when the DOM loads call the initialise() function

function initialise(){
    showTab('users'); // show the users tab by default
}

function showTab(tab) {
    if (tab === 'users') {
        $('#users').addClass('active');
        $('#logs').removeClass('active');
    } else if (tab === 'logs') {
        $('#logs').addClass('active');
        $('#users').removeClass('active');
    }
}

function openAddUserModal() {
    document.getElementById("modal").style.display = "flex";
}

function closeAddUserModal() {
    document.getElementById("modal").style.display = "none";
}
