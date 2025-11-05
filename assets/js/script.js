// assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Form validation for booking form
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(event) {
            const checkinDate = new Date(document.getElementById('checkin_date').value);
            const checkoutDate = new Date(document.getElementById('checkout_date').value);
            const today = new Date();

            // Check if check-in date is before today
            if (checkinDate < today) {
                alert('Check-in date cannot be in the past.');
                event.preventDefault();
                return;
            }

            // Check if check-out date is after check-in date
            if (checkoutDate <= checkinDate) {
                alert('Check-out date must be after check-in date.');
                event.preventDefault();
                return;
            }
        });
    }

    // Dynamic content updates can be added here
});