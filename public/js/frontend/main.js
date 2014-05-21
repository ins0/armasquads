$(document).ready(function(){

    var confirmDelete = false;
    $('.delete-confirm').click(function(event){
        event.preventDefault();

        $('div#delete-confirm').on($.modal.BLOCK, function(){
            $(this).find("[rel^='modal:confirm']").click(function(e){
                e.preventDefault();
                confirmDelete = true;

            });
        }).modal();
    });

});