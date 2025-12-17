<?php
/**
 * Ollie Child Theme Functions
 *
 * @package Ollie_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue parent and child theme styles
 */
function ollie_child_enqueue_styles() {
	// Enqueue parent theme stylesheet
	wp_enqueue_style(
		'ollie-parent-style',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme()->parent()->get( 'Version' )
	);

	// Enqueue child theme stylesheet
	wp_enqueue_style(
		'ollie-child-style',
		get_stylesheet_uri(),
		array( 'ollie-parent-style' ),
		wp_get_theme()->get( 'Version' )
	);

	// Enqueue Google Fonts - Outfit and Questrial
	wp_enqueue_style(
		'ollie-child-fonts',
		'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Questrial&display=swap',
		array(),
		null
	);

	// Enqueue Phosphor Icons
	wp_enqueue_style(
		'phosphor-icons',
		'https://unpkg.com/@phosphor-icons/web@2.0.3/src/regular/style.css',
		array(),
		'2.0.3'
	);

	// Enqueue custom styles
	wp_enqueue_style(
		'ollie-child-custom',
		get_stylesheet_directory_uri() . '/assets/css/custom.css',
		array( 'ollie-child-style' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'ollie_child_enqueue_styles' );

/**
 * Add theme support for Full Site Editing features
 */
function ollie_child_setup() {
	// Add support for editor styles
	add_theme_support( 'editor-styles' );

	// Add support for responsive embeds
	add_theme_support( 'responsive-embeds' );

	// Add support for custom line height
	add_theme_support( 'custom-line-height' );

	// Add support for custom spacing
	add_theme_support( 'custom-spacing' );

	// Add support for custom units
	add_theme_support( 'custom-units' );

	// Add support for link color
	add_theme_support( 'link-color' );

	// Add support for appearance tools
	add_theme_support( 'appearance-tools' );

	// Add support for border
	add_theme_support( 'border' );
}
add_action( 'after_setup_theme', 'ollie_child_setup' );

/**
 * Note: The glass_3d_app shortcode is registered by the
 * Clickwave Glass Configurator plugin (clickwave-glass-configurator)
 * Make sure the plugin is activated to use [glass_3d_app] shortcode
 */

/**
 * SEO Meta Tags - Optimized titles and descriptions
 */
function deglaswand_add_seo_meta_tags() {
	if ( is_page() ) {
		global $post;

		// SEO data per pagina
		$seo_data = array(
			'home' => array(
				'title' => 'De Glaswand - Glazen Schuifwanden op Maat | Premium Kwaliteit',
				'description' => 'Specialist in hoogwaardige glazen schuifwanden voor veranda\'s en overkappingen. Maatwerk ✓ Gratis offerte ✓ Professionele montage ✓ 10 jaar garantie. Configureer nu online!'
			),
			'configurator' => array(
				'title' => '3D Configurator - Ontwerp Je Glazen Schuifwand | De Glaswand',
				'description' => 'Ontwerp je eigen glazen schuifwand in onze 3D configurator. Kies afmetingen, kleuren en opties. Direct prijs inzichtelijk. Vraag vrijblijvend offerte aan!'
			),
			'glazen-schuifwand' => array(
				'title' => 'Glazen Schuifwand op Maat - Frameless & Modern | De Glaswand',
				'description' => 'Premium glazen schuifwanden zonder verticale profielen. Optimaal lichtinval, vrij uitzicht en perfect windscherm. Verkrijgbaar in RAL kleuren. Bekijk mogelijkheden!'
			),
			'steellook' => array(
				'title' => 'Steellook Glazen Schuifwand - Industriële Uitstraling | De Glaswand',
				'description' => 'Glazen schuifwanden met stalen look profielen voor een industriële uitstraling. Verschillende designs beschikbaar. Combineer modern design met functionaliteit!'
			),
			'contact' => array(
				'title' => 'Contact - Glazen Schuifwanden Specialist | De Glaswand',
				'description' => 'Neem contact op voor advies of een vrijblijvende offerte. Bel 06 15 24 63 83 of mail naar info@deglaswand.nl. Vakkundig advies van onze specialisten!'
			),
			'over-ons' => array(
				'title' => 'Over Ons - Specialist in Glazen Schuifwanden | De Glaswand',
				'description' => 'Specialist in hoogwaardige glazen schuifwanden sinds jaren. Ervaring, vakmanschap en oog voor detail. Lees over onze aanpak en waarom klanten voor ons kiezen.'
			),
			'algemene-voorwaarden' => array(
				'title' => 'Algemene Voorwaarden - De Glaswand',
				'description' => 'Lees hier de algemene voorwaarden van De Glaswand. Transparante afspraken over levering, garantie en service van onze glazen schuifwanden.'
			),
			'privacybeleid' => array(
				'title' => 'Privacybeleid - Veilige Gegevensverwerking | De Glaswand',
				'description' => 'Privacybeleid van De Glaswand. Wij gaan zorgvuldig om met uw persoonlijke gegevens. Lees hier hoe we uw privacy waarborgen volgens AVG wetgeving.'
			),
			'retourbeleid' => array(
				'title' => 'Retourbeleid - Voorwaarden & Service | De Glaswand',
				'description' => 'Het retourbeleid van De Glaswand. Informatie over retourneren, garantie en nazorg bij glazen schuifwanden. Klanttevredenheid staat bij ons voorop.'
			)
		);

		$slug = $post->post_name;

		// Bepaal de SEO data
		$title = isset( $seo_data[$slug] ) ? $seo_data[$slug]['title'] : get_the_title() . ' | De Glaswand';
		$description = isset( $seo_data[$slug] ) ? $seo_data[$slug]['description'] : get_the_excerpt();

		// Output meta tags
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";

		// Open Graph tags voor social media
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
		echo '<meta property="og:type" content="website">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( get_permalink() ) . '">' . "\n";
		echo '<meta property="og:site_name" content="De Glaswand">' . "\n";

		// Twitter Card tags
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'deglaswand_add_seo_meta_tags', 1 );

/**
 * Customize document title for SEO
 */
function deglaswand_custom_document_title( $title ) {
	if ( is_page() ) {
		global $post;

		$seo_titles = array(
			'home' => 'De Glaswand - Glazen Schuifwanden op Maat | Premium Kwaliteit',
			'configurator' => '3D Configurator - Ontwerp Je Glazen Schuifwand | De Glaswand',
			'glazen-schuifwand' => 'Glazen Schuifwand op Maat - Frameless & Modern | De Glaswand',
			'steellook' => 'Steellook Glazen Schuifwand - Industriële Uitstraling | De Glaswand',
			'contact' => 'Contact - Glazen Schuifwanden Specialist | De Glaswand',
			'over-ons' => 'Over Ons - Specialist in Glazen Schuifwanden | De Glaswand',
			'algemene-voorwaarden' => 'Algemene Voorwaarden - De Glaswand',
			'privacybeleid' => 'Privacybeleid - Veilige Gegevensverwerking | De Glaswand',
			'retourbeleid' => 'Retourbeleid - Voorwaarden & Service | De Glaswand'
		);

		$slug = $post->post_name;

		if ( isset( $seo_titles[$slug] ) ) {
			return $seo_titles[$slug];
		}
	}

	return $title;
}
add_filter( 'document_title_parts', function( $title ) {
	return array( 'title' => deglaswand_custom_document_title( implode( ' ', $title ) ) );
}, 10 );

/**
 * Add JSON-LD Schema markup for better SEO
 */
function deglaswand_add_schema_markup() {
	if ( is_front_page() || is_page( 'home' ) ) {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type' => 'LocalBusiness',
			'name' => 'De Glaswand',
			'description' => 'Specialist in hoogwaardige glazen schuifwanden voor veranda\'s, overkappingen en tuinhuizen',
			'url' => home_url(),
			'telephone' => '06-15246383',
			'email' => 'info@deglaswand.nl',
			'priceRange' => '€€',
			'address' => array(
				'@type' => 'PostalAddress',
				'addressCountry' => 'NL'
			),
			'openingHours' => 'Mo-Sa 08:00-17:00',
			'sameAs' => array(
				'https://www.instagram.com/deglaswand/',
				'https://www.facebook.com/deglaswand'
			)
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'deglaswand_add_schema_markup', 2 );

/**
 * Optimize robots.txt for better SEO
 */
function deglaswand_custom_robots_txt( $output ) {
	$output .= "Sitemap: " . home_url( '/wp-sitemap.xml' ) . "\n\n";
	$output .= "# Allow crawling of CSS, JS and images\n";
	$output .= "Allow: /wp-content/uploads/\n";
	$output .= "Allow: /wp-content/themes/\n";
	$output .= "Allow: /wp-content/plugins/\n\n";
	$output .= "# Disallow admin areas\n";
	$output .= "Disallow: /wp-admin/\n";
	$output .= "Disallow: /wp-includes/\n";
	$output .= "Disallow: /readme.html\n";
	$output .= "Disallow: /license.txt\n\n";

	return $output;
}
add_filter( 'robots_txt', 'deglaswand_custom_robots_txt', 10, 1 );

/**
 * Add canonical URLs to prevent duplicate content
 */
function deglaswand_add_canonical_url() {
	if ( is_singular() ) {
		echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'deglaswand_add_canonical_url', 1 );

/**
 * Include Google Reviews Widget
 */
require_once get_stylesheet_directory() . '/inc/google-reviews-widget.php';
