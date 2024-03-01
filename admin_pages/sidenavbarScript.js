    /*===== LINK ACTIVE =====*/
    const linkColor = document.querySelectorAll('.nav_link');

    function colorLink() {
        linkColor.forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    }

    linkColor.forEach(l => l.addEventListener('click', colorLink));

    // Keep the active class for the current page
    const currentPage = window.location.href;
    linkColor.forEach(link => {
        const linkPath = link.getAttribute('href'); // Get the href attribute
        if (currentPage.includes(linkPath) && linkPath !== '#') {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });