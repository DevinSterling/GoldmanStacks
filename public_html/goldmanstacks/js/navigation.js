/* Nvaigation Bar (menubar) functions */

/* Used for navigation bar (menubar) active list item */
const url = window.location.href; // Url of current tab
const li =  document.querySelectorAll(".menuitem a"); // menuitems

/* Used for mobile navigation bar (menubar) */
const toggle = document.querySelector(".menutoggle");
const menu = document.querySelectorAll(".menugroup");

/* Active list item */
li.forEach(function(element) {
    if (url.includes(element.href)) {
        element.closest("li").classList.add("selected-item");
    }
});

/* Toggle mobile menu */
function toggleMenu() {
    if (menu[0].classList.contains("activez")) {
        menu.forEach(function(element){
            element.classList.remove("activez");
        });
        
        // adds the menu (hamburger) icon
        toggle.querySelector("a").innerHTML = "<i class=\"fas fa-bars\"></i>";
    } else {
        menu.forEach(function(element){
            element.classList.add("activez");
        });
        
        // adds the close (x) icon
        toggle.querySelector("a").innerHTML = "<i class=\"fas fa-times\"></i>";
    }
}

/* Event Listener */
toggle.addEventListener("click", toggleMenu, false);