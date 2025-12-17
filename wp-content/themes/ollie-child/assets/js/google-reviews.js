jQuery(document).ready(function($) {

    function initGoogleReviews() {
        const $widget = $('.google-reviews-widget');

        if ($widget.length === 0) {
            return;
        }

        const placeId = $widget.data('place-id');
        const maxReviews = parseInt($widget.data('max-reviews')) || 6;
        const minRating = parseInt($widget.data('min-rating')) || 4;

        // Check if Google Places API is loaded
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.warn('Google Places API niet geladen. Fallback naar statische reviews.');
            showFallbackReviews($widget);
            return;
        }

        // Create a map element (required by Places API but hidden)
        const mapDiv = document.createElement('div');
        mapDiv.style.display = 'none';
        document.body.appendChild(mapDiv);

        const map = new google.maps.Map(mapDiv);
        const service = new google.maps.places.PlacesService(map);

        const request = {
            placeId: placeId,
            fields: ['name', 'rating', 'user_ratings_total', 'reviews']
        };

        service.getDetails(request, function(place, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK && place.reviews) {
                displayReviews(place, $widget, maxReviews, minRating);
            } else {
                console.error('Google Places API error:', status);
                showFallbackReviews($widget);
            }
        });
    }

    function displayReviews(place, $widget, maxReviews, minRating) {
        const $loading = $widget.find('.reviews-loading');
        const $container = $widget.find('.reviews-container');
        const $error = $widget.find('.reviews-error');

        // Filter reviews by minimum rating
        const filteredReviews = place.reviews
            .filter(review => review.rating >= minRating)
            .slice(0, maxReviews);

        if (filteredReviews.length === 0) {
            $loading.hide();
            $error.show();
            return;
        }

        // Build HTML
        let html = '<div class="reviews-header">';
        html += '<div class="google-rating-badge">';
        html += '<svg class="google-logo" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">';
        html += '<path fill="#4285F4" d="M45.12 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h11.84c-.51 2.75-2.06 5.08-4.39 6.64v5.52h7.11c4.16-3.83 6.56-9.47 6.56-16.17z"/>';
        html += '<path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.11-5.52c-1.97 1.32-4.49 2.1-7.45 2.1-5.73 0-10.58-3.87-12.31-9.07H4.34v5.7C7.96 41.07 15.4 46 24 46z"/>';
        html += '<path fill="#FBBC05" d="M11.69 28.18C11.25 26.86 11 25.45 11 24s.25-2.86.69-4.18v-5.7H4.34C2.85 17.09 2 20.45 2 24c0 3.55.85 6.91 2.34 9.88l7.35-5.7z"/>';
        html += '<path fill="#EA4335" d="M24 10.75c3.23 0 6.13 1.11 8.41 3.29l6.31-6.31C34.91 4.18 29.93 2 24 2 15.4 2 7.96 6.93 4.34 14.12l7.35 5.7c1.73-5.2 6.58-9.07 12.31-9.07z"/>';
        html += '</svg>';
        html += '<div class="rating-info">';
        html += '<div class="rating-stars">' + getStars(place.rating) + '</div>';
        html += '<p class="rating-text">' + place.rating.toFixed(1) + ' sterren • ' + place.user_ratings_total + ' reviews</p>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        html += '<div class="reviews-grid">';

        filteredReviews.forEach(function(review) {
            const initials = getInitials(review.author_name);
            const relativeTime = review.relative_time_description;

            html += '<div class="review-card">';
            html += '<div class="review-header">';
            html += '<div class="review-avatar">' + initials + '</div>';
            html += '<div class="review-author-info">';
            html += '<h3 class="review-author">' + escapeHtml(review.author_name) + '</h3>';
            html += '<div class="review-rating">' + getStars(review.rating) + '</div>';
            html += '</div>';
            html += '</div>';
            html += '<p class="review-text">' + escapeHtml(review.text) + '</p>';
            html += '<p class="review-date">' + relativeTime + '</p>';
            html += '</div>';
        });

        html += '</div>';

        html += '<div class="view-all-button">';
        html += '<a href="https://www.google.com/search?q=Dé+Glaswand" target="_blank" rel="noopener">Bekijk alle ' + place.user_ratings_total + ' reviews op Google →</a>';
        html += '</div>';

        $container.html(html);
        $loading.hide();
        $container.show();
    }

    function showFallbackReviews($widget) {
        const $loading = $widget.find('.reviews-loading');
        const $container = $widget.find('.reviews-container');

        // Fallback static reviews
        const fallbackReviews = [
            {
                author: 'Familie van Dijk',
                rating: 5,
                text: 'Fantastische service en vakmanschap! De glaswand is prachtig geïnstalleerd en heeft onze tuin getransformeerd. Rick en zijn team waren professioneel en zorgvuldig.',
                date: '2 weken geleden'
            },
            {
                author: 'Peter Janssen',
                rating: 5,
                text: 'Perfect advies en uitvoering. We zijn super blij met onze glazen schuifwand. De kwaliteit is uitstekend en het ziet er prachtig uit!',
                date: '1 maand geleden'
            },
            {
                author: 'Marieke de Groot',
                rating: 5,
                text: 'Snelle levering en installatie. De medewerkers waren vriendelijk en kundig. Onze terrasoverkapping is nu compleet met deze prachtige glaswand!',
                date: '3 weken geleden'
            }
        ];

        let html = '<div class="reviews-header">';
        html += '<h3 style="font-size: 32px; margin-bottom: 8px;">Wat onze klanten zeggen</h3>';
        html += '<p style="color: #666; font-size: 18px;">Beoordeeld met <strong>★★★★★</strong> op Google</p>';
        html += '</div>';

        html += '<div class="reviews-grid">';

        fallbackReviews.forEach(function(review) {
            const initials = getInitials(review.author);

            html += '<div class="review-card">';
            html += '<div class="review-header">';
            html += '<div class="review-avatar">' + initials + '</div>';
            html += '<div class="review-author-info">';
            html += '<h3 class="review-author">' + escapeHtml(review.author) + '</h3>';
            html += '<div class="review-rating">' + getStars(review.rating) + '</div>';
            html += '</div>';
            html += '</div>';
            html += '<p class="review-text">' + escapeHtml(review.text) + '</p>';
            html += '<p class="review-date">' + review.date + '</p>';
            html += '</div>';
        });

        html += '</div>';

        html += '<div class="view-all-button">';
        html += '<a href="https://www.google.com/search?q=Dé+Glaswand" target="_blank" rel="noopener">Bekijk alle reviews op Google →</a>';
        html += '</div>';

        $container.html(html);
        $loading.hide();
        $container.show();
    }

    function getStars(rating) {
        const fullStars = Math.floor(rating);
        const halfStar = rating % 1 >= 0.5 ? 1 : 0;
        const emptyStars = 5 - fullStars - halfStar;

        let stars = '';
        for (let i = 0; i < fullStars; i++) stars += '★';
        if (halfStar) stars += '☆';
        for (let i = 0; i < emptyStars; i++) stars += '☆';

        return stars;
    }

    function getInitials(name) {
        const parts = name.split(' ');
        if (parts.length >= 2) {
            return parts[0].charAt(0).toUpperCase() + parts[parts.length - 1].charAt(0).toUpperCase();
        }
        return parts[0].charAt(0).toUpperCase();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize when Google API is ready
    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
        initGoogleReviews();
    } else {
        // Wait for Google API to load
        window.addEventListener('load', function() {
            setTimeout(initGoogleReviews, 1000);
        });
    }
});
