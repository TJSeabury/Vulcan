<div class="wrap">
	<h1><?= $title; ?></h1>
	<p>Various options to toggle theme functions and components.</p>
	<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting "vulcan_options"
		settings_fields( 'vulcan_test_group' );
		// output setting sections and their fields
		// (sections are registered for "vulcan", each field is registered to a specific section)
		do_settings_sections( 'vulcan' );
		// output save settings button
		submit_button( 'Save Settings' );
		?>
	</form>
</div>