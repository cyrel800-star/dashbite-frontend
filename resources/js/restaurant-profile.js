export function renderRestaurantProfile(profile, escapeHtml, heading) {
    const restaurant = profile || {};
    const openingHours = restaurant.opening_hours || {};
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    return `
        ${heading('Restaurant workspace', 'Restaurant profile', 'Update the details customers see when they browse your restaurant.')}
        <section class="fd-card fd-card-pad">
            <form data-form="restaurant-profile">
                <div class="fd-form-grid">
                    <label class="fd-form-field"><span class="fd-label">Restaurant name</span><input class="fd-input" name="name" maxlength="255" value="${escapeHtml(restaurant.name || '')}" required></label>
                    <label class="fd-form-field"><span class="fd-label">Cuisine</span><input class="fd-input" name="cuisine" maxlength="100" value="${escapeHtml(restaurant.cuisine || '')}"></label>
                    <label class="fd-form-field is-wide"><span class="fd-label">Address</span><input class="fd-input" name="address" maxlength="255" value="${escapeHtml(restaurant.address || '')}" required></label>
                    <label class="fd-form-field"><span class="fd-label">Phone</span><input class="fd-input" name="phone" maxlength="30" value="${escapeHtml(restaurant.phone || '')}"></label>
                    <label class="fd-form-field"><span class="fd-label">Preparation time (minutes)</span><input class="fd-input" name="prep_time_minutes" type="number" min="1" max="240" value="${Number(restaurant.prep_time_minutes || 20)}" required></label>
                    <label class="fd-form-field"><span class="fd-label">Cover image URL</span><input class="fd-input" name="cover_image_url" type="url" value="${escapeHtml(restaurant.cover_image_url || '')}"></label>
                    <label class="fd-form-field"><span class="fd-label">Profile image URL</span><input class="fd-input" name="profile_image_url" type="url" value="${escapeHtml(restaurant.profile_image_url || '')}"></label>
                    <label class="fd-form-field is-wide"><span class="fd-label">Display badge</span><input class="fd-input" name="display_badge" maxlength="60" value="${escapeHtml(restaurant.display_badge || '')}" placeholder="e.g. Local favorite"></label>
                    <div class="fd-form-field is-wide">
                        <span class="fd-label">Opening hours (use “Closed” when not open)</span>
                        <div class="fd-form-grid">${days.map((day) => `<label class="fd-form-field"><span class="fd-label">${day.charAt(0).toUpperCase() + day.slice(1)}</span><input class="fd-input" name="hours[${day}]" maxlength="60" value="${escapeHtml(openingHours[day] || '')}" placeholder="9:00 AM - 8:00 PM"></label>`).join('')}</div>
                    </div>
                </div>
                <div class="fd-form-actions"><button class="fd-button" type="submit">Save restaurant profile</button></div>
            </form>
        </section>`;
}
