<div class="wrap">
	<h1 class="uix-title"><?php esc_html_e( $uix['page_title'] , 'uix' ); ?>
		<?php if( !empty( $uix['save_button'] ) ){ ?>
		<a class="page-title-action" href="#save-object" data-save-object="true">
			<span class="spinner uix-save-spinner"></span>
			<?php esc_html_e( $uix['save_button'], 'uix' ); ?>
		</a>
		<?php } ?>
	</h1>
	<?php if( !empty( $uix['tabs'] ) ){ ?>
	<nav class="uix-sub-nav" <?php if( count( $uix['tabs'] ) === 1 ){ ?>style="display:none;"<?php } ?>>
		<?php foreach( (array) $uix['tabs'] as $tab_slug => $tab ){ ?><a data-tab="<?php echo esc_attr( $tab_slug ); ?>" href="#<?php echo esc_attr( $tab_slug ) ?>"><?php echo esc_html( $tab['menu_title'] ); ?></a><?php } ?>
	</nav>
	<?php } ?>
	<?php wp_nonce_field( 'uix', 'uix_setup' ); ?>
	<?php 
	if( !empty( $uix['tabs'] ) ){
		foreach( (array) $uix['tabs'] as $tab_slug => $tab ){ ?>
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
		}
	}else{
		if( !empty( $uix['template'] ) && file_exists( $uix['template'] ) ){
			include $uix['template'];
		}
	}
	?>
</div>

<script type="text/html" data-template="__notice">
<div class="{{#if success}}updated{{else}}error{{/if}} notice uix-notice is-dismissible">
	<p>{{{data}}}</p>
	<button class="notice-dismiss" type="button">
		<span class="screen-reader-text">Dismiss this notice.</span>
	</button>
</div>
</script>