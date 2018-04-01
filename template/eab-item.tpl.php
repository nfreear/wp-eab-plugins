
<li id="i<?php echo $pm->issue; ?>"
	<?php echo $is_current ? 'title="current"><i id="current"></i>' : '>'; ?>
	Issue <?php echo $pm->issue; ?>, <a href="<?php the_permalink(); ?>" class="htm"
	title="<?php the_title(); ?>"><?php echo $pm->date; ?> HTML</a>,
  <a href="<?php self::text_url(); ?>"><?php echo $pm->date; ?> text</a>.
</li>
