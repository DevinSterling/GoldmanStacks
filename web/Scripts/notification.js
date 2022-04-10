/* Notification Element */
let notification = document.getElementById('notification');
let notificationText = document.getElementById('notification-text');
let notificationIcon = document.getElementById('notification-icon');

function setSuccessNotification(message) {
    notificationText.textContent = message;
    if (!notification.classList.contains('success')) {
        notification.classList.add('success');
        notification.classList.remove('failure');
        notificationIcon.classList.add('fa-check');
        notificationIcon.classList.remove('fa-times');
    }
}

function setFailNotification(message) {
    notificationText.textContent = message;
    if (!notification.classList.contains('failure')) {
        notification.classList.add('failure');
        notification.classList.remove('success');
        notificationIcon.classList.add('fa-times');
        notificationIcon.classList.remove('fa-check');
    }
}

function hideNotification() {
    notification.classList.add('collapse');
}
