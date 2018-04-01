
<?php // LEGACY archive. ?>

<?php foreach ( $eab_archive->issues as $year => $yr_issues ): ?>
	<?php if ( ! self::legacy_filter( $year ) ) { continue; } ?>
<div id="y<?php echo $year; ?>" class="ten">
	<h3><?php echo $year; ?></h3><ul>

  <?php foreach ( $yr_issues as $iss_num => $iss ): ?>
		<li id="i<?= $iss_num ?>">Issue <?= $iss_num ?>:
			<a href="<?php self::lurl( $year, $iss->html_file ); ?>" title="<?php self::ltitle( $iss_num, $year, $iss->month ); ?>"
				><?php echo self::lname( $year, $iss->month ); ?> HTML</a>,
			<a href="<?php self::lurl( $year, $iss->text_file ); ?>"><?php echo self::lname( $year, $iss->month ); ?> text</a>.
		</li>
	<?php endforeach; ?>
</ul></div>

<?php endforeach; ?>

<p id="build-time">Build: <?php echo $eab_archive->build_time; ?></p>

<?php // print_r( $eab_archive ); ?>
