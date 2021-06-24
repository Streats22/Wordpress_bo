<?php sfw_orderform_open(); ?>

  <div class="--configurations show-form-available">

    <div class="--configuration">

      <p><label><?php _e( 'Appearance', 'apparelcuts-spreadshirt' ); ?></label></p>

      <p><?php sfw_orderform_appearance_field(); ?></p>

    </div>

    <div class="--configuration">

      <p><label><?php _e( 'Size', 'apparelcuts-spreadshirt' ); ?></label> <a class="--size-tip" href="#size-table">( <?php _e( 'Size Chart', 'apparelcuts-spreadshirt' ); ?> )</a></p>

      <?php sfw_size_fit_hint( '<p class="--fit-hint">', '</p>');?>

      <p><?php sfw_orderform_size_field(); ?></p>

    </div>

    <?php do_action( 'sfw/theme/orderform/configurations' ); ?>

  </div>




  <?php do_action( 'sfw/theme/orderform/before_price' ); ?>


  <div class="sfw-price">

    <p><?php sfw_stockstate_message(); ?></p>

    <?php sfw_article_price(); ?>
    <?php sfw_price_hint(); ?>

  </div>


  <?php do_action( 'sfw/theme/orderform/after_price' ); ?>


  <div class="--actions">

    <?php sfw_add_to_cart_button(); ?>


    <?php do_action( 'sfw/theme/orderform/actions' ); ?>

  </div>

<?php sfw_orderform_close(); ?>
