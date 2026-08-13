function getTimeformat(tf24) {

    return tf24 ? {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    } : {
        hour: 'numeric',
        minute: '2-digit',
        meridiem: 'narrow'
    };
}
