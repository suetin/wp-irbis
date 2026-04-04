<?php
// Template for pagination
$data         = $data ?? new stdClass();
$current_page = $data->current_page ?? '';
$total_pages  = $data->total_pages ?? '';
?>

<ul class="pagination">
	<li><a href="?paged=1">Начало</a></li>
	<li class="<?php if ( $current_page <= 1 ) {
		echo 'disabled';
	} ?>">
		<a href="<?php if ( $current_page <= 1 ) {
			echo '#';
		} else {
			echo "?paged=" . ( $current_page - 1 );
		} ?>">Назад</a>
	</li>
	<li class="<?php if ( $current_page >= $total_pages ) {
		echo 'disabled';
	} ?>">
		<a href="<?php if ( $current_page >= $total_pages ) {
			echo '#';
		} else {
			echo "?paged=" . ( $current_page + 1 );
		} ?>">Далее</a>
	</li>
	<li><a href="?paged=<?php echo $total_pages; ?>">Последняя</a></li>
</ul>
