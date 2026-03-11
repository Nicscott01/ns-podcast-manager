# NS Podcast Manager

A lightweight, drop-in WordPress plugin for managing podcast episodes. Registers a custom post type (CPT) with ACF-powered episode fields, a settings page to configure every aspect of the integration, and automatic permalink handling — no boilerplate required.

## Requirements

- WordPress 6.0+
- [Advanced Custom Fields](https://www.advancedcustomfields.com/) (free) — required for episode meta fields
- [ACF Pro](https://www.advancedcustomfields.com/pro/) — required only if you enable the **Guest Repeater** feature

## Installation

1. Clone or download this repository into your `wp-content/plugins/` directory:

   ```bash
   git clone https://github.com/nscott/ns-podcast-manager.git wp-content/plugins/ns-podcast-manager
   ```

2. Activate the plugin from **Plugins → Installed Plugins** in the WordPress admin.

3. Navigate to **Settings → Podcast Manager** to configure the plugin.

That's it. No build step, no configuration files.

## Features

### Custom Post Type
- Registers a `podcast` CPT with full support for title, editor, featured image, excerpt, author, and revisions.
- Archive page and REST API exposure are each independently toggleable.
- URL slug, singular label, plural label, show name, and admin menu icon are all configurable from the settings page — no code changes needed.
- Changing the URL slug automatically flushes WordPress rewrite rules on the next request.

### ACF Episode Fields

Every episode gets a tabbed meta box with the following fields:

| Field | Type | Notes |
|---|---|---|
| Episode Number | Number | Always shown |
| Season | Number | Optional — toggle on/off in settings |
| Duration | Text | e.g. `42:30` |
| Audio Player | oEmbed | Paste a share URL from Spotify, Buzzsprout, Podbean, SoundCloud, etc. |
| Video Player | oEmbed | Paste a YouTube, Vimeo, or other video URL |
| Episode Links | Tab | Per-episode deep links for Spotify, Apple Podcasts, YouTube, Amazon Music |
| Show Notes | WYSIWYG | Full rich-text editor with media upload |
| Transcript | Textarea | Optional — toggle on/off in settings |
| Guests | Repeater | Optional, requires ACF Pro — name, title/company, website, photo, bio |

### Show-Level Platform URLs

Store your podcast's main page URL for each platform once, then use them anywhere in your theme (footer, sidebar, "Listen On" badges):

- Spotify
- Apple Podcasts
- YouTube
- Amazon Music
- iHeart Radio
- RSS Feed
- One configurable "Other" platform with a custom label

Retrieve them in your theme with:

```php
$opts = ns_pm_options();
echo $opts['show_url_spotify']; // e.g. https://open.spotify.com/show/...
```

### Settings Page

All configuration lives under **Settings → Podcast Manager** and is organized into three sections:

- **General** — CPT slug, labels, archive, REST API, menu icon
- **Episode Fields** — toggle Season, Transcript, and Guest Repeater fields on/off
- **Show URLs** — platform links for the show as a whole

## Accessing Episode Meta in Templates

Fields are stored via ACF and can be retrieved with standard ACF functions:

```php
// Inside a podcast post template or loop
$episode_number = get_field( 'episode_number' );
$duration       = get_field( 'duration' );
$audio_url      = get_field( 'audio_url' );
$show_notes     = get_field( 'show_notes' );

// Guest repeater (ACF Pro)
if ( have_rows( 'guests' ) ) {
    while ( have_rows( 'guests' ) ) : the_row();
        $name  = get_sub_field( 'guest_name' );
        $title = get_sub_field( 'guest_title' );
        $photo = get_sub_field( 'guest_photo' );
        $bio   = get_sub_field( 'guest_bio' );
    endwhile;
}
```

## License

GPL-2.0-or-later. See [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html).
