var numchecked = 0;
var questionnum = $("#qnum").html();
var max = $(".mark").attr('title');
var totalq = $("#totalq").html();
var questionid = $(".questiontext").attr('id');
var countstart = true;

var voicePitch = 1;
var voiceRate = 1;

var process = false;
var proceed = false;

var testended = false;

var selectednum = $(".selected").length;
var imageselectednum = $(".imgselected").length;
if((selectednum || imageselectednum)!= 0){
    if(selectednum != 0){numchecked = selectednum;}
    else{numchecked = imageselectednum;}
}

$(".answer").click(function(event){
    var clicked = event.target;

    if(!$(clicked).hasClass("sound")){ // Makes sure not to select if sound element clicked
        if($(this).is('.selected')){
            numchecked--;
            var remove = true;
        }else{
            numchecked++;
        }
        
        var checkstatus = checkmarked(numchecked);
        if(checkstatus == 'OK'){
            $(this).toggleClass("selected");
            markAnswer($(this).attr('id'), questionid, remove, false);
        }
        else if(checkstatus == 'REPLACE'){
            numchecked--;
            $("div").removeClass("selected");
            $(this).toggleClass("selected");
            markAnswer($(this).attr('id'), questionid, remove, true);
        }
        else{
            numchecked--;
            process = false;
        }
    }
});

$(".answerimage").click(function(){
    if($(this).is('.imgselected')){
        numchecked--;
        var remove = true;
    }else{
        numchecked++;
    }
    
    var checkstatus = checkmarked(numchecked);
    if(checkstatus == 'OK'){
        $(this).toggleClass("selected imgselected");
        markAnswer($(this).attr('id'), questionid, remove, false);
    }
    else if(checkstatus == 'REPLACE'){
        numchecked--;
        $("div").removeClass("selected imgselected");
        $(this).toggleClass("selected imgselected");
        markAnswer($(this).attr('id'), questionid, remove, true);
    }
    else{
        numchecked--;
        process = false;
    }
});

function markAnswer(answer, question, remove, replace){
    if(process == false){
        process = true;
        if(remove == true){
            $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&remove=" + answer + "&prim=" + question, function(){process = false;});
        }
        else{
            if(replace == false){
                $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&add=" + answer + "&prim=" + question, function(){process = false;});
            }
            else{
                $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&replace=" + answer + "&prim=" + question, function(){process = false;});
            }
        }
    }
    else{
        setTimeout(function(){
            markAnswer(answer, question, remove, replace);
        }, 100);
    }
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
    if($(".audioswitch").hasClass('audioon')){
        $(".audioswitch").addClass('audiooff').removeClass('audioon');
        $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&audio=on", function(){questionData(questionid);});
    }
    else{
        $(".audioswitch").addClass('audioon').removeClass('audiooff');
        $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&audio=off", function(){questionData(questionid);});
    }
});

$(".prevquestion").click(function(){
    if(numchecked == max || proceed == true){
        moveToPrevious();
    }
    else{
        flashMark();
        proceed = true;
    }
});

$(".nextquestion").click(function(){
    if(numchecked == max || proceed == true){
        moveToNext();
    }
    else{
        flashMark();
        proceed = true;
    }
});

$(".flag").click(function(){
    $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&flag=" + questionid);
    $(this).toggleClass("flagged");
});

$(".review").click(function(){
    $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&review=" + questionid, function(data){
        $("#question").html(data);
    });
});

$(".reviewall").click(function(){
    var firstquestion = $(".reviewall").attr('id');
    $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&reviewonly=all", function(){questionData(firstquestion);});
});

$(".reviewincomplete").click(function(){
    var firstincomplete = $(".reviewincomplete").attr('id');
    if(firstincomplete != 'none'){
        $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&reviewonly=incomplete", function(){questionData(firstincomplete);});
    }
    else{
        alert('No incomplete questions');
    }
});

$(".reviewflagged").click(function(){
    var firstflagged = $(".reviewflagged").attr('id');
    if(firstflagged != 'none'){
        $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&reviewonly=flagged", function(){questionData(firstflagged);});
    }
    else{
        alert('No flagged questions');
    }
});

$(".endtest").click(function(){
    endTest($("#time").html());
});

$("#gohome").click(function(){
    testended = true;
});

function questionData(question){
    $.get("<?php echo($page); ?>?time=" + $("#time").html() + "&question=" + question, function(data){
        $("#question").html(data.html);
        $("#qnum").html(data.questionnum);
        //$('html, body').animate({ scrollTop: 0 }, 0);
   }, "json");
}

function moveToNext(){
    if(process == false){
        questionData($(".nextquestion").attr('id'));
    }
    else{
        setTimeout(function(){
            moveToNext();
        }, 100);
    }
}

function moveToPrevious(){
    if(process == false){
        questionData($(".prevquestion").attr('id'));
    }
    else{
        setTimeout(function(){
            moveToPrevious();
        }, 100);
    }
}

function checkmarked(num){
    if(num > max){
        if(num == 2){
            return 'REPLACE';
        }
        else{
            toomanymarked(max);
        }
    }
    else{
        return 'OK';
    }
}

function flashMark(){
    $(".mark").fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120).fadeOut(120).fadeIn(120);    
}

$(function(){
    var $element = $('.alert');
    function fadeInOut(){
        $element.fadeIn(500, function () {
            $element.fadeOut(500, function () {
                $element.fadeIn(500, function () {
                    setTimeout(fadeInOut, 800);
                });
            });
        });
    }

    fadeInOut();
});

function toomanymarked(max){
    alert("You can only select " + max + " answer please un-check one option if you wish to select this answer!");
}

function endTest(time){
    testended = true;
    clearInterval(countdown);
    $("#questiondata").html('');
    $("#countdown").html('');
    $.get("<?php echo($page); ?>?endtest=true&time=" + time, function(data){
        $("#question").html(data);
	$.get("<?php echo($page); ?>?linkdata=true");
    });
}

window.onbeforeunload = function(){
    $.get("<?php echo($page); ?>?saveinfo=true");
    $('a').click(function(){testended = true;});
    if(testended === false){
        return "The results data for the questions you have answered will be lost!";
    }
};