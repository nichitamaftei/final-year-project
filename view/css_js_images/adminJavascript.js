$(document).ready(function(){
    initialise();
    initialiseFiltering();
}); // when the DOM loads call the initialise() function

function initialise(){
    showTab('users'); // show the users tab by default
}

function showTab(tab){
    if (tab === "users"){
        $("#users").addClass("active");
        $("#logs").removeClass("active");
        $("#usersTabButton").addClass("active");
        $("#logsTabButton").removeClass("active");
    } else if (tab === "logs"){
        $("#logs").addClass("active");
        $("#users").removeClass("active");
        $("#logsTabButton").addClass("active");
        $("#usersTabButton").removeClass("active");
    }
}

function openAddUserModal(){
    $("#modalContainer").css("display", "flex");
}

function closeAddUserModal(){
    $("#modalContainer").css("display", "none");
}

function updateFilterIcons(field, value){
    let offIcon = document.getElementById(field + "FilterOffIcon");
    let filterIcon = document.getElementById(field + "FilterIcon");
    let upIcon = document.getElementById(field + "UpIcon");
    let downIcon = document.getElementById(field + "DownIcon");

    if (value === "not set"){
        offIcon.style.display = "block";
        filterIcon.style.display = "none";
        upIcon.style.display = "none";
        downIcon.style.display = "none";
    } else if (value === "asc"){
        offIcon.style.display = "none";
        filterIcon.style.display = "inline";
        upIcon.style.display = "inline";
        downIcon.style.display = "none";
    } else if (value === "desc"){
        offIcon.style.display = "none";
        filterIcon.style.display = "inline";
        upIcon.style.display = "none";
        downIcon.style.display = "inline";
    }
}

function initialiseFiltering(){

    $.ajax({ // get the department json data 
        url: "fetchFilteringState.php", // specificying which php file
        method: "POST", // fetch type
        success: function(data){

            console.log(data);

            updateFilterIcons("name", data.name);
            updateFilterIcons("email", data.email);
            updateFilterIcons("logIn", data.logIn);
        },
        error: function(error){  
            console.error("Error fetching data:", error);
        }
    });
}

function submitNameFilterForm(){
    $("#nameFilteringForm").submit();
}

function submitEmailFilterForm(){
    $("#emailFilteringForm").submit();
}

function submitLogInFilterForm(){
    $("#logInFilteringForm").submit();
}