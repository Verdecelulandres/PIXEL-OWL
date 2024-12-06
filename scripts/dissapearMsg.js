document.addEventListener('DOMContentLoaded', function () {
    const successMsg = document.getElementById('successMsg');
    const errorMsg = document.getElementById('errorMsg');
    if (successMsg) {
        setTimeout(() => {
            successMsg.style.display = 'none'; 
        }, 5000);
    } else if (errorMsg) {
        setTimeout(() => {
            errorMsg.style.display = 'none'; 
        }, 5000);
    }
});