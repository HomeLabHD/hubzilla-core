<div class="generic-content-wrapper">
	<div class="section-title-wrapper">
		<h2>{{$title}}</h2>
	</div>
	<div class="section-content-wrapper">
		<form action="rmagic" method="post" >
			{{include file="field_input.tpl" field=$address}}
			<input class="btn btn-primary" type="submit" name="submit" id="rmagic-submit-button" value="{{$submit}}" />
		</form>
	</div>
</div>
