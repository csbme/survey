var counter = 1;
var limit = 5;

function addInput(divName) {

    if (counter != limit) {

        var newdiv = document.createElement('div');
        newdiv.setAttribute("id", counter);

        newdiv.innerHTML = "Anwort " + (counter + 1) +

            "<br><input type='text' name='answer[]'>" +
            "<input type='checkbox' value='true' style='position: relative; left: 25px' onclick='this.name=\"boolean[\"+ this.parentNode.id + \"]\"'>";

        document.getElementById(divName).appendChild(newdiv);
        counter++;
    }
}


function removeDiv() {
    var index = this.counter - 1;

    if (index > 0){
        document.getElementById(index).remove();
        counter--;
    }
}

