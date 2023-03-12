const elements = document.querySelectorAll(".btn");
var fontSize = 1.8;

// JavaScript behind the rich text editor in the send email menu bar
elements.forEach(element => {
    element.addEventListener("click", () => {
        let command = element.dataset["element"];

        if (command == "bold" || command == "italic" || command == "underline" ) {
            document.execCommand(command, false, null);
        }

        else if (command == "link") {
            var text = prompt("Add Summary of Link.");
            var URL = prompt("URL");
            var href = document.createElement("a");
            href.href = URL;
            var button =  document.createElement("button");
            button.innerText = text;
            button.type = "button";
            button.onclick = "location.replace('" + URL +"');";
            button.id = "email-button";
            button.style.borderRadius = ".7em";
            button.style.backgroundColor = "white";
            button.style.borderColor = "orange";
            button.style.width = "7em";
            button.style.height = "3em";

            href.appendChild(button);
            var div = document.getElementById("textBox");
            div.appendChild(href);
        }

        else if (command == "insertImage") {
            let URL = prompt("URL of Image", "http://");
            var img = document.createElement("img");
            img.src = URL;
            img.alt = "IMG";
            img.style.width = "20em";

            var div = document.getElementById("textBox");
            div.appendChild(img);

        }

        
    });
});

document.getElementById("arrowUp").addEventListener("click", () => {
    textBoxValue = document.getElementById("fontText").value.replace(/ /g,"")
    if(!isNaN(textBoxValue)) {
        fontSize = textBoxValue;
    }

    document.getElementById("fontText").value = fontSize;

    document.getElementById("textBox").style.fontSize = fontSize +"em";
});


document.getElementById("blue").addEventListener("click", () => {
    changeColor("#0063AA");

});

document.getElementById("black").addEventListener("click", () => {
    changeColor("#000000");

});

document.getElementById("orange").addEventListener("click", () => {
    changeColor("#FFA400");

});

document.getElementById("red").addEventListener("click", () => {
    changeColor("#FF0000");

});

function changeColor(color){
    document.execCommand("foreColor", false, color);
}


// Prevent a user from submitting the form via enter
$(document).ready(function() {
    $(window).keydown(function(event){
      if(event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });
  });