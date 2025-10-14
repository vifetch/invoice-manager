const toastElement = document.getElementById('postToast');
if (toastElement) {
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
    });
    toast.show();
}