let buttons = document.querySelectorAll('.tab-button');
let title = document.getElementById("title");

buttons.forEach((button) => {
    button.addEventListener('click', () => {
        changeSelected(button);
    });
});

function changeSelected(selected) {
    if (document.body.contains(document.getElementById('notification'))) {
        let notification = document.getElementById('notification');
        
        if (!notification.classList.contains('collapse')) notification.classList.add('collapse');
    }
    
    buttons.forEach((button) => {
        let field = document.getElementById(button.dataset.id);
        
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
