
function slick_slider(){
    jQuery('.works-slideshow .slide-item').each(function() {
        var slider = jQuery(this);
        slider.slick({
            arrows: true,
            infinite: true,
            rtl: true,
            centerMode: true,
            // variableWidth: true,
            centerPadding: '60px',
            slidesToShow: 4,
            slidesToScroll: 1,
            speed: 500,
            responsive: [{
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    });
}