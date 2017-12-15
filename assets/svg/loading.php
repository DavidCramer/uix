<?php
header( 'Content-type: image/svg+xml' );
if ( empty( $_GET['base_color'] ) ) {
	$base_color = '#' . stripslashes( $_GET['base_color'] );
} else {
	$color = '#7CB342';
}
if ( ! preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
	$color = '#7CB342';
}
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<svg width='70px' height='70px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-ring-alt">
	<rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect>
	<circle cx="50" cy="50" r="40" stroke="<?php echo $color; ?>" fill="none" stroke-width="10" stroke-linecap="round"></circle>
	<circle cx="50" cy="50" r="40" stroke="#ffffff" fill="none" stroke-width="6" stroke-linecap="round">
		<animate attributeName="stroke-dashoffset" dur="2s" repeatCount="indefinite" from="0" to="502"></animate>
		<animate attributeName="stroke-dasharray" dur="2s" repeatCount="indefinite" values="150.6 100.4;1 250;150.6 100.4"></animate>
	</circle>
</svg>