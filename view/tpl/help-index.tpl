<div class="accordion" id="accordionExample">
  {{if $sections}}
    {{foreach $sections as $section => $links}}
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{$section}}"
        aria-expanded="false" aria-controls="{{$section}}">
        {{$section}}
      </button>
    </h2>
    <div id="{{$section}}" class="accordion-collapse collapse" data-bs-parent="#accordion">
      <div class="accordion-body list-group list-group-flush p-2">
        {{foreach $links as $label => $url}}
        <li class="list-group-item"><a href="{{$url}}">{{$label}}</a></li>
        {{/foreach}}
      </div>
    </div>
  </div>
    {{/foreach}}
  {{else}}
  {{$contents}}
  {{/if}}

</div>
