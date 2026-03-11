<?php
/**
 * Seed script — creates (or removes) sample podcast episodes with full ACF meta.
 *
 * Seed:   wp eval-file web/app/plugins/ns-podcast-manager/seed-episodes.php --allow-root
 * Unseed: wp eval-file web/app/plugins/ns-podcast-manager/seed-episodes.php --allow-root -- --unseed
 */

// Run with `-- --unseed` to delete all seeded episodes.
$unseed = in_array( '--unseed', $args ?? [], true );

if ( $unseed ) {
	$seeded = get_posts( [
		'post_type'      => 'podcast',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'meta_key'       => '_ns_pm_seeded',
		'meta_value'     => '1',
		'fields'         => 'ids',
	] );

	if ( empty( $seeded ) ) {
		WP_CLI::warning( 'No seeded episodes found.' );
		return;
	}

	foreach ( $seeded as $id ) {
		wp_delete_post( $id, true );
		WP_CLI::success( "Deleted post ID {$id}" );
	}

	WP_CLI::success( count( $seeded ) . ' seeded episode(s) removed.' );
	return;
}

$episodes = [
	[
		'title'   => 'Finding Your Way Back: Somatic Healing After Burnout',
		'excerpt' => 'Dr. Mara Solano joins us to talk about what the body holds onto after prolonged stress and how somatic practices create a path back to yourself.',
		'content' => "<p>In this episode we sit down with Dr. Mara Solano, a somatic therapist and burnout recovery specialist, to unpack what's actually happening in the nervous system when we push past our limits for too long.</p><p>We talk about the difference between mental exhaustion and true burnout, why conventional advice often makes things worse, and what a body-first recovery actually looks like in practice.</p>",
		'meta'    => [
			'episode_number'   => 47,
			'duration'         => '52:14',
			'audio_url'        => 'https://open.spotify.com/episode/example047',
			'video_embed_url'  => 'https://www.youtube.com/watch?v=example047',
			'spotify_url'      => 'https://open.spotify.com/episode/example047',
			'apple_url'        => 'https://podcasts.apple.com/us/podcast/example/id000000047',
			'youtube_url'      => 'https://www.youtube.com/watch?v=example047',
			'amazon_url'       => 'https://music.amazon.com/podcasts/example/episodes/ep047',
			'show_notes'       => '<h3>Episode Highlights</h3><ul><li>The 3-stage burnout cycle most people don\'t recognize until stage 3</li><li>Somatic markers your body uses to signal dysregulation</li><li>A simple 5-minute grounding practice you can do anywhere</li><li>Why "rest" alone rarely resolves burnout</li><li>How to advocate for yourself with a skeptical employer or partner</li></ul><h3>Resources Mentioned</h3><ul><li><a href="#">The Body Keeps the Score</a> — Bessel van der Kolk</li><li><a href="#">Burnout</a> — Emily and Amelia Nagoski</li><li><a href="#">Dr. Solano\'s free nervous system reset guide</a></li></ul>',
			'guests'           => 1,
			'guests_0_name'    => 'Dr. Mara Solano',
			'guests_0_title'   => 'Somatic Therapist & Burnout Recovery Specialist',
			'guests_0_url'     => 'https://www.marasolano.com',
			'guests_0_bio'     => 'Dr. Mara Solano is a licensed somatic therapist with 18 years of clinical experience specializing in burnout recovery, trauma, and nervous system regulation. She is the author of "Returning to Yourself" and runs retreats across the US and Europe.',
		],
	],
	[
		'title'   => 'The Gut-Brain Connection: What Your Digestion Is Trying to Tell You',
		'excerpt' => 'Functional medicine practitioner Dr. Kwame Asante breaks down the science behind the gut-brain axis and why healing your gut may be the missing piece in your mental health journey.',
		'content' => "<p>We've heard it before — you have a \"second brain\" in your gut. But what does that actually mean, and what can you do about it? Dr. Kwame Asante joins us to separate fact from fad and give us a practical roadmap for improving the gut-brain connection.</p><p>This one gets into the weeds on microbiome science, but Dr. Asante has a gift for making complex biology feel approachable and actionable.</p>",
		'meta'    => [
			'episode_number'   => 48,
			'duration'         => '1:04:38',
			'audio_url'        => 'https://open.spotify.com/episode/example048',
			'video_embed_url'  => 'https://www.youtube.com/watch?v=example048',
			'spotify_url'      => 'https://open.spotify.com/episode/example048',
			'apple_url'        => 'https://podcasts.apple.com/us/podcast/example/id000000048',
			'youtube_url'      => 'https://www.youtube.com/watch?v=example048',
			'amazon_url'       => 'https://music.amazon.com/podcasts/example/episodes/ep048',
			'show_notes'       => '<h3>Episode Highlights</h3><ul><li>What the vagus nerve actually does (and how to stimulate it)</li><li>The role of short-chain fatty acids in mood regulation</li><li>Foods that are quietly destroying your microbiome</li><li>How stress reshapes gut bacteria composition within 24 hours</li><li>A 30-day gut reset framework Dr. Asante uses with patients</li></ul><h3>Resources Mentioned</h3><ul><li><a href="#">Fiber Fueled</a> — Dr. Will Bulsiewicz</li><li><a href="#">Dr. Asante\'s 30-day gut reset protocol (free download)</a></li><li><a href="#">ZOE Nutrition Study</a></li></ul>',
			'guests'           => 1,
			'guests_0_name'    => 'Dr. Kwame Asante',
			'guests_0_title'   => 'Functional Medicine Practitioner, MD',
			'guests_0_url'     => 'https://www.kwameasantemd.com',
			'guests_0_bio'     => 'Dr. Kwame Asante is a board-certified functional medicine physician and researcher whose work focuses on the microbiome\'s role in mental and metabolic health. He has been featured in The New York Times, Time, and on the Huberman Lab podcast.',
		],
	],
	[
		'title'   => 'From Surviving to Thriving: A Live Coaching Session on Rebuilding Identity After Loss',
		'excerpt' => 'In this special episode, host Jade Rivers coaches listener Priya M. through rebuilding a sense of self after a life-altering loss. Raw, real, and deeply useful.',
		'content' => "<p>This is a different kind of episode. Instead of a guest interview, we're sharing a live coaching session (with full permission from our participant, Priya) that deals with something many of you have written in about: who am I now?</p><p>After losing her mother, her marriage, and her career within 18 months, Priya found herself standing in the rubble of a life she no longer recognized. This session follows the arc of a single 60-minute coaching call that changed everything for her.</p>",
		'meta'    => [
			'episode_number'   => 49,
			'duration'         => '58:02',
			'audio_url'        => 'https://open.spotify.com/episode/example049',
			'video_embed_url'  => 'https://www.youtube.com/watch?v=example049',
			'spotify_url'      => 'https://open.spotify.com/episode/example049',
			'apple_url'        => 'https://podcasts.apple.com/us/podcast/example/id000000049',
			'youtube_url'      => 'https://www.youtube.com/watch?v=example049',
			'amazon_url'       => 'https://music.amazon.com/podcasts/example/episodes/ep049',
			'show_notes'       => '<h3>Episode Highlights</h3><ul><li>The identity audit: mapping who you were vs. who you are now</li><li>Why grief isn\'t linear — and why that\'s actually useful information</li><li>The "smallest true thing" technique for reconnecting with values</li><li>How to distinguish between rebuilding and escaping</li><li>Priya\'s follow-up: where she is six months later</li></ul><h3>Listener Resources</h3><ul><li><a href="#">Free identity audit worksheet (download)</a></li><li><a href="#">Episode 31: Grief That Doesn\'t Have a Name</a></li><li><a href="#">Apply to be coached on the podcast</a></li></ul>',
			'guests'           => 2,
			'guests_0_name'    => 'Priya M.',
			'guests_0_title'   => 'Listener & Coaching Participant',
			'guests_0_url'     => '',
			'guests_0_bio'     => 'Priya is a 42-year-old project manager from Toronto who graciously agreed to share her coaching session with our audience. She asked that we use only her first name.',
			'guests_1_name'    => 'Jade Rivers',
			'guests_1_title'   => 'Host & Certified Life Coach',
			'guests_1_url'     => 'https://www.jaderivers.com',
			'guests_1_bio'     => 'Jade Rivers is a certified life coach, speaker, and the host of this podcast. She specializes in identity reconstruction and resilience coaching for people navigating major life transitions.',
		],
	],
	[
		'title'   => 'Sleep, Stress & the Cortisol Trap: Breaking the Exhaustion Cycle',
		'excerpt' => 'Sleep researcher Dr. Fiona Mercer explains why sleeping more isn\'t always the answer — and what the cortisol cycle is doing to your recovery even when you\'re in bed for 9 hours.',
		'content' => '<p>You\'re getting 8, even 9 hours of sleep, and you\'re still waking up exhausted. Sound familiar? Sleep researcher Dr. Fiona Mercer joins us to explain why sleep quantity is often the wrong metric, what cortisol dysregulation is doing to your deep sleep stages, and the counterintuitive habits that may actually be making your sleep worse.</p>',
		'meta'    => [
			'episode_number'   => 50,
			'duration'         => '47:55',
			'audio_url'        => 'https://open.spotify.com/episode/example050',
			'video_embed_url'  => 'https://www.youtube.com/watch?v=example050',
			'spotify_url'      => 'https://open.spotify.com/episode/example050',
			'apple_url'        => 'https://podcasts.apple.com/us/podcast/example/id000000050',
			'youtube_url'      => 'https://www.youtube.com/watch?v=example050',
			'amazon_url'       => 'https://music.amazon.com/podcasts/example/episodes/ep050',
			'show_notes'       => '<h3>Episode Highlights</h3><ul><li>The difference between sleep quantity and sleep architecture</li><li>How cortisol peaks interfere with REM and slow-wave sleep</li><li>Why late-night workouts may be quietly sabotaging your recovery</li><li>The morning light protocol Dr. Mercer recommends to every patient</li><li>Supplements with actual evidence behind them (and the ones to skip)</li></ul><h3>Resources Mentioned</h3><ul><li><a href="#">Why We Sleep</a> — Matthew Walker</li><li><a href="#">Dr. Mercer\'s cortisol reset protocol</a></li><li><a href="#">Oura Ring sleep tracking</a> (not sponsored)</li></ul>',
			'guests'           => 1,
			'guests_0_name'    => 'Dr. Fiona Mercer',
			'guests_0_title'   => 'Sleep Researcher & Neurologist',
			'guests_0_url'     => 'https://www.fionamercer.com',
			'guests_0_bio'     => 'Dr. Fiona Mercer is a neurologist and sleep researcher at the University of Edinburgh whose work focuses on cortisol\'s role in sleep architecture and recovery. She is the author of "Sleeping Well Under Pressure" and consults with professional sports teams on sleep optimization.',
		],
	],
];

foreach ( $episodes as $ep ) {
	$meta = $ep['meta'];

	$post_id = wp_insert_post( [
		'post_type'    => 'podcast',
		'post_status'  => 'publish',
		'post_title'   => $ep['title'],
		'post_excerpt' => $ep['excerpt'],
		'post_content' => $ep['content'],
	] );

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( 'Failed to create post: ' . $ep['title'] );
		continue;
	}

	// Mark as seeded so --unseed can find and remove these posts.
	update_post_meta( $post_id, '_ns_pm_seeded', '1' );

	// Scalar ACF fields.
	$scalar_map = [
		'episode_number'  => 'field_ns_pm_episode_number',
		'duration'        => 'field_ns_pm_duration',
		'audio_url'       => 'field_ns_pm_audio_url',
		'video_embed_url' => 'field_ns_pm_video_embed_url',
		'spotify_url'     => 'field_ns_pm_spotify_url',
		'apple_url'       => 'field_ns_pm_apple_url',
		'youtube_url'     => 'field_ns_pm_youtube_url',
		'amazon_url'      => 'field_ns_pm_amazon_url',
		'show_notes'     => 'field_ns_pm_show_notes',
	];

	foreach ( $scalar_map as $key => $field_key ) {
		update_post_meta( $post_id, $key, $meta[ $key ] );
		update_post_meta( $post_id, '_' . $key, $field_key );
	}

	// Guests repeater.
	$guest_count = $meta['guests'];
	update_post_meta( $post_id, 'guests', $guest_count );
	update_post_meta( $post_id, '_guests', 'field_ns_pm_guests' );

	for ( $i = 0; $i < $guest_count; $i++ ) {
		$sub_fields = [
			'guest_name'  => 'field_ns_pm_guest_name',
			'guest_title' => 'field_ns_pm_guest_title',
			'guest_url'   => 'field_ns_pm_guest_url',
			'guest_photo' => 'field_ns_pm_guest_photo',
			'guest_bio'   => 'field_ns_pm_guest_bio',
		];

		foreach ( $sub_fields as $sub_key => $field_key ) {
			$meta_key = "guests_{$i}_{$sub_key}";
			// Derive which seed key to look for (e.g. guests_0_name → guest_name minus "guest_").
			$short_key = str_replace( 'guest_', '', $sub_key );
			$value = $meta[ "guests_{$i}_{$short_key}" ] ?? '';

			update_post_meta( $post_id, "guests_{$i}_{$sub_key}", $value );
			update_post_meta( $post_id, "_guests_{$i}_{$sub_key}", $field_key );
		}
	}

	WP_CLI::success( "Created episode #{$meta['episode_number']}: {$ep['title']} (ID: {$post_id})" );
}

WP_CLI::success( 'All sample episodes seeded.' );
