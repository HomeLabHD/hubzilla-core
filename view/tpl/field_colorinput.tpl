	<div class='mb-3 field input color'>
		<label for='id_{{$field.0}}' id='label_{{$field.0}}'>{{$field.1}}</label>
		<input class='form-control color' name='{{$field.0}}' id='id_{{$field.0}}' type="text" value="{{$field.2}}">{{if $field.4}} <span class="required">{{$field.4}}</span> {{/if}}
		<small id='help_{{$field.0}}' class='form-text text-muted'>{{$field.3}}</small>
		<div id='end_{{$field.0}}' class='field_end'></div>
	</div>
