<div class="container mt-4 mb-4">
	<div class="jumbotron">
		<h1>{{$title}}</h1>
		<hr class="my-4">
		{{if $icon}}
		<h2><i class="fa fa-{{$icon}}"></i>&nbsp; {{$pass}}</h2>
		{{/if}}
	</div>

	{{if $status}}
	<div class="alert alert-danger">{{$status}}</div>
	{{/if}}

	<div class="alert alert-info">{{$text}}</div>
	<br>
	{{if $what_next}}{{$what_next}}{{/if}}
</div>
