<?php
/**
 * Özelleştirici (Customizer): tema rengi, hava durumu şehri, iletişim & sosyal linkler
 */

/** Reklam kodu <script>/<ins> içerir; sadece yetkili (unfiltered_html) kullanıcıdan olduğu gibi kabul edilir. */
function veng_sanitize_ad_code( $value ) {
	return current_user_can( 'unfiltered_html' ) ? (string) $value : wp_kses_post( (string) $value );
}

function veng_customize_register( $wp_customize ) {

	$wp_customize->add_section( 'veng_site_settings', array(
		'title'    => 'Veng Haber Ayarları',
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'veng_accent_color', array( 'default' => '#5b21b6', 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'veng_accent_color', array(
		'label'   => 'Tema Rengi',
		'section' => 'veng_site_settings',
	) ) );

	$wp_customize->add_setting( 'veng_weather_city', array( 'default' => 'Diyarbakır', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'veng_weather_city', array(
		'label'   => 'Hava Durumu Şehri',
		'section' => 'veng_site_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'veng_contact_email', array( 'default' => 'info@venghaber.com', 'sanitize_callback' => 'sanitize_email' ) );
	$wp_customize->add_control( 'veng_contact_email', array( 'label' => 'İletişim E-posta', 'section' => 'veng_site_settings', 'type' => 'email' ) );

	$wp_customize->add_setting( 'veng_contact_phone', array( 'default' => '0412 000 00 00', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'veng_contact_phone', array( 'label' => 'İletişim Telefon', 'section' => 'veng_site_settings', 'type' => 'text' ) );

	$wp_customize->add_setting( 'veng_address', array( 'default' => 'Diyarbakır, Türkiye', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'veng_address', array( 'label' => 'Adres', 'section' => 'veng_site_settings', 'type' => 'text' ) );

	// Reklam alanları: slider altı (ana sayfa) ve kenar çubuğu. Reklam kodu (AdSense vb.) verilirse
	// o gösterilir; yoksa banner görseli + bağlantı; ikisi de boşsa "reklam verin" yer tutucusu.
	$wp_customize->add_section( 'veng_ads', array(
		'title'       => 'Reklam Alanları',
		'description' => 'Reklam kodu (AdSense vb.) doluysa o gösterilir. Boşsa banner görseli kullanılır. İkisi de boşsa "Reklam verin" kutusu görünür.',
		'priority'    => 31,
	) );
	foreach ( array( 'home' => 'Ana sayfa (slider altı)', 'side' => 'Kenar çubuğu' ) as $slot => $slot_label ) {
		$wp_customize->add_setting( 'veng_ad_' . $slot . '_code', array( 'default' => '', 'sanitize_callback' => 'veng_sanitize_ad_code' ) );
		$wp_customize->add_control( 'veng_ad_' . $slot . '_code', array( 'label' => $slot_label . ' · Reklam kodu', 'section' => 'veng_ads', 'type' => 'textarea' ) );
		$wp_customize->add_setting( 'veng_ad_' . $slot . '_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'veng_ad_' . $slot . '_image', array( 'label' => $slot_label . ' · Banner görseli', 'section' => 'veng_ads' ) ) );
		$wp_customize->add_setting( 'veng_ad_' . $slot . '_link', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( 'veng_ad_' . $slot . '_link', array( 'label' => $slot_label . ' · Banner bağlantısı', 'section' => 'veng_ads', 'type' => 'url' ) );
	}

	$socials = array(
		'facebook'  => 'Facebook URL',
		'x'         => 'X (Twitter) URL',
		'instagram' => 'Instagram URL',
		'linkedin'  => 'LinkedIn URL',
	);
	foreach ( $socials as $key => $label ) {
		$wp_customize->add_setting( 'veng_social_' . $key, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( 'veng_social_' . $key, array( 'label' => $label, 'section' => 'veng_site_settings', 'type' => 'url' ) );
	}
}
add_action( 'customize_register', 'veng_customize_register' );
