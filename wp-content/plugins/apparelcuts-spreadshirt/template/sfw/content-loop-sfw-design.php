<div class="cell small-12 medium-6 large-4 grid-margin-x grid-margin-y">

  <?php do_action( 'sfw/theme/design-loop/before' ); ?>

  <a href="<?php the_permalink() ?>">

    <?php sfw_design_image()->img() ?>

  </a>

  <?php do_action( 'sfw/theme/design-loop/before_title' ); ?>

  <h3>

    <a href="<?php the_permalink() ?>">

      <?php the_title() ?>

    </a>

  </h3>

  <?php do_action( 'sfw/theme/design-loop/after' ); ?>

</div>
