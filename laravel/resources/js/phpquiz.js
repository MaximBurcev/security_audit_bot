// @todo: Temporary. Refactoring it
$(document).ready(function(){
    let question =1;
    $(document).on('click','.answer_buttons button',function(){

        $(".next_question_buttons").removeClass('hidden');
        $(".answer_buttons").addClass('hidden');
        $(".comment_area").removeClass('hidden');
    });

    $(document).on('click','.next_question_buttons button',function(){
        //alert(question);
        question++;
        $(".next_question_buttons").addClass('hidden');
        $(".answer_buttons").removeClass('hidden');
        $(".comment_area").addClass('hidden');

        $(".comment_area").html($(".a"+question).html());
        $(".question").html($(".q"+question).html());

    });

});
