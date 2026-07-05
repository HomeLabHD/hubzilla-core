/*
 * SPDX-FileCopyrightText: 2026 The Hubzilla Community
 * SPDX-FileContributor: Harald Eilertsen <haraldei@anduin.net>
 * SPDX-FileContributor: Mario Vavti <mario@mariovavti.com>
 *
 * SPDX-License-Identifier: MIT
 */
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
						let toggle = document.getElementById('perfstat-profiler-toggle');
						toggle.innerText = json[item]['new_state']['label'];
						toggle.dataset.action = json[item]['new_state']['action'];
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
			if (json.status === 'success') {
				toggle.innerText = json.new_state.label;
				toggle.dataset.action = json.new_state.action;
			}

			toggle.disabled = false;
		});
	}
}

document.addEventListener("DOMContentLoaded", function() {
	status_update_monitor.start();
});
