<div class="dirtagblock widget">
	<h3>{{$title}}</h3>
	<div class="tags text-center">
		{{foreach $tags as $tag}}
		<span class="tag tag{{$tag.normalise}}">#</span><a href="{{$baseurl}}{{$tag.term}}" class="tag tag{{$tag.normalise}}" rel="nofollow">{{$tag.term}}</a>
		{{/foreach}}
	</div>
</div>


