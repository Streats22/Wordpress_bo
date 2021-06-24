<?php




?>
<div class="sfw-image-box">

	<div class="--main-image">

		<?php @sfw_article_image( array( 500, 500 ) )->img(); ?>

	</div>

	<div class="--image-list">

		<?php	while( sfw_have_views() ): ?>

				<?php @sfw_article_image( array( 75, 75 ) )->img();  ?>

		<?php endwhile; ?>

		<?php	while( sfw_have_configurations() ): ?>

				<?php @sfw_configuration_image( array( 75, 75 ) )->img();  ?>

		<?php endwhile; ?>

	</div>

</div>