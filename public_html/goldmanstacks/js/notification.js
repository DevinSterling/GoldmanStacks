/* Notification Element */
let notification = document.getElementById('notification');
let notificationText = document.getElementById('notification-text');
let notificationIcon = document.getElementById('notification-icon');

function setSuccessNotification(message, id = null) {
    if (id !== null) {
        let notification = document.getElementById(id);
        let notificationText = document.getElementById(notification.dataset.message);
        let notificationIcon = document.getElementById(notification.dataset.icon);
        
        notificationText.textContent = message;
        
        if (!notification.classList.contains('success')) {
            notification.classList.add('success');
            notification.classList.remove('failure');
            notificationIcon.classList.add('fa-check');
            notificationIcon.classList.remove('fa-times');
        }
        
    /* Default */
    } else {
        notificationText.textContent = message;
        
        if (!notification.classList.contains('success')) {
            notification.classList.add('success');
            notification.classList.remove('failure');
            notificationIcon.classList.add('fa-check');
            notificationIcon.classList.remove('fa-times');
        }
    }
}

function setFailNotification(message, id = null) {
    if (id !== null) {
        let notification = document.getElementById(id);
        let notificationText = document.getElementById(notification.dataset.message);
        let notificationIcon = document.getElementById(notification.dataset.icon);
        
        notificationText.textContent = message;
        
        if (!notification.classList.contains('failure')) {
            notification.classList.add('failure');
            notification.classList.remove('success');
            notificationIcon.classList.add('fa-times');
            notificationIcon.classList.remove('fa-check');
        }
    /* Default */
    } else {
        notificationText.textContent = message;
        
        if (!notification.classList.contains('failure')) {
            notification.classList.add('failure');
            notification.classList.remove('success');
            notificationIcon.classList.add('fa-times');
            notificationIcon.classList.remove('fa-check');
        }
    }
}

function showNotification(id = null) {
    if (id !== null) {
        let notification = document.getElementById(id);
        
        if (notification.classList.contains('collapse')) notification.classList.remove('collapse');
    
    /* Default */
    } else {
        if (notification.classList.contains('collapse')) notification.classList.remove('collapse');
    }
}

function hideNotification(id = null) {
    if (id !== null) {
        let notification = document.getElementById(id);
        
        if (!notification.classList.contains('collapse')) notification.classList.add('collapse');

    /* Default */
    } else {
        if (!notification.classList.contains('collapse')) notification.classList.add('collapse');
    }
}
