<div class="mb-1 text-uppercase">
	<i class="bi bi-{{$icon|escape}} generic-icons-nav"></i>{{$label|escape}}
</div>
<div class="card mb-4">
	<div class="card-body clearfix">
		<table>
		{{foreach $items as $id => $item}}
			{{if $id != 'ts'}}
			<tr>
				<td id="perfstat-{{$id}}-label" class="perfstat-label">{{$labels.$id|escape}}:</td>
				<td id="perfstat-{{$id}}-value" class="perfstat-value">{{$item|escape}}</td>
			</tr>
			{{/if}}
		{{/foreach}}
		</table>
	</div>
</div>
<script>
	status_update_ts = 0;
	status_update_last_q = 0;

	setInterval(() => {
		fetch('/perfstats', {
			headers: {
				"Accept": "application/json",
			},
			credentials: "include",
		})
		.then((response) => response.json())
		.then((json) => {
			for (const item in json) {
				element = document.getElementById(`perfstat-${item}-value`);
				if (element) {
					if (item === "dbqueries") {
						console.log(`dbqueries = ${json['dbqueries']}, ts = ${json['ts']}`);
						if (status_update_ts !== 0) {
							let dt = json['ts'] - status_update_ts;
							let dq = json['dbqueries'] - status_update_last_q;

							element.innerText = dq / dt;
						}

						status_update_ts = json['ts'];
						status_update_last_q = json['dbqueries'];
					} else if (item !== 'ts') {
						element.innerText = json[item];
					}
				}
			}
		});
	}, 5000);
</script>
