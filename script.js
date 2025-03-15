let slideIndex = 0;

function moveSlide(n) {
    slideIndex += n;
    const slides = document.querySelectorAll('.carousel-item');
    if (slideIndex < 0) slideIndex = slides.length - 1;
    if (slideIndex >= slides.length) slideIndex = 0;
    document.querySelector('.carousel').style.transform = `translateX(-${100 * slideIndex}%)`;
}

// Automatic sliding every 5 seconds
setInterval(() => {
    moveSlide(1);
}, 5000);
