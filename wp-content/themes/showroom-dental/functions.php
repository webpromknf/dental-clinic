<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'dc-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap', array(), null );
	wp_enqueue_style( 'dc-main', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0.0' );
	wp_enqueue_script( 'dc-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true );
	wp_localize_script( 'dc-main', 'dcData', array(
		'success' => dc_t( 'success' ),
		'invalid' => dc_t( 'invalid' ),
		'slots' => dc_slots(),
	) );
} );

add_filter( 'query_vars', function( $vars ) {
	$vars[] = 'dc_lang';
	$vars[] = 'dc_view';
	$vars[] = 'dc_item';
	return $vars;
} );

add_action( 'parse_request', function( $wp ) {
	$path = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
	if ( ! $path ) {
		return;
	}
	$parts = explode( '/', $path );
	if ( ! isset( $parts[0] ) || ! array_key_exists( $parts[0], dc_langs() ) ) {
		return;
	}
	$wp->query_vars['dc_lang'] = $parts[0];
	if ( isset( $parts[1] ) && 'tratamento' === $parts[1] && isset( $parts[2] ) ) {
		$wp->query_vars['dc_view'] = 'tratamento';
		$wp->query_vars['dc_item'] = sanitize_title( $parts[2] );
		return;
	}
	$view = isset( $parts[1] ) && $parts[1] ? $parts[1] : 'home';
	if ( array_key_exists( $view, dc_views() ) ) {
		$wp->query_vars['dc_view'] = $view;
	}
} );

add_filter( 'template_include', function( $template ) {
	if ( get_query_var( 'dc_lang' ) || get_query_var( 'dc_view' ) ) {
		return get_template_directory() . '/index.php';
	}
	return $template;
} );

add_filter( 'language_attributes', function() {
	$map = array( 'pt' => 'pt-PT', 'en' => 'en-US', 'es' => 'es-ES' );
	return 'lang="' . esc_attr( $map[ dc_lang() ] ) . '"';
} );

add_action( 'wp_head', 'dc_meta', 1 );

function dc_langs() {
	return array( 'pt' => 'PT', 'en' => 'EN', 'es' => 'ES' );
}

function dc_views() {
	return array(
		'home' => array( 'pt' => 'Inicio', 'en' => 'Home', 'es' => 'Inicio' ),
		'tratamentos' => array( 'pt' => 'Tratamentos', 'en' => 'Treatments', 'es' => 'Tratamientos' ),
		'marcar' => array( 'pt' => 'Marcar', 'en' => 'Book', 'es' => 'Reservar' ),
		'equipa' => array( 'pt' => 'Equipa', 'en' => 'Team', 'es' => 'Equipo' ),
		'sobre' => array( 'pt' => 'Clinica', 'en' => 'Clinic', 'es' => 'Clinica' ),
		'resultados' => array( 'pt' => 'Antes e Depois', 'en' => 'Before and After', 'es' => 'Antes y Despues' ),
		'testemunhos' => array( 'pt' => 'Testemunhos', 'en' => 'Reviews', 'es' => 'Opiniones' ),
		'blog' => array( 'pt' => 'Blog', 'en' => 'Journal', 'es' => 'Blog' ),
		'contacto' => array( 'pt' => 'Contacto', 'en' => 'Contact', 'es' => 'Contacto' ),
		'privacidade' => array( 'pt' => 'Privacidade', 'en' => 'Privacy', 'es' => 'Privacidad' ),
		'termos' => array( 'pt' => 'Termos', 'en' => 'Terms', 'es' => 'Terminos' ),
		'cookies' => array( 'pt' => 'Cookies', 'en' => 'Cookies', 'es' => 'Cookies' ),
	);
}

function dc_lang() {
	$lang = get_query_var( 'dc_lang' );
	return array_key_exists( $lang, dc_langs() ) ? $lang : 'pt';
}

function dc_view() {
	return get_query_var( 'dc_view' ) ? get_query_var( 'dc_view' ) : 'home';
}

function dc_url( $view = 'home', $lang = null ) {
	$lang = $lang ? $lang : dc_lang();
	return 'home' === $view ? '/' . $lang . '/' : '/' . $lang . '/' . $view . '/';
}

function dc_treatment_url( $slug, $lang = null ) {
	$lang = $lang ? $lang : dc_lang();
	return '/' . $lang . '/tratamento/' . $slug . '/';
}

function dc_main_site_url( $lang = null ) {
	$lang = $lang ? $lang : dc_lang();
	$map = array( 'pt' => 'https://pt.webpromk.com/', 'en' => 'https://us.webpromk.com/', 'es' => 'https://es.webpromk.com/' );
	return $map[ $lang ];
}

function dc_texts() {
	return array(
		'brand' => array( 'pt' => 'Clinica Oral Lumina', 'en' => 'Lumina Dental Clinic', 'es' => 'Clinica Oral Lumina' ),
		'tagline' => array( 'pt' => 'Medicina dentaria moderna em Lisboa', 'en' => 'Modern dental care in Lisbon', 'es' => 'Odontologia moderna en Lisboa' ),
		'hero_title' => array( 'pt' => 'Sorrisos saudaveis com cuidado, tecnologia e confianca.', 'en' => 'Healthy smiles with care, technology and trust.', 'es' => 'Sonrisas saludables con cuidado, tecnologia y confianza.' ),
		'hero_copy' => array( 'pt' => 'Showroom para clinica dentaria com marcacao online simulada, tratamentos, equipa medica, resultados e SEO local para saude oral.', 'en' => 'Dental clinic showroom with simulated online booking, treatments, medical team, results and local oral-health SEO.', 'es' => 'Showroom para clinica dental con reserva online simulada, tratamientos, equipo medico, resultados y SEO local.' ),
		'hero_alt' => array( 'pt' => 'Clinica dentaria moderna e luminosa', 'en' => 'Bright modern dental clinic', 'es' => 'Clinica dental moderna y luminosa' ),
		'book' => array( 'pt' => 'Marcar Consulta', 'en' => 'Book Appointment', 'es' => 'Reservar Consulta' ),
		'treatments' => array( 'pt' => 'Tratamentos principais', 'en' => 'Main treatments', 'es' => 'Tratamientos principales' ),
		'why' => array( 'pt' => 'Porque escolher-nos', 'en' => 'Why choose us', 'es' => 'Por que elegirnos' ),
		'team' => array( 'pt' => 'Equipa medica', 'en' => 'Medical team', 'es' => 'Equipo medico' ),
		'results' => array( 'pt' => 'Antes e depois', 'en' => 'Before and after', 'es' => 'Antes y despues' ),
		'reviews' => array( 'pt' => 'Testemunhos de pacientes', 'en' => 'Patient reviews', 'es' => 'Opiniones de pacientes' ),
		'back_main' => array( 'pt' => 'Voltar a WebPromk', 'en' => 'Back to WebPromk', 'es' => 'Volver a WebPromk' ),
		'view_treatment' => array( 'pt' => 'Ver tratamento', 'en' => 'View treatment', 'es' => 'Ver tratamiento' ),
		'view_treatments' => array( 'pt' => 'Ver tratamentos', 'en' => 'View treatments', 'es' => 'Ver tratamientos' ),
		'digital_xray' => array( 'pt' => 'Raio-X digital', 'en' => 'Digital X-ray', 'es' => 'Radiografia digital' ),
		'sterilisation_protocols' => array( 'pt' => 'Protocolos de esterilizacao', 'en' => 'Sterilisation protocols', 'es' => 'Protocolos de esterilizacion' ),
		'online_booking' => array( 'pt' => 'Marcacao online', 'en' => 'Online booking', 'es' => 'Reserva online' ),
		'booking_intro_title' => array( 'pt' => 'Escolha tratamento, medico, data e hora em poucos passos.', 'en' => 'Choose treatment, dentist, date and time in a few steps.', 'es' => 'Elige tratamiento, dentista, fecha y hora en pocos pasos.' ),
		'booking_intro_copy' => array( 'pt' => 'Fluxo estatico em JavaScript, ideal para demonstrar uma experiencia de marcacao sem backend.', 'en' => 'Static JavaScript flow, ideal for demonstrating a booking experience without a backend.', 'es' => 'Flujo estatico en JavaScript, ideal para demostrar una experiencia de reserva sin backend.' ),
		'tech_alt' => array( 'pt' => 'Tecnologia dentaria moderna', 'en' => 'Modern dental technology', 'es' => 'Tecnologia dental moderna' ),
		'patients_demo' => array( 'pt' => '+8.500 pacientes demo', 'en' => '+8,500 demo patients', 'es' => '+8.500 pacientes demo' ),
		'experience_years' => array( 'pt' => '15 anos de experiencia', 'en' => '15 years of experience', 'es' => '15 anos de experiencia' ),
		'quick_whatsapp' => array( 'pt' => 'WhatsApp rapido', 'en' => 'Quick WhatsApp', 'es' => 'WhatsApp rapido' ),
		'results_note' => array( 'pt' => 'Resultados demonstrativos com nota legal.', 'en' => 'Demo results with a legal notice.', 'es' => 'Resultados demostrativos con aviso legal.' ),
		'results_disclaimer' => array( 'pt' => 'Resultados demonstrativos. Podem variar de paciente para paciente.', 'en' => 'Demo results. Outcomes may vary from patient to patient.', 'es' => 'Resultados demostrativos. Pueden variar de un paciente a otro.' ),
		'result_before_after_alt' => array( 'pt' => 'Resultado antes e depois', 'en' => 'Before and after result', 'es' => 'Resultado antes y despues' ),
		'result_after_alt' => array( 'pt' => 'Resultado depois', 'en' => 'After result', 'es' => 'Resultado despues' ),
		'compare_results' => array( 'pt' => 'Comparar antes e depois', 'en' => 'Compare before and after', 'es' => 'Comparar antes y despues' ),
		'treatments_page_copy' => array( 'pt' => 'Servicos organizados por especialidade, duracao e CTA de marcacao.', 'en' => 'Services organised by specialty, duration and booking CTA.', 'es' => 'Servicios organizados por especialidad, duracion y CTA de reserva.' ),
		'treatment_process' => array( 'pt' => array( 'Avaliacao clinica e diagnostico digital.', 'Plano de tratamento explicado em linguagem clara.', 'Execucao com protocolos de seguranca e conforto.', 'Acompanhamento e recomendacoes pos-consulta.' ), 'en' => array( 'Clinical assessment and digital diagnosis.', 'Treatment plan explained in clear language.', 'Procedure with safety and comfort protocols.', 'Follow-up and post-appointment recommendations.' ), 'es' => array( 'Evaluacion clinica y diagnostico digital.', 'Plan de tratamiento explicado de forma clara.', 'Ejecucion con protocolos de seguridad y confort.', 'Seguimiento y recomendaciones posteriores.' ) ),
		'booking_page_copy' => array( 'pt' => 'Sistema de marcacao multi-passo simulado, sem envio real.', 'en' => 'Simulated multi-step booking system, with no real submission.', 'es' => 'Sistema de reserva multipaso simulado, sin envio real.' ),
		'booking_demo_note' => array( 'pt' => 'Demonstracao - nenhuma marcacao sera processada. Dados de saude exigem consentimento explicito.', 'en' => 'Demo - no appointment will be processed. Health data requires explicit consent.', 'es' => 'Demostracion - no se procesara ninguna cita. Los datos de salud requieren consentimiento explicito.' ),
		'step_treatment' => array( 'pt' => 'Tratamento', 'en' => 'Treatment', 'es' => 'Tratamiento' ),
		'step_dentist' => array( 'pt' => 'Dentista', 'en' => 'Dentist', 'es' => 'Dentista' ),
		'step_date_time' => array( 'pt' => 'Data e hora', 'en' => 'Date and time', 'es' => 'Fecha y hora' ),
		'date' => array( 'pt' => 'Data', 'en' => 'Date', 'es' => 'Fecha' ),
		'time' => array( 'pt' => 'Hora', 'en' => 'Time', 'es' => 'Hora' ),
		'slot_note' => array( 'pt' => 'Horarios ocupados sao simulados visualmente.', 'en' => 'Busy time slots are visually simulated.', 'es' => 'Los horarios ocupados se simulan visualmente.' ),
		'patient_details' => array( 'pt' => 'Dados do paciente', 'en' => 'Patient details', 'es' => 'Datos del paciente' ),
		'phone' => array( 'pt' => 'Telefone', 'en' => 'Phone', 'es' => 'Telefono' ),
		'consent' => array( 'pt' => 'Autorizo o tratamento dos dados para resposta a este pedido demonstrativo.', 'en' => 'I authorise data processing to respond to this demo request.', 'es' => 'Autorizo el tratamiento de datos para responder a esta solicitud demostrativa.' ),
		'notes' => array( 'pt' => 'Observacoes', 'en' => 'Notes', 'es' => 'Observaciones' ),
		'confirmation' => array( 'pt' => 'Confirmacao', 'en' => 'Confirmation', 'es' => 'Confirmacion' ),
		'booking_summary' => array( 'pt' => 'Confirme os dados para criar a marcacao simulada.', 'en' => 'Confirm the details to create the simulated appointment.', 'es' => 'Confirma los datos para crear la cita simulada.' ),
		'previous' => array( 'pt' => 'Anterior', 'en' => 'Previous', 'es' => 'Anterior' ),
		'next' => array( 'pt' => 'Seguinte', 'en' => 'Next', 'es' => 'Siguiente' ),
		'team_page_copy' => array( 'pt' => 'Perfis com especialidade, experiencia e numero OMD ficticio.', 'en' => 'Profiles with specialty, experience and fictional OMD number.', 'es' => 'Perfiles con especialidad, experiencia y numero OMD ficticio.' ),
		'results_page_copy' => array( 'pt' => 'Galeria demonstrativa. Resultados podem variar.', 'en' => 'Demo gallery. Results may vary.', 'es' => 'Galeria demostrativa. Los resultados pueden variar.' ),
		'reviews_page_copy' => array( 'pt' => 'Avaliacoes simuladas e prova social para conversao.', 'en' => 'Simulated reviews and social proof for conversion.', 'es' => 'Opiniones simuladas y prueba social para conversion.' ),
		'blog_title' => array( 'pt' => 'Blog de Saude Oral', 'en' => 'Oral Health Journal', 'es' => 'Blog de Salud Oral' ),
		'blog_copy' => array( 'pt' => 'Conteudo SEO sobre prevencao, ortodontia, implantes e higiene oral.', 'en' => 'SEO content about prevention, orthodontics, implants and oral hygiene.', 'es' => 'Contenido SEO sobre prevencion, ortodoncia, implantes e higiene oral.' ),
		'clinic_about_kicker' => array( 'pt' => 'Sobre a clinica', 'en' => 'About the clinic', 'es' => 'Sobre la clinica' ),
		'clinic_about_title' => array( 'pt' => 'Espaco clinico moderno com foco em seguranca e conforto.', 'en' => 'A modern clinical space focused on safety and comfort.', 'es' => 'Un espacio clinico moderno centrado en seguridad y confort.' ),
		'clinic_about_copy' => array( 'pt' => 'Instalacoes ficticias mas realistas para demonstrar tecnologia, certificacoes, protocolos de esterilizacao e experiencia do paciente.', 'en' => 'Fictional but realistic facilities to showcase technology, certifications, sterilisation protocols and patient experience.', 'es' => 'Instalaciones ficticias pero realistas para mostrar tecnologia, certificaciones, protocolos de esterilizacion y experiencia del paciente.' ),
		'clinic_features' => array( 'pt' => array( 'Scanner intraoral 3D', 'Raio-X digital de baixa dose', 'Protocolos de esterilizacao auditaveis', 'Registo ERS DEMO-2026' ), 'en' => array( '3D intraoral scanner', 'Low-dose digital X-ray', 'Auditable sterilisation protocols', 'ERS DEMO-2026 registration' ), 'es' => array( 'Escaner intraoral 3D', 'Radiografia digital de baja dosis', 'Protocolos de esterilizacion auditables', 'Registro ERS DEMO-2026' ) ),
		'clinic_alt' => array( 'pt' => 'Instalacoes da clinica', 'en' => 'Clinic facilities', 'es' => 'Instalaciones de la clinica' ),
		'contact' => array( 'pt' => 'Contacto', 'en' => 'Contact', 'es' => 'Contacto' ),
		'contact_name' => array( 'pt' => 'Nome', 'en' => 'Name', 'es' => 'Nombre' ),
		'contact_message' => array( 'pt' => 'Mensagem', 'en' => 'Message', 'es' => 'Mensaje' ),
		'send' => array( 'pt' => 'Enviar', 'en' => 'Send', 'es' => 'Enviar' ),
		'schedule' => array( 'pt' => 'Seg-Sex 09:00-19:00 - Sab 09:00-13:00', 'en' => 'Mon-Fri 09:00-19:00 - Sat 09:00-13:00', 'es' => 'Lun-Vie 09:00-19:00 - Sab 09:00-13:00' ),
		'complaints_book' => array( 'pt' => 'Livro de Reclamacoes Eletronico', 'en' => 'Electronic Complaints Book', 'es' => 'Libro de Reclamaciones Electronico' ),
		'legal_page_copy' => array( 'pt' => 'Pagina demonstrativa para RGPD, cookies, termos, Livro de Reclamacoes, registo ERS, consentimento explicito para dados de saude e aviso medico.', 'en' => 'Demo page for GDPR, cookies, terms, complaints book, ERS registration, explicit consent for health data and medical disclaimer.', 'es' => 'Pagina demostrativa para RGPD, cookies, terminos, libro de reclamaciones, registro ERS, consentimiento explicito para datos de salud y aviso medico.' ),
		'legal_demo_copy' => array( 'pt' => 'O conteudo deste showroom e ficticio, nao substitui avaliacao medica e nenhuma marcacao sera processada.', 'en' => 'This showroom content is fictional, does not replace medical assessment and no appointment will be processed.', 'es' => 'El contenido de este showroom es ficticio, no sustituye una valoracion medica y no se procesara ninguna cita.' ),
		'fictional' => array( 'pt' => 'ficticio', 'en' => 'fictional', 'es' => 'ficticio' ),
		'footer_demo' => array( 'pt' => 'Website demo para showroom WebPromk.', 'en' => 'Demo website for the WebPromk showroom.', 'es' => 'Website demo para el showroom WebPromk.' ),
		'success' => array( 'pt' => 'Marcacao demonstrativa criada. Nenhuma consulta sera processada.', 'en' => 'Demo booking created. No appointment will be processed.', 'es' => 'Reserva demo creada. No se procesara ninguna cita.' ),
		'invalid' => array( 'pt' => 'Preencha os campos obrigatorios para continuar.', 'en' => 'Fill in the required fields to continue.', 'es' => 'Complete los campos obligatorios para continuar.' ),
		'meta' => array( 'pt' => 'Dentista em Lisboa com marcacao online simulada, ortodontia, implantologia, estetica dentaria, equipa medica e SEO local.', 'en' => 'Dentist in Lisbon with simulated online booking, orthodontics, implants, cosmetic dentistry, medical team and local SEO.', 'es' => 'Dentista en Lisboa con reserva online simulada, ortodoncia, implantes, estetica dental, equipo medico y SEO local.' ),
	);
}
function dc_t( $key ) {
	$texts = dc_texts();
	$lang = dc_lang();
	return isset( $texts[ $key ][ $lang ] ) ? $texts[ $key ][ $lang ] : $texts[ $key ]['pt'];
}

function dc_l( $value ) {
	if ( is_array( $value ) ) {
		$lang = dc_lang();
		return isset( $value[ $lang ] ) ? $value[ $lang ] : $value['pt'];
	}
	return $value;
}
function dc_img( $id, $w = 1200, $h = 800 ) {
	$imgs = array(
		'hero' => 'photo-1629909613654-28e377c37b09',
		'clinic' => 'photo-1606811971618-4486d14f3f99',
		'chair' => 'photo-1588776814546-1ffcf47267a5',
		'doctor1' => 'photo-1559839734-2b71ea197ec2',
		'doctor2' => 'photo-1594824476967-48c8b964273f',
		'doctor3' => 'photo-1537368910025-700350fe46c7',
		'doctor4' => 'photo-1622253692010-333f2da6031d',
		'smile' => 'photo-1609840114035-3c981b782dfe',
		'kid' => 'photo-1581056771107-24ca5f033842',
		'tech' => 'photo-1579684385127-1ef15d508118',
	);
	$photo = isset( $imgs[ $id ] ) ? $imgs[ $id ] : $imgs['clinic'];
	return 'https://images.unsplash.com/' . $photo . '?auto=format&fit=crop&w=' . absint( $w ) . '&h=' . absint( $h ) . '&q=82';
}

function dc_treatments() {
	return array(
		array( 'slug' => 'medicina-dentaria-geral', 'icon' => '+', 'name' => array( 'pt' => 'Medicina Dentaria Geral', 'en' => 'General Dentistry', 'es' => 'Odontologia General' ), 'duration' => '30-45 min', 'price' => array( 'pt' => 'desde EUR 45', 'en' => 'from EUR 45', 'es' => 'desde EUR 45' ), 'desc' => array( 'pt' => 'Consultas, restauracoes, diagnostico e prevencao para manter a saude oral no dia a dia.', 'en' => 'Check-ups, fillings, diagnosis and prevention to keep everyday oral health under control.', 'es' => 'Consultas, restauraciones, diagnostico y prevencion para mantener la salud oral diaria.' ) ),
		array( 'slug' => 'ortodontia', 'icon' => 'O', 'name' => array( 'pt' => 'Ortodontia', 'en' => 'Orthodontics', 'es' => 'Ortodoncia' ), 'duration' => '45 min', 'price' => array( 'pt' => 'plano sob avaliacao', 'en' => 'plan after assessment', 'es' => 'plan tras valoracion' ), 'desc' => array( 'pt' => 'Alinhadores transparentes e aparelhos fixos para corrigir o alinhamento dentario.', 'en' => 'Clear aligners and fixed braces to improve dental alignment.', 'es' => 'Alineadores transparentes y brackets para corregir la alineacion dental.' ) ),
		array( 'slug' => 'implantologia', 'icon' => 'I', 'name' => array( 'pt' => 'Implantologia', 'en' => 'Dental Implants', 'es' => 'Implantologia' ), 'duration' => '60-90 min', 'price' => array( 'pt' => 'sob orcamento', 'en' => 'quote after assessment', 'es' => 'presupuesto personalizado' ), 'desc' => array( 'pt' => 'Substituicao de dentes ausentes com planeamento digital e materiais certificados.', 'en' => 'Replacement of missing teeth with digital planning and certified materials.', 'es' => 'Sustitucion de dientes ausentes con planificacion digital y materiales certificados.' ) ),
		array( 'slug' => 'estetica-dentaria', 'icon' => 'E', 'name' => array( 'pt' => 'Estetica Dentaria', 'en' => 'Cosmetic Dentistry', 'es' => 'Estetica Dental' ), 'duration' => '45-60 min', 'price' => array( 'pt' => 'desde EUR 90', 'en' => 'from EUR 90', 'es' => 'desde EUR 90' ), 'desc' => array( 'pt' => 'Facetas, recontorno e solucoes esteticas para um sorriso natural e harmonioso.', 'en' => 'Veneers, reshaping and cosmetic solutions for a natural, balanced smile.', 'es' => 'Carillas, remodelado y soluciones esteticas para una sonrisa natural y armoniosa.' ) ),
		array( 'slug' => 'branqueamento', 'icon' => 'W', 'name' => array( 'pt' => 'Branqueamento', 'en' => 'Teeth Whitening', 'es' => 'Blanqueamiento' ), 'duration' => '60 min', 'price' => array( 'pt' => 'desde EUR 180', 'en' => 'from EUR 180', 'es' => 'desde EUR 180' ), 'desc' => array( 'pt' => 'Branqueamento dentario seguro, acompanhado por medico dentista.', 'en' => 'Safe teeth whitening supervised by a dentist.', 'es' => 'Blanqueamiento dental seguro supervisado por un dentista.' ) ),
		array( 'slug' => 'periodontologia', 'icon' => 'P', 'name' => array( 'pt' => 'Periodontologia', 'en' => 'Periodontology', 'es' => 'Periodoncia' ), 'duration' => '45 min', 'price' => array( 'pt' => 'desde EUR 65', 'en' => 'from EUR 65', 'es' => 'desde EUR 65' ), 'desc' => array( 'pt' => 'Tratamento das gengivas, destartarizacao avancada e acompanhamento periodontal.', 'en' => 'Gum care, advanced scaling and periodontal follow-up.', 'es' => 'Tratamiento de encias, limpieza avanzada y seguimiento periodontal.' ) ),
		array( 'slug' => 'odontopediatria', 'icon' => 'K', 'name' => array( 'pt' => 'Odontopediatria', 'en' => 'Paediatric Dentistry', 'es' => 'Odontopediatria' ), 'duration' => '30 min', 'price' => array( 'pt' => 'desde EUR 40', 'en' => 'from EUR 40', 'es' => 'desde EUR 40' ), 'desc' => array( 'pt' => 'Cuidados dentarios para criancas num ambiente tranquilo e educativo.', 'en' => 'Dental care for children in a calm, educational setting.', 'es' => 'Atencion dental infantil en un entorno tranquilo y educativo.' ) ),
	);
}
function dc_treatment_by_slug( $slug ) {
	foreach ( dc_treatments() as $treatment ) {
		if ( $treatment['slug'] === $slug ) {
			return $treatment;
		}
	}
	return null;
}

function dc_doctors() {
	return array(
		array( 'id' => 'sofia', 'name' => 'Dra. Sofia Almeida', 'role' => array( 'pt' => 'Direcao clinica - Estetica Dentaria', 'en' => 'Clinical Director - Cosmetic Dentistry', 'es' => 'Direccion clinica - Estetica Dental' ), 'reg' => 'OMD 12456', 'exp' => array( 'pt' => '15 anos', 'en' => '15 years', 'es' => '15 anos' ), 'image' => dc_img( 'doctor2', 700, 800 ), 'bio' => array( 'pt' => 'Foco em estetica dentaria conservadora, planeamento digital e comunicacao clara com o paciente.', 'en' => 'Focused on conservative cosmetic dentistry, digital planning and clear patient communication.', 'es' => 'Especializada en estetica dental conservadora, planificacion digital y comunicacion clara con el paciente.' ) ),
		array( 'id' => 'miguel', 'name' => 'Dr. Miguel Rocha', 'role' => array( 'pt' => 'Implantologia e Cirurgia Oral', 'en' => 'Implantology and Oral Surgery', 'es' => 'Implantologia y Cirugia Oral' ), 'reg' => 'OMD 15680', 'exp' => array( 'pt' => '13 anos', 'en' => '13 years', 'es' => '13 anos' ), 'image' => dc_img( 'doctor3', 700, 800 ), 'bio' => array( 'pt' => 'Experiencia em reabilitacao oral, implantes guiados e procedimentos minimamente invasivos.', 'en' => 'Experienced in oral rehabilitation, guided implants and minimally invasive procedures.', 'es' => 'Experiencia en rehabilitacion oral, implantes guiados y procedimientos minimamente invasivos.' ) ),
		array( 'id' => 'ines', 'name' => 'Dra. Ines Costa', 'role' => array( 'pt' => 'Ortodontia', 'en' => 'Orthodontics', 'es' => 'Ortodoncia' ), 'reg' => 'OMD 17842', 'exp' => array( 'pt' => '9 anos', 'en' => '9 years', 'es' => '9 anos' ), 'image' => dc_img( 'doctor1', 700, 800 ), 'bio' => array( 'pt' => 'Ortodontia com alinhadores e aparelhos fixos para adolescentes e adultos.', 'en' => 'Orthodontics with aligners and fixed braces for teenagers and adults.', 'es' => 'Ortodoncia con alineadores y brackets para adolescentes y adultos.' ) ),
		array( 'id' => 'tiago', 'name' => 'Dr. Tiago Matos', 'role' => array( 'pt' => 'Medicina Dentaria Geral', 'en' => 'General Dentistry', 'es' => 'Odontologia General' ), 'reg' => 'OMD 20114', 'exp' => array( 'pt' => '8 anos', 'en' => '8 years', 'es' => '8 anos' ), 'image' => dc_img( 'doctor4', 700, 800 ), 'bio' => array( 'pt' => 'Prevencao, restauracao e atendimento de urgencia com abordagem tranquila.', 'en' => 'Prevention, restorative care and urgent appointments with a calm approach.', 'es' => 'Prevencion, restauracion y urgencias con un enfoque tranquilo.' ) ),
	);
}
function dc_reviews() {
	return array(
		array( 'name' => 'Marta Silva', 'text' => array( 'pt' => 'Marcacao simples, equipa muito cuidadosa e explicacao clara do tratamento.', 'en' => 'Simple booking, very careful team and clear explanation of the treatment.', 'es' => 'Reserva sencilla, equipo muy atento y explicacion clara del tratamiento.' ) ),
		array( 'name' => 'Pedro Martins', 'text' => array( 'pt' => 'A consulta de ortodontia foi tranquila e senti confianca desde o primeiro minuto.', 'en' => 'The orthodontic appointment was calm and I felt confident from the first minute.', 'es' => 'La consulta de ortodoncia fue tranquila y senti confianza desde el primer minuto.' ) ),
		array( 'name' => 'Ana Ribeiro', 'text' => array( 'pt' => 'Excelente higiene, tecnologia moderna e atendimento sem pressa.', 'en' => 'Excellent hygiene, modern technology and unhurried care.', 'es' => 'Excelente higiene, tecnologia moderna y atencion sin prisas.' ) ),
		array( 'name' => 'Joao Costa', 'text' => array( 'pt' => 'Gostei do resumo antes de confirmar a marcacao. Muito profissional.', 'en' => 'I liked the summary before confirming the appointment. Very professional.', 'es' => 'Me gusto el resumen antes de confirmar la cita. Muy profesional.' ) ),
	);
}

function dc_posts() {
	return array(
		array( 'title' => array( 'pt' => 'Como prevenir caries no dia a dia', 'en' => 'How to prevent cavities every day', 'es' => 'Como prevenir caries en el dia a dia' ), 'date' => array( 'pt' => '18 Setembro 2026', 'en' => '18 September 2026', 'es' => '18 septiembre 2026' ), 'text' => array( 'pt' => 'Habitos simples de higiene oral, alimentacao e consultas de rotina.', 'en' => 'Simple habits for oral hygiene, nutrition and routine check-ups.', 'es' => 'Habitos sencillos de higiene oral, alimentacion y revisiones rutinarias.' ) ),
		array( 'title' => array( 'pt' => 'Alinhadores invisiveis: para quem sao indicados?', 'en' => 'Clear aligners: who are they for?', 'es' => 'Alineadores invisibles: para quien estan indicados?' ), 'date' => array( 'pt' => '02 Setembro 2026', 'en' => '02 September 2026', 'es' => '02 septiembre 2026' ), 'text' => array( 'pt' => 'Vantagens, cuidados e expectativas realistas antes de iniciar ortodontia.', 'en' => 'Benefits, care tips and realistic expectations before starting orthodontics.', 'es' => 'Ventajas, cuidados y expectativas realistas antes de iniciar ortodoncia.' ) ),
		array( 'title' => array( 'pt' => 'Implantes dentarios: fases do tratamento', 'en' => 'Dental implants: treatment stages', 'es' => 'Implantes dentales: fases del tratamiento' ), 'date' => array( 'pt' => '20 Agosto 2026', 'en' => '20 August 2026', 'es' => '20 agosto 2026' ), 'text' => array( 'pt' => 'Diagnostico, planeamento digital, cirurgia e reabilitacao.', 'en' => 'Diagnosis, digital planning, surgery and rehabilitation.', 'es' => 'Diagnostico, planificacion digital, cirugia y rehabilitacion.' ) ),
	);
}
function dc_slots() {
	return array( '09:00', '09:30', '10:00', '10:30', '11:30', '12:00', '14:30', '15:00', '15:30', '16:30', '17:00', '18:00' );
}

function dc_meta() {
	if ( is_admin() ) {
		return;
	}
	$title = dc_t( 'brand' ) . ' - ' . dc_t( 'tagline' );
	$desc = dc_t( 'meta' );
	$image = dc_img( 'hero', 1200, 630 );
	$item = dc_treatment_by_slug( get_query_var( 'dc_item' ) );
	$schema = array(
		'@context' => 'https://schema.org',
		'@type' => array( 'Dentist', 'MedicalClinic' ),
		'name' => dc_t( 'brand' ),
		'description' => $desc,
		'url' => dc_url(),
		'image' => $image,
		'telephone' => '+351 930 511 187',
		'email' => 'webpromknf@gmail.com',
		'medicalSpecialty' => array( 'Dentistry', 'Orthodontics', 'Oral Surgery' ),
		'address' => array( '@type' => 'PostalAddress', 'streetAddress' => 'Avenida da SaÃƒÂºde Oral, 45', 'addressLocality' => 'Lisboa', 'addressCountry' => 'PT' ),
	);
	if ( $item ) {
		$title = dc_l( $item['name'] ) . ' - ' . dc_t( 'brand' );
		$desc = dc_l( $item['desc'] );
		$schema = array( '@context' => 'https://schema.org', '@type' => 'MedicalProcedure', 'name' => dc_l( $item['name'] ), 'description' => dc_l( $item['desc'] ), 'bodyLocation' => 'Mouth', 'procedureType' => 'Dental treatment' );
	}
	echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
