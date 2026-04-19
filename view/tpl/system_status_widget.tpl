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
				<td id="perfstat-profiler-valie" class="perfstat-value">
					<button id="perfstat-profiler-toggle" data-action="enable_profiling" onclick="system_profiler.toggle()">{{$labels.enable}}</button>
					<a href="/admin/profiler">{{$labels.configure}}</a>
				</td>
			</tr>
		</table>
	</div>
</div>
<script>
	status_update_monitor = {
		last_ts: 0,
		last_q: 0,

		updateStatus: function () {
			if (!this.isVisible()) {
				return;
			}

			fetch('/perfstats', {
				headers: {
					"Accept": "application/json",
				},
				credentials: "include",
			})
			.then((response) => response.json())
			.then((json) => {
				for (const item in json) {
					let element = document.getElementById(`perfstat-${item}-value`);
					if (element) {
						if (item === "loadavg") {
							element.innerText = json['loadavg']
								.map((v) => v.toPrecision(3))
								.join(" / ");
						} else if (item === "dbqueries") {
							if (this.last_ts !== 0) {
								let dt = json['ts'] - this.last_ts;
								let dq = json['dbqueries'] - this.last_q;

								element.innerText = dq / dt;
							}

							this.last_ts = json['ts'];
							this.last_q = json['dbqueries'];
						} else if (item === "profiler") {
							let action = json[item] ? 'disable_profiling' : 'enable_profiling';
							let label = json[item] ? '{{$labels.disable}}' : '{{$labels.enable}}';
							let toggle = element.getElementById('perfstat-profiler-toggle');
							toggle.innerText = label;
							toggle.dataset.action = action;
						} else if (item !== 'ts') {
							element.innerText = json[item];
						}
					}
				}
			});
		},

		start: function() {
			this.updateStatus();
			setInterval(() => this.updateStatus(), 5000);
		},

		isVisible: function () {
			const element = document.getElementById('channel-activities');

			if (!element) {
				return false;
			}

			const style = window.getComputedStyle(element);
			return style.display !== 'none';
		}
	}

	system_profiler = {
		toggle: function() {
			let toggle = document.getElementById('perfstat-profiler-toggle');
			toggle.disabled = true;

			let action = toggle.dataset.action;

			if (action !== 'enable_profiling' && action !== 'disable_profiling') {
				return;
			}

			fetch('/admin/profiler', {
				method: 'POST',
				headers: {
					"Accept": "application/json",
				},
				credentials: "include",
				body: JSON.stringify({
					action: action,
				}),
			})
			.then((response) => response.json())
			.then((json) => {
				toggle.innerText = action === 'enable_profiling' ? '{{$labels.disable}}' : '{{$labels.enable}}';
				toggle.dataset.action = action === 'enable_profiling' ? 'disable_profiling' : 'enable_profiling';
				toggle.disabled = false;
			});
		}
	}

	document.addEventListener("DOMContentLoaded", function() {
		status_update_monitor.start();
	});
</script>
