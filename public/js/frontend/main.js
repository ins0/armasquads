$(document).ready(function(){

    //modal delete confirm
    $('.delete-confirm, .btn-confirm').click(function(event){
        event.preventDefault();
        var delLnk = $(this).attr('href');
        var delJs = $(this).data('confirm');
        $('div#delete-confirm').on($.modal.BLOCK, function(){
            $(this).find("[rel^='modal:confirm']").click(function(e){
                e.preventDefault();
                if( delJs ) {
                    var returnBool = new Function(delJs)();
                    returnBool == true ? document.location = delLnk : $.modal.close();
                } else {
                    document.location = delLnk;
                }
            });
        }).modal();
    });

});