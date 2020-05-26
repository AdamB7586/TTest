var questionnum = $("#qnum").html();
var questionid = $(".questiontext").attr('id');

var testended = false;

var selectednum = $(".selected").length;
var imageselectednum = $(".imgselected").length;
if((selectednum || imageselectednum)!= 0){
    if(selectednum != 0){numchecked = selectednum;}
    else{numchecked = imageselectednum;}
}

$(".sound").click(function(event){
    if ('speechSynthesis' in window) {
        speechSynthesis.cancel();
        var msg = new SpeechSynthesisUtterance();
        var voices = window.speechSynthesis.getVoices();
        msg.voice = voices[0];
        msg.rate = voiceRate;
        msg.pitch = voicePitch;
        msg.text = $('#audio'+$(this).data('audio-id')).text();
        speechSynthesis.speak(msg);
    }
    else{
        alert("Audio is not supported on your browser. Please use a modern browser such as Google Chrome or Firefox!");
    }
});

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