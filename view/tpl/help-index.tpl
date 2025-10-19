<div class="help-index">
    <div id="accordion" class="vstack">
        {{if $sections}}
            {{foreach $sections as $section => $links}}
                <div class="mb-3">
                    <div>
                        <h3 class="panel-title">{{$section}}</h3>
                    </div>
                    <div id="{{$section|replace:' ':'_'}}" class="doco-section">
                        <div class="vstack">
                            {{foreach $links as $label => $url}}
                                <a href="{{$url}}">{{$label}}</a>
                            {{/foreach}}
                        </div>
                    </div>
                </div>
            {{/foreach}}
        {{else}}
            {{$contents}}
        {{/if}}
    </div>
</div>
