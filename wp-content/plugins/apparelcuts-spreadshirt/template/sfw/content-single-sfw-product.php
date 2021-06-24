<article itemscope itemtype="http://schema.org/Product" data-article="<?php echo esc_attr( sfw_get_article_id() ); ?>" id="post-<?php the_ID(); ?>" <?php post_class('sfw'); ?> itemscope itemtype="http://schema.org/Product">

	<?php do_action( 'sfw/theme/single-product/before' ); ?>

	<header class="product-header">

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<?php do_action( 'sfw/theme/single-product/header' ); ?>

	</header>


	<div class="grid-x --padding">

		<div class="cell medium-5">

			<?php do_action( 'sfw/theme/single-product/images/before' ); ?>

			<?php sfw_include_template( 'sfw/product-image-box.php' ); ?>

			<?php do_action( 'sfw/theme/single-product/images/after' ); ?>

			<?php sfw_article_design_more(); ?>

			<?php the_terms( get_the_ID(), 'sfw-productgroup', '<p class="--productgroups">', ', ', '</p>' ); ?>

		</div>

		<div class="cell medium-7 ">

			<?php do_action( 'sfw/theme/single-product/content/before' ); ?>

			<?php	the_content(); ?>

			<?php do_action( 'sfw/theme/single-product/content/after' ); ?>

			<?php sfw_include_template( 'sfw/content-single-sfw-product-orderform.php' ); ?>

			<p><?php @sfw_producttype_detail_image( array( 400, 130 ) )->img(); ?></p>

		</div>

	</div>


	<div class=" grid-x ">

		<div class="cell">

			<h3><a href="<?php sfw_producttype_permalink(); ?>"><?php sfw_producttype_name(); ?></a></h3>

		  <?php sfw_brand_name( '<p class="--brand">' . _x( 'by', 'by brand', 'apparelcuts-spreadshirt' ) . ' ', '</p>'); ?>

			<p><?php sfw_producttype_description(); ?></p>

		</div>


		<div class="cell medium-4">

			<?php @sfw_producttype_size_image( array( 200, 200 ) )->img();  ?>

		</div>


		<div class="cell medium-8">

			<?php sfw_size_fit_table(); ?>

		</div>


	</div>


	<footer class="entry-footer">

		<?php do_action( 'sfw/theme/single-product/footer' ); ?>

	</footer>

	<?php do_action( 'sfw/theme/single-product/after' ); ?>

</article>

