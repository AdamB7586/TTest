var numchecked = 0;
var questionnum = $("#qnum").html();
var max = $(".mark").attr('title');
var totalq = $("#totalq").html();
var questionid = $(".questiontext").attr('id');

var correctcheck = false;
if($(".signal").hasClass('signalcorrect') || $(".signal").hasClass('signalincorrect')){correctcheck = true;}
else{correctcheck = false;}

var voicePitch = 1;
var voiceRate = 1;

var process = false;
var proceed = false;

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
            $("div").removeClass("selected selectedcorrect selectedincorrect");
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
        $("div").removeClass("selected imgselected selectedcorrect selectedincorrect imgcorrect imgincorrect");
        $(this).toggleClass("selected imgselected");
        markAnswer($(this).attr('id'), questionid, remove, true);
    }
    else{
        numchecked--;
        process = false;
    }
});

function markAnswer(answer, question, remove, replace){
    correctcheck = false;
    if(process == false){
        process = true;
        $(".selectedcorrect").removeClass('selectedcorrect');
        $(".selectedincorrect").removeClass('selectedincorrect');
        $(".check .btn-icon").removeClass('fa-times fa-check').addClass('fa-question'); 
        $(".check .btn-text").html(' Check Answer');
        if(remove == true){
            $(".check").removeClass("recheck checkcorrect checkincorrect");
            $(".signal").removeClass("signalincorrect signalcorrect").addClass("signalunattempted");
            $.get("<?php echo($page); ?>?remove=" + answer + "&prim=" + question, function(data){process = false;});
        }
        else if(replace == false){
            $.get("<?php echo($page); ?>?add=" + answer + "&prim=" + question, function(data){
                if(numchecked == max){checkCorrect(question);}
                $(".signal").removeClass("signalincorrect signalcorrect").addClass("signalunattempted");
                process = false;
            });
        }
        else{
            $.get("<?php echo($page); ?>?replace=" + answer + "&prim=" + question, function(data){
                if(numchecked == max){checkCorrect(question);}
                $(".signal").removeClass("signalincorrect signalcorrect").addClass("signalunattempted");
                process = false;
            });
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
            if(correctcheck == true || numchecked != max){
                moveQuestion($(".prevquestion").attr('id'));
            }
            else{
                checkCorrect(questionid);
            }
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
            if(correctcheck == true || numchecked != max){
                moveQuestion($(".nextquestion").attr('id'));
            }
            else{
                checkCorrect(questionid);
            }
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
    $(".showhint").toggleClass("visable");
    $.get("<?php echo($page); ?>?hint=true");
});

$(".check").click(function(){
    checkCorrect(questionid);
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

function checkCorrect(question){
    $(".check").removeClass("recheck");
    if(process == false){
        if(numchecked == max){
            $.getJSON("<?php echo($page); ?>?check=" + question, function(data){
                if(data === 'CORRECT'){
                    $(".answer").removeClass('selectedcorrect selectedincorrect'); 
                    $(".selected").removeClass("selectedincorrect").addClass("selectedcorrect");
                    $(".signal").removeClass("signalunattempted signalincorrect").addClass("signalcorrect");
                    $(".check").removeClass("recheck checkincorrect").addClass("checkcorrect");
                    $(".check .btn-icon").removeClass('fa-times fa-question').addClass('fa-check');
                    $(".check .btn-text").html(' Correct');
                    correctcheck = true;
                }
                else{
                    $(".answer").removeClass('selectedcorrect selectedincorrect');
                    $(".selected").removeClass("selectedcorrect").addClass("selectedincorrect");
                    $(".signal").removeClass("signalunattempted signalcorrect").addClass("signalincorrect");
                    $(".check").removeClass("recheck checkcorrect").addClass("checkincorrect");
                    $(".check .btn-icon").removeClass('fa-check fa-question').addClass('fa-times');
                    $(".check .btn-text").html(' Incorrect');
                    correctcheck = true;
                }
            });
        }
        else{
            $(".selectedincorrect, .selectedcorrect").removeClass("selected selectedincorrect selectedcorrect");
            $(".answer").removeClass("selected selectedincorrect selectedcorrect");
            $(".signal").removeClass("signalincorrect signalcorrect").addClass("signalunattempted");
            $(".check").removeClass("recheck checkcorrect checkincorrect");
            $(".check .btn-icon").removeClass('fa-times fa-check').addClass('fa-question');
            $(".check .btn-text").html(' Check Answer');
            correctcheck = true;
        }
    }
    else{
        setTimeout(function(){
            checkCorrect(question);
        }, 100);
    }
}

function questionData(question){
    $.getJSON("<?php echo($page); ?>?question=" + question, function(data){
        $("#question").html(data.html);
        $("#qnum").html(data.questionnum);
        //$('html, body').animate({ scrollTop: 0 }, 0);
    });
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

function checkmarked(num){
    if(num > max){
        //if(num == 2){
            return 'REPLACE';
        /*}
        else{
            toomanymarked(max);
        }*/
    }
    else{
        return 'OK';
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