<div class="generic-content-wrapper-styled">
	<h3>{{$title1}}</h3>
	{{foreach $bookmarks as $bm}}
		{{$bm}}
	{{/foreach}}

	<h3>{{$title2}}</h3>
	{{foreach $conn_bookmarks as $bm}}
		{{$bm}}
	{{/foreach}}
</div>
