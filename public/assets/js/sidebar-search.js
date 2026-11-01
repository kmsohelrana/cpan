$(function() {
    const $input = $('#sidebar-menu-search');
    const $nav = $('#sidebar .nav-main');
    if (!$input.length || !$nav.length) return;

    const $wrapper = $input.parent();

    const $results = $('<div>', {
        id: 'sidebar-menu-search-results',
        class: 'list-group shadow-lg rounded'
    }).css({
        position: 'absolute',
        top: '100%',
        left: 0,
        marginTop: '0.5rem',
        minWidth: '280px',
        zIndex: 1050,
        maxHeight: '320px',
        overflowX: 'hidden',
        overflowY: 'auto',
        display: 'none'
    }).appendTo($wrapper);

    // Build a flat, searchable index of real menu links (skip submenu togglers, which just have href="#").
    const items = [];
    $nav.find('a.nav-main-link').each(function() {
        const $link = $(this);
        const href = $link.attr('href') || '';
        if (!href || href === '#' || href.startsWith('javascript:')) return;

        const label = $.trim($link.find('.nav-main-link-name').text());
        if (!label) return;

        const $submenu = $link.closest('.nav-main-submenu');
        let group = '';
        if ($submenu.length) {
            const $groupLink = $submenu.closest('.nav-main-item').find('> a.nav-main-link > .nav-main-link-name');
            group = $.trim($groupLink.text());
        }

        items.push({ label: label, href: href, group: group });
    });

    let activeIndex = -1;

    function renderResults(matches) {
        $results.empty();
        activeIndex = -1;

        if (!matches.length) {
            $results.hide();
            return;
        }

        matches.slice(0, 10).forEach(function(item) {
            $('<a>', {
                href: item.href,
                class: 'list-group-item list-group-item-action py-2',
                html: '<span class="fw-medium">' + item.label + '</span>' +
                    (item.group ? ' <span class="text-muted fs-xs">in ' + item.group + '</span>' : '')
            }).appendTo($results);
        });

        $results.show();
    }

    function highlight(index) {
        const $options = $results.find('.list-group-item');
        $options.removeClass('active');
        if (index >= 0 && index < $options.length) {
            const target = $options.eq(index).addClass('active')[0];
            target.scrollIntoView({ block: 'nearest' });
        }
        activeIndex = index;
    }

    $input.on('input', function() {
        const query = $.trim($(this).val()).toLowerCase();
        if (!query) {
            $results.hide();
            return;
        }
        const matches = items.filter(function(item) {
            return item.label.toLowerCase().includes(query) || item.group.toLowerCase().includes(query);
        });
        renderResults(matches);
    });

    $input.on('focus', function() {
        if ($.trim($(this).val()) && $results.children().length) {
            $results.show();
        }
    });

    $input.on('keydown', function(e) {
        const $options = $results.find('.list-group-item');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if ($options.length) highlight((activeIndex + 1) % $options.length);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if ($options.length) highlight((activeIndex - 1 + $options.length) % $options.length);
        } else if (e.key === 'Enter') {
            const $target = activeIndex >= 0 ? $options.eq(activeIndex) : $options.eq(0);
            if ($target.length) {
                e.preventDefault();
                window.location.href = $target.attr('href');
            }
        } else if (e.key === 'Escape') {
            $(this).val('');
            $results.hide();
            $(this).blur();
        }
    });

    $(document).on('click', function(e) {
        if (!$.contains($wrapper[0], e.target)) {
            $results.hide();
        }
    });
});
