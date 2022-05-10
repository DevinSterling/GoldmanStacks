function printSelected(elementId) {
    const page = document.body.innerHTML;
    const content = document.getElementById(elementId).innerHTML;
    
    // Selected content only when printing
    document.body.innerHTML = content;
    window.print();
    
    // Restore original content
    document.body.innerHTML = page;
}
