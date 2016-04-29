<?php
global $object, $post, $prev_id, $next_id, $inside, $mon_docs, $mon_id;

if ( is_single() ) $object = $post;
$post_id = $object->ID;
$logged = is_user_logged_in();
$oz_meta = get_post_custom( $post_id );
// $count = 0;
foreach ( $oz_meta as $key => $value ) {
	if ( strpos( $key, 'oz_' ) !== 0 ) {
		unset( $oz_meta[$key] );
	}
}

$jpegs = json_decode($oz_meta['oz_jpegs'][0], true);
$pdfID = $oz_meta['oz_pdf'];
$djvu = $oz_meta['oz_djvu'];
$oz_meta['oz_jpegs'][0] = count($jpegs);
$mon_ids = json_decode($oz_meta['oz_monuments'][0], true);
unset( $oz_meta['oz_pdf'] );
unset( $oz_meta['oz_djvu'] );
unset( $oz_meta['oz_monuments'] );
$labels = array (
	'oz_document_institution' => __( 'Nazwa instytucji', 'otwarte2013' ),
	'oz_document_type' => __( 'Typ publikacji', 'otwarte2013' ),
	'oz_jpegs' => __( 'Liczba stron skanów', 'otwarte2013' ),
	'oz_document_scans' => __( 'Liczba stron oryginału', 'otwarte2013' ),
	'oz_document_signature' => __( 'Sygatura/numer zespołu', 'otwarte2013' ),
	'oz_document_date' => __( 'Data powstania', 'otwarte2013' ),
	'oz_document_creator' => __( 'Twórca', 'otwarte2013' ),
	'oz_document_sponsor' => __( 'Projekt/sponsor digitalizacji', 'otwarte2013' ),
	'oz_document_description' => __( 'Opis', 'otwarte2013' ),
	'oz_document_rights' => __('Udostępnianie publikacji cyfrowej', 'otwarte2013'),
	'oz_document_source' => __('Pochodzenie dokumentu', 'otwarte2013'),
	'oz_document_city' => __('Miejscowość', 'otwarte2013'),
	);

// cook cords from lat ang lng
//var_dump( $post );


$inside_ats = '';
if ($inside) {
if (!empty($mon_docs)) {
	$inside_ats .= 'data-documents-ids="' . $mon_docs . '" ';
	$mon_docs = explode(',', $mon_docs);
	$cur = array_search($post_id, $mon_docs);
		if ($cur < 0) :
			$prev_id = '';
		elseif ($cur > count($mon_docs)) :
			$next_id = '';
		else :
			$prev_id = $mon_docs[$cur - 1];
			$next_id = $mon_docs[$cur + 1];
		endif;
	}
}
?>

<section id="inside-controler" class="popup-controls document-controls" <?php echo

$inside_ats?>  data-url="<?php echo get_post_permalink($post_id);?>">
	<?php if ( !is_single() ) : ?>
		<?php if ( !empty( $prev_id ) ) : ?>
			<a id="object-prev" class="popup-nav <?php echo

$inside ? 'inside-nav': '' ;?> arrow-prev-green" data-post-id="<?php echo $prev_id?>">
				<span class="ico"></span>
			</a>
		<?php endif ?>
		<?php if ( !empty( $next_id ) ) : ?>
			<a id="object-next" class="popup-nav <?php echo

$inside ? 'inside-nav': '' ;?> arrow-next-green" data-post-id="<?php echo $next_id?>">
				<span class="ico"></span>
			</a>
		<?php endif; ?>
	<?php endif; ?>

	<div class="row">
		<div class="large-<?php echo

is_user_logged_in() ? '6' : '7'; ?> columns">
			<h5><span class="doc-ico ico"></span>
				<span class="full-title">
					<?php echo $object->post_title ?>
				</span>
				<span class="short-title">
					<?php
					if (is_user_logged_in()) {
						echo substr($object->post_title,0, 50) . '...' ;
					} else {
						echo substr($object->post_title,0, 70) . '...' ;
					}
					?>
				</span>
			</h5>
		</div>
		<div class="large-<?php echo

is_user_logged_in() ? '6' : '5'; ?> columns">
			<div class="button-group large-12 columns popup-info" data-post-id="<?php echo $post_id ?>">
				<a href="<?php echo is_single() ? get_permalink( get_option( 'oz_search_page' ) ) : '#' ;?>" class="close-popup <?php echo (is_single() xor !empty($mon_id) )? '' : 'close-reveal-modal' ;?>" <?php echo

!empty($mon_id) && $inside ? 'data-post-id="' . $mon_id . '" ' : ''; ?>><span class="ico "></span></a>
				<a href="<?php echo wp_get_attachment_url( get_post_meta($post_id, 'oz_pdf', true) );?>" class="oz-button right zip-cnt">
					<span class="ico pdf-ico"></span>
					<span><?php _e( 'Pobierz pdf', 'otwarte2013' ) ?></span>
				</a>
				<a href="<?php echo wp_get_attachment_url( get_post_meta($post_id, 'oz_djvu', true) );?>" class="oz-button right zip-cnt">
					<span class="ico zip-ico"></span>
					<span><?php _e( 'Pobierz DjVu', 'otwarte2013' ) ?></span>
				</a>

				<?php if ( $logged ) : ?>

					<?php if ( is_fav( $post_id ) ) : ?>
						<a href="" class="oz-button right alreadyfav fav-cnt">
							<span class="ico fav-star-ico"></span>
							<span><?php _e( 'Odznacz', 'otwarte2013' ) ?></span>
						</a>
					<?php else : ?>
						<a href="" class="oz-button right fav-cnt">
							<span class="ico fav-star-ico"></span>
							<span><?php _e( 'Zaznacz', 'otwarte2013' ) ?></span>
						</a>
					<?php endif?>
				<?php endif ?>
			</div>
		</div>
	</div>
</section>
<section class="single-cnt single-doc">
	<section id="detailed-info-cnt">
		<div id="detailed-info" class="row">
			<div class="top-section large-12 columns">
				<div class="large-4 columns thumb-cnt">
					<?php echo get_the_post_thumbnail( $post_id, 'single-thumbnail' ); ?>
				</div>
				<div class="large-8 columns data-cnt">
					<table>
					<?php if (!empty($mon_ids)) : ?>
					<tr>
						<td><?php _e('Przypisany do zabytku', 'otwarte2013') ?> </td>
						<td>
							 <?php
							 	foreach ($mon_ids as $key) {
									echo '<a href="' . get_permalink( $key ) . '">' . get_the_title($key) . '</a>';
								}
							?>
						</td>
					</tr>
					<?php endif; ?>
						<?php foreach ( $oz_meta as $label => $value ) : ?>
							<?php //if($value[0]!='') { ?>
							<?php if($label=='oz_document_sponsor') continue; ?>
								<tr>
									<td><?php echo $labels[$label]?> </td>
									<td><?php echo $value[0]?> </td>
								</tr>
							<?php //} ?>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</section>
	<section id="document-browser-cnt">
		<div id="document-browser" class="row">
			<div class="button-group">
				<span class="info-button ico caps"> <span class="closed"> <?php _e('Informacje', 'otwarte2013') ?> </span> <span class="opened"> <?php _e('Zwiń', 'otwarte2013') ?> </span> </span>
				<span id="zoomOutButton" class="zoom-minus ico caps">–</span>
				<span id="zoomInButton" class="zoom-plus ico caps">+</span>
				<span id="nav-next-page" class="next-page ico caps">&gt;</span>
				<span class="page-total ico caps"><?php echo

$oz_meta['oz_jpegs'][0]?></span>
				<span class="page-out-of ico caps"><?php _e('z', 'otwarte2013') ?></span>
				<span class="page-start ico caps">1</span>
				<span id="nav-prev-page" class="prev-page ico caps">&lt;</span>
			</div>

			<div class="large-10 large-centered columns zoom-container">
				<?php echo get_the_post_thumbnail( $post_id, 'full' ); ?>
			</div>
		</div>
	</section>
	<section id="docs-scan-cnt">
	<div class="row">&nbsp;</div>
			<div id="docs-scans" class="large-12 columns large-centered">
				<div class="jcarousel-wrapper">
					<div class="jcarousel">
						<ul>
							<?php
							$count =1 ;
							// for($i=1;$i<20;$i++) {
								foreach ($jpegs as $page) {
									$attachment = wp_get_attachment_image_src( $page, 'full', false );
									echo $count == 1 ? "<li class='active'><a data-full-src=" . $attachment[0] . ">" : "<li><a data-full-src=" . $attachment[0] . ">" ;
									echo wp_get_attachment_image( $page, 'document-page-thumb' );
									echo "</a></li>";
									$count++;
								}
							// }
							?>
						</ul>
					</div>

					<a href="#" class="jcarousel-control-prev">&lsaquo;</a>
					<a href="#" class="jcarousel-control-next">&rsaquo;</a>

					<p class="jcarousel-pagination"></p>
				</div>
			</div>
	</section>
</section>
