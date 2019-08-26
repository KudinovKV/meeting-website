

$('.futurepartner').slick({
    adaptiveHeight: false,
    variableWidth: true,
    autoplay: false,
    dots: true,
    arrows: false,
    centerMode: true,
    infinite: false,
    swipe: true,
    edgeFriction: 0.5
});

var currentSlide = -1;

$('.futurepartner').on('swipe', function(event, slick, direction){
    // Get the current slide
    var nextSlide = $('.futurepartner').slick('slickCurrentSlide');
    if(currentSlide == -1 )
    {
        currentSlide = nextSlide;
    }
    else if(currentSlide == nextSlide && direction == 'left')
    {
        //window.location.href = "like.php";
        alert('LIKE');
    }
    else if(currentSlide == nextSlide && direction == 'right')
    {
      alert('DISLIKE');
    }
    else{
      currentSlide = nextSlide;
    }
});