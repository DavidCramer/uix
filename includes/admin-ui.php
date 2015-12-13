<div class="wrap">
	<h1><?php esc_html_e( $uix['page_title'] , 'uix' ); ?> <a class="page-title-action" href="#save-object" data-save-object="true"><?php echo esc_html__( 'Save Changes', 'uix' ); ?></a></h1>
	<?php if( !empty( $uix['tabs'] ) ){ ?>
	<nav class="uix-sub-nav">
		<?php foreach( (array) $uix['tabs'] as $tab_slug => $tab ){ ?><a data-tab="<?php echo esc_attr( $tab_slug ); ?>" href="#<?php echo esc_attr( $tab_slug ) ?>"><?php echo esc_html( $tab['menu_title'] ); ?></a><?php } ?>
	</nav>
	<?php } ?>
	<?php wp_nonce_field( 'uix', 'uix_setup' ); ?>
	<?php foreach( (array) $uix['tabs'] as $tab_slug => $tab ){ ?>
		<div class="uix-tab-canvas" data-app="<?php echo esc_attr( $tab_slug ); ?>"></div>
		<script type="text/html" data-template="<?php echo esc_attr( $tab_slug ); ?>">
			<h4><?php 
				echo esc_attr( $tab['page_title'] ); 
				if( !empty( $tab['page_description'] ) ){ ?> <small><?php echo $tab['page_description']; ?></small> <?php } 
			?></h4>
			<?php
				// include this tabs template
				if( !empty( $tab['template'] ) && file_exists( $tab['template'] ) ){
					include $tab['template'];
				}else{
					echo esc_html__( 'Template not found: ', 'uix' ) . $tab['page_title'];
				}
			?>
		</script>
		<?php if( !empty( $tab['partials'] ) ){
			foreach( $tab['partials'] as $partial_id => $partial ){
				?>
				<script type="text/html" data-handlebars-partial="<?php echo esc_attr( $partial_id ); ?>">
					<?php
						// include this tabs template
						if( !empty( $partial ) && file_exists( $partial ) ){
							include $partial;
						}else{
							echo esc_html__( 'Partial Template not found: ', 'uix' ) . $partial_id;
						}
					?>
				</script>
				<?php
			}
		}
	} ?>
</div>