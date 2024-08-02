<div class="container mt-4 mb-4">
	<div class="jumbotron">
		<h1>{{$title}}</h1>
		<hr class="my-4">
		<h2><i class="bi fa-heartbeat"></i>&nbsp; {{$pass}}</h2>
	</div>
	<form  action="{{$baseurl}}/index.php?q=setup" method="post">
		<table class="table">
			{{foreach $checks as $check}}
			<tr><td{{if ! $check.status}} class="text-danger"{{/if}}>{{$check.title}}</td><td><i class="bi {{if $check.status}}bi-check-square{{else}}{{if $check.required}}bi-square{{else}}fa-exclamation text-danger{{/if}}{{/if}}"></i></td><td>{{if $check.required}}(required){{/if}}</td></tr>
			{{if $check.help}}
			<tr><td colspan="3" class="border-top-0 pt-0 pb-0"><div class="alert alert-info">{{$check.help}}</div></td></tr>
			{{/if}}
			{{/foreach}}
		</table>

		{{if $phpath}}
		<input type="hidden" name="phpath" value="{{$phpath}}">
		{{/if}}

		{{if $passed}}
		<input type="hidden" name="pass" value="2">
		<button class="btn btn-success" type="submit"><i class="bi bi-check-lg"></i> {{$next}}</button>
		{{else}}
		<input type="hidden" name="pass" value="1">
		<button class="btn btn-warning" type="submit"><i class="bi fa-refresh"></i> {{$reload}}</button>
		{{/if}}
	</form>
</div>
