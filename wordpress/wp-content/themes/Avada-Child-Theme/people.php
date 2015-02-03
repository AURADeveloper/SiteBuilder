<?php
// Template Name: People
get_header(); ?>
	<div id="content" class="full-width">
		<?php while(have_posts()): the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php echo avada_render_rich_snippets_for_pages(); ?>
			<?php echo avada_featured_images_for_pages(); ?>
			<div class="post-content">
				<div class="fusion-fullwidth fullwidth-box aura-people">
					<div class="avada-row">
						<div class="fusion-two-third two-third fusion-layout-column fusion-column spacing-yes aura-people-profile">
							<div class="fusion-column-wrapper">
								<?php the_content(); ?>
							</div>
						</div>
						<div class="fusion-one-third one-third fusion-layout-column fusion-column last spacing-yes aura-people-list">
							<div class="fusion-column-wrapper">
								<?php $args = array(
									'order' => 'ASC',
									'orderby' => 'menu_order',
									'post_type' => 'person'
								);
								$the_query = new WP_Query($args);
								$num_people = $the_query->post_count;
								$num_people_per_row = 3;
								$current_person = 0;
								switch($num_people_per_row) {
									case 2:
										$column_css = 'fusion-one-half one_half';
										break;
									case 3:
										$column_css = 'fusion-one-third one_third';
										break;
									case 4:
										$column_css = 'fusion-one-fourth one_fourth';
										break;
								}

								while ( $the_query->have_posts() ) :;
									$the_query->next_post();
									$is_current_person = $the_query->post->ID == get_the_ID();
									$end_of_row = ($current_person % $num_people_per_row == ($num_people_per_row-1));
									$last_person = $current_person == $num_people-1;
									$person_css = "aura-person-photo";
									$remaining_cols = $num_people_per_row - ($current_person+1 % $num_people_per_row);

									if ($end_of_row) { $column_css .= ' last'; }
									if ($is_current_person) { $person_css .= ' active'; }

									if ($current_person % $num_people_per_row == 0): ?>
										<div class="avada-row">
									<?php endif; ?>
									<div class="<?php echo $column_css; ?> fusion-column spacing-yes">
										<div class="fusion-column-wrapper">
											<a href="<?php echo get_post_permalink( $the_query->post->ID ); ?>">
												<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $the_query->post->ID ), 'single-post-thumbnail' ); ?>
												<div class="<?php echo $person_css; ?>"
													 style="background-image: url('<?php echo $image[0]; ?>');">
													<div class="aura-photo-overlay">
														<?php echo get_field( 'consultant_name', $the_query->post->ID); ?>
													</div>
												</div>
											</a>
										</div>
									</div>
									<?php if ($last_person && $remaining_cols > 0):
										switch($num_people_per_row):
											case 2:
												echo '<div class="fusion-one-half one_half fusion-column spacing-yes last"></div>';
												break;
											case 3:
												switch($remaining_cols) {
													case 1:
														echo '<div class="fusion-two_thirds two_thirds fusion-column spacing-yes last"></div>';
														break;
													case 2:
														echo '<div class="fusion-one-third one_third fusion-column spacing-yes last"></div>';
														break;
												}
												break;
											case 4:
												switch($remaining_cols) {
													case 1:
														echo '<div class="fusion-three_fourths three_fourths fusion-column spacing-yes last"></div>';
														break;
													case 2:
														echo '<div class="fusion-one-half one_half fusion-column spacing-yes last"></div>';
														break;
													case 3:
														echo '<div class="fusion-one_fourth one_fourth fusion-column spacing-yes last"></div>';
														break;
												}
												break;
										endswitch;
									endif; ?>
									<?php if ($end_of_row || $last_person): ?>
										</div>
									<?php endif; ?>
									<?php $current_person++;
								endwhile; ?>
							</div>
						</div>
						<div class="fusion-clearfix"></div>
					</div>
				</div>
			</div>
		</div>
		<?php endwhile; ?>
	</div>
<?php get_footer(); ?>