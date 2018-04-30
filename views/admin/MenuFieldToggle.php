<div class="vulcan-toggle">
	<div class="slidebox">
		<input type="checkbox" id="<?= $id; ?>" name="<?= $id; ?>" value="1" <?php checked( '1', get_option( $id ) ); ?> >
		<label for="<?= $id; ?>"></label>
	</div>
</div>
<p><?= $desc; ?></p>