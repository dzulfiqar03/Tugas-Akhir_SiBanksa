$(document).ready(function () {

    const $card = $('#card_pengajuan');

    const $icon = $('.card-header').find('i');

    $(document).on('click', '.btn-tool', function () {
        // toggle collapsed state
        const isCollapsed = $card.hasClass('collapsed-card');

        if (isCollapsed) {
            // buka card
            $card.removeClass('collapsed-card');
            $card.find('.card-body').slideDown(200);
            $icon.removeClass('fa-plus').addClass('fa-minus');
            $card.find('.btn-cancel').removeClass('hidden');
        } else {
            // tutup card
            $card.addClass('collapsed-card');
            $card.find('.card-body').slideUp(200);
            $icon.removeClass('fa-minus').addClass('fa-plus');
            $card.find('.btn-cancel').addClass('hidden');
        }
    });
});
