

$.ajax({
    type: "POST",
    url: "fururepartner-handler.php",
    data:"ACTION=GETID",
    success:function(id)
    {
        var idToShow = Number(id)
        if(isNaN(idToShow))
        {
            // This is error
            alert(id);
            window.location.href = "index.php";
        }
        getPaths(id);     
    }
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

function initSlider(){
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
}

function getPaths(id){
    $.ajax({
            type: "POST",
            url: "fururepartner-handler.php",
            data:{
                ACTION: "GETPATHS" ,
                ID: id
            },
            success:function(PathsToShow)
            {
                CorrectPaths = JSON.parse(PathsToShow);
                // TODO : Добавить проверку полученного значения
                drawProfilePhotos(id , CorrectPaths , Object.keys(CorrectPaths).length);
            }
      });
}


function drawProfilePhotos(CurrentID , Paths , Length) {
    var div_futurepartner = document.getElementById("main");

    var keys = Object.keys(Paths);
    for (var key in keys) 
    {
        // image
        var div_slide = document.createElement("div");
        div_slide.className = "slide";
        div_futurepartner.appendChild(div_slide);
    
        var div_slideimage = document.createElement("div");
        div_slideimage.className = "slideimage";
        div_slide.appendChild(div_slideimage);
        var img = document.createElement("img");
        img.src = 'images/'+ CurrentID + '/' + Paths[keys[key]];
        div_slideimage.appendChild(img);
        // description
        var div_slidetext = document.createElement("div");
        div_slidetext.className = "slidetext";
        div_slide.appendChild(div_slidetext);

        var div_slideheading = document.createElement("h3");
        div_slideheading.className = "slideheading";
        div_slidetext.appendChild(div_slideheading);
        div_slideheading.innerHTML = "Slide " + key;

        var div_slidedescription = document.createElement("p");
        div_slidedescription.className = "slidedescription";
        div_slidetext.appendChild(div_slidedescription);
        div_slidedescription.innerHTML = "Description slide " + key;
    }
    
    initSlider();
}