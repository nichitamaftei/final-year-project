$(document).ready(function() {
    initialise();
    initialiseFiltering();
}); // when the DOM loads call the initialise() function

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


function initialiseFiltering(){
    var nameFilterOffIcon = document.getElementById('nameFilterOffIcon');
    var nameFilterIcon = document.getElementById('nameFilterIcon');
    var nameUpIcon = document.getElementById('nameUpIcon');
    var nameDownIcon = document.getElementById('nameDownIcon');

    var emailFilterOffIcon = document.getElementById('emailFilterOffIcon');
    var emailFilterIcon = document.getElementById('emailFilterIcon');
    var emailUpIcon = document.getElementById('emailUpIcon');
    var emailDownIcon = document.getElementById('emailDownIcon');

    var logInFilterOffIcon = document.getElementById('logInFilterOffIcon');
    var logInFilterIcon = document.getElementById('logInFilterIcon');
    var logInUpIcon = document.getElementById('logInUpIcon');
    var logInDownIcon = document.getElementById('logInDownIcon');


    $.ajax({ // get the department json data 
        url: 'fetchFilteringState.php', // specificying which php file
        method: 'POST', // fetch type
        success: function(data){

            console.log(data);

            if(data.name === "not set"){
                nameFilterOffIcon.style.display = 'block'
                nameFilterIcon.style.display = 'none'
                nameUpIcon.style.display = 'none'
                nameDownIcon.style.display = 'none'
            }else if(data.name === "asc"){
                nameFilterOffIcon.style.display = 'none'
                nameFilterIcon.style.display = 'inline'
                nameUpIcon.style.display = 'inline'
                nameDownIcon.style.display = 'none'
            }else if(data.name === "desc"){
                nameFilterOffIcon.style.display = 'none'
                nameFilterIcon.style.display = 'inline'
                nameUpIcon.style.display = 'none'
                nameDownIcon.style.display = 'inline'
            }

            if(data.email === "not set"){
                emailFilterOffIcon.style.display = 'block'
                emailFilterIcon.style.display = 'none'
                emailUpIcon.style.display = 'none'
                emailDownIcon.style.display = 'none'
            }else if(data.email === "asc"){
                emailFilterOffIcon.style.display = 'none'
                emailFilterIcon.style.display = 'inline'
                emailUpIcon.style.display = 'inline'
                emailDownIcon.style.display = 'none'
            }else if(data.email === "desc"){
                emailFilterOffIcon.style.display = 'none'
                emailFilterIcon.style.display = 'inline'
                emailUpIcon.style.display = 'none'
                emailDownIcon.style.display = 'inline'
            }

            if(data.lastLogIn === "not set"){
                logInFilterOffIcon.style.display = 'block'
                logInFilterIcon.style.display = 'none'
                logInUpIcon.style.display = 'none'
                logInDownIcon.style.display = 'none'
            }else if(data.lastLogIn === "asc"){
                logInFilterOffIcon.style.display = 'none'
                logInFilterIcon.style.display = 'inline'
                logInUpIcon.style.display = 'inline'
                logInDownIcon.style.display = 'none'
            }else if(data.lastLogIn === "desc"){
                logInFilterOffIcon.style.display = 'none'
                logInFilterIcon.style.display = 'inline'
                logInUpIcon.style.display = 'none'
                logInDownIcon.style.display = 'inline'
            }


        },
        error: function(error){  
            console.error("Error fetching data:", error);
        }
    });


}

function submitNameFilterForm() {
    document.getElementById("nameFilteringForm").submit();
}

function submitEmailFilterForm() {
    document.getElementById("emailFilteringForm").submit();
}

function submitLogInFilterForm() {
    document.getElementById("logInFilteringForm").submit();
}