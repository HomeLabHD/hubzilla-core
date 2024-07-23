<div id="help-content" class="generic-content-wrapper">
	<div class="clearfix section-title-wrapper">
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
