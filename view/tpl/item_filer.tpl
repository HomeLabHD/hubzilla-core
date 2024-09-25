{{if $categories}}
<!--div class="filesavetags"-->
{{foreach $categories as $cat}}
<span class="item-category badge rounded-pill bg-danger"><i class="bi bi-folder"></i>&nbsp;{{$cat.term}}&nbsp;<a href="{{$cat.removelink}}" class="text-white" title="{{$remove}}" onClick="itemFilerRm({{$cat.id}}, '{{$cat.term}}'); return false;"><i class="bi bi-x-lg"></i></a></span>
{{/foreach}}
<!--/div-->
{{/if}}

