<div id="help-content" class="generic-content-wrapper">
	<div class="clearfix section-title-wrapper">
		<div class="float-end">
			<div class="btn-group">
				<button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
					<i class="fa fa-language" style="font-size: 1.4em;"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-end flex-column lang-selector">
					<a class="dropdown-item lang-choice" href="/help">de</a>
					<a class="dropdown-item lang-choice" href="/help">en</a>
					<a class="dropdown-item lang-choice" href="/help">es</a>
					<a class="dropdown-item lang-choice" href="/help">fr</a>
				</div>
			</div>
		</div>
		<h2>{{$module->get_page_title()}}</h2>
	</div>
	<div class="section-content-wrapper" id="doco-content">
		<h3 id="doco-top-toc-heading">
			<span class="fakelink" onclick="docoTocToggle(); return false;">
				<i class="fa fa-fw fa-caret-right fakelink" id="doco-toc-toggle"></i>
				{{$module->get_toc_heading()}}
			</span>
		</h3>
		<ul id="doco-top-toc" style="margin-bottom: 1.5em; display: none;"></ul>
		{{$module->render_content()}}
	</div>
</div>
<script>
	var help_language = '{{$module->lang["language"]}}'
</script>
