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

    // squad search box
    $('#squadSearch').bind('keyup', function(){
        var searchTerm = $(this).val();
        var squads = $('#squads div.squad');
        var display = squads.length;

        if( searchTerm.length <= 0 )
        {
            squads.show();
            $('#squad-count-display').html(display);
            return;
        }

        display = 0;
        squads.show().each(function(){
            var squadName = $(this).find('b.squad-name').html();
            if( squadName.toLowerCase().indexOf(searchTerm.toLowerCase()) == -1 )
            {
                $(this).hide();
            } else {
                display++;
            }
        });
        $('#squad-count-display').html(display);
    });

    // member search box
    $('#memberSearch').bind('keyup', function(){
        var searchTerm = $(this).val();
        var members = $('div.members div.member');
        var hidden = 0;

        if( searchTerm.length <= 0 )
        {
            members.show();
            $('#member-count-hidden').hide();
            return;
        }

        members.show().each(function(){
            var playerUid = $(this).find('input.uuid').val().toLowerCase();
            var playerUsername = $(this).find('input.username').val().toLowerCase();

            if( playerUid.indexOf(searchTerm) == -1 && playerUsername.indexOf(searchTerm) == -1 )
            {
                hidden++;
                $(this).hide();
            }
        });
        $('#member-count-hidden').html("/ "+ hidden + " Filtered (Not shown)");
    });

});


// add member
function add_member()
{
    // current fields
    var currentCount = $('.members > .members-collection > div.item').length;

    // form template
    var template = $('.template-member').clone();
    template.find('div.panel-body').html(template.data('template') + "<label>* required fields</label>");
    template.removeAttr('data-template');
    template.removeClass('template-member');
    template = template.get(0).outerHTML.replace(/__index__/g, currentCount);

    // update form
    $('.members > .members-collection').append(template);
    $('.members p.no-member-hint').hide();
    $('.members > .members-collection > div').last().stop(true, true).slideDown();

    // update member info
    $('span.member-count').html(currentCount + 1);

    return false;
}
// remove member
function remove_member(obj)
{
    $(obj).parent().parent().slideUp(function(){
        // remove field
        $(this).remove();

        // current member fields
        var currentCount = $('.members > .members-collection > div.item').length;

        // update member info
        $('span.member-count').html(currentCount);

        // show info for empty squad
        if( currentCount == 0 )
        {
            $('.members p.no-member-hint').show();
        }
    });
    return false;
}