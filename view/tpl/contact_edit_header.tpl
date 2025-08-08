<a href="{{$href}}" title="{{$link_label}}" target="_blank">
	<img src="{{$img_src}}" class="rounded menu-img-3" />
	<div>
		<div class="text-truncate h3 m-0"><strong>{{if $is_group}}<i class="bi bi-chat-quote" title="{{$group_label}}"></i> {{/if}}{{$name}}</strong></div>
		<div class="text-truncate text-muted">{{$addr}}</div>
	</div>
</a>
