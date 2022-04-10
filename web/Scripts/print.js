function printSelected(elementId) {
    var page = document.body.innerHTML;
    var content = document.getElementById(elementId).innerHTML;
    
    // Selected content only when printing
    document.body.innerHTML = content;
    window.print();
    
    // Restore original content
    document.body.innerHTML = page;
}