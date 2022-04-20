async function getJson(url, formData) {
    /* Json to retrieve */
    let json = null;
    
    /* Create request */
    request = new Request(url, {
        body: formData,
        method: 'POST',
    });
    
    /* Retrieve response */
    const response = await fetch(request).catch((error) => {
        /* Returns null on network error */
        return null;
    });
    
    /* Check response status code */
    if (response.status >= 200 && response.status <= 299) {
        /* Retrieve json */
        json = response.json();
    }
    
    return json;
}

function isEmptyJson(json) {
    return json === null;
}
