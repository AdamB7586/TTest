var questionnum = $("#qnum").html();
var questionid = $(".questiontext").attr('id');

var audio;

var testended = false;

var selectednum = $(".selected").length;
var imageselectednum = $(".imgselected").length;
if((selectednum || imageselectednum)!= 0){
    if(selectednum != 0){numchecked = selectednum;}
    else{numchecked = imageselectednum;}
}

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
    else{
        //alert('Your browser does not support sound');
    }
});

//this function should be called on a click event handler otherwise video & audio won't start loading on iOS
function initAudio(){
    audio.play(); //start loading, didn't used `audio.load()` since it causes problems with the `ended` event
    if(audio.readyState !== 4){ //HAVE_ENOUGH_DATA
        audio.addEventListener('canplaythrough', onCanPlay, false);
        audio.addEventListener('load', onCanPlay, false); //add load event as well to avoid errors, sometimes 'canplaythrough' won't dispatch.
        setTimeout(function(){
            audio.pause(); //block play so it buffers before playing
        }, 1); //it needs to be after a delay otherwise it doesn't work properly.
    }else{
        //audio is ready
    }
}

$(".audioswitch").click(function(){
    if($(".audioswitch").text() == 'Turn Sound ON'){
        $(".audioswitch").addClass('audiooff').removeClass('audioon');
        $(".audioswitch").text('Turn Sound OFF');
        $.get("<?php echo($page); ?>?audio=on", function(){questionData(questionid);});
    }
    else{
        $(".audioswitch").addClass('audioon').removeClass('audiooff');
        $(".audioswitch").text('Turn Sound ON');
        $.get("<?php echo($page); ?>?audio=off", function(){questionData(questionid);});
    }
});

$(".reviewtest").click(function(){
    $.get("<?php echo($page); ?>?reviewonly=answers", function(){
        setTimeout(function(){
            $("#questiondata").html('Question <span id="qnum">1</span> of <span id="totalq">' + $('.numquestions').attr('id') + '</span>');
            $("#questiondata").addClass('questionright');
            questionData($(".reviewtest").attr('id'));
        }, 100);
    });
});

$(".exittest").click(function(){
    testended = true;
    window.location = "<?php echo($exitpage); ?>";
});

$("#gohome").click(function(){
    testended = true;
});

$(".prevquestion").click(function(){
    questionData($(".prevquestion").attr('id'));
});

$(".nextquestion").click(function(){
    questionData($(".nextquestion").attr('id'));
});

$(".questionreview").click(function(event){
    //if($(this).hasClass('incomplete')){
    //    alert('You did not attempt this question, therefore it cannot be reviewed');
    //}
    //else{
        qid = event.target.id;
        questionData(qid);
    //}
});

$(".viewfeedback").click(function(){
    $(".viewfeedback").toggleClass('flagged');
    $(".explanation").slideToggle('slow', function(){
        if($(".explanation").is(":visible")){
            $(document).scrollTop(500);
        }
    });
    $.get("<?php echo($page); ?>?hint=true");
});

$(".endreview").click(function(){
    $.get("<?php echo($page); ?>?endtest=true", function(data){
        $("#questiondata").html('');
        $("#question").html(data);
    });
});

function questionData(question){
    $.get("<?php echo($page); ?>?question=" + question, function(data){
        $("#question").html(data.html);
        $("#qnum").html(data.questionnum);
   }, "json");
}

function endTest(time){
    testended = true;
    clearInterval(countdown);
    $("#questiondata").html('');
    $("#countdown").html('');
    $.get("<?php echo($page); ?>?endtest=true&time=" + time, function(data){
        $("#question").html(data);
    });
}