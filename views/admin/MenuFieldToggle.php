<div class="slide_toggle">
	<input type="checkbox" id="<?= $id ?>" name="<?= $id ?>" value="1" <?php checked( '1', get_option( $id ) ) ?> >
	<label for="<?= $id ?>"></label>
</div>
<p><?= $desc ?></p>