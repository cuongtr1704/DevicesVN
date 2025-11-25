/**
 * Main JavaScript File
 */

$(document).ready(function() {
    
    let searchTimeout;
    
    $('#searchInput').on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            $('#searchSuggestions').removeClass('show').empty();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: BASE_URL + 'search/suggestions',
                method: 'GET',
                data: { q: query },
                dataType: 'json',
                success: function(data) {
                    displaySearchSuggestions(data);
                }
            });
        }, 300);
    });
    
    function displaySearchSuggestions(suggestions) {
        const $container = $('#searchSuggestions');
        $container.empty();
        
        if (suggestions.length === 0) {
            $container.removeClass('show');
            return;
        }
        
        suggestions.forEach(function(item) {
            const $item = $('<a>')
                .addClass('search-suggestion-item')
                .attr('href', item.url)
                .append($('<img>').attr('src', item.image).attr('alt', item.name))
                .append(
                    $('<div>')
                        .append($('<div>').css('font-weight', '500').text(item.name))
                        .append($('<div>').css({'color': '#0066cc', 'font-size': '14px'}).text(item.price))
                );
            
            $container.append($item);
        });
        
        $container.addClass('show');
    }
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-form').length) {
            $('#searchSuggestions').removeClass('show');
        }
    });
    
    // Categories dropdown logic - separate mobile and desktop
    
    // Mobile: Click parent link to navigate, click icon to toggle nested
    if (window.innerWidth < 992) {
        // Main categories dropdown toggle
        $('#categoriesDropdown').on('click', function(e) {
            e.preventDefault();
            const $menu = $('#categoriesMenu');
            const $parent = $(this).parent();
            
            $parent.toggleClass('show');
            $menu.toggleClass('show');
        });
        
        // Nested dropdown - click icon to toggle
        $('.navbar-collapse .dropend .nested-toggle-icon').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $parentLink = $(this).closest('.parent-category-link');
            const $dropdownMenu = $parentLink.next('.dropdown-menu');
            const $parentDropend = $(this).closest('.dropend');
            
            // Toggle this dropdown
            $parentDropend.toggleClass('show');
            $dropdownMenu.toggleClass('show');
            
            // Close other nested dropdowns
            $('.navbar-collapse .dropend').not($parentDropend).removeClass('show')
                .find('.dropdown-menu').removeClass('show');
        });
        
        // Parent category link - allow navigation
        $('.navbar-collapse .parent-category-link').on('click', function(e) {
            // Check if click was on the icon
            if ($(e.target).hasClass('nested-toggle-icon')) {
                return false;
            }
            // Otherwise, navigate normally
        });
    }
    
    // Desktop: Click to open/close, with slower auto-close on hover out
    if (window.innerWidth >= 992) {
        let closeTimer;
        
        // Main categories dropdown - click to toggle
        $('#categoriesDropdown').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $menu = $('#categoriesMenu');
            const $parent = $(this).parent();
            
            if ($parent.hasClass('show')) {
                $parent.removeClass('show');
                $menu.removeClass('show');
            } else {
                $parent.addClass('show');
                $menu.addClass('show');
            }
        });
        
        // Hover support - with delay before closing
        $('.navbar .dropdown').hover(
            function() {
                clearTimeout(closeTimer);
                $(this).addClass('show');
                $(this).find('>.dropdown-menu').addClass('show');
            },
            function() {
                const $dropdown = $(this);
                closeTimer = setTimeout(function() {
                    $dropdown.removeClass('show');
                    $dropdown.find('>.dropdown-menu').removeClass('show');
                }, 500); // 500ms delay before closing
            }
        );
        
        // Nested dropdown hover
        $('.navbar .dropend').hover(
            function() {
                clearTimeout(closeTimer);
                $(this).find('>.dropdown-menu').addClass('show').css('display', 'block');
            },
            function() {
                const $dropend = $(this);
                closeTimer = setTimeout(function() {
                    $dropend.find('>.dropdown-menu').removeClass('show').css('display', 'none');
                }, 500);
            }
        );
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.navbar .dropdown').length) {
                $('.navbar .dropdown').removeClass('show')
                    .find('.dropdown-menu').removeClass('show');
            }
        });
    }
    
});

const BASE_URL = window.location.origin + '/devicesvn/public/';
