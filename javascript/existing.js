var countstart = false;
$("#time").html('00:00');
$("#qnum").html('0');

$(".continue").click(function(){
    var number = $(".timeremaining").data('time');
    $.get('<?php echo($page); ?>?question=' + $(".continue").attr('id'), function(data){
        $("#qnum").html(data.questionnum);
        $("#question").html(data.html);
        countdown(number);
    }, "json");
});

$(".newtest").click(function(){
    $.get('<?php echo($page); ?>?startnew=true', function(){
        location.reload();
    });
});