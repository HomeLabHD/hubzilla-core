<div class="mb-1 text-uppercase">
	<a href="{{$url}}"><i class="bi bi-{{$icon|escape}} generic-icons-nav"></i>{{$label|escape}}</a>
</div>
<div class="card">
	<div class="card-body clearfix">
		<table>
		{{foreach $items as $id => $item}}
			<tr>
				<td id="perfstat-{{$id}}-label" class="perfstat-label">{{$id|escape}}:</td>
				<td id="perfstat-{{$id}}-value" class="perfstat-value">{{$item|escape}}</td>
			</tr>
		{{/foreach}}
		</table>
	</div>
</div>
<script>
setInterval(() => {
	fetch('/perfstats', {
		headers: {
			"Accept": "application/json",
		}
	})
	.then((response) => response.json())
	.then((json) => {
		for (const item in json) {
			//console.log(`${item}: ${json[item]}`);
			element = document.getElementById(`perfstat-${item}-value`);
			if (element) {
				element.innerText = json[item];
			}
		}
	});
}, 5000);
</script>
