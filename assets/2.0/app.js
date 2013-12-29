(function() {
    /**
     * Turn Keywords into affiliate links
     * Bixt - http://bixt.net
     * Copyright (C) 2011-2050 Svetoslav Marinov (Slavi) | http://orbisius.com
     * Author: orbisius.com
     *
     * Note: if this script is made to be loaded with another JS ala Google Analytics I read somewhere that
     * doc.write could break the original page's HTML content.
     * We use doc.write for outputting the iframe.
     *
     * V 2.0.0
     * http://bixt.net
     *
     */

    var XRegExp;
    var tracking = 0; // Keep things simple. Don't track. GAnalytics?

    if (!"console" in window) {
        window.console = {
            log: function () {}
        }
    }

    if (typeof window.Bixt == 'undefined') {
        if (tracking) {
            // Tracking iframe
            document.write('<iframe id="Bixt_sys_iframe" name="Bixt_sys_iframe" src="javascript:false;" '
                + ' class="Bixt_sys_iframe" style="display:none;height:1px;" '
                + ' frameborder="0" scrolling="no" '
                + '></iframe>'
            );

            // Tooltip styles
            document.write(' <style type="text/css">'
                + '.bixt_tooltip {'
                + '	border-bottom: 1px dotted #000000; '
                + '	color: #000000;'
                + '	outline: none;'
                + '	_cursor: help;'
                + '	text-decoration: none;'
                + '	position: relative;'
                + '}'
                + '.bixt_tooltip span {'
                + '	margin-left: -999em;'
                + '	position: absolute;'
                + '}'
                + '.bixt_tooltip:hover span {'
                + '	border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;'
                + '	box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.1); -webkit-box-shadow: 5px 5px rgba(0, 0, 0, 0.1); -moz-box-shadow: 5px 5px rgba(0, 0, 0, 0.1);'
                + '	font-family: Calibri, Tahoma, Geneva, sans-serif;'
                + '	position: absolute; left: 1em; top: 2em; z-index: 99;'
                +	'margin-left: 0; width: 250px;'
                + '}'
                + '.bixt_tooltip:hover img {'
                + '	border: 0; margin: -10px 0 0 -55px;'
                +	'float: left; position: absolute;'
                + '}'
                + '.bixt_tooltip:hover em {'
                + '		font-family: Candara, Tahoma, Geneva, sans-serif; font-size: 1.2em; font-weight: bold;'
                + '		display: block; padding: 0.2em 0 0.3em 0;'
                + ' }'
                + ' .bixt_tooltip_classic { padding: 0.3em 0.5em; }'
                + ' * html a:hover { background: transparent; }'
                + ' .bixt_tooltip_classic {background: #eee; color:#666; border: 1px solid #bbb; }'
                + ' </style>');
        }

        // http://xregexp.com/api/
        //XRegExp 1.5.0 <xregexp.com> MIT License
        if (!XRegExp) {
            if(XRegExp){throw Error("can't load XRegExp twice in the same frame")}(function(){XRegExp=function(w,r){var q=[],u=XRegExp.OUTSIDE_CLASS,x=0,p,s,v,t,y;if(XRegExp.isRegExp(w)){if(r!==undefined){throw TypeError("can't supply flags when constructing one RegExp from another")}return j(w)}if(g){throw Error("can't call the XRegExp constructor within token definition functions")}r=r||"";p={hasNamedCapture:false,captureNames:[],hasFlag:function(z){return r.indexOf(z)>-1},setFlag:function(z){r+=z}};while(x<w.length){s=o(w,x,u,p);if(s){q.push(s.output);x+=(s.match[0].length||1)}else{if(v=m.exec.call(i[u],w.slice(x))){q.push(v[0]);x+=v[0].length}else{t=w.charAt(x);if(t==="["){u=XRegExp.INSIDE_CLASS}else{if(t==="]"){u=XRegExp.OUTSIDE_CLASS}}q.push(t);x++}}}y=RegExp(q.join(""),m.replace.call(r,h,""));y._xregexp={source:w,captureNames:p.hasNamedCapture?p.captureNames:null};return y};XRegExp.version="1.5.0";XRegExp.INSIDE_CLASS=1;XRegExp.OUTSIDE_CLASS=2;var c=/\$(?:(\d\d?|[$&`'])|{([$\w]+)})/g,h=/[^gimy]+|([\s\S])(?=[\s\S]*\1)/g,n=/^(?:[?*+]|{\d+(?:,\d*)?})\??/,g=false,k=[],m={exec:RegExp.prototype.exec,test:RegExp.prototype.test,match:String.prototype.match,replace:String.prototype.replace,split:String.prototype.split},a=m.exec.call(/()??/,"")[1]===undefined,e=function(){var p=/^/g;m.test.call(p,"");return !p.lastIndex}(),f=function(){var p=/x/g;m.replace.call("x",p,"");return !p.lastIndex}(),b=RegExp.prototype.sticky!==undefined,i={};i[XRegExp.INSIDE_CLASS]=/^(?:\\(?:[0-3][0-7]{0,2}|[4-7][0-7]?|x[\dA-Fa-f]{2}|u[\dA-Fa-f]{4}|c[A-Za-z]|[\s\S]))/;i[XRegExp.OUTSIDE_CLASS]=/^(?:\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9]\d*|x[\dA-Fa-f]{2}|u[\dA-Fa-f]{4}|c[A-Za-z]|[\s\S])|\(\?[:=!]|[?*+]\?|{\d+(?:,\d*)?}\??)/;XRegExp.addToken=function(s,r,q,p){k.push({pattern:j(s,"g"+(b?"y":"")),handler:r,scope:q||XRegExp.OUTSIDE_CLASS,trigger:p||null})};XRegExp.cache=function(r,p){var q=r+"/"+(p||"");return XRegExp.cache[q]||(XRegExp.cache[q]=XRegExp(r,p))};XRegExp.copyAsGlobal=function(p){return j(p,"g")};XRegExp.escape=function(p){return p.replace(/[-[\]{}()*+?.,\\^$|#\s]/g,"\\$&")};XRegExp.execAt=function(s,r,t,q){r=j(r,"g"+((q&&b)?"y":""));r.lastIndex=t=t||0;var p=r.exec(s);if(q){return(p&&p.index===t)?p:null}else{return p}};XRegExp.freezeTokens=function(){XRegExp.addToken=function(){throw Error("can't run addToken after freezeTokens")}};XRegExp.isRegExp=function(p){return Object.prototype.toString.call(p)==="[object RegExp]"};XRegExp.iterate=function(u,p,v,s){var t=j(p,"g"),r=-1,q;while(q=t.exec(u)){v.call(s,q,++r,u,t);if(t.lastIndex===q.index){t.lastIndex++}}if(p.global){p.lastIndex=0}};XRegExp.matchChain=function(q,p){return function r(s,x){var v=p[x].regex?p[x]:{regex:p[x]},u=j(v.regex,"g"),w=[],t;for(t=0;t<s.length;t++){XRegExp.iterate(s[t],u,function(y){w.push(v.backref?(y[v.backref]||""):y[0])})}return((x===p.length-1)||!w.length)?w:r(w,x+1)}([q],0)};RegExp.prototype.apply=function(q,p){return this.exec(p[0])};RegExp.prototype.call=function(p,q){return this.exec(q)};RegExp.prototype.exec=function(t){var r=m.exec.apply(this,arguments),q,p;if(r){if(!a&&r.length>1&&l(r,"")>-1){p=RegExp(this.source,m.replace.call(d(this),"g",""));m.replace.call(t.slice(r.index),p,function(){for(var u=1;u<arguments.length-2;u++){if(arguments[u]===undefined){r[u]=undefined}}})}if(this._xregexp&&this._xregexp.captureNames){for(var s=1;s<r.length;s++){q=this._xregexp.captureNames[s-1];if(q){r[q]=r[s]}}}if(!e&&this.global&&!r[0].length&&(this.lastIndex>r.index)){this.lastIndex--}}return r};if(!e){RegExp.prototype.test=function(q){var p=m.exec.call(this,q);if(p&&this.global&&!p[0].length&&(this.lastIndex>p.index)){this.lastIndex--}return !!p}}String.prototype.match=function(q){if(!XRegExp.isRegExp(q)){q=RegExp(q)}if(q.global){var p=m.match.apply(this,arguments);q.lastIndex=0;return p}return q.exec(this)};String.prototype.replace=function(r,s){var t=XRegExp.isRegExp(r),q,p,u;if(t&&typeof s.valueOf()==="string"&&s.indexOf("${")===-1&&f){return m.replace.apply(this,arguments)}if(!t){r=r+""}else{if(r._xregexp){q=r._xregexp.captureNames}}if(typeof s==="function"){p=m.replace.call(this,r,function(){if(q){arguments[0]=new String(arguments[0]);for(var v=0;v<q.length;v++){if(q[v]){arguments[0][q[v]]=arguments[v+1]}}}if(t&&r.global){r.lastIndex=arguments[arguments.length-2]+arguments[0].length}return s.apply(null,arguments)})}else{u=this+"";p=m.replace.call(u,r,function(){var v=arguments;return m.replace.call(s,c,function(x,w,A){if(w){switch(w){case"$":return"$";case"&":return v[0];case"`":return v[v.length-1].slice(0,v[v.length-2]);case"'":return v[v.length-1].slice(v[v.length-2]+v[0].length);default:var y="";w=+w;if(!w){return x}while(w>v.length-3){y=String.prototype.slice.call(w,-1)+y;w=Math.floor(w/10)}return(w?v[w]||"":"$")+y}}else{var z=+A;if(z<=v.length-3){return v[z]}z=q?l(q,A):-1;return z>-1?v[z+1]:x}})})}if(t&&r.global){r.lastIndex=0}return p};String.prototype.split=function(u,p){if(!XRegExp.isRegExp(u)){return m.split.apply(this,arguments)}var w=this+"",r=[],v=0,t,q;if(p===undefined||+p<0){p=Infinity}else{p=Math.floor(+p);if(!p){return[]}}u=XRegExp.copyAsGlobal(u);while(t=u.exec(w)){if(u.lastIndex>v){r.push(w.slice(v,t.index));if(t.length>1&&t.index<w.length){Array.prototype.push.apply(r,t.slice(1))}q=t[0].length;v=u.lastIndex;if(r.length>=p){break}}if(u.lastIndex===t.index){u.lastIndex++}}if(v===w.length){if(!m.test.call(u,"")||q){r.push("")}}else{r.push(w.slice(v))}return r.length>p?r.slice(0,p):r};function j(r,q){if(!XRegExp.isRegExp(r)){throw TypeError("type RegExp expected")}var p=r._xregexp;r=XRegExp(r.source,d(r)+(q||""));if(p){r._xregexp={source:p.source,captureNames:p.captureNames?p.captureNames.slice(0):null}}return r}function d(p){return(p.global?"g":"")+(p.ignoreCase?"i":"")+(p.multiline?"m":"")+(p.extended?"x":"")+(p.sticky?"y":"")}function o(v,u,w,p){var r=k.length,y,s,x;g=true;try{while(r--){x=k[r];if((w&x.scope)&&(!x.trigger||x.trigger.call(p))){x.pattern.lastIndex=u;s=x.pattern.exec(v);if(s&&s.index===u){y={output:x.handler.call(p,s,w),match:s};break}}}}catch(q){throw q}finally{g=false}return y}function l(s,q,r){if(Array.prototype.indexOf){return s.indexOf(q,r)}for(var p=r||0;p<s.length;p++){if(s[p]===q){return p}}return -1}XRegExp.addToken(/\(\?#[^)]*\)/,function(p){return m.test.call(n,p.input.slice(p.index+p[0].length))?"":"(?:)"});XRegExp.addToken(/\((?!\?)/,function(){this.captureNames.push(null);return"("});XRegExp.addToken(/\(\?<([$\w]+)>/,function(p){this.captureNames.push(p[1]);this.hasNamedCapture=true;return"("});XRegExp.addToken(/\\k<([\w$]+)>/,function(q){var p=l(this.captureNames,q[1]);return p>-1?"\\"+(p+1)+(isNaN(q.input.charAt(q.index+q[0].length))?"":"(?:)"):q[0]});XRegExp.addToken(/\[\^?]/,function(p){return p[0]==="[]"?"\\b\\B":"[\\s\\S]"});XRegExp.addToken(/^\(\?([imsx]+)\)/,function(p){this.setFlag(p[1]);return""});XRegExp.addToken(/(?:\s+|#.*)+/,function(p){return m.test.call(n,p.input.slice(p.index+p[0].length))?"":"(?:)"},XRegExp.OUTSIDE_CLASS,function(){return this.hasFlag("x")});XRegExp.addToken(/\./,function(){return"[\\s\\S]"},XRegExp.OUTSIDE_CLASS,function(){return this.hasFlag("s")})})();
        }
    }

    window.Bixt = window.Bixt || {};
    window.Bixt.Widgets = window.Bixt.Widgets || {};

    Bixt.Widgets.Words = function (cfg) {
        this.init(cfg);
    };

    // Calls our tracking URL with some data. The data should be previously escaped because could cause JS errors in the
    // newly generated link.
    // Static method
    Bixt.track = function (opts) {
        if (document.getElementById && document.getElementById('Bixt_sys_iframe')) {
            document.getElementById('Bixt_sys_iframe').src = Bixt.Util.getMainUrl() + 'aff/track'
                    + '?'
                    + 'word=' + opts.word
                    + '&user_id=' + opts.user_id
                    + '&domain=' + opts.domain
                    + '&target_url=' + opts.target_url
                    + '';
        }
    };

    Bixt.Widgets.Words.prototype = {
        ver : '1.0',

        init : function (options) {
            Bixt.Util.log(new Date());

            this.options = {
                user_id: 0,
                max_links: 4,
                auto_parse : 1, // the parse method is called as soon as the data is loaded.
                domain: '',

                '' : '' // blank
            }

            // override the defaults
            for (attr in options) {
                this.options[attr] = options[attr];
            }

            var local_this = this;

            Bixt.Util.log('User Data URL: ' + Bixt.Util.getUserDataFile(this.options));

            this.loadScript(Bixt.Util.getUserDataFile(this.options), function () {
                Bixt.Util.log('User data loaded.');
                Bixt.Util.log(Bixt_data);

                local_this.setData(Bixt_data);

                if (local_this.options.auto_parse) {
                    Bixt.Util.log('Auto parsing links.');
                    local_this.parse();
                }
            });

            return this;
        },

        setData : function (data) {
            this.options.user_data = data;
        },

        // Do not use doc.write in the loaded script ?!?
        loadScript : function (url, callback) {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.async = true;
            script.src = url;

            if (document.getElementsByTagName('head')) {
                document.getElementsByTagName('head')[0].appendChild(script);
            } else if (document.getElementsByTagName('body')) {
                document.getElementsByTagName('body')[0].appendChild(script);
            } else {
                Bixt.Util.log('Bixt: HTML document is missing the HEAD or BODY tag. Cannot continue. What document is that ?');
                //alert('Bixt: HTML document is missing the HEAD or BODY tag. Cannot continue. What document is that ?');
            }

            if (callback) {
                script.onreadystatechange = function () {
                    if (script.readyState == 'loaded' || script.readyState == 'complete') {
                        callback();
                    } else {
                        //setTimeout(arguments.callee, 0);
                    }
                };

                script.onload = function () {
                    callback();
                    return;
                };

                // TODO Safari & Opera on doc load
            }
        },

        // checks if the keyword is in
        // a) attribute -> look left and stop if "<" is found, ok if >
        // b) HTML comment -> look left and stop if "<!--" is found, ok if >
        checkPosition : function (param_obj) {
            var pos = param_obj.match.index;
            var buff = param_obj.buffer;

            Bixt.Util.log(param_obj);

            while (pos) {
               // go left first: attrib/comment
               if (buff.charAt(pos) == '<'
                    || (buff.charAt(pos) == '-' && buff.charAt(pos-1) == '-' && buff.charAt(pos-2) == '!' && buff.charAt(pos-3) == '<')) {
                   Bixt.Util.log('L:attrib/comment; word:' + param_obj.match.word);
                   return 0;
               } else if (buff.charAt(pos) == '>') {
                   Bixt.Util.log('R:our of a tag; word:' + param_obj.match.word);
                   return 1;
               }

               pos--;
            }

            return 0;
        },

        // child is actually the keyword
        // parse_params is the params sent to _parse method
        create_link : function (child, parse_params) {
            var link = document.createElement('a');

            link.setAttribute('target', '_blank');
            //link.setAttribute('title', 'title');
            link.setAttribute('href', parse_params.target_url);
            link.setAttribute('rel', "external nofollow norewrite");

            if (tracking) {
                link.setAttribute('onclick', "Bixt.track({"
                    + " word: \'" + Bixt.Util.escape(child.data) + "\',"
                    + " user_id: \'" +  Bixt.Util.escape(this.options.user_id) + "\',"
                    + " domain: \'" + Bixt.Util.escape(this.options.domain) + "\',"
                    +  "target_url : \'" + Bixt.Util.escape(parse_params.target_url)
                    + "\'});");
            }

            link.innerHTML = child.data;

            // if no description exists and the link points to amazon
            // extract the info before dp "http://www.amazon.com/Build-Your-Site-Right-Using/dp/0980455278?tag=devequicrefe-20"
            if (Bixt.Util.empty(parse_params.rec.description)
                  && parse_params.rec.url.indexOf('amazon') != -1) {

                parse_params.rec.description = parse_params.rec.url;
                parse_params.rec.description = parse_params.rec.description.replace(/.*?amazon\.\w{2,4}(?:\.\w{2,4})?\/(.*?)\/dp.*/i, '$1');
                parse_params.rec.description = parse_params.rec.description.replace(/[-_]/ig, ' ');
            }

            // The tooltip must be a span in a link
            if (!Bixt.Util.empty(parse_params.rec.description)) {
                var powered_by = ' | words2aff links by Bixt.net';
                var tooltip_text = parse_params.rec.description;
                link.setAttribute('title', tooltip_text + powered_by);

                // This creates extra info which doesn't look nice.
                // sticking to a simple link 'title' attribute.
                /*var tooltip_node = document.createElement('SPAN');
                tooltip_node.setAttribute('class', "bixt_tooltip_classic");
                tooltip_node.innerHTML = tooltip_text + ' <br/><br/> Powered by bixt.net';

                link.setAttribute('class', "bixt_tooltip");
                link.appendChild(tooltip_node);*/
            }

            var node = document.createElement('SPAN');

            //node.style.backgroundColor = '#fff';
            //node.style.color = 'red';
            node.appendChild(link);

            return node;
        },

        // holds info about many times a keyword has been replaced.
        replace_cnt : {},

        // Parses the text nodes and replaces the keywords with links
        // credits: http://www.iamcal.com/publish/articles/js/google_highlighting/
        _parse : function (obj_params) {
            var term = obj_params.keyword;
            var term_low = obj_params.keyword.toLowerCase();
            var container = obj_params.container;
            var re = XRegExp("\\b(" + XRegExp.escape(term) + ")\\b", "i");

            // if the body doesn't have the keyword at all don't spent time to go through DOM
            if (container.nodeName.toLowerCase() == 'body') {
                var buff = container.innerHTML;
                buff = buff.toLowerCase();

                if (buff.indexOf(term_low) == -1 || !re.test(buff)) {
                    return;
                }
            }

            for(var i = 0; i < container.childNodes.length; i++){
                var node = container.childNodes[i];
                var parentNode = node.parentNode ? node.parentNode.nodeName.toLowerCase() : '';

                if (node.nodeType == 3) {
                    if (parentNode && node.parentNode.className.indexOf('bixt_tooltip') == -1
                            && (parentNode == 'body' || parentNode == 'span' || parentNode == 'p' || parentNode == 'div')) {
                        //Bixt.Util.log('term: ' + term + ' parentNode: ' + parentNode + "\n");

                        var result;
                        var data = node.data;
                        var data_low = data.toLowerCase();

                        // skip if the kwd was replaced more than 2
                        if (data_low.indexOf(term_low) != -1 && (!(term_low in this.replace_cnt) || this.replace_cnt[term_low] < 2)) {
                            if ((result = data.search(re)) != -1) {
                                var new_node = document.createElement('SPAN');
                                new_node.className += "Bixt_parent_link_container term_" + term;
                                node.parentNode.replaceChild(new_node, node);

                                result = data_low.indexOf(term_low);
                                new_node.appendChild(document.createTextNode(data.substr(0, result)));
                                new_node.appendChild(this.create_link(document.createTextNode(data.substr(result,term.length)), obj_params));
                                data = data.substr(result + term.length);
                                data_low = data_low.substr(result + term.length);

                                new_node.appendChild(document.createTextNode(data));

                                this.replace_cnt[term_low] = this.replace_cnt[term_low] || 0;
                                this.replace_cnt[term_low]++;
                            }
                        }
                    }
                } else {
                    var p = obj_params;
                    p.container = node;
                    //recurse
                    this._parse(p);
                }
            }
        },

        // Loops through keywords and calls
        // it is called when the data (e.g. domain.js) has finished loading.
        parse : function () {
            var keywords = this.options.user_data.data.keywords; // this should be loaded from another js file.

            for (var idx in keywords) {
                var rec = keywords[idx];
                var keyword = rec.keyword;

                if (Bixt.Util.empty(keyword)) {
                    continue;
                }

                var target_url = rec.url || this.options.user_data.meta.default_target_url;

                this._parse({
                    keyword : keyword,
                    target_url : target_url,
                    container : document.body,
                    rec : rec,

                    '' : ''
                });
            }

            // just to play it safe
            if ( document.createElement && (typeof bixt_cfg.branding == 'undefined' || bixt_cfg.branding) ) {
                var divTag = document.createElement("div");
                divTag.id = "Bixt_branding_booter";
                divTag.setAttribute("align", "center");
                divTag.style.margin = "0px auto";
                divTag.className ="Bixt_branding_footer_must_always_remain_visible Bixt_branding_footer";
                divTag.innerHTML = "Affiliate links generated by <a href='http://bixt.net/aff/site/"
                         + '?utm_source=' + window.location.hostname + '&utm_medium=branding_footer&utm_campaign=product'
                         + " ' target='_blank' title='Convert words into affiliate links.'>Bixt.net</a>";
                document.body.appendChild(divTag);
            }

            return this;
        },

        '' : ''
    };

    Bixt.Util = {
        // must be in sync with app.ini
        domain: {
            production: {
                host : 'bixt.net',
                cdnHost : 'cdn.bixt.net',
                webPath : '/',

                sslHost : 'ssl.orbisius.com',
                sslCdnHost : 'ssl.orbisius.com',
                sslWebPath : '/apps/bixt/public/'
            },

            development : {
                host : 'localhost.orbisius.com',
                cdnHost : 'localhost.orbisius.com',
                webPath : '/projects/aff/project/public/',

                sslHost : 'localhost.orbisius.com',
                sslCdnHost : 'localhost.orbisius.com',
                sslWebPath : '/projects/aff/project/public/'
            }
        },

        // returns the live/dev host
        getMainUrl: function () {
            // dev server
            if ( (typeof bixt_cfg.env == 'undefined') || bixt_cfg.env != 'live'
                    && ( window.location.href.indexOf('.dev.') != -1 || window.location.href.indexOf('localhost') != -1) ) {
                return ("https:" == document.location.protocol)
                            ? 'http://' + this.domain.development.sslHost + this.domain.development.sslWebPath
                            : 'http://' + this.domain.development.host + this.domain.development.webPath;
            } else {
                return ("https:" == document.location.protocol)
                            ? 'https://' + this.domain.production.sslHost + this.domain.production.sslWebPath
                            : 'http://' + this.domain.production.host + this.domain.production.webPath;
            }
        },

        // returns the location of user's keyword js file
        // e.g. http://s.bixt.net/user_data/a/b/c/d/12345/domain.js?__rnd=123
        // e.g. https://ssl.orbisius.com/domains/bixt.net/htdocs/user_data/a/b/c/d/12345/domain.js?__rnd=12345
        getUserDataFile: function (params) {
            var url  = this.getMainUrl();
            var hash = this.calcHash(params.user_id);

            url += 'user_data/'
                    + hash.charAt(0) + '/'
                    + hash.charAt(1) + '/'
                    + hash.charAt(2) + '/'
                    + hash.charAt(3) + '/'
                    + params.user_id + '/' + params.domain + '.js?__rnd=' + (parseInt(Math.random()*99999999));

            return url;
        },

        empty: function (myStr) {
            return (typeof myStr == 'undefined') || (myStr == '');
        },

        escape: function (myStr) {
            return escape(myStr);
        },

        unescape: function (myStr) {
            return unescape(myStr);
        },

        /*
         *I use it to test some new stuff.
         **/
        test: function (myStr) {
            return (typeof myStr == 'undefined') || (myStr == '');
        },

        utf8_encode : function (argString) {
            // http://kevin.vanzonneveld.net
            // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: sowberry
            // +    tweaked by: Jack
            // +   bugfixed by: Onno Marsman
            // +   improved by: Yves Sucaet
            // +   bugfixed by: Onno Marsman
            // +   bugfixed by: Ulrich
            // +   bugfixed by: Rafal Kukawski
            // *     example 1: utf8_encode('Kevin van Zonneveld');
            // *     returns 1: 'Kevin van Zonneveld'

            if (argString === null || typeof argString === "undefined") {
                return "";
            }

            var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
            var utftext = "",
                start, end, stringl = 0;

            start = end = 0;
            stringl = string.length;
            for (var n = 0; n < stringl; n++) {
                var c1 = string.charCodeAt(n);
                var enc = null;

                if (c1 < 128) {
                    end++;
                } else if (c1 > 127 && c1 < 2048) {
                    enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
                } else {
                    enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
                }
                if (enc !== null) {
                    if (end > start) {
                        utftext += string.slice(start, end);
                    }
                    utftext += enc;
                    start = end = n + 1;
                }
            }

            if (end > start) {
                utftext += string.slice(start, stringl);
            }

            return utftext;
        },

        // sha1
        calcHash : function  (str) {
            // http://kevin.vanzonneveld.net
            // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
            // + namespaced by: Michael White (http://getsprink.com)
            // +      input by: Brett Zamir (http://brett-zamir.me)
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // -    depends on: utf8_encode
            // *     example 1: sha1('Kevin van Zonneveld');
            // *     returns 1: '54916d2e62f65b3afa6e192e6a601cdbe5cb5897'
            var rotate_left = function (n, s) {
                var t4 = (n << s) | (n >>> (32 - s));
                return t4;
            };

            var cvt_hex = function (val) {
                var str = "";
                var i;
                var v;

                for (i = 7; i >= 0; i--) {
                    v = (val >>> (i * 4)) & 0x0f;
                    str += v.toString(16);
                }
                return str;
            };

            var blockstart;
            var i, j;
            var W = new Array(80);
            var H0 = 0x67452301;
            var H1 = 0xEFCDAB89;
            var H2 = 0x98BADCFE;
            var H3 = 0x10325476;
            var H4 = 0xC3D2E1F0;
            var A, B, C, D, E;
            var temp;

            str = this.utf8_encode(str);
            var str_len = str.length;

            var word_array = [];
            for (i = 0; i < str_len - 3; i += 4) {
                j = str.charCodeAt(i) << 24 | str.charCodeAt(i + 1) << 16 | str.charCodeAt(i + 2) << 8 | str.charCodeAt(i + 3);
                word_array.push(j);
            }

            switch (str_len % 4) {
            case 0:
                i = 0x080000000;
                break;
            case 1:
                i = str.charCodeAt(str_len - 1) << 24 | 0x0800000;
                break;
            case 2:
                i = str.charCodeAt(str_len - 2) << 24 | str.charCodeAt(str_len - 1) << 16 | 0x08000;
                break;
            case 3:
                i = str.charCodeAt(str_len - 3) << 24 | str.charCodeAt(str_len - 2) << 16 | str.charCodeAt(str_len - 1) << 8 | 0x80;
                break;
            }

            word_array.push(i);

            while ((word_array.length % 16) != 14) {
                word_array.push(0);
            }

            word_array.push(str_len >>> 29);
            word_array.push((str_len << 3) & 0x0ffffffff);

            for (blockstart = 0; blockstart < word_array.length; blockstart += 16) {
                for (i = 0; i < 16; i++) {
                    W[i] = word_array[blockstart + i];
                }
                for (i = 16; i <= 79; i++) {
                    W[i] = rotate_left(W[i - 3] ^ W[i - 8] ^ W[i - 14] ^ W[i - 16], 1);
                }

                A = H0;
                B = H1;
                C = H2;
                D = H3;
                E = H4;

                for (i = 0; i <= 19; i++) {
                    temp = (rotate_left(A, 5) + ((B & C) | (~B & D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }

                for (i = 20; i <= 39; i++) {
                    temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }

                for (i = 40; i <= 59; i++) {
                    temp = (rotate_left(A, 5) + ((B & C) | (B & D) | (C & D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }

                for (i = 60; i <= 79; i++) {
                    temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
                    E = D;
                    D = C;
                    C = rotate_left(B, 30);
                    B = A;
                    A = temp;
                }

                H0 = (H0 + A) & 0x0ffffffff;
                H1 = (H1 + B) & 0x0ffffffff;
                H2 = (H2 + C) & 0x0ffffffff;
                H3 = (H3 + D) & 0x0ffffffff;
                H4 = (H4 + E) & 0x0ffffffff;
            }

            temp = cvt_hex(H0) + cvt_hex(H1) + cvt_hex(H2) + cvt_hex(H3) + cvt_hex(H4);
            return temp.toLowerCase();
        },

        // log info. not used at the moment
        log : function (data) {
            // firebug ?
            if ("console" in window) {
                if (typeof data == 'string') {
                    data = 'Bixt: ' + data;
                } else {
                    console.log('Bixt:');
                }

                console.log(data);
            }
        },

        '' : ''
    };

    if (typeof bixt_cfg != 'undefined') {
        Bixt.Util.log('bixt_cfg config found.');
        Bixt.Util.log(bixt_cfg);
        new Bixt.Widgets.Words(bixt_cfg);
    } else {
        Bixt.Util.log('bixt_cfg config NOT found.');
    }
})();