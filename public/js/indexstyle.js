const imageResize = document.querySelector('.resizeimg')
const boxResponsive = document.querySelector('.content')
const titleResponve = document.querySelector('.titlesmartphone')


window.addEventListener("resize", function() {
    const imgbrk = 1200;
    const imgbrk2 = 530;
    const imgbrk3 = 555;

    if (window.innerWidth <= imgbrk) {
        imageResize.style.width = "75%";
    } if (window.innerWidth >= imgbrk) {
        imageResize.style.width = "50%";
    } if (window.innerWidth <= imgbrk2) {
        imageResize.style.width = "90%";
    } if (window.innerWidth <= imgbrk3) {
        boxResponsive.classList.remove('mt-5')
        boxResponsive.classList.remove('border')
        boxResponsive.classList.remove('rounded')
        titleResponve.classList.add('h3')
    } if (window.innerWidth >= imgbrk3) {
        boxResponsive.classList.add('mt-5')
        boxResponsive.classList.add('border')
        boxResponsive.classList.add('rounded')
        titleResponve.classList.remove('h3')
    }
});

window.addEventListener("load", function() {
    const imgbrk = 1200;
    const imgbrk2 = 530;
    const imgbrk3 = 555;

    if (window.innerWidth <= imgbrk) {
        imageResize.style.width = "75%";
    } if (window.innerWidth >= imgbrk) {
        imageResize.style.width = "50%";
    } if (window.innerWidth <= imgbrk2) {
        imageResize.style.width = "90%";
    } if (window.innerWidth <= imgbrk3) {
        boxResponsive.classList.remove('mt-5')
        boxResponsive.classList.remove('border')
        boxResponsive.classList.remove('rounded')
        titleResponve.classList.add('h3')
    } if (window.innerWidth >= imgbrk3) {
        boxResponsive.classList.add('mt-5')
        boxResponsive.classList.add('border')
        boxResponsive.classList.add('rounded')
        titleResponve.classList.remove('h3')
    }
});