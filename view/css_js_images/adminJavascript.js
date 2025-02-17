$(document).ready(function(){
    initialiseTab();
    initialiseFiltering();
}); // when the DOM loads call the initialise() function

function initialiseTab(){

    $.ajax({ // get the department json data 
        url: "fetchCurrentAdminTab.php", // specificying which php file
        method: "POST", // fetch type
        success: function(data){

            console.log(data)

            if (data == "users"){
                $("#users").addClass("active");
                $("#logs").removeClass("active");
                $("#usersTabButton").addClass("active");
                $("#logsTabButton").removeClass("active");
            } else if (data == "logs"){
                $("#logs").addClass("active");
                $("#users").removeClass("active");
                $("#logsTabButton").addClass("active");
                $("#usersTabButton").removeClass("active");
            }
        },
        error: function(error){  
            console.error("Error fetching data:", error);
        }
    });
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

    $.ajax({ // get the user tab filtering values json data 
        url: "fetchFilteringState.php", // specificying which php file
        method: "POST", // fetch type
        data: {request: "user"},
        success: function(data){

            updateFilterIcons("name", data.name);
            updateFilterIcons("email", data.email);
            updateFilterIcons("logIn", data.logIn);
        },
        error: function(error){  
            console.error("Error fetching data:", error);
        }
    });

    $.ajax({ // get the logs tab filtering values json data 
        url: "fetchFilteringState.php", // specificying which php file
        method: "POST", // fetch type
        data: {request: "logs"},
        success: function(data){

            updateFilterIcons("date", data.date);
            updateFilterIcons("time", data.time);
            updateFilterIcons("nameLog", data.nameLog);
            updateFilterIcons("eventType", data.eventType);
            updateFilterIcons("details", data.details);
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



function submitDateFilterForm(){
    $("#dateFilteringForm").submit();
}

function submitTimeFilterForm(){
    $("#timeFilteringForm").submit();
}

function submitNameLogFilterForm(){
    $("#nameLogFilteringForm").submit();
}

function submitEventTypeFilterForm(){
    $("#eventTypeFilteringForm").submit();
}

function submitDetailsFilterForm(){
    $("#detailsFilteringForm").submit();
}