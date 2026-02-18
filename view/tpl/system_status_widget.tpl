<div class="mb-1 text-uppercase">
	<a href="{{$url}}"><i class="bi bi-{{$icon}} generic-icons-nav"></i>{{$label}}</a>
</div>
<div class="card">
	<div class="card-body clearfix">
		<table>
		{{foreach $items as $title => $item}}
			<tr>
				<td><strong>{{$title}}:</strong></td>
				<td><span>{{$item}}</span></td>
			</tr>
		{{/foreach}}
		</table>
	</div>
</div>
