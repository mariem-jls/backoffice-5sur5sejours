document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('#productCarousel');
    const thumbnails = document.querySelectorAll('.img-thumbnail');

    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => {
            // Remove active class from all thumbnails
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            // Add active class to the clicked thumbnail
            thumbnail.classList.add('active');

            // Move the carousel to the clicked thumbnail's slide
            const carouselInstance = bootstrap.Carousel.getInstance(carousel);
            carouselInstance.to(index);
        });
    });

    // Update thumbnail active state on carousel slide change
    carousel.addEventListener('slide.bs.carousel', (event) => {
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        thumbnails[event.to].classList.add('active');
    });
});
