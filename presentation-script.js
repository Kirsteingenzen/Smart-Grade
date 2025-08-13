let currentSlide = 1
const totalSlides = 12

function showSlide(n) {
  const slides = document.querySelectorAll(".slide")

  if (n > totalSlides) currentSlide = 1
  if (n < 1) currentSlide = totalSlides

  slides.forEach((slide) => slide.classList.remove("active"))
  slides[currentSlide - 1].classList.add("active")

  document.getElementById("slideNumber").textContent = `${currentSlide} / ${totalSlides}`

  // Update navigation buttons
  document.getElementById("prevBtn").disabled = currentSlide === 1
  document.getElementById("nextBtn").disabled = currentSlide === totalSlides
}

function nextSlide() {
  currentSlide++
  showSlide(currentSlide)
}

function previousSlide() {
  currentSlide--
  showSlide(currentSlide)
}

// Keyboard navigation
document.addEventListener("keydown", (event) => {
  if (event.key === "ArrowRight" || event.key === " ") {
    nextSlide()
  } else if (event.key === "ArrowLeft") {
    previousSlide()
  }
})

// Initialize
showSlide(currentSlide)

// Auto-advance slides (optional - uncomment to enable)
// setInterval(() => {
//     if (currentSlide < totalSlides) {
//         nextSlide();
//     }
// }, 10000); // 10 seconds per slide
