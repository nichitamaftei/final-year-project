$(document).ready(initiatilse);

let graph = new joint.dia.Graph(); // 'graph' holds the data

function initiatilse(){

    $.ajax({ // get the department json data 
        url: 'fetchDepartmentData.php', // specificying which php file
        method: 'POST', // fetch type
        success: function(data){

            console.log(data); // debugging department data

            console.log(data["name"]);

            // --- creating and adding the shapes and links to the view ---

            const number_circle = createShape('Circle', null, 100, 200, 50, 50, 12);
            number_circle.addTo(graph);

            const number_text = createTextBlock('Calling '+ data.auto_attendant.phone_number, 20, 260, 200, 30, 12);
            number_text.addTo(graph);

            const aa_name_ellipse = createShape('Ellipse', data.auto_attendant.name, 250, 185, 180, 80, 14);
            aa_name_ellipse.addTo(graph);

            const number_to_aa_link = createLink(number_circle, aa_name_ellipse, null);
            number_to_aa_link.addTo(graph); // display in the view


            var greeting_wrapped_text = joint.util.breakText(data.auto_attendant.aa_greeting, {
                width: 150,
                height: 150
            });
            const aa_greeting_square = createShape('Rectangle', 'Greeting: \n \n' + greeting_wrapped_text, 520, 150, 150, 150, 14);
            aa_greeting_square.addTo(graph);

            const aa_to_greeting = createLink(aa_name_ellipse, aa_greeting_square, null);
            aa_to_greeting.addTo(graph); // display in the view

            const businesshours_decision = createDecision(800, 160, 130, 130);
            businesshours_decision.addTo(graph);

            const bussinesshours_text = createTextBlock('During Business \n hours?', 815, 182, 100, 80, 18);
            bussinesshours_text.addTo(graph);

            const greeting_to_businesshours = createLink(aa_greeting_square, businesshours_decision, null);
            greeting_to_businesshours.addTo(graph); // display in the view

            const bankholiday_decision = createDecision(1100, 160, 130, 130);
            bankholiday_decision.addTo(graph);

            const bankholiday_text = createTextBlock('During Bank \n Holiday?', 1115, 182, 100, 80, 18);
            bankholiday_text.addTo(graph);

            const businesshours_to_bankholiday = createLink(businesshours_decision, bankholiday_decision, 'Yes', 25);
            businesshours_to_bankholiday.addTo(graph); // display in the view

            var options_wrapped_text = joint.util.breakText(data.auto_attendant.options_greeting, {
                width: 180,
                height: 180
            });
            const options_square = createShape('Rectangle', 'Greeting: \n \n' + options_wrapped_text, 1400, 140, 170, 170, 14);
            options_square.addTo(graph);

            const bankholidays_to_options_square = createLink(bankholiday_decision, options_square, 'No', 25);
            bankholidays_to_options_square.addTo(graph); // display in the view

            var business_hoursgreeting_wrapped_text = joint.util.breakText(data.auto_attendant.business_hours.greeting, {
                width: 150,
                height: 150
            });

            const businesshours_greeting_square = createShape('Rectangle', 'Greeting: \n \n' + business_hoursgreeting_wrapped_text, 785, 400, 150, 150, 14);
            businesshours_greeting_square.addTo(graph);


            const businesshours_decision_to_businesshours_greeting_square = createLink(businesshours_decision, businesshours_greeting_square, 'No', 22);
            businesshours_decision_to_businesshours_greeting_square.addTo(graph); // display in the view


            var bankholiday_greeting_wrapped_text = joint.util.breakText(data.auto_attendant.bank_holiday.greeting, {
                width: 150,
                height: 150
            });
            const bankholiday_greeting_square = createShape('Rectangle', 'Greeting: \n \n' + bankholiday_greeting_wrapped_text, 1085, 400, 150, 150, 14);
            bankholiday_greeting_square.addTo(graph);

            const bankholiday_decision_to_businesshours_greeting_square = createLink(bankholiday_decision, bankholiday_greeting_square, 'Yes', 22);
            bankholiday_decision_to_businesshours_greeting_square.addTo(graph); // display in the view


            const membersText = data.auto_attendant.voicemail.members.join('\n');
            const voicemail_group = createShape('Rectangle', "Voicemail \n \n Owner \n \n" + data.auto_attendant.voicemail.group_name + '\n  \n \n' + "Members \n \n" + membersText, 935, 652, 170, 270, 10);
            voicemail_group.addTo(graph);

            const businesshours_greeting_square_to_voicemail_group = createLink(businesshours_greeting_square, voicemail_group, null, 22);
            businesshours_greeting_square_to_voicemail_group.addTo(graph); // display in the view

            const bankholiday_greeting_to_voicemail_group = createLink(bankholiday_greeting_square, voicemail_group, null, 22);
            bankholiday_greeting_to_voicemail_group.addTo(graph); // display in the view


            let valueYtoAdd = -500; // setting up a variable to offset the dynamic call queue displaying

            
            data.auto_attendant.call_queues.forEach((queue, index) => { // loop through every call queue within the department

                console.log(queue); // debugging

                if (data.auto_attendant.call_queues.length <= 1) { // if there is only 1 call queue, don't offset the Y position
                    valueYtoAdd = 0;
                }


                const callqueue_name = createShape('Rectangle', data.auto_attendant.call_queues[index].queue_name, 1685, (190 + valueYtoAdd), 150, 70, 'red', 14);
                callqueue_name.addTo(graph);

                const options_square_to_call_queue_name = createLink(options_square, callqueue_name, index+1, 30);
                options_square_to_call_queue_name.addTo(graph); // display in the view


                const routing_type = createShape('Rectangle', data.auto_attendant.call_queues[index].routing_type, 1985, (188 + valueYtoAdd), 150, 70, 'black', 14);
                routing_type.addTo(graph);

                const call_queue_name_to_routing_type = createLink(callqueue_name, routing_type, null);
                call_queue_name_to_routing_type.addTo(graph); // display in the view


                const maximum_decision = createDecision(2250, (170 + valueYtoAdd), 120, 120);
                maximum_decision.addTo(graph);

                const maximum_text = createTextBlock('Has the call queue reached its maximum (' + data.auto_attendant.call_queues[index].max_calls +') ?', 2260, (195 + valueYtoAdd), 100, 70, 11);
                maximum_text.addTo(graph);

                const routing_type_to_maximum_decision = createLink(routing_type, maximum_decision, null);
                routing_type_to_maximum_decision.addTo(graph); // display in the view


                const answertime_decision = createDecision(2520, (170 + valueYtoAdd), 120, 120);
                answertime_decision.addTo(graph);

                const answertime_text = createTextBlock('Did someone answer in ' + data.auto_attendant.call_queues[index].answer_timeout_seconds + ' seconds?', 2530, (195 + valueYtoAdd), 100, 70, 11);
                answertime_text.addTo(graph);

                const maximum_decisione_to_answertime_decision = createLink(maximum_decision, answertime_decision, "No", 20);
                maximum_decisione_to_answertime_decision.addTo(graph); // display in the view


                if (data.auto_attendant.call_queues[index].max_calls_exceeeded  === "disconnect") {
                    const disconnect_ellipse = createShape('Ellipse', 'Disconnect', 2217, (375 + valueYtoAdd), 180, 80, 14);
                    disconnect_ellipse.addTo(graph);

                    const maximum_decision_to_disconnect_ellipse = createLink(maximum_decision, disconnect_ellipse, 'Yes', 24);
                    maximum_decision_to_disconnect_ellipse.addTo(graph); // display in the view

                } else {
                    const callqueue_name_max = createShape('Rectangle', data.auto_attendant.call_queues[index].max_calls_exceeeded, 2217, (375 + valueYtoAdd), 180, 80, 'red', 14);
                    callqueue_name_max.addTo(graph);

                    const maximum_decision_to_callqueue_name_max = createLink(maximum_decision, callqueue_name_max, 'Yes', 24);
                    maximum_decision_to_callqueue_name_max.addTo(graph); // display in the view
                }


                if (data.auto_attendant.call_queues[index].answer_timeout_seconds_exceeded  === "disconnect") {
                    const disconnect_ellipse = createShape('Ellipse', 'Disconnect', 2490, (365 + valueYtoAdd), 180, 80, 14);
                    disconnect_ellipse.addTo(graph);

                    const answertime_decision_to_disconnect_ellipse = createLink(answertime_decision, disconnect_ellipse, 'No', 24);
                    answertime_decision_to_disconnect_ellipse.addTo(graph); // display in the view

                } else {
                    const answertime_callqueue_name = createShape('Rectangle', data.auto_attendant.call_queues[index].answer_timeout_seconds_exceeded, 2490, (365 + valueYtoAdd), 180, 80, 'red', 14);
                    answertime_callqueue_name.addTo(graph);

                    const answertime_decision_to_callqueue_name_max = createLink(answertime_decision, answertime_callqueue_name, 'No', 24);
                    answertime_decision_to_callqueue_name_max.addTo(graph); // display in the view
                }

                const callQueueMembersText = data.auto_attendant.call_queues[index].group.members.join('\n');
                const callQueue_group = createShape('Rectangle', data.auto_attendant.call_queues[index].group.group_name + '\n \n \n' + "Owner \n \n" + data.auto_attendant.call_queues[index].group.group_owner + " \n \nMembers \n \n" + callQueueMembersText, 2790, (100 + valueYtoAdd), 170, 270, 10);
                callQueue_group.addTo(graph);

                const answertime_decision_to_callQueue_group = createLink(answertime_decision, callQueue_group, 'Yes', 24);
                answertime_decision_to_callQueue_group.addTo(graph); // display in the view

                valueYtoAdd = valueYtoAdd + 500 // continue offsetting future call queues

            })

        },
        error: function(err) {
            console.error("Error fetching data:", err); // logs any errors
        }
    });

    // --- defining specific shapes --- 

    function createShape(type, text, x, y, width, height, stroke, fontsize){
        const shape = new joint.shapes.standard[type](); // instantiate the specified shape
        shape.position(x, y); // set the position of the shape
        shape.resize(width, height);  // set the size of the shape

        let labelAttrs = undefined;
        if (text) { // if text is supplied
            labelAttrs = { // add attributes, otherwise leave undefined
                text: text,
                fill: 'black',
                style: {fontSize: fontsize + 'px', textAnchor: 'middle'}
            };
        }
        shape.attr({ // styling the shape
            body:{
                fill: 'white',       
                stroke: stroke,         
                strokeWidth: 2,    
            },
            label: labelAttrs
        });
        return shape;
    }

    function createDecision(x, y, width, height, stroke){
        const decision = new joint.shapes.standard.Rectangle(); // instantiate the rectangle
        decision.position(x, y); // position of the rectangle within the canvas
        decision.resize(width, height);   // size of the rectangle
        decision.rotate(45);   // rotate the rectangle by 45 degrees
        decision.attr({ // styling the rectangle
            body:{
                fill: 'white',       
                stroke: stroke,         
                strokeWidth: 2,    
            },
        });
        return decision;
    }

    function createTextBlock(text, x, y, width, height, fontsize){
        const textBlock = new joint.shapes.standard.TextBlock(); // instantiate the specified text
        textBlock.position(x, y); // set the position of the text 
        textBlock.resize(width, height);  // set the size of the text
        textBlock.attr({ // styling the text
            body:{
                stroke: 'none',
            },
            label:{
                text: text,              
                style: {'font-size': fontsize + 'px'}, 
                background: 'none'       
            }
        });
        return textBlock; // return the created text block
    }

    function createLink(source, target, text, fontsize){
        const link = new joint.shapes.standard.Link();
        link.source(source);
        link.target(target);
    
        if (text) { // if text is supplied
            link.appendLabel({
                attrs: { // append label with text and fontsize
                    text: {
                        text: text,
                        fill: 'black',
                        style: {fontSize: fontsize + 'px', textAnchor: 'middle'}
                    }
                }
            });
        }
        link.attr({ // styling the link
            line:{ 
                stroke: 'white',
                strokeWidth: 2,
            },
        });
        return link;
    }
    

    // ---- setting up the view within the small canvas ---

    const paper = new joint.dia.Paper({ // 'paper' renders the view
        el: document.getElementById('smallCanvas'), // target the canvas div
        model: graph,
        width: 700, 
        height: 300, 
        gridSize: 10, 
        drawGrid: true, 
        interactive: false, 
    });

    // setting and applying the default scale and position for the smallCanvas
    let defaultScale = 0.2; 
    paper.scale(defaultScale, defaultScale); // apply default scale

    let defaulttX = 50; 
    let defaultY = 100; 
    paper.translate(defaulttX, defaultY); // apply default position
};

function fullView() { // when the smallCanvas is clicked:

    const modal = document.getElementById('canvas-modal'); // grabbing the hidden modal
    modal.style.display = 'flex' // enabling it to be shown

    const bigCanvas = document.getElementById('bigCanvas'); // grabbing the 'bigCanvas'

    const fullPaper = new joint.dia.Paper({ // creating a new paper to render the data in a bigger view
        el: bigCanvas, // displaying it within the now visible bigCanvas
        model: graph, // using the same data
        width: bigCanvas.offsetWidth, // the new view will use the bigCanvas's width
        height: bigCanvas.offsetHeight,  // the new view will use the bigCanvas's hegiht
        gridSize: 10,
        drawGrid: true,
        interactive: false, // doesn't allow the user to move the flow chart
    });

    // setting and applying the default scale and position for the bigCanvas
    let initialScale = 0.43;
    fullPaper.scale(initialScale, initialScale); // apply default scale

    let initialX = 30;
    let initialY = 200;
    fullPaper.translate(initialX, initialY); // apply default position
    
    // handle zooming
    bigCanvas.addEventListener('wheel', (event) =>{
        event.preventDefault(); // ensures only the canvas scrolls, not the website itself

        let delta = 0;

        if (event.deltaY < 0){ // if the event scroll is positive
            delta = 0.1; // prepare to zoom in
        } else {
            delta = -0.1; // prepare to zoom out
        }

        initialScale = Math.max(0.44, Math.min(2, initialScale + delta)); // limits the zoom between 0.44 and 2
        fullPaper.scale(initialScale, initialScale); // apply scale
    });

    // handle panning
    let isPanning = false;
    let lastX = 0;
    let lastY = 0;

    bigCanvas.addEventListener('mousedown', (event) =>{ // when the user presses down on the canvas
        isPanning = true; // set panning to true

        // store the click events' axis position
        lastX = event.clientX; 
        lastY = event.clientY; 
    });

    bigCanvas.addEventListener('mousemove', (event) =>{ // when the user moves the mouse within the canvas
        if (isPanning){ // if the user has clicked onto the canvas

            // calculate the axis change from the click event to the last known value
            let dx = event.clientX - lastX;
            let dy = event.clientY - lastY;

            // update the last known axis change from the click event
            lastX = event.clientX;
            lastY = event.clientY;

            // update the change
            initialX += dx;
            initialY += dy;
            fullPaper.translate(initialX, initialY); // pan to the change
        } else{
            return false;
        }
    });

    bigCanvas.addEventListener('mouseup', () => isPanning = false); // when the user releases their mouse click, set the panning flag to false
}



function closeBigCanvas(){
    const modal = document.getElementById('canvas-modal'); // grabbing the hidden modal
    modal.style.display = 'none' // enabling it to be hidden
}

function showMenu(){
    const sideMenu = document.getElementById('side-menu'); // grabbing the side bar
    sideMenu.style.display = 'block' // enabling it to be shown
}

function hideMenu(){
    const menu = document.getElementById('side-menu'); // grabbing the side bar
    menu.style.display = 'none' // hiding the side bar
}



