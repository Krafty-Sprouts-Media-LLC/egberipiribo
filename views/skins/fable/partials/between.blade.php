{{--
    What plugins place between a list's items (Teme 1.0's `x-teme::between-items`), after the item just drawn. Put
    it inside a list's loop, right after the item. Takes $after (the item just drawn, counted from 1) and $last
    (whether it is the list's last item). The slot spans a whole row of a grid and hides itself while nothing is
    placed there (see .between-slot in the skin's CSS), so a list with no plugin looks exactly as it did.

    @package egberipiribo
    @since   0.1.0
--}}
<div class="between-slot"><x-teme::between-items :after="$after" :last="$last" /></div>
