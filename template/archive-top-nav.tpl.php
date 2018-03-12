
<p class="eab-top-nav">

		<?php for ( $year = self::START_YEAR; $year <= self::end_year(); $year++ ): ?>
			<a href="#y<?= $year ?>"><?php echo $year ?></a>,
		<?php endfor; ?>

</p>
