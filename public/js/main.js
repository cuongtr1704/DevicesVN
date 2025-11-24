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
    
});

const BASE_URL = window.location.origin + '/devicesvn/public/';
