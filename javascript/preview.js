var numchecked = 0;
var questionnum = $("#qnum").html();
var max = $(".mark").attr('title');
var totalq = $("#totalq").html();
var questionid = $(".questiontext").attr('id');

var audio;

var process = false;
var proceed = false;

function audioCompatible(){
    audioElement = document.createElement('audio');
    var canPlayMP4 = audioElement.canPlayType("audio/mpeg");
    var canPlayOGG = audioElement.canPlayType("audio/ogg");
    if(canPlayMP4.match(/maybe|probably/i) || canPlayOGG.match(/maybe|probably/i)){
        return true;
    }
    else{
        return false;
    }
}

$(".sound").click(function(event){
    audioid = event.target.id.replace('audioanswer', '');
    
    if(audioCompatible()){
        if(audio != undefined){audio.pause();}
        audio = document.getElementById('audio' + audioid);
        
        initAudio();
        audio.currentTime = 0;
        audio.play();
    }
});

//this function should be called on a click event handler otherwise video & audio won't start loading on iOS
function initAudio(){
    audio.play(); //start loading, didn't used `.load()` since it causes problems with the `ended` event
    
    if(audio.readyState !== 4){ //HAVE_ENOUGH_DATA
        audio.addEventListener('canplaythrough', onCanPlay, false);
        audio.addEventListener('load', onCanPlay, false); //add load event as well to avoid errors, sometimes 'canplaythrough' won't dispatch.
        setTimeout(function(){
            audio.pause(); //block play so it buffers before playing
        }, 200); //it needs to be after a delay otherwise it doesn't work properly.
    }
}

$(".audioswitch").click(function(){
    if($(".audioswitch").hasClass('audioon')){
        $(".audioswitch").addClass('audiooff').removeClass('audioon');
        $.get("<?php echo($page); ?>?audio=on", function(){questionData(questionid);});
    }
    else{
        $(".audioswitch").addClass('audioon').removeClass('audiooff');
        $.get("<?php echo($page); ?>?audio=off", function(){questionData(questionid);});
    }
});

$(".prevquestion").click(function(){
    //if(numchecked == max || proceed == true){
        if($(this).attr('id') == 'none'){
            alert('no more incomplete');
        }
        else{
            moveQuestion($(".prevquestion").attr('id'));
        }
    /*}
    else{
        flashMark();
        $(".signal").removeClass("signalincorrect signalcorrect").addClass("signalunattempted");
        proceed = true;
    }*/
});

$(".nextquestion").click(function(){
    //if(numchecked == max || proceed == true){
        if($(this).attr('id') == 'none'){
            alert('no more incomplete');
        }
        else{
            moveQuestion($(".nextquestion").attr('id'));
        }
    /*}
    else{
        flashMark();
        $(".signal").removeClass("signalincorrect signalcorrect").addClass("signalunattempted");
        proceed = true;
    }*/
});

$(".hint").click(function(){
    $(this).toggleClass("studyon");
    $(".showhint").slideToggle('slow', function(){
        if($(".showhint").is(":visible")){
            $(document).scrollTop(500);
        }
    });
    $.get("<?php echo($page); ?>?hint=true");
});

$(".skipcorrect").click(function(){
    if($(this).hasClass("flagged")){
        removeCookie('skipCorrect');
    }
    else{
        setCookie('skipCorrect', '1');
    }
    questionData($(".questiontext").attr('id'));
});

function questionData(question){
    $.get("<?php echo($page); ?>?question=" + question, function(data){
        $("#question").html(data.html);
        $("#qnum").html(data.questionnum);
        //$('html, body').animate({ scrollTop: 0 }, 0);
    }, "json");
}

function moveQuestion(question){
    if(process == false){
        questionData(question);
    }
    else{
        setTimeout(function(){
            moveQuestion(question);
        }, 100);
    }
}

function setCookie(cookie_name, value){
    document.cookie = cookie_name + "=" + value + "; path=/";
}

function removeCookie(cookie_name){
    var now = new Date();
    var expirationDate = new Date();

    expirationDate.setDate(now.getDate() - 7);
    document.cookie = cookie_name + "=; path=/; expires=" + expirationDate.toUTCString();
}

/*function flashMark(){
    $(".mark").fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120);    
}*/

/*function toomanymarked(max){
    alert("You can only select " + max + " answer please un-check one option if you wish to select this answer!");
}

window.onbeforeunload = function(){
    $.get("<?php echo($page); ?>?update=true");
    return "Are you sure you want to leave the learning section?";
};*/