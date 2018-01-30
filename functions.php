function ed_ajax_job_filter_function(){

	$pos_filter = $_POST['pos_filt'];
	$loc_filter = $_POST['loc_filt'];	
	
	$args = array(
		'post_type' => 'jobs',
	);
	
	if( isset( $_POST['pos_filt'] ) && !isset( $_POST['loc_filt'] ))	
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'job_category',
				'field' => 'slug',
				'terms' => $pos_filter
			)
		);
			
	if( isset( $_POST['loc_filt'] ) && !isset( $_POST['pos_filt'] ))
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'job_location',
				'field' => 'slug',
				'terms' => $loc_filter
			)
		);
	
	if( isset( $_POST['loc_filt'] ) && isset( $_POST['pos_filt'] ))	
		$args['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'job_category',
				'field' => 'slug',
				'terms' => $pos_filter
			),
			array(
				'taxonomy' => 'job_location',
				'field' => 'slug',
				'terms' => $loc_filter
			)
		);
	 
 
	$query = new WP_Query( $args );
 
	if( $query->have_posts() ) :?>
	
	<?php while( $query->have_posts() ): $query->the_post();
			$post = $query->post; ?>
			<div class="col-md-4">
				<div class="a360job" data-job-id="<?php echo $post->ID; ?>">
					<div class="job_header">				
						<h3><?php echo $post->post_title; ?></h3>
						<?php $term_list = wp_get_post_terms($post->ID, 'job_location', array("fields" => "all")); ?>
						<?php foreach ( $term_list as $term ) : ?>
							<span class="job_location"><?php echo $term->name; ?></span>
						<?php endforeach; ?>
					</div>
					<p class="green">Details <img src="/wp-content/uploads/2018/01/greenarrow.svg" alt="job details arrow icon" class="job_icon_detail"/></p>									
				</div>
			</div>
			
			<?php endwhile; ?>
			<script>	
			jQuery(function($){
				$('.a360job').on('click', function () {
					$('#response').hide();
					$("#job_fil_sidebar").addClass("sidebar_narrow").removeClass("col-md-3");
					$("#job_fil_sidebar").html("<a href='/jobs/'><img src='/wp-content/uploads/2018/01/greenarrow.svg' alt='backarrow icon' class='back_arrow_icon' />back</a>");
					$.post(ajax_object.ajaxurl, {
						action: 'singlejob',
						post_id: $(this).attr('data-job-id'),
						},
						function(data) {
						$('#job_details').html(data);		
					});
			
				});
			});
			</script>
		<?php 
		wp_reset_postdata();
	else :
		echo 'No jobs found';
	endif;
 
 
	die();
}
 
add_action('wp_ajax_jobfilter', 'ed_ajax_job_filter_function'); 
add_action('wp_ajax_nopriv_jobfilter', 'ed_ajax_job_filter_function');
