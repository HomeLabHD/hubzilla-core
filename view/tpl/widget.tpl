{{* Generic template for widgets.
  *
  * To use this template, pass the `$this` object reference of the widget,
  * and implement the `title()` and `contents()` functions as public methods
  * on the widget class.
  *}}
<div class="widget">
	{{if $widget->title()}}
	<h3>{{$widget->title()}}</h3>
	{{/if}}
	{{$widget->contents()}}
</div>
