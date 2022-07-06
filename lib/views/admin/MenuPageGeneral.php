<?php global $vulcan; ?>
<div class="vulcan-options-page">
	<header>
		<h1><?= $title; ?></h1>
		<p>Various options to toggle theme functions and components.</p>
	</header>
	<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting "vulcan_options"
		settings_fields( $slug );
		// output setting sections and their fields
		// (sections are registered for "vulcan", each field is registered to a specific section)
		?>
		<div class="vulcan-sections-wrapper">
		<?php $vulcan->do_settings_sections( 'vulcan' ); ?>
		</div>
		<?php
		// output save settings button
		submit_button( 'Save Settings' );
		?>
	</form>
</div>