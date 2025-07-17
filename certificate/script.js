// Get student name from URL parameters
const urlParams = new URLSearchParams(window.location.search);
const studentName = urlParams.get('student');

// If student name is provided, update the input field
if (studentName) {
    document.getElementById('studentName').value = decodeURIComponent(studentName);
}

// Make all inputs read-only
document.querySelectorAll('.editable-input').forEach(input => {
    input.readOnly = true;
});

// Currently no advanced logic, but allows for extension
document.querySelectorAll(".editable-input").forEach(input => {
  input.addEventListener("input", () => {
    console.log(`${input.id} updated: ${input.value}`);
  });
});
