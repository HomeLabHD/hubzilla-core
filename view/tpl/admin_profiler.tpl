<h2>{{$title}}</h2>

<form action="/admin/profiler" method="POST">
	<input type="hidden" name="security" value="{{$security}}">
	{{include file="field_input.tpl" field=$option_filename}}
	<input type="submit" name="submit" class="btn btn-primary" value="{{$submit}}" />
</form>
