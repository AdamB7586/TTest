var countstart = false;
$("#time").html('00:00');
$("#qnum").html('0');

$(".continue").click(function(){
    var number = parseInt($(".timeremaining").attr('id'));
    $.get('<?php echo($page); ?>?question=' + $(".continue").attr('id'), function(data){
        data = $.parseJSON(data);
        $("#qnum").html(data.questionnum);
        $("#question").html(data.html);
        countdown(number);
    });
});

$(".newtest").click(function(){
    $.get('<?php echo($page); ?>?startnew=true', function(){
        location.reload();
    });
});