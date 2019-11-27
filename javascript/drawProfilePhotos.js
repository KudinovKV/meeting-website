

var ID;
var CSRFTOCKEN;
var USERIMAGEBYTE;

CreateProfileForm();

var currentSlide = 0;

function SafeSwipe(action){
    if (action == 'LIKE')
    {
        $.ajax(
        {
            type: "POST",
            url: "api/index.php",
            data:{
                apiandfunctionname:"apiphoto_like",
                csrftocken: CSRFTOCKEN,
                genid:ID
            },
            success:function(Answer)
            {
                Msg = JSON.parse(Answer);
                if(!$.isEmptyObject(Msg['response']['result']))
                {
                    if(Msg['response']['result'] == 'MATCH')
                    {
                        alert('Congratulation! You have a match!');
                        UpdateProfileForm();
                        CreateProfileForm();
                    }
                    else {
                        // Uncorrect action => GoNext
                        UpdateProfileForm();
                        CreateProfileForm();    
                    }
                }
                else if(!$.isEmptyObject(Msg['ERROR']))
                {
                    alert(Msg['ERROR']);
                    UpdateProfileForm();
                    CreateProfileForm();
                }
                else{
                    // Uncorrect answer => GoNext
                    UpdateProfileForm();
                    CreateProfileForm();
                }
            }
        });
    }
    else
    {
        UpdateProfileForm();
        CreateProfileForm();    
    }
}

function CreateProfileForm(){
    $.ajax({
        type: "POST",
        url: "api/index.php",
        data:"apiandfunctionname=apiauth_getcsrftocken",
        success:function(jsonObj)
        {
            result = JSON.parse(jsonObj);
            CSRFTOCKEN = result['response']['csrftocken'];
            drawProfilePhotos();
        }
    });
}

function UpdateProfileForm(){
    // Delete div
    var old_div_futurepartner = document.getElementById('main');
    old_div_futurepartner.remove();
    // Create new
    var fathermain = document.getElementById('fathermain');

    var div_futurepartner = document.createElement("div");
    div_futurepartner.className = "futurepartner";
    div_futurepartner.setAttribute("id", "main");
    
    fathermain.appendChild(div_futurepartner);
}


function initSlider(){
    $('.futurepartner').slick(
    {
        adaptiveHeight: false,
        variableWidth: true,
        autoplay: false,
        dots: false,
        arrows: false,
        centerMode: true,
        infinite: false,
        swipe: true,
        edgeFriction: 0.2
    });

    $('.futurepartner').on('swipe', function(event, slick, direction){
        // Get the current slide
        var nextSlide = $('.futurepartner').slick('slickCurrentSlide');
        if(currentSlide == nextSlide && direction == 'left')
        {
            //window.location.href = "like.php";
            SafeSwipe('LIKE');
            currentSlide = 0;
        }
        else if(currentSlide == nextSlide && direction == 'right')
        {
            SafeSwipe('DISLIKE');
            currentSlide = 0;
        }
        else{
          currentSlide = nextSlide;
        }
    });
}


function drawProfilePhotos() {
    $.post(
        "api/index.php",
        {
            apiandfunctionname: "apiphoto_getphoto" ,
            version           : "brawser",
            selectid          : "0",
            csrftocken        : CSRFTOCKEN
        },
        function(Information,status)
            {
                var div_futurepartner = document.getElementById("main");
                var UserObject = JSON.parse(Information);
                var userimages = UserObject['response']['images'];
                var result = UserObject['response']['result'];
                ID = UserObject['response']['genid'];
                if (result != "Failed")
                {
                    userimages.forEach(function(image)
                    {
                        var div_slide = document.createElement("div");
                        div_slide.className = "slide";
                        div_futurepartner.appendChild(div_slide);
                    
                        var div_slideimage = document.createElement("div");
                        div_slideimage.className = "slideimage";
                        div_slide.appendChild(div_slideimage);
                        var img = document.createElement("img");
                        img.src = 'data:image/jpeg;base64,' + image;
                        div_slideimage.appendChild(img);

                        var div_slidetext = document.createElement("div");
                        div_slidetext.className = "slidetext";
                        div_slide.appendChild(div_slidetext);

                        var div_slideheading = document.createElement("h3");
                        div_slideheading.className = "slideheading";
                        div_slidetext.appendChild(div_slideheading);
                        div_slideheading.innerHTML = UserObject['response']['firstname'] + ' ' + UserObject['response']['lastname'];

                        var div_slidedescription = document.createElement("p");
                        div_slidedescription.className = "slidedescription";
                        div_slidetext.appendChild(div_slidedescription);
                        div_slidedescription.innerHTML = UserObject['response']['aboutyou'];                    

                    });
                    initSlider();
                 }
                 else
                 {
                    alert('Sorry! There are nothing for you yet!');

                 }

            }
                // TODO : Добавить проверку полученного значения
      );
}

