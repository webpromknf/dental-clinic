<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lang = dc_lang();
$view = dc_view();
$views = dc_views();
$nav = array( 'tratamentos', 'marcar', 'equipa', 'resultados', 'contacto' );
$item = dc_treatment_by_slug( get_query_var( 'dc_item' ) );

function dc_header( $lang, $view, $views, $nav ) {
	?>
	<header class="site-header" data-header>
		<a class="brand" href="<?php echo esc_url( dc_url( 'home', $lang ) ); ?>" aria-label="<?php echo esc_attr( dc_t( 'brand' ) ); ?>">
			<span class="brand-mark">LO</span>
			<span><strong><?php echo esc_html( dc_t( 'brand' ) ); ?></strong><small><?php echo esc_html( dc_t( 'tagline' ) ); ?></small></span>
		</a>
		<button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="site-nav"><span></span><span></span><span></span></button>
		<nav class="site-nav" id="site-nav" data-nav>
			<?php foreach ( $nav as $item_view ) : ?>
				<a href="<?php echo esc_url( dc_url( $item_view, $lang ) ); ?>"><?php echo esc_html( $views[ $item_view ][ $lang ] ); ?></a>
			<?php endforeach; ?>
			<a class="main-site" href="<?php echo esc_url( dc_main_site_url( $lang ) ); ?>"><?php echo esc_html( dc_t( 'back_main' ) ); ?></a>
			<div class="lang-switch" translate="no">
				<?php foreach ( dc_langs() as $code => $label ) : ?>
					<a class="<?php echo $code === $lang ? 'is-active' : ''; ?>" href="<?php echo esc_url( 'tratamento' === $view && get_query_var( 'dc_item' ) ? dc_treatment_url( get_query_var( 'dc_item' ), $code ) : dc_url( $view, $code ) ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</div>
		</nav>
		<a class="header-cta" href="<?php echo esc_url( dc_url( 'marcar', $lang ) ); ?>"><?php echo esc_html( dc_t( 'book' ) ); ?></a>
	</header>
	<?php
}

function dc_section_head( $kicker, $title, $copy = '' ) {
	echo '<div class="section-head reveal"><p class="eyebrow">' . esc_html( $kicker ) . '</p><h2>' . esc_html( $title ) . '</h2>';
	if ( $copy ) {
		echo '<p>' . esc_html( $copy ) . '</p>';
	}
	echo '</div>';
}

function dc_treatments_grid( $limit = 0 ) {
	$treatments = $limit ? array_slice( dc_treatments(), 0, $limit ) : dc_treatments();
	echo '<div class="treatment-grid">';
	foreach ( $treatments as $treatment ) {
		echo '<article class="treatment-card reveal"><span class="icon">' . esc_html( $treatment['icon'] ) . '</span><h3>' . esc_html( dc_l( $treatment['name'] ) ) . '</h3><p>' . esc_html( dc_l( $treatment['desc'] ) ) . '</p><small>' . esc_html( $treatment['duration'] ) . ' · ' . esc_html( dc_l( $treatment['price'] ) ) . '</small><a href="' . esc_url( dc_treatment_url( $treatment['slug'] ) ) . '">' . esc_html( dc_t( 'view_treatment' ) ) . '</a></article>';
	}
	echo '</div>';
}

function dc_team_grid() {
	echo '<div class="team-grid">';
	foreach ( dc_doctors() as $doctor ) {
		echo '<article class="doctor-card reveal"><img src="' . esc_url( $doctor['image'] ) . '" alt="' . esc_attr( $doctor['name'] ) . '" loading="lazy"><div><h3>' . esc_html( $doctor['name'] ) . '</h3><p>' . esc_html( dc_l( $doctor['role'] ) ) . '</p><small>' . esc_html( $doctor['reg'] ) . ' · ' . esc_html( dc_l( $doctor['exp'] ) ) . '</small><p>' . esc_html( dc_l( $doctor['bio'] ) ) . '</p><a class="button secondary" href="' . esc_url( dc_url( 'marcar' ) . '?doctor=' . $doctor['id'] ) . '">' . esc_html( dc_t( 'book' ) ) . '</a></div></article>';
	}
	echo '</div>';
}

function dc_results_grid() {
	$items = array(
		array( 'pt' => 'Branqueamento', 'en' => 'Whitening', 'es' => 'Blanqueamiento' ),
		array( 'pt' => 'Ortodontia', 'en' => 'Orthodontics', 'es' => 'Ortodoncia' ),
		array( 'pt' => 'Estética Dentária', 'en' => 'Cosmetic Dentistry', 'es' => 'Estética Dental' ),
		array( 'pt' => 'Implantologia', 'en' => 'Dental Implants', 'es' => 'Implantología' ),
		array( 'pt' => 'Periodontologia', 'en' => 'Periodontology', 'es' => 'Periodoncia' ),
		array( 'pt' => 'Reabilitação', 'en' => 'Rehabilitation', 'es' => 'Rehabilitación' ),
	);
	echo '<div class="results-grid">';
	foreach ( $items as $i => $label ) {
		echo '<article class="result-card reveal"><div class="before-after" data-before-after><img src="' . esc_url( dc_img( $i % 2 ? 'smile' : 'chair', 800, 520 ) ) . '" alt="' . esc_attr( dc_t( 'result_before_after_alt' ) ) . '"><div class="after"><img src="' . esc_url( dc_img( 'smile', 800, 520 ) ) . '" alt="' . esc_attr( dc_t( 'result_after_alt' ) ) . '"></div><input type="range" min="20" max="80" value="50" aria-label="' . esc_attr( dc_t( 'compare_results' ) ) . '"></div><h3>' . esc_html( dc_l( $label ) ) . '</h3><p>' . esc_html( dc_t( 'results_disclaimer' ) ) . '</p></article>';
	}
	echo '</div>';
}

function dc_booking_form() {
	?>
	<form class="booking-form reveal" data-booking-form data-netlify="true" name="dental-booking-demo">
		<input type="hidden" name="form-name" value="dental-booking-demo">
		<p class="demo-note"><?php echo esc_html( dc_t( 'booking_demo_note' ) ); ?></p>
		<div class="steps" data-steps>
			<section class="step is-active" data-step-panel="1"><h3>1. <?php echo esc_html( dc_t( 'step_treatment' ) ); ?></h3><div class="choice-grid"><?php foreach ( dc_treatments() as $treatment ) : ?><label><input type="radio" name="treatment" value="<?php echo esc_attr( dc_l( $treatment['name'] ) ); ?>" required><span><?php echo esc_html( $treatment['icon'] . ' ' . dc_l( $treatment['name'] ) ); ?></span></label><?php endforeach; ?></div></section>
			<section class="step" data-step-panel="2"><h3>2. <?php echo esc_html( dc_t( 'step_dentist' ) ); ?></h3><div class="choice-grid"><?php foreach ( dc_doctors() as $doctor ) : ?><label><input type="radio" name="doctor" value="<?php echo esc_attr( $doctor['name'] ); ?>" required><span><?php echo esc_html( $doctor['name'] ); ?><small><?php echo esc_html( dc_l( $doctor['role'] ) ); ?></small></span></label><?php endforeach; ?></div></section>
			<section class="step" data-step-panel="3"><h3>3. <?php echo esc_html( dc_t( 'step_date_time' ) ); ?></h3><div class="form-grid"><label><?php echo esc_html( dc_t( 'date' ) ); ?><input name="date" type="date" required></label><label><?php echo esc_html( dc_t( 'time' ) ); ?><select name="time" required><?php foreach ( dc_slots() as $slot ) : ?><option><?php echo esc_html( $slot ); ?></option><?php endforeach; ?></select></label></div><div class="slot-note"><?php echo esc_html( dc_t( 'slot_note' ) ); ?></div></section>
			<section class="step" data-step-panel="4"><h3>4. <?php echo esc_html( dc_t( 'patient_details' ) ); ?></h3><div class="form-grid"><label><?php echo esc_html( dc_t( 'contact_name' ) ); ?><input name="name" autocomplete="name" required></label><label>Email<input name="email" type="email" autocomplete="email" required></label><label><?php echo esc_html( dc_t( 'phone' ) ); ?><input name="phone" autocomplete="tel" required></label><label>NIF<input name="nif"></label><label class="span-2 consent"><input name="consent" type="checkbox" required><span><?php echo esc_html( dc_t( 'consent' ) ); ?></span></label><label class="span-2"><?php echo esc_html( dc_t( 'notes' ) ); ?><textarea name="message" rows="4"></textarea></label></div></section>
			<section class="step" data-step-panel="5"><h3>5. <?php echo esc_html( dc_t( 'confirmation' ) ); ?></h3><div class="booking-summary" data-booking-summary><?php echo esc_html( dc_t( 'booking_summary' ) ); ?></div><button class="button primary" type="submit"><?php echo esc_html( dc_t( 'book' ) ); ?></button><p class="form-status" data-form-status></p></section>
		</div>
		<div class="step-actions"><button type="button" class="button ghost-dark" data-prev disabled><?php echo esc_html( dc_t( 'previous' ) ); ?></button><button type="button" class="button primary" data-next><?php echo esc_html( dc_t( 'next' ) ); ?></button></div>
	</form>
	<?php
}

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'dc-view-' . esc_attr( $view ) ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#conteudo">Skip</a>
<?php dc_header( $lang, $view, $views, $nav ); ?>
<main id="conteudo">
<?php if ( 'home' === $view ) : ?>
	<section class="hero">
		<img src="<?php echo esc_url( dc_img( 'hero', 1800, 1050 ) ); ?>" alt="<?php echo esc_attr( dc_t( 'hero_alt' ) ); ?>" decoding="async">
		<div class="hero-shade"></div><div class="hero-content reveal"><p class="eyebrow">ERS DEMO-2026 · Lisboa</p><h1><?php echo esc_html( dc_t( 'hero_title' ) ); ?></h1><p><?php echo esc_html( dc_t( 'hero_copy' ) ); ?></p><div class="actions"><a class="button primary" href="<?php echo esc_url( dc_url( 'marcar' ) ); ?>"><?php echo esc_html( dc_t( 'book' ) ); ?></a><a class="button ghost" href="<?php echo esc_url( dc_url( 'tratamentos' ) ); ?>"><?php echo esc_html( dc_t( 'view_treatments' ) ); ?></a></div><ul class="proof"><li>Scanner 3D</li><li><?php echo esc_html( dc_t( 'digital_xray' ) ); ?></li><li><?php echo esc_html( dc_t( 'sterilisation_protocols' ) ); ?></li></ul></div>
	</section>
	<section class="trust-strip"><span><?php echo esc_html( dc_t( 'patients_demo' ) ); ?></span><span><?php echo esc_html( dc_t( 'experience_years' ) ); ?></span><span>Google Reviews 4.9</span><span><?php echo esc_html( dc_t( 'quick_whatsapp' ) ); ?></span></section>
	<section class="section"><div class="container"><?php dc_section_head( dc_t( 'brand' ), dc_t( 'treatments' ) ); dc_treatments_grid( 6 ); ?></div></section>
	<section class="section muted"><div class="container split"><div class="reveal"><p class="eyebrow"><?php echo esc_html( dc_t( 'online_booking' ) ); ?></p><h2><?php echo esc_html( dc_t( 'booking_intro_title' ) ); ?></h2><p><?php echo esc_html( dc_t( 'booking_intro_copy' ) ); ?></p><a class="button primary" href="<?php echo esc_url( dc_url( 'marcar' ) ); ?>"><?php echo esc_html( dc_t( 'book' ) ); ?></a></div><img class="feature-img reveal" src="<?php echo esc_url( dc_img( 'tech', 900, 900 ) ); ?>" alt="<?php echo esc_attr( dc_t( 'tech_alt' ) ); ?>" loading="lazy"></div></section>
	<section class="section"><div class="container"><?php dc_section_head( dc_t( 'brand' ), dc_t( 'team' ) ); dc_team_grid(); ?></div></section>
	<section class="section muted"><div class="container"><?php dc_section_head( dc_t( 'brand' ), dc_t( 'results' ), dc_t( 'results_note' ) ); dc_results_grid(); ?></div></section>
	<section class="section"><div class="container"><?php dc_section_head( dc_t( 'brand' ), dc_t( 'reviews' ) ); ?><div class="review-grid"><?php foreach ( dc_reviews() as $review ) : ?><blockquote class="reveal"><p><?php echo esc_html( dc_l( $review['text'] ) ); ?></p><cite><?php echo esc_html( $review['name'] ); ?></cite></blockquote><?php endforeach; ?></div></div></section>
<?php elseif ( 'tratamentos' === $view ) : ?>
	<section class="page-hero"><div class="container"><?php dc_section_head( dc_t( 'brand' ), $views['tratamentos'][ $lang ], dc_t( 'treatments_page_copy' ) ); ?></div></section><section class="section"><div class="container"><?php dc_treatments_grid(); ?></div></section>
<?php elseif ( 'tratamento' === $view && $item ) : ?>
	<section class="section"><div class="container treatment-detail"><div class="reveal"><p class="eyebrow"><?php echo esc_html( $item['duration'] ); ?></p><h1><?php echo esc_html( dc_l( $item['name'] ) ); ?></h1><p><?php echo esc_html( dc_l( $item['desc'] ) ); ?></p><ul class="process"><?php foreach ( dc_t( 'treatment_process' ) as $step ) : ?><li><?php echo esc_html( $step ); ?></li><?php endforeach; ?></ul><p><strong><?php echo esc_html( dc_l( $item['price'] ) ); ?></strong></p><a class="button primary" href="<?php echo esc_url( dc_url( 'marcar' ) ); ?>"><?php echo esc_html( dc_t( 'book' ) ); ?></a></div><img class="feature-img reveal" src="<?php echo esc_url( dc_img( 'chair', 900, 900 ) ); ?>" alt="<?php echo esc_attr( dc_l( $item['name'] ) ); ?>" loading="lazy"></div></section>
<?php elseif ( 'marcar' === $view ) : ?>
	<section class="page-hero"><div class="container"><?php dc_section_head( dc_t( 'brand' ), dc_t( 'book' ), dc_t( 'booking_page_copy' ) ); ?></div></section><section class="section"><div class="container"><?php dc_booking_form(); ?></div></section>
<?php elseif ( 'equipa' === $view ) : ?>
	<section class="page-hero"><div class="container"><?php dc_section_head( dc_t( 'brand' ), dc_t( 'team' ), dc_t( 'team_page_copy' ) ); ?></div></section><section class="section"><div class="container"><?php dc_team_grid(); ?></div></section>
<?php elseif ( 'sobre' === $view ) : ?>
	<section class="section"><div class="container split"><div class="reveal"><p class="eyebrow"><?php echo esc_html( dc_t( 'clinic_about_kicker' ) ); ?></p><h1><?php echo esc_html( dc_t( 'clinic_about_title' ) ); ?></h1><p><?php echo esc_html( dc_t( 'clinic_about_copy' ) ); ?></p><ul class="process"><?php foreach ( dc_t( 'clinic_features' ) as $feature ) : ?><li><?php echo esc_html( $feature ); ?></li><?php endforeach; ?></ul></div><img class="feature-img reveal" src="<?php echo esc_url( dc_img( 'clinic', 900, 900 ) ); ?>" alt="<?php echo esc_attr( dc_t( 'clinic_alt' ) ); ?>" loading="lazy"></div></section>
<?php elseif ( 'resultados' === $view ) : ?>
	<section class="page-hero"><div class="container"><?php dc_section_head( dc_t( 'brand' ), dc_t( 'results' ), dc_t( 'results_page_copy' ) ); ?></div></section><section class="section"><div class="container"><?php dc_results_grid(); ?></div></section>
<?php elseif ( 'testemunhos' === $view ) : ?>
	<section class="section"><div class="container"><?php dc_section_head( dc_t( 'brand' ), dc_t( 'reviews' ), dc_t( 'reviews_page_copy' ) ); ?><div class="review-grid"><?php foreach ( dc_reviews() as $review ) : ?><blockquote class="reveal"><p><?php echo esc_html( dc_l( $review['text'] ) ); ?></p><cite><?php echo esc_html( $review['name'] ); ?></cite></blockquote><?php endforeach; ?></div></div></section>
<?php elseif ( 'blog' === $view ) : ?>
	<section class="page-hero"><div class="container"><?php dc_section_head( dc_t( 'brand' ), dc_t( 'blog_title' ), dc_t( 'blog_copy' ) ); ?></div></section><section class="section"><div class="container blog-grid"><?php foreach ( dc_posts() as $post ) : ?><article class="blog-card reveal"><span><?php echo esc_html( dc_l( $post['date'] ) ); ?></span><h3><?php echo esc_html( dc_l( $post['title'] ) ); ?></h3><p><?php echo esc_html( dc_l( $post['text'] ) ); ?></p></article><?php endforeach; ?></div></section>
<?php elseif ( 'contacto' === $view ) : ?>
	<section class="section"><div class="container contact-grid"><div class="reveal"><p class="eyebrow">Lisboa</p><h1><?php echo esc_html( dc_t( 'contact' ) ); ?></h1><ul class="process"><li>+351 930 511 187</li><li>webpromknf@gmail.com</li><li>Avenida da Saúde Oral, 45 - Lisboa</li><li><?php echo esc_html( dc_t( 'schedule' ) ); ?></li><li><a href="https://wa.me/351930511187">WhatsApp click-to-chat</a></li><li><a href="https://www.livroreclamacoes.pt/Inicio/" rel="noopener"><?php echo esc_html( dc_t( 'complaints_book' ) ); ?></a></li></ul></div><form class="contact-form reveal" data-contact-form data-netlify="true" name="dental-contact-demo"><input type="hidden" name="form-name" value="dental-contact-demo"><label><?php echo esc_html( dc_t( 'contact_name' ) ); ?><input name="name" required></label><label>Email<input name="email" type="email" required></label><label><?php echo esc_html( dc_t( 'contact_message' ) ); ?><textarea name="message" rows="5" required></textarea></label><button class="button primary" type="submit"><?php echo esc_html( dc_t( 'send' ) ); ?></button><p data-form-status></p></form><div class="map reveal"><iframe title="Mapa Lisboa" src="https://www.google.com/maps?q=Lisboa%20Portugal&output=embed" loading="lazy"></iframe></div></div></section>
<?php else : ?>
	<section class="section legal"><div class="container narrow reveal"><p class="eyebrow">Legal</p><h1><?php echo esc_html( $views[ $view ][ $lang ] ?? 'Legal' ); ?></h1><p><?php echo esc_html( dc_t( 'legal_page_copy' ) ); ?></p><p><?php echo esc_html( dc_t( 'legal_demo_copy' ) ); ?></p></div></section>
<?php endif; ?>
</main>
<a class="mobile-book" href="<?php echo esc_url( dc_url( 'marcar' ) ); ?>"><?php echo esc_html( dc_t( 'book' ) ); ?></a>
<footer class="site-footer"><div class="container footer-grid"><div><strong><?php echo esc_html( dc_t( 'brand' ) ); ?></strong><p><?php echo esc_html( dc_t( 'tagline' ) ); ?></p></div><div><a href="<?php echo esc_url( dc_url( 'privacidade' ) ); ?>"><?php echo esc_html( $views['privacidade'][ $lang ] ); ?></a><a href="<?php echo esc_url( dc_url( 'termos' ) ); ?>"><?php echo esc_html( $views['termos'][ $lang ] ); ?></a><a href="<?php echo esc_url( dc_url( 'cookies' ) ); ?>"><?php echo esc_html( $views['cookies'][ $lang ] ); ?></a></div><div><a href="<?php echo esc_url( dc_main_site_url() ); ?>"><?php echo esc_html( dc_t( 'back_main' ) ); ?></a><span>ERS DEMO-2026 · OMD <?php echo esc_html( dc_t( 'fictional' ) ); ?></span><span><?php echo esc_html( dc_t( 'footer_demo' ) ); ?></span></div></div></footer>
<div class="toast" data-toast hidden></div>
<?php wp_footer(); ?>
</body>
</html>