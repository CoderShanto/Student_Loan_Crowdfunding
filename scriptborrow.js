// Add interactivity to Borrow button
document.querySelectorAll('.borrow-btn').forEach(button => {
    button.addEventListener('click', () => {
        alert('Borrow Request Sent!');
    });
});
