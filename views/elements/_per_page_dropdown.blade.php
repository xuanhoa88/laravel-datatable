<div class="pull-left">
	<label>Show 
		<select id="per-page-dropdown" class="per-page-dropdown">
			@foreach($tableView->present()->perPageOptions() as $length)
				{!! $tableView->present()->perPageOptionTagFor( $length ) !!}
			@endforeach
		</select> entries
	</label>
</div>