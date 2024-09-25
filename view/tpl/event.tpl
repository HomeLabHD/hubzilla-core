{{foreach $events as $event}}
	<div class="event-wrapper">
		<div class="event">
			<div class="event-owner">
				{{if $event.item.author.xchan_name}}<a href="{{$event.item.author.xchan_url}}" ><img src="{{$event.item.author.xchan_photo_s}}">{{$event.item.author.xchan_name}}</a>{{/if}}
			</div>
			{{$event.html}}
			<div class="event-buttons">
				{{if $event.item.plink}}<a href="{{$event.plink.0}}" title="{{$event.plink.1}}"  class="btn btn-sm btn-outline-secondary border-0 plink-event-link"><i class="bi bi-box-arrow-up-right" ></i></a>{{/if}}
				{{if $event.edit}}<a href="{{$event.edit.0}}" title="{{$event.edit.1}}" class="btn btn-sm btn-outline-secondary border-0 edit-event-link"><i class="bi bi-pencil"></i></a>{{/if}}
				{{if $event.drop}}<a href="{{$event.drop.0}}" title="{{$event.drop.1}}" class="btn btn-sm btn-outline-secondary border-0 drop-event-link"><i class="bi bi-trash"></i></a>{{/if}}
			</div>
			<div class="clear"></div>
		</div>
	</div>
{{/foreach}}
