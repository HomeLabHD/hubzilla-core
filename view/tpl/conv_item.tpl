{{if $item.comment_firstcollapsed}}
<div id="hide-comments-outer-{{$item.parent}}" class="hide-comments-outer fakelink" onclick="showHideComments({{$item.id}});">
	<span id="hide-comments-{{$item.id}}" class="hide-comments">{{$item.hide_text}}</span>&nbsp;<span id="hide-comments-total-{{$item.id}}" class="hide-comments-total">{{$item.num_comments}}</span>
</div>
<div id="collapsed-comments-{{$item.id}}" class="collapsed-comments" style="display: none;">
{{/if}}
	<div id="thread-wrapper-{{$item.id}}" class="thread-wrapper{{if $item.toplevel}} {{$item.toplevel}} generic-content-wrapper h-entry {{else}} u-comment h-cite{{/if}} clearfix" data-b64mids='{{$item.mids}}'>
		<a name="item_{{$item.id}}" ></a>
		<div class="wall-item-outside-wrapper{{if $item.is_comment}} comment{{/if}}{{if $item.previewing}} preview{{/if}}" id="wall-item-outside-wrapper-{{$item.id}}" >
			<div class="rounded wall-item-content-wrapper{{if $item.is_comment}} comment{{/if}}" id="wall-item-content-wrapper-{{$item.id}}">
				{{if $item.photo}}
				<div class="wall-photo-item" id="wall-photo-item-{{$item.id}}">
					{{$item.photo}}
				</div>
				{{/if}}
				{{if $item.event}}
				<div class="wall-event-item" id="wall-event-item-{{$item.id}}">
					{{$item.event}}
				</div>
				{{/if}}
				{{if $item.title && !$item.event}}
				<div class="p-2{{if $item.is_new}} bg-primary text-white{{/if}} wall-item-title h3{{if !$item.photo}} rounded-top{{/if}}" id="wall-item-title-{{$item.id}}">
					{{if $item.title_tosource}}{{if $item.plink}}<a href="{{$item.plink.href}}" title="{{$item.title}} ({{$item.plink.title}})" rel="nofollow">{{/if}}{{/if}}{{$item.title}}{{if $item.title_tosource}}{{if $item.plink}}</a>{{/if}}{{/if}}
				</div>
				{{if ! $item.is_new}}
				<hr class="m-0">
				{{/if}}
				{{/if}}
				<div class="p-2 wall-item-head{{if !$item.title && !$item.event && !$item.photo}} rounded-top{{/if}}{{if $item.is_new && !$item.event && !$item.is_comment}} wall-item-head-new{{/if}} clearfix">
					<div class="text-end float-end">
						<div class="wall-item-ago opacity-75" id="wall-item-ago-{{$item.id}}">
							{{if $item.editedtime}}
							<i class="fa fa-pencil"></i>
							{{/if}}
							{{if $item.delayed}}
							<i class="fa fa-clock-o"></i>
							{{/if}}
							{{if $item.location}}
							<small class="wall-item-location p-location" id="wall-item-location-{{$item.id}}">{{$item.location}}</small>
							{{/if}}
							{{if $item.verified}}
							<i class="fa fa-check text-success" title="{{$item.verified}}"></i>
							{{elseif $item.forged}}
							<i class="fa fa-exclamation text-danger" title="{{$item.forged}}"></i>
							{{/if}}
							<small class="autotime" title="{{$item.isotime}}"><time class="dt-published" datetime="{{$item.isotime}}">{{$item.localtime}}</time>{{if $item.editedtime}}&nbsp;{{$item.editedtime}}{{/if}}{{if $item.expiretime}}&nbsp;{{$item.expiretime}}{{/if}}</small>
						</div>
						{{if $item.thr_parent_uuid}}
						<a href="javascript:doscroll('{{$item.thr_parent_uuid}}',{{$item.parent}});" class="ms-3" title="{{$item.top_hint}}"><i class="fa fa-angle-double-up"></i></a>
						{{/if}}
						{{if $item.pinned}}
						<div class="wall-item-pinned" title="{{$item.pinned}}" id="wall-item-pinned-{{$item.id}}"><i class="fa fa-thumb-tack"></i></div>
						{{/if}}
					</div>
					<div class="float-start wall-item-info pe-2" id="wall-item-info-{{$item.id}}" >
						<div class="wall-item-photo-wrapper{{if $item.owner_url}} wwfrom{{/if}} h-card p-author" id="wall-item-photo-wrapper-{{$item.id}}">
							{{if $item.contact_id}}
							<div class="spinner-wrapper contact-edit-rotator contact-edit-rotator-{{$item.contact_id}}"><div class="spinner s"></div></div>
							{{/if}}
							<img src="{{$item.thumb}}" class="fakelink wall-item-photo{{$item.sparkle}} u-photo p-name" id="wall-item-photo-{{$item.id}}" alt="{{$item.name}}" loading="lazy" data-bs-toggle="dropdown" />
							{{if $item.author_is_group_actor}}
							<i class="fa fa-comments-o wall-item-photo-group-actor" title="{{$item.author_is_group_actor}}"></i>
							{{/if}}
							{{if $item.thread_author_menu}}
							<i class="fa fa-caret-down wall-item-photo-caret cursor-pointer" data-bs-toggle="dropdown"></i>
							<div class="dropdown-menu">
								{{foreach $item.thread_author_menu as $mitem}}
								<a class="dropdown-item{{if $mitem.class}} {{$mitem.class}}{{/if}}" {{if $mitem.href}}href="{{$mitem.href}}"{{/if}} {{if $mitem.action}}onclick="{{$mitem.action}}"{{/if}} {{if $mitem.title}}title="{{$mitem.title}}"{{/if}}{{if $mitem.data}} {{$mitem.data}}{{/if}}>{{$mitem.title}}</a>
								{{/foreach}}
							</div>
							{{/if}}
						</div>
					</div>
					<div class="wall-item-author">
						{{if $item.lock}}
						<div class="float-start dropdown wall-item-lock">
							<i class="fa {{if $item.locktype == 2}}fa-envelope-o{{else if $item.locktype == 1}}fa-lock{{else}}fa-unlock{{/if}} lockview{{if $item.privacy_warning}} text-danger{{/if}}" data-bs-toggle="dropdown" title="{{$item.lock}}" onclick="lockview('item',{{$item.id}});" ></i>&nbsp;
							<div id="panel-{{$item.id}}" class="dropdown-menu"></div>
						</div>
						{{/if}}
						<div class="text-truncate">
							<a href="{{$item.profile_url}}" class="lh-sm wall-item-name-link u-url"{{if $item.app}} title="{{$item.str_app}}"{{/if}}><span class="wall-item-name{{$item.sparkle}}" id="wall-item-name-{{$item.id}}" ><bdi>{{$item.name}}</bdi></span></a>{{if $item.owner_url}}&nbsp;{{$item.via}}&nbsp;<a href="{{$item.owner_url}}" title="{{$item.olinktitle}}" class="wall-item-name-link"><span class="wall-item-name{{$item.osparkle}}" id="wall-item-ownername-{{$item.id}}"><bdi>{{$item.owner_name}}</bdi></span></a>{{/if}}
						</div>
						<small class="lh-sm text-truncate d-block wall-item-addr opacity-75">{{$item.author_id}}</small>
					</div>
				</div>
				{{if $item.divider}}
				<hr class="wall-item-divider">
				{{/if}}
				{{if $item.body}}
				<div class="p-2 wall-item-content clearfix" id="wall-item-content-{{$item.id}}">
					<div class="wall-item-body e-content" id="wall-item-body-{{$item.id}}"{{if $item.rtl}} dir="rtl"{{/if}}>
						{{$item.body}}
					</div>
				</div>
				{{/if}}
				{{if $item.has_tags}}
				<div class="p-2 wall-item-tools clearfix">
					<div class="body-tags">
						<span class="tag">{{$item.mentions}} {{$item.tags}} {{$item.categories}} {{$item.folders}}</span>
					</div>
				</div>
				{{/if}}
				<div class="p-2 wall-item-tools d-flex justify-content-between">
					<div class="wall-item-tools-left hstack gap-1" id="wall-item-tools-left-{{$item.id}}">
						{{foreach $item.responses as $verb=>$response}}
						{{if $item.reactions_allowed || (!$item.reactions_allowed && $response.count)}}
						<div class="">
							<button type="button" title="{{$response.count}} {{$response.button.label}}" class="btn btn-outline-secondary border-0 wall-item-{{$response.button.class}}"{{if $response.modal}} data-bs-toggle="modal" data-bs-target="#{{$verb}}Modal-{{$item.id}}"{{else if $response.count}} data-bs-toggle="dropdown"{{elseif $item.reactions_allowed}} onclick="{{$response.button.onclick}}({{$item.id}},'{{$verb}}'); return false;"{{/if}} id="wall-item-{{$verb}}-{{$item.id}}"><i class="fa fa-{{$response.button.icon}}"></i>{{if $response.count}} <span class="{{if $item.my_responses.$verb}}text-decoration-underline{{/if}}">{{$response.count}}</span>{{/if}}</button>
							{{if $response.modal}}
							<div class="modal" id="{{$verb}}Modal-{{$item.id}}">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h3 class="modal-title">{{$response.count}} {{$response.button.label}}</h3>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
										</div>
										{{if $item.reactions_allowed && ($item.my_responses.$verb && $verb != 'announce')}} {{** undo announce is not yet supported **}}
										<div class="modal-header">
											<a href="#" class="text-reset" onclick="{{$response.button.onclick}}({{$item.id}},'{{$verb}}'); return false;">{{if $item.my_responses.like}}- Remove{{else}}+ Add{{/if}} yours</a>
										</div>
										{{/if}}
										<div class="modal-body response-list">
											<ul class="nav nav-pills flex-column">
												{{foreach $response.list as $liker}}
												{{$liker}}
												{{/foreach}}
												</ul>
										</div>
										<div class="modal-footer clear">
											<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{$item.modal_dismiss}}</button>
										</div>
									</div><!-- /.modal-content -->
								</div><!-- /.modal-dialog -->
							</div><!-- /.modal -->
							{{else}}
							<div class="dropdown-menu">
								{{if $item.reactions_allowed && ($item.my_responses.$verb && $verb != 'announce')}} {{** undo announce is not yet supported **}}
								<div class="dropdown-item">
									<a href="#" class="text-reset" onclick="{{$response.button.onclick}}({{$item.id}},'{{$verb}}'); return false;">{{if $item.my_responses.$verb}}- Remove{{else}}+ Add{{/if}} yours</a>
								</div>
								<div class="dropdown-divider"></div>
								{{/if}}
								{{foreach $response.list as $liker}}{{$liker}}{{/foreach}}
							</div>
							{{/if}}
						</div>
						{{/if}}
						{{/foreach}}
						{{if $item.toplevel && $item.emojis && $item.reactions}}
						<div class="">
							<button type="button" class="btn btn-outline-secondary border-0" data-bs-toggle="dropdown" id="wall-item-react-{{$item.id}}">
								<i class="fa fa-smile-o"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-start container text-center w-25">
								<div class="row g-0">
									{{foreach $item.reactions as $react}}
									<div class="col-3 p-2">
										<a class="" href="#" onclick="jotReact({{$item.id}},'{{$react}}'); return false;"><img class="menu-img-1" src="/images/emoji/{{$react}}.png" alt="{{$react}}" /></a>
									</div>
									{{/foreach}}
								</div>
							</div>
						</div>
						{{/if}}
						<div class="">
							<div id="like-rotator-{{$item.id}}" class="spinner-wrapper">
								<div class="spinner s"></div>
							</div>
						</div>
					</div>
					<div class="wall-item-tools-right hstack gap-1" id="wall-item-tools-right-{{$item.id}}">
						{{if $item.moderate}}
						<a href="moderate/{{$item.id}}/approve" onclick="moderate_approve({{$item.id}}); return false;" class="btn btn-sm btn-outline-success"><i class="fa fa-check" ></i> {{$item.moderate_approve}}</a>
						<a href="moderate/{{$item.id}}/drop" onclick="moderate_drop({{$item.id}}); return false;" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash-o" ></i> {{$item.moderate_delete}}</a>
						{{else}}
						{{if $item.star && $item.star.isstarred}}
						<div class="" id="star-button-{{$item.id}}">
							<button type="button" class="btn btn-outline-secondary border-0 wall-item-star" onclick="dostar({{$item.id}});"><i class="fa fa-star"></i></button>
						</div>
						{{/if}}
						{{if $item.attachments}}
						<div class="">
							<button type="button" class="btn btn-outline-secondary border-0 wall-item-attach" data-bs-toggle="dropdown" id="attachment-menu-{{$item.id}}"><i class="fa fa-paperclip"></i></button>
							<div class="dropdown-menu dropdown-menu-end">{{$item.attachments}}</div>
						</div>
						{{/if}}
						{{if $item.reply_to}}
						<button type="button" title="{{$item.reply_to.0}}" class="btn btn-outline-secondary border-0" onclick="doreply({{$item.parent}}, {{$item.id}}, '{{$item.author_id}}', '{{$item.reply_to.2}} {{$item.name|escape:javascript}}');">
							<i class="fa fa-reply" ></i>
						</button>
						{{/if}}
						<div class="">
							<button type="button" class="btn btn-outline-secondary border-0" data-bs-toggle="dropdown" id="wall-item-menu-{{$item.id}}">
								<i class="fa fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-end" role="menu" aria-labelledby="wall-item-menu-{{$item.id}}">
								{{if $item.share}}
								<!--a class="dropdown-item" href="#" onclick="jotShare({{$item.id}},{{$item.item_type}}); return false"><i class="generic-icons-nav fa fa-fw fa-retweet" title="{{$item.share.0}}"></i>{{$item.share.0}}</a-->
								{{/if}}
								{{if $item.embed}}
								<a class="dropdown-item" href="#" onclick="jotEmbed({{$item.id}},{{$item.item_type}}); return false"><i class="generic-icons-nav fa fa-fw fa-share" title="{{$item.embed.0}}"></i>{{$item.embed.0}}</a>
								{{/if}}
								{{if $item.plink}}
								<a class="dropdown-item" href="{{$item.plink.href}}" title="{{$item.plink.title}}" class="u-url"><i class="generic-icons-nav fa fa-fw fa-external-link"></i>{{$item.plink.title}}</a>
								{{/if}}
								{{if $item.edpost}}
								<a class="dropdown-item" href="{{$item.edpost.0}}" title="{{$item.edpost.1}}"><i class="generic-icons-nav fa fa-fw fa-pencil"></i>{{$item.edpost.1}}</a>
								{{/if}}
								{{if $item.tagger}}
								<a class="dropdown-item" href="#"  onclick="itemTag({{$item.id}}); return false;"><i id="tagger-{{$item.id}}" class="generic-icons-nav fa fa-fw fa-tag" title="{{$item.tagger.tagit}}"></i>{{$item.tagger.tagit}}</a>
								{{/if}}
								{{if $item.filer}}
								<a class="dropdown-item" href="#" onclick="itemFiler({{$item.id}}); return false;"><i id="filer-{{$item.id}}" class="generic-icons-nav fa fa-fw fa-folder-open" title="{{$item.filer}}"></i>{{$item.filer}}</a>
								{{/if}}
								{{if $item.pinnable}}
								<a class="dropdown-item dropdown-item-pinnable" href="#" onclick="dopin({{$item.id}}); return false;" id="item-pinnable-{{$item.id}}"><i class="generic-icons-nav fa fa-fw fa-thumb-tack"></i>{{$item.pinme}}</a>
								{{/if}}
								{{if $item.bookmark}}
								<a class="dropdown-item" href="#" onclick="itemBookmark({{$item.id}}); return false;"><i id="bookmarker-{{$item.id}}" class="generic-icons-nav fa fa-fw fa-bookmark" title="{{$item.bookmark}}"></i>{{$item.bookmark}}</a>
								{{/if}}
								{{if $item.addtocal}}
								<a class="dropdown-item" href="#" onclick="itemAddToCal({{$item.id}}); return false;"><i id="addtocal-{{$item.id}}" class="generic-icons-nav fa fa-fw fa-calendar" title="{{$item.addtocal}}"></i>{{$item.addtocal}}</a>
								{{/if}}
								{{if $item.star}}
								<a class="dropdown-item" href="#" onclick="dostar({{$item.id}}); return false;"><i id="starred-{{$item.id}}" class="generic-icons-nav fa fa-fw{{if $item.star.isstarred}} starred fa-star{{else}} unstarred fa-star-o{{/if}}" title="{{$item.star.toggle}}"></i>{{$item.star.toggle}}</a>
								{{/if}}
								{{if $item.thread_action_menu}}
								{{foreach $item.thread_action_menu as $mitem}}
								<a class="dropdown-item" {{if $mitem.href}}href="{{$mitem.href}}"{{/if}} {{if $mitem.action}}onclick="{{$mitem.action}}"{{/if}} {{if $mitem.title}}title="{{$mitem.title}}"{{/if}} ><i class="generic-icons-nav fa fa-fw fa-{{$mitem.icon}}"></i>{{$mitem.title}}</a>
								{{/foreach}}
								{{/if}}
								{{if $item.drop.dropping}}
								<a class="dropdown-item" href="#" onclick="dropItem('item/drop/{{$item.id}}', '#thread-wrapper-{{$item.id}}', '{{$item.mid}}'); return false;" title="{{$item.drop.delete}}" ><i class="generic-icons-nav fa fa-fw fa-trash-o"></i>{{$item.drop.delete}}</a>
								{{/if}}
								{{if $item.dropdown_extras}}
								<div class="dropdown-divider"></div>
								{{$item.dropdown_extras}}
								{{/if}}
								{{if $item.edpost && $item.dreport}}
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="dreport/{{$item.dreport_link}}">{{$item.dreport}}</a>
								{{/if}}
								{{if $item.settings}}
								<div class="dropdown-divider"></div>
								<a class="dropdown-item conversation-settings-link" href="" data-bs-toggle="modal" data-bs-target="#conversation_settings">{{$item.settings}}</a>
								{{/if}}
							</div>
						</div>
						{{/if}}
					</div>
				</div>
			</div>
		</div>
		{{if $item.toplevel}}
		{{foreach $item.children as $child}}
			{{include file="{{$child.template}}" item=$child}}
		{{/foreach}}
		{{/if}}
		{{if $item.comment}}
		<div id="wall-item-comment-wrapper-{{$item.id}}" class="p-2 wall-item-comment-wrapper{{if $item.children}} wall-item-comment-wrapper-wc{{/if}}" >
			{{$item.comment}}
		</div>
		{{/if}}
	</div>
{{if $item.comment_lastcollapsed}}
</div>
{{/if}}
