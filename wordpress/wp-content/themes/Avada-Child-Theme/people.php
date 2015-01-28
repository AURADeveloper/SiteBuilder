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
								<?php if (get_post_meta(get_the_ID(), 'person_is_root', true)): ?>
									<?php the_content(); ?>
								<?php else: ?>
									<?php $linkedin = get_post_meta(get_the_ID(), 'person_linkedin', true);
									if ($linkedin): ?>
									<a class="pull-right" target="_blank"
									   href="https://www.linkedin.com/profile/view?id=<?php echo $linkedin; ?>">
										<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/linkedin_badge.png" alt="LinkedIn Profile" />
									</a>
									<? endif; ?>
									<h1><?php echo get_post_meta(get_the_ID(), 'person_full_name', true); ?></h1>
									<h2><?php echo get_post_meta(get_the_ID(), 'person_title', true); ?></h2>
									<h3><?php echo get_post_meta(get_the_ID(), 'person_qualification', true); ?></h3>
									<img class="aura-person-photo"
										 alt="<?php echo get_post_meta(get_the_ID(), 'person_full_name', true); ?>"
										 src="<?php echo get_post_meta(get_the_ID(), 'person_photo', true); ?>" />
									<?php the_content(); ?>
								<?php endif; ?>
							</div>
						</div>
						<div class="fusion-one-third one-third fusion-layout-column fusion-column last spacing-yes aura-people-list">
							<div class="fusion-column-wrapper">
								<?php if (get_post_meta(get_the_ID(), 'person_is_root', true)): ?>
									<?php $parent_id = get_the_ID(); ?>
								<?php else: ?>
									<?php $parent_id = wp_get_post_parent_id( get_the_ID() ); ?>
								<?php endif; ?>
								<?php $args = array(
										'sort_order' => 'ASC',
									    'sort_column' => 'menu_order',
										'parent' => $parent_id,
										'post_type' => 'page',
										'post_status' => 'publish'
									);
									$pages = get_pages($args);
									$num_people = count($pages);
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

									foreach($pages as $page):
										$is_current_person = $page->ID == get_the_ID();
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
												<a href="<?php echo get_page_link($page->ID); ?>">
													<div class="<?php echo $person_css; ?>"
														 style="background-image: url('<?php echo get_post_meta($page->ID, 'person_photo', true); ?>');">
														<div class="aura-photo-overlay">
															<?php echo get_post_meta($page->ID, 'person_full_name', true); ?>
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
								endforeach; ?>
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