<h3 class="mb-3">{{$app.name}}{{if $app.price}} ({{$app.price}}){{/if}}</h3>
<a class="app-icon" href="{{$app.url}}" >
	{{if $icon}}
	<i class="app-icon bi bi-{{$icon}} mb-3"></i>
	{{else}}
	<img src="{{$app.photo}}" width="80" height="80" class="mb-3" />
	{{/if}}
</a>
<div class="mb-3">
	{{if $app.desc}}{{$app.desc}}{{/if}}
</div>
{{if $action_label}}
<div class="app-tools">
	<form action="{{$hosturl}}appman" method="post">
		<input type="hidden" name="papp" value="{{$app.papp}}" />
		{{if $action_label}}
		<button type="submit" name="install" value="{{$action_label}}" class="btn btn-{{if $installed}}outline-secondary{{else}}success{{/if}} btn-sm" title="{{$action_label}}" ><i class="bi{{if $installed}} bi-repeat{{else}}bi-arrow-down-circle{{/if}}" ></i> {{$action_label}}</button>
		{{/if}}
		{{if $purchase && $app.type !== 'system'}}
		<a href="{{$app.page}}" class="btn btn-sm btn-link" title="{{$purchase}}" ><i class="bi bi-box-arrow-up-right"></i></a>
		{{/if}}
	</form>
</div>
{{/if}}


