<script>

	var bParam_cmd = "{{$baseurl}}/update/{{$pgtype}}";

	{{if $conv_mode}}
	var conv_mode = '{{$conv_mode}}';
	{{/if}}

	{{if $page_mode}}
	var page_mode = '{{$page_mode}}';
	{{/if}}

	var bParam_uid = {{$uid}};
	var bParam_gid = {{$gid}};
	var bParam_cid = {{$cid}};
	var bParam_cmin = {{$cmin}};
	var bParam_cmax = {{$cmax}};
	var bParam_star = {{$star}};
	var bParam_liked = {{$liked}};
	var bParam_conv = {{$conv}};
	var bParam_spam = {{$spam}};
	var bParam_new = {{$nouveau}};
	var bParam_page = {{$page}};
	var bParam_wall = {{$wall}};
	var bParam_list = {{$list}};
	var bParam_fh = {{$fh}};
	var bParam_dm = {{$dm}};

	var bParam_search = "{{$search}}";
	var bParam_xchan = "{{$xchan}}";
	var bParam_order = "{{$order}}";
	var bParam_file = "{{$file}}";
	var bParam_cats = "{{$cats}}";
	var bParam_tags = "{{$tags}}";
	var bParam_dend = "{{$dend}}";
	var bParam_dbegin = "{{$dbegin}}";
	var bParam_mid = "{{$mid}}";
	var bParam_verb = "{{$verb}}";
	var bParam_net = "{{$net}}";
	var bParam_pf = "{{$pf}}";
	var bParam_unseen = "{{$unseen}}";

	function buildCmd() {
		let udargs = ((page_load) ? "/load" : "");
		let bCmd = bParam_cmd + udargs;

		if(bParam_uid) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "p=" + bParam_uid;
		if(bParam_cmin != (-1)) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "cmin=" + bParam_cmin;
		if(bParam_cmax != (-1)) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "cmax=" + bParam_cmax;
		if(bParam_gid != 0) { bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "gid=" + bParam_gid; } else
		if(bParam_cid != 0) { bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "cid=" + bParam_cid; }
		if(bParam_star != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "star=" + bParam_star;
		if(bParam_liked != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "liked=" + bParam_liked;
		if(bParam_conv!= 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "conv=" + bParam_conv;
		if(bParam_spam != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "spam=" + bParam_spam;
		if(bParam_new != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "new=" + bParam_new;
		if(bParam_wall != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "wall=" + bParam_wall;
		if(bParam_list != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "list=" + bParam_list;
		if(bParam_fh != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "fh=" + bParam_fh;
		if(bParam_dm != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "dm=" + bParam_dm;
		if(bParam_search != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "search=" + bParam_search;
		if(bParam_xchan != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "xchan=" + bParam_xchan;
		if(bParam_order != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "order=" + bParam_order;
		if(bParam_file != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "file=" + bParam_file;
		if(bParam_cats != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "cat=" + bParam_cats;
		if(bParam_tags != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "tag=" + bParam_tags;
		if(bParam_dend != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "dend=" + bParam_dend;
		if(bParam_dbegin != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "dbegin=" + bParam_dbegin;
		if(bParam_mid != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "mid=" + bParam_mid;
		if(bParam_verb != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "verb=" + bParam_verb;
		if(bParam_net != "") bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "net=" + bParam_net;
		if(bParam_page != 1) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "page=" + bParam_page;
		if(bParam_pf != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "pf=" + bParam_pf;
		if(bParam_unseen != 0) bCmd = bCmd + ((bCmd.includes('?')) ? '&' : '?') + "unseen=" + bParam_unseen;
		return(bCmd);
	}

</script>

