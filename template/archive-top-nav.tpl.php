
<ul class="eab-top-nav">

			<li><a href="#current">Current month's Bulletin</a>
			<li>
		<?php for ( $year = self::START_YEAR; $year <= self::end_year(); $year++ ): ?>
			<a href="#y<?= $year ?>"><?php echo $year ?></a>,
		<?php endfor; ?>

</ul>
