$(document).ready(function () {

    // delete confirm
    var deleteConfirmMessage = $('script[data-delete-confirm]').data('delete-confirm');
    $('.delete-confirm').on('click', function (event) {
        if (!window.confirm(deleteConfirmMessage)) {
            event.preventDefault();
        }
    });

    // squad link full copy
    $('#squads div.squad .squad-xml').on('click', function () {
        $(this).select();
    });

    // squad search box
    $('#squadSearch').on('keyup', function () {
        var searchTerm = $(this).val().toLowerCase();
        var squads = $('#squads div.squad');
        var display = 0;

        squads.each(function () {
            var visible = $(this).find('b.squad-name').text().toLowerCase().indexOf(searchTerm) !== -1;
            $(this).toggle(visible);
            display += visible ? 1 : 0;
        });

        $('#squad-count-display').text(display);
    });

    // member search box
    $('#memberSearch').on('keyup', function () {
        var searchTerm = $(this).val().toLowerCase();
        var members = $('div.members div.member');
        var display = 0;

        members.each(function () {
            var playerUid = $(this).find('input.uuid').val().toLowerCase();
            var playerUsername = $(this).find('input.username').val().toLowerCase();
            var visible = playerUid.indexOf(searchTerm) !== -1 || playerUsername.indexOf(searchTerm) !== -1;
            $(this).toggle(visible);
            display += visible ? 1 : 0;
        });

        $('#member-count-display').text(display);
    });

    // add and remove members
    var memberIndex = $('.members-collection > div.member').length;

    function updateMemberCount() {
        var count = $('.members-collection > div.member').length;
        $('span.member-count, #member-count-display').text(count);
        $('.members p.no-member-hint').toggle(count === 0);
    }

    $('.add-member').on('click', function () {
        var template = $('#member-template').html().replace(/__index__/g, String(memberIndex++));
        var member = $(template).hide();
        $('.members-collection').append(member);
        member.slideDown();
        updateMemberCount();
    });

    $('.members-collection').on('click', '.remove-member', function () {
        $(this).closest('div.member').slideUp(function () {
            $(this).remove();
            updateMemberCount();
        });
    });
});
