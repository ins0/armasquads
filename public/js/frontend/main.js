$(document).ready(function(){

    //modal delete confirm
    $('.delete-confirm').click(function(event){
        event.preventDefault();
        var delLnk = $(this).attr('href');
        $('div#delete-confirm').on($.modal.BLOCK, function(){
            $(this).find("[rel^='modal:confirm']").click(function(e){
                e.preventDefault();
                document.location = delLnk;
            });
        }).modal();
    });

});