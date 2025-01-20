$(document).ready(initialise); // when the DOM loads call the initialise() function

function initialise(){
    showTab('users'); // show the users tab by default
}

function showTab(tab) {
    if (tab === 'users') {
        $('#users').addClass('active');
        $('#logs').removeClass('active');
        $('#usersTabButton').addClass('active');
        $('#logsTabButton').removeClass('active');
    } else if (tab === 'logs') {
        $('#logs').addClass('active');
        $('#users').removeClass('active');
        $('#logsTabButton').addClass('active');
        $('#usersTabButton').removeClass('active');
    }
}

function openAddUserModal(){
    document.getElementById("modalContainer").style.display = "flex";
}

function closeAddUserModal(){
    document.getElementById("modalContainer").style.display = "none";
}