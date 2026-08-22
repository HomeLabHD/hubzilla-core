{{if $wrap}}
{{$body}}
{{else}}
<div class="{{$class}}">
    {{if $show_title}}
    <h3>{{$title}}</h3>
    {{/if}}
    {{$body}}
</div>
{{/if}}
