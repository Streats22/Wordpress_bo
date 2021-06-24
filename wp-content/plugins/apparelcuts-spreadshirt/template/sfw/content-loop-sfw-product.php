<div class="cell small-12 medium-6 large-4 grid-margin-x grid-margin-y">

  <?php do_action( 'sfw/theme/product-loop/before' ); ?>

  <a href="<?php the_permalink() ?>">

    <?php sfw_article_image()->img() ?>

  </a>

  <?php do_action( 'sfw/theme/product-loop/before_title' ); ?>

  <h3>

    <a href="<?php the_permalink() ?>">

      <?php the_title() ?>

    </a>

  </h3>

  <p>

    <?php sfw_article_price() ?>

  </p>

  <?php do_action( 'sfw/theme/product-loop/after' ); ?>

</div>
