<?php if( have_posts()): ?>

  <div class="grid-x article-loop">

    <?php while ( have_posts() ) : the_post(); ?>

      <?php sfw_include_template( 'sfw/content-loop-sfw-product.php' ); ?>

    <?php endwhile; ?>

  </div>

  <?php posts_nav_link(); ?>

<?php endif; ?>