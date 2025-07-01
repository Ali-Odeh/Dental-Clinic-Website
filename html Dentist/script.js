/* ---------------- Booking Section ---------------- */
document.getElementById("booking-form").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent page reload on form submission

    // Display confirmation message
    document.getElementById("confirmation").style.display = "block";

    // Clear the form inputs
    this.reset();
});


/* ---------------- Sign in / Sign up ---------------- */
document.querySelectorAll("form").forEach(form => {
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        alert("Form submitted successfully!"); // Replace with actual backend integration
        form.reset();
    });
});


/* ---------------- Consultation Section ---------------- */
document.getElementById("consultation-form").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent page reload
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;

    // Basic check
    if (name && email) {
        alert(`Thank you, ${name}! Your consultation request has been submitted.`);
        document.getElementById("consultation-form").reset();
        document.getElementById("confirmation").style.display = "block";
    }
});


/* ---------------- Gallery Section ---------------- */




/* ---------------- for index page ---------------- */



