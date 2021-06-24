<?php sfw_include_template( 'sfw/template-before.php' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

      <?php sfw_include_template( 'sfw/content-single-sfw-product.php' ); ?>

    <?php endwhile; ?>

<?php sfw_include_template( 'sfw/template-after.php' ); ?>
