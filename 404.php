<?php get_header(); ?>
<div class="container" style="text-align:center;padding:60px 16px 20px;">
	<h1 style="font-size:48px;font-weight:800;margin-bottom:12px;">404</h1>
	<p style="color:var(--muted);margin-bottom:24px;">Aradığınız haber artık yayında değil ya da adres değişmiş olabilir.</p>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn">Ana Sayfaya Dön</a>
</div>
<div class="container" style="max-width:700px;padding-bottom:60px;">
	<?php
	// Ziyaretçi eski/kırık bir habere geldiyse onu çıplak hata sayfasında bırakmak yerine
	// güncel haberlere yönlendiriyoruz — hem kullanıcı deneyimi hem de dahili linkleme için.
	$recent = get_posts( array( 'post_type' => array( 'post', 'makale' ), 'post_status' => 'publish', 'posts_per_page' => 8, 'orderby' => 'date', 'order' => 'DESC' ) );
	if ( $recent ) :
	?>
		<h2 style="text-align:center;margin-bottom:16px;">Güncel Haberler</h2>
		<div class="grid">
			<?php foreach ( $recent as $p ) : veng_render_gcard( $p->ID ); endforeach; ?>
		</div>
	<?php endif; ?>
</div>
<?php get_footer(); ?>
