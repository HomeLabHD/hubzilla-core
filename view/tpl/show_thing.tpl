<h2>{{$header}}</h2>
{{if $thing}}
<div class="thing-show">
	{{if $thing.obj_imgurl}}
	<a href="{{$thing.obj_url}}" ><img src="{{$thing.obj_imgurl}}" style="max-width: 100%;" alt="{{$thing.obj_term}}" /></a>
	{{else}}
	<a href="{{$thing.obj_url}}" >{{$thing.obj_url}}</a>
	{{/if}}
</div>
{{if $canedit}}
<div class="thing-edit-links">
	<a href="thing/edit/{{$thing.obj_obj}}" title="{{$edit}}" class="btn btn-outline-secondary" ><i class="bi bi-pencil thing-edit-icon"></i></a>
	<a href="thing/drop/{{$thing.obj_obj}}" onclick="return confirmDelete();" title="{{$delete}}" class="btn btn-outline-secondary" ><i class="bi bi-trash drop-icons"></i></a>
</div>
<div class="thing-edit-links-end"></div>
{{/if}}
{{/if}}

