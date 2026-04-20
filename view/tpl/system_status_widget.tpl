<div class="mb-1 text-uppercase">
	<a href="/admin"><i class="bi bi-{{$icon|escape}} generic-icons-nav"></i>{{$label|escape}}</a>
</div>
<div class="card mb-4">
	<div class="card-body clearfix">
		<table>
		{{foreach $items as $id => $item}}
			{{if $id != 'ts' && $id != 'profiler'}}
			<tr>
				<td id="perfstat-{{$id}}-label" class="perfstat-label">{{$labels.$id|escape}}:</td>
				<td id="perfstat-{{$id}}-value" class="perfstat-value">{{$item|escape}}</td>
			</tr>
			{{/if}}
		{{/foreach}}
			<tr>
				<td id="perfstat-profiler-label" class="perfstat-label">{{$labels.profiler}}:</td>
				<td id="perfstat-profiler-value" class="perfstat-value">
					<button
						id="perfstat-profiler-toggle"
						data-action="{{if $items.profiler}}disable{{else}}enable{{/if}}_profiling"
						onclick="system_profiler.toggle()">
							{{if $items.profiler}}{{$labels.disable}}{{else}}{{$labels.enable}}{{/if}}
					</button>
					<a href="/admin/profiler">{{$labels.configure}}</a>
				</td>
			</tr>
		</table>
	</div>
</div>
