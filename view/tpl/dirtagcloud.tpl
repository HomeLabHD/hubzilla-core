	<div class="dirtagblock widget">
    <h3>{{$title}}</h3>
    <div class="tags">
      {{foreach $tags as $tag}}
      <span class="tags">#</span><a href="{{$baseurl}}{{$tag['term']}}" class="tag tag{{$tag['normalise']}} me-1" rel="nofollow">{{$tag['term']}}</a>
      {{/foreach}}
		</div>
    </div>


