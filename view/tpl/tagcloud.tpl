	<div class="tagblock widget">
	<h3>{{$title}}</h3>
	<div class="tags text-center">
		{{foreach $tags as $tag}}
		<span class="tag{{$tag.2}}">#</span><a href="{{$baseurl}}{{$tag.0}}" class="tag{{$tag.2}}">{{$tag.0}}</a>
		{{/foreach}}
	</div>
  </div>


