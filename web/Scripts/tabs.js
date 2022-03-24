var buttons = document.querySelectorAll('.tab-button');
var title = document.getElementById("title");

buttons.forEach((button) => {
    button.addEventListener('click', () => {
        changeSelected(button);
    });
});

function changeSelected(selected) {
    buttons.forEach((button) => {
        var field = document.getElementById(button.dataset.id);
        
        if (button == selected) { 
            button.classList.add("selected"); 
            title.textContent = button.dataset.title;
            
            field.classList.remove("hidden");
        }
        else {
            button.classList.remove("selected");
            field.classList.add("hidden");
        }
    });
}