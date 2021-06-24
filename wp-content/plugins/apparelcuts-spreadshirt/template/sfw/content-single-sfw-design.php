  <h2><?php the_title(); ?></h2>

  <div class="entry">

    <?php the_content(); ?>

  </div>

	<?php the_terms( get_the_ID(), 'sfw-designgroup', '<p class="--designgroups">', ', ', '</p>' ); ?>

  <?php if( sfw_design_have_articles() ): ?>

    <?php sfw_include_template( 'sfw/loop-sfw-product.php' ); ?>

  <?php endif; ?>

	<?php the_terms( get_the_ID(), 'sfw-designtag', '<p class="--designtags">', ', ', '</p>' ); ?>