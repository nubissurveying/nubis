(function(d){if(!String.prototype.includes){(function(){var j={}.toString;var g=(function(){try{var n={};var m=Object.defineProperty;var k=m(n,n,n)&&m}catch(l){}return k}());var i="".indexOf;var h=function(p){if(this==null){throw TypeError()}var n=String(this);if(p&&j.call(p)=="[object RegExp]"){throw TypeError()}var l=n.length;var m=String(p);var o=m.length;var k=arguments.length>1?arguments[1]:undefined;var r=k?Number(k):0;if(r!=r){r=0}var q=Math.min(Math.max(r,0),l);if(o+q>l){return false}return i.call(n,m,r)!=-1};if(g){g(String.prototype,"includes",{value:h,configurable:true,writable:true})}else{String.prototype.includes=h}}())}if(!String.prototype.startsWith){(function(){var g=(function(){try{var m={};var l=Object.defineProperty;var j=l(m,m,m)&&l}catch(k){}return j}());var i={}.toString;var h=function(q){if(this==null){throw TypeError()}var n=String(this);if(q&&i.call(q)=="[object RegExp]"){throw TypeError()}var j=n.length;var r=String(q);var l=r.length;var m=arguments.length>1?arguments[1]:undefined;var p=m?Number(m):0;if(p!=p){p=0}var k=Math.min(Math.max(p,0),j);if(l+k>j){return false}var o=-1;while(++o<l){if(n.charCodeAt(k+o)!=r.charCodeAt(o)){return false}}return true};if(g){g(String.prototype,"startsWith",{value:h,configurable:true,writable:true})}else{String.prototype.startsWith=h}}())}d.expr[":"].icontains=function(j,g,i){var k=d(j);var h=(k.data("tokens")||k.text()).toUpperCase();return h.includes(i[3].toUpperCase())};d.expr[":"].ibegins=function(j,g,i){var k=d(j);var h=(k.data("tokens")||k.text()).toUpperCase();return h.startsWith(i[3].toUpperCase())};d.expr[":"].aicontains=function(j,g,i){var k=d(j);var h=(k.data("tokens")||k.data("normalizedText")||k.text()).toUpperCase();return h.includes(h,i[3])};d.expr[":"].aibegins=function(j,g,i){var k=d(j);var h=(k.data("tokens")||k.data("normalizedText")||k.text()).toUpperCase();return h.startsWith(i[3].toUpperCase())};function f(h){var g=[{re:/[\xC0-\xC6]/g,ch:"A"},{re:/[\xE0-\xE6]/g,ch:"a"},{re:/[\xC8-\xCB]/g,ch:"E"},{re:/[\xE8-\xEB]/g,ch:"e"},{re:/[\xCC-\xCF]/g,ch:"I"},{re:/[\xEC-\xEF]/g,ch:"i"},{re:/[\xD2-\xD6]/g,ch:"O"},{re:/[\xF2-\xF6]/g,ch:"o"},{re:/[\xD9-\xDC]/g,ch:"U"},{re:/[\xF9-\xFC]/g,ch:"u"},{re:/[\xC7-\xE7]/g,ch:"c"},{re:/[\xD1]/g,ch:"N"},{re:/[\xF1]/g,ch:"n"}];d.each(g,function(){h=h.replace(this.re,this.ch)});return h}function e(h){var j={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"};var l="(?:"+Object.keys(j).join("|")+")",k=new RegExp(l),i=new RegExp(l,"g"),g=h==null?"":""+h;return k.test(g)?g.replace(i,function(m){return j[m]}):g}var b=function(h,g,i){if(i){i.stopPropagation();i.preventDefault()}this.$element=d(h);this.$newElement=null;this.$button=null;this.$menu=null;this.$lis=null;this.options=g;if(this.options.title===null){this.options.title=this.$element.attr("title")}this.val=b.prototype.val;this.render=b.prototype.render;this.refresh=b.prototype.refresh;this.setStyle=b.prototype.setStyle;this.selectAll=b.prototype.selectAll;this.deselectAll=b.prototype.deselectAll;this.destroy=b.prototype.remove;this.remove=b.prototype.remove;this.show=b.prototype.show;this.hide=b.prototype.hide;this.init()};b.VERSION="1.6.3";b.DEFAULTS={noneSelectedText:"Nothing selected",noneResultsText:"No results matched {0}",countSelectedText:function(h,g){return(h==1)?"{0} item selected":"{0} items selected"},maxOptionsText:function(g,h){return[(g==1)?"Limit reached ({n} item max)":"Limit reached ({n} items max)",(h==1)?"Group limit reached ({n} item max)":"Group limit reached ({n} items max)"]},selectAllText:"Select All",deselectAllText:"Deselect All",doneButton:false,doneButtonText:"Close",multipleSeparator:", ",style:"btn-default",size:"auto",title:null,selectedTextFormat:"values",width:false,container:false,hideDisabled:false,showSubtext:false,showIcon:true,showContent:true,dropupAuto:true,header:false,liveSearch:false,liveSearchPlaceholder:null,liveSearchNormalize:false,liveSearchStyle:"contains",actionsBox:false,iconBase:"glyphicon",tickIcon:"glyphicon-ok",maxOptions:false,mobile:false,selectOnTab:false,dropdownAlignRight:false};b.prototype={constructor:b,init:function(){var g=this,h=this.$element.attr("id");this.$element.hide();this.multiple=this.$element.prop("multiple");this.autofocus=this.$element.prop("autofocus");this.$newElement=this.createView();this.$element.after(this.$newElement);this.$menu=this.$newElement.children(".dropdown-menu");this.$button=this.$newElement.children("button");this.$searchbox=this.$newElement.find("input");if(this.options.dropdownAlignRight){this.$menu.addClass("dropdown-menu-right")}if(typeof h!=="undefined"){this.$button.attr("data-id",h);d('label[for="'+h+'"]').click(function(i){i.preventDefault();g.$button.focus()})}this.checkDisabled();this.clickListener();if(this.options.liveSearch){this.liveSearchListener()}this.render();this.liHeight();this.setStyle();this.setWidth();if(this.options.container){this.selectPosition()}this.$menu.data("this",this);this.$newElement.data("this",this);if(this.options.mobile){this.mobile()}},createDropdown:function(){var h=this.multiple?" show-tick":"",i=this.$element.parent().hasClass("input-group")?" input-group-btn":"",n=this.autofocus?" autofocus":"";var m=this.options.header?'<div class="popover-title"><button type="button" class="close" aria-hidden="true">&times;</button>'+this.options.header+"</div>":"";var l=this.options.liveSearch?'<div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"'+(null===this.options.liveSearchPlaceholder?"":' placeholder="'+e(this.options.liveSearchPlaceholder)+'"')+"></div>":"";var k=this.options.actionsBox?'<div class="bs-actionsbox"><div class="btn-group btn-group-sm btn-block"><button class="actions-btn bs-select-all btn btn-default">'+this.options.selectAllText+'</button><button class="actions-btn bs-deselect-all btn btn-default">'+this.options.deselectAllText+"</button></div></div>":"";var g=this.multiple&&this.options.doneButton?'<div class="bs-donebutton"><div class="btn-group btn-block"><button class="btn btn-sm btn-default">'+this.options.doneButtonText+"</button></div></div>":"";var j='<div class="btn-group bootstrap-select'+h+i+'"><button type="button" class="btn dropdown-toggle form-control selectpicker" data-toggle="dropdown"'+n+'><span class="filter-option pull-left"></span>&nbsp;<span class="caret"></span></button><div class="dropdown-menu open">'+m+l+k+'<ul class="dropdown-menu inner selectpicker" role="menu"></ul>'+g+"</div></div>";return d(j)},createView:function(){var g=this.createDropdown();var h=this.createLi();g.find("ul").append(h);return g},reloadLi:function(){this.destroyLi();var g=this.createLi();this.$menu.find("ul").append(g)},destroyLi:function(){this.$menu.find("li").remove()},createLi:function(){var j=this,i=[],k=0;var g=function(o,m,n,l){return"<li"+((typeof n!=="undefined"&""!==n)?' class="'+n+'"':"")+((typeof m!=="undefined"&null!==m)?' data-original-index="'+m+'"':"")+((typeof l!=="undefined"&null!==l)?'data-optgroup="'+l+'"':"")+">"+o+"</li>"};var h=function(o,l,n,m){return'<a tabindex="0"'+(typeof l!=="undefined"?' class="'+l+'"':"")+(typeof n!=="undefined"?' style="'+n+'"':"")+' data-normalized-text="'+f(e(o))+'"'+(typeof m!=="undefined"||m!==null?' data-tokens="'+m+'"':"")+">"+o+'<span class="'+j.options.iconBase+" "+j.options.tickIcon+' check-mark"></span></a>'};this.$element.find("option").each(function(n){var p=d(this);var m=p.attr("class")||"",o=p.attr("style"),v=p.data("content")?p.data("content"):p.html(),q=p.data("tokens")?p.data("tokens"):null,t=typeof p.data("subtext")!=="undefined"?'<small class="text-muted">'+p.data("subtext")+"</small>":"",r=typeof p.data("icon")!=="undefined"?'<span class="'+j.options.iconBase+" "+p.data("icon")+'"></span> ':"",u=p.is(":disabled")||p.parent().is(":disabled");if(r!==""&&u){r="<span>"+r+"</span>"}if(!p.data("content")){v=r+'<span class="text">'+v+t+"</span>"}if(j.options.hideDisabled&&u){return}if(p.parent().is("optgroup")&&p.data("divider")!==true){if(p.index()===0){k+=1;var s=p.parent().attr("label");var w=typeof p.parent().data("subtext")!=="undefined"?'<small class="text-muted">'+p.parent().data("subtext")+"</small>":"";var l=p.parent().data("icon")?'<span class="'+j.options.iconBase+" "+p.parent().data("icon")+'"></span> ':"";s=l+'<span class="text">'+s+w+"</span>";if(n!==0&&i.length>0){i.push(g("",null,"divider"))}i.push(g(s,null,"dropdown-header",k))}i.push(g(h(v,"opt "+m,o,q),n,"",k))}else{if(p.data("divider")===true){i.push(g("",n,"divider"))}else{if(p.data("hidden")===true){i.push(g(h(v,m,o,q),n,"hidden is-hidden"))}else{i.push(g(h(v,m,o,q),n))}}}});if(!this.multiple&&this.$element.find("option:selected").length===0&&!this.options.title){this.$element.find("option").eq(0).prop("selected",true).attr("selected","selected")}return d(i.join(""))},findLis:function(){if(this.$lis==null){this.$lis=this.$menu.find("li")}return this.$lis},render:function(j){var i=this;if(j!==false){this.$element.find("option").each(function(o){i.setDisabled(o,d(this).is(":disabled")||d(this).parent().is(":disabled"));i.setSelected(o,d(this).is(":selected"))})}this.tabIndex();var l=this.options.hideDisabled?":not([disabled])":"";var n=this.$element.find("option:selected"+l).map(function(){var q=d(this);var p=q.data("icon")&&i.options.showIcon?'<i class="'+i.options.iconBase+" "+q.data("icon")+'"></i> ':"";var o;if(i.options.showSubtext&&q.attr("data-subtext")&&!i.multiple){o=' <small class="text-muted">'+q.data("subtext")+"</small>"}else{o=""}if(typeof q.attr("title")!=="undefined"){return q.attr("title")}else{if(q.data("content")&&i.options.showContent){return q.data("content")}else{return p+q.html()+o}}}).toArray();var k=!this.multiple?n[0]:n.join(this.options.multipleSeparator);if(this.multiple&&this.options.selectedTextFormat.indexOf("count")>-1){var g=this.options.selectedTextFormat.split(">");if((g.length>1&&n.length>g[1])||(g.length==1&&n.length>=2)){l=this.options.hideDisabled?", [disabled]":"";var h=this.$element.find("option").not('[data-divider="true"], [data-hidden="true"]'+l).length,m=(typeof this.options.countSelectedText==="function")?this.options.countSelectedText(n.length,h):this.options.countSelectedText;k=m.replace("{0}",n.length.toString()).replace("{1}",h.toString())}}if(this.options.title==undefined){this.options.title=this.$element.attr("title")}if(this.options.selectedTextFormat=="static"){k=this.options.title}if(!k){k=typeof this.options.title!=="undefined"?this.options.title:this.options.noneSelectedText}this.$button.attr("title",d.trim(k.replace(/<[^>]*>?/g,"")));this.$newElement.find(".filter-option").html(k)},setStyle:function(i,h){if(this.$element.attr("class")){this.$newElement.addClass(this.$element.attr("class").replace(/selectpicker|mobile-device|validate\[.*\]/gi,""))}var g=i?i:this.options.style;if(h=="add"){this.$button.addClass(g)}else{if(h=="remove"){this.$button.removeClass(g)}else{this.$button.removeClass(this.options.style);this.$button.addClass(g)}}},liHeight:function(){if(this.options.size===false){return}var k=this.$menu.parent().clone().children(".dropdown-toggle").prop("autofocus",false).end().appendTo("body"),l=k.addClass("open").children(".dropdown-menu"),j=l.find("li").not(".divider").not(".dropdown-header").filter(":visible").children("a").outerHeight(),i=this.options.header?l.find(".popover-title").outerHeight():0,m=this.options.liveSearch?l.find(".bs-searchbox").outerHeight():0,g=this.options.actionsBox?l.find(".bs-actionsbox").outerHeight():0,h=this.multiple?l.find(".bs-donebutton").outerHeight():0;k.remove();this.$newElement.data("liHeight",j).data("headerHeight",i).data("searchHeight",m).data("actionsHeight",g).data("doneButtonHeight",h)},setSize:function(){this.findLis();var n=this,h=this.$menu,o=h.find(".inner"),z=this.$newElement.outerHeight(),j=this.$newElement.data("liHeight"),x=this.$newElement.data("headerHeight"),r=this.$newElement.data("searchHeight"),m=this.$newElement.data("actionsHeight"),k=this.$newElement.data("doneButtonHeight"),q=this.$lis.filter(".divider").outerHeight(true),w=parseInt(h.css("padding-top"))+parseInt(h.css("padding-bottom"))+parseInt(h.css("border-top-width"))+parseInt(h.css("border-bottom-width")),u=this.options.hideDisabled?", .disabled":"",t=d(window),l=w+parseInt(h.css("margin-top"))+parseInt(h.css("margin-bottom"))+2,v,A,y,p=function(){A=n.$newElement.offset().top-t.scrollTop();y=t.height()-A-z};p();if(this.options.header){h.css("padding-top",0)}if(this.options.size=="auto"){var i=function(){var C,B=n.$lis.not(".hidden");p();v=y-l;if(n.options.dropupAuto){n.$newElement.toggleClass("dropup",A>y&&(v-l)<h.height())}if(n.$newElement.hasClass("dropup")){v=A-l}if((B.length+B.filter(".dropdown-header").length)>3){C=j*3+l-2}else{C=0}h.css({"max-height":v+"px",overflow:"hidden","min-height":C+x+r+m+k+"px"});o.css({"max-height":v-x-r-m-k-w+"px","overflow-y":"auto","min-height":Math.max(C-w,0)+"px"})};i();this.$searchbox.off("input.getSize propertychange.getSize").on("input.getSize propertychange.getSize",i);t.off("resize.getSize").on("resize.getSize",i);t.off("scroll.getSize").on("scroll.getSize",i)}else{if(this.options.size&&this.options.size!="auto"&&h.find("li"+u).length>this.options.size){var s=this.$lis.not(".divider"+u).children().slice(0,this.options.size).last().parent().index();var g=this.$lis.slice(0,s+1).filter(".divider").length;v=j*this.options.size+g*q+w;if(n.options.dropupAuto){this.$newElement.toggleClass("dropup",A>y&&v<h.height())}h.css({"max-height":v+x+r+m+k+"px",overflow:"hidden"});o.css({"max-height":v-w+"px","overflow-y":"auto"})}}},setWidth:function(){if(this.options.width=="auto"){this.$menu.css("min-width","0");var i=this.$newElement.clone().appendTo("body");var g=i.children(".dropdown-menu").css("width");var h=i.css("width","auto").children("button").css("width");i.remove();this.$newElement.css("width",Math.max(parseInt(g),parseInt(h))+"px")}else{if(this.options.width=="fit"){this.$menu.css("min-width","");this.$newElement.css("width","").addClass("fit-width")}else{if(this.options.width){this.$menu.css("min-width","");this.$newElement.css("width",this.options.width)}else{this.$menu.css("min-width","");this.$newElement.css("width","")}}}if(this.$newElement.hasClass("fit-width")&&this.options.width!=="fit"){this.$newElement.removeClass("fit-width")}},selectPosition:function(){var i=this,h="<div />",j=d(h),l,k,g=function(m){j.addClass(m.attr("class").replace(/form-control/gi,"")).toggleClass("dropup",m.hasClass("dropup"));l=m.offset();k=m.hasClass("dropup")?0:m[0].offsetHeight;j.css({top:l.top+k,left:l.left,width:m[0].offsetWidth,position:"absolute"})};this.$newElement.on("click",function(){if(i.isDisabled()){return}g(d(this));j.appendTo(i.options.container);j.toggleClass("open",!d(this).hasClass("open"));j.append(i.$menu)});d(window).resize(function(){g(i.$newElement)});d(window).on("scroll",function(){g(i.$newElement)});d("html").on("click",function(m){if(d(m.target).closest(i.$newElement).length<1){j.removeClass("open")}})},setSelected:function(g,h){this.findLis();this.$lis.filter('[data-original-index="'+g+'"]').toggleClass("selected",h)},setDisabled:function(g,h){this.findLis();if(h){this.$lis.filter('[data-original-index="'+g+'"]').addClass("disabled").find("a").attr("href","#").attr("tabindex",-1)}else{this.$lis.filter('[data-original-index="'+g+'"]').removeClass("disabled").find("a").removeAttr("href").attr("tabindex",0)}},isDisabled:function(){return this.$element.is(":disabled")},checkDisabled:function(){var g=this;if(this.isDisabled()){this.$button.addClass("disabled").attr("tabindex",-1)}else{if(this.$button.hasClass("disabled")){this.$button.removeClass("disabled")}if(this.$button.attr("tabindex")==-1){if(!this.$element.data("tabindex")){this.$button.removeAttr("tabindex")}}}this.$button.click(function(){return !g.isDisabled()})},tabIndex:function(){if(this.$element.is("[tabindex]")){this.$element.data("tabindex",this.$element.attr("tabindex"));this.$button.attr("tabindex",this.$element.data("tabindex"))}},clickListener:function(){var g=this;this.$newElement.on("touchstart.dropdown",".dropdown-menu",function(h){h.stopPropagation()});this.$newElement.on("click",function(){g.setSize();if(!g.options.liveSearch&&!g.multiple){setTimeout(function(){g.$menu.find(".selected a").focus()},10)}});this.$menu.on("click","li a",function(w){var m=d(this),h=m.parent().data("originalIndex"),u=g.$element.val(),o=g.$element.prop("selectedIndex");if(g.multiple){w.stopPropagation()}w.preventDefault();if(!g.isDisabled()&&!m.parent().hasClass("disabled")){var s=g.$element.find("option"),t=s.eq(h),j=t.prop("selected"),r=t.parent("optgroup"),y=g.options.maxOptions,p=r.data("maxOptions")||false;if(!g.multiple){s.prop("selected",false);t.prop("selected",true);g.$menu.find(".selected").removeClass("selected");g.setSelected(h,true)}else{t.prop("selected",!j);g.setSelected(h,!j);m.blur();if(y!==false||p!==false){var i=y<s.filter(":selected").length,l=p<r.find("option:selected").length;if((y&&i)||(p&&l)){if(y&&y==1){s.prop("selected",false);t.prop("selected",true);g.$menu.find(".selected").removeClass("selected");g.setSelected(h,true)}else{if(p&&p==1){r.find("option:selected").prop("selected",false);t.prop("selected",true);var v=m.data("optgroup");g.$menu.find(".selected").has('a[data-optgroup="'+v+'"]').removeClass("selected");g.setSelected(h,true)}else{var k=(typeof g.options.maxOptionsText==="function")?g.options.maxOptionsText(y,p):g.options.maxOptionsText,x=k[0].replace("{n}",y),n=k[1].replace("{n}",p),q=d('<div class="notify"></div>');if(k[2]){x=x.replace("{var}",k[2][y>1?0:1]);n=n.replace("{var}",k[2][p>1?0:1])}t.prop("selected",false);g.$menu.append(q);if(y&&i){q.append(d("<div>"+x+"</div>"));g.$element.trigger("maxReached.bs.select")}if(p&&l){q.append(d("<div>"+n+"</div>"));g.$element.trigger("maxReachedGrp.bs.select")}setTimeout(function(){g.setSelected(h,false)},10);q.delay(750).fadeOut(300,function(){d(this).remove()})}}}}}if(!g.multiple){g.$button.focus()}else{if(g.options.liveSearch){g.$searchbox.focus()}}if((u!=g.$element.val()&&g.multiple)||(o!=g.$element.prop("selectedIndex")&&!g.multiple)){g.$element.change()}}});this.$menu.on("click","li.disabled a, .popover-title, .popover-title :not(.close)",function(h){if(h.currentTarget==this){h.preventDefault();h.stopPropagation();if(!g.options.liveSearch){g.$button.focus()}else{g.$searchbox.focus()}}});this.$menu.on("click","li.divider, li.dropdown-header",function(h){h.preventDefault();h.stopPropagation();if(!g.options.liveSearch){g.$button.focus()}else{g.$searchbox.focus()}});this.$menu.on("click",".popover-title .close",function(){g.$button.focus()});this.$searchbox.on("click",function(h){h.stopPropagation()});this.$menu.on("click",".actions-btn",function(h){if(g.options.liveSearch){g.$searchbox.focus()}else{g.$button.focus()}h.preventDefault();h.stopPropagation();if(d(this).is(".bs-select-all")){g.selectAll()}else{g.deselectAll()}g.$element.change()});this.$element.change(function(){g.render(false)})},liveSearchListener:function(){var h=this,g=d('<li class="no-results"></li>');this.$newElement.on("click.dropdown.data-api touchstart.dropdown.data-api",function(){h.$menu.find(".active").removeClass("active");if(!!h.$searchbox.val()){h.$searchbox.val("");h.$lis.not(".is-hidden").removeClass("hidden");if(!!g.parent().length){g.remove()}}if(!h.multiple){h.$menu.find(".selected").addClass("active")}setTimeout(function(){h.$searchbox.focus()},10)});this.$searchbox.on("click.dropdown.data-api focus.dropdown.data-api touchend.dropdown.data-api",function(i){i.stopPropagation()});this.$searchbox.on("input propertychange",function(){if(h.$searchbox.val()){var i=h.$lis.not(".is-hidden").removeClass("hidden").find("a");if(h.options.liveSearchNormalize){i=i.not(":a"+h._searchStyle()+"("+f(h.$searchbox.val())+")")}else{i=i.not(":"+h._searchStyle()+"("+h.$searchbox.val()+")")}i.parent().addClass("hidden");h.$lis.filter(".dropdown-header").each(function(){var k=d(this),j=k.data("optgroup");if(h.$lis.filter("[data-optgroup="+j+"]").not(k).filter(":visible").length===0){k.addClass("hidden")}});if(!h.$menu.find("li").filter(":visible:not(.no-results)").length){if(!!g.parent().length){g.remove()}g.html(h.options.noneResultsText.replace("{0}",'"'+e(h.$searchbox.val())+'"')).show();h.$menu.find("li").last().after(g)}else{if(!!g.parent().length){g.remove()}}}else{h.$lis.not(".is-hidden").removeClass("hidden");if(!!g.parent().length){g.remove()}}h.$menu.find("li.active").removeClass("active");h.$menu.find("li").filter(":visible:not(.divider)").eq(0).addClass("active").find("a").focus();d(this).focus()})},_searchStyle:function(){var g="icontains";switch(this.options.liveSearchStyle){case"begins":case"startsWith":g="ibegins";break;case"contains":default:break}return g},val:function(g){if(typeof g!=="undefined"){this.$element.val(g);this.render();return this.$element}else{return this.$element.val()}},selectAll:function(){this.findLis();this.$lis.not(".divider").not(".disabled").not(".selected").filter(":visible").find("a").click()},deselectAll:function(){this.findLis();this.$lis.not(".divider").not(".disabled").filter(".selected").filter(":visible").find("a").click()},keydown:function(w){var i=d(this),r=(i.is("input"))?i.parent().parent():i.parent(),h,m=r.data("this"),j,t,l,p,s,g,n,u,q={32:" ",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",59:";",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z",96:"0",97:"1",98:"2",99:"3",100:"4",101:"5",102:"6",103:"7",104:"8",105:"9"};if(m.options.liveSearch){r=i.parent().parent()}if(m.options.container){r=m.$menu}h=d("[role=menu] li a",r);u=m.$menu.parent().hasClass("open");if(!u&&/([0-9]|[A-z])/.test(String.fromCharCode(w.keyCode))){if(!m.options.container){m.setSize();m.$menu.parent().addClass("open");u=true}else{m.$newElement.trigger("click")}m.$searchbox.focus()}if(m.options.liveSearch){if(/(^9$|27)/.test(w.keyCode.toString(10))&&u&&m.$menu.find(".active").length===0){w.preventDefault();m.$menu.parent().removeClass("open");m.$button.focus()}h=d("[role=menu] li:not(.divider):not(.dropdown-header):visible a",r);if(!i.val()&&!/(38|40)/.test(w.keyCode.toString(10))){if(h.filter(".active").length===0){h=m.$newElement.find("li a");if(m.options.liveSearchNormalize){h=h.filter(":a"+m._searchStyle()+"("+f(q[w.keyCode])+")")}else{h=h.filter(":"+m._searchStyle()+"("+q[w.keyCode]+")")}}}}if(!h.length){return}if(/(38|40)/.test(w.keyCode.toString(10))){j=h.index(h.filter(":focus"));l=h.parent(":not(.disabled):visible").first().index();p=h.parent(":not(.disabled):visible").last().index();t=h.eq(j).parent().nextAll(":not(.disabled):visible").eq(0).index();s=h.eq(j).parent().prevAll(":not(.disabled):visible").eq(0).index();g=h.eq(t).parent().prevAll(":not(.disabled):visible").eq(0).index();if(m.options.liveSearch){h.each(function(y){if(d(this).is(":not(.disabled)")){d(this).data("index",y)}});j=h.index(h.filter(".active"));l=h.filter(":not(.disabled):visible").first().data("index");p=h.filter(":not(.disabled):visible").last().data("index");t=h.eq(j).nextAll(":not(.disabled):visible").eq(0).data("index");s=h.eq(j).prevAll(":not(.disabled):visible").eq(0).data("index");g=h.eq(t).prevAll(":not(.disabled):visible").eq(0).data("index")}n=i.data("prevIndex");if(w.keyCode==38){if(m.options.liveSearch){j-=1}if(j!=g&&j>s){j=s}if(j<l){j=l}if(j==n){j=p}}if(w.keyCode==40){if(m.options.liveSearch){j+=1}if(j==-1){j=0}if(j!=g&&j<t){j=t}if(j>p){j=p}if(j==n){j=l}}i.data("prevIndex",j);if(!m.options.liveSearch){h.eq(j).focus()}else{w.preventDefault();if(!i.is(".dropdown-toggle")){h.removeClass("active");h.eq(j).addClass("active").find("a").focus();i.focus()}}}else{if(!i.is("input")){var o=[],k,x;h.each(function(){if(d(this).parent().is(":not(.disabled)")){if(d.trim(d(this).text().toLowerCase()).substring(0,1)==q[w.keyCode]){o.push(d(this).parent().index())}}});k=d(document).data("keycount");k++;d(document).data("keycount",k);x=d.trim(d(":focus").text().toLowerCase()).substring(0,1);if(x!=q[w.keyCode]){k=1;d(document).data("keycount",k)}else{if(k>=o.length){d(document).data("keycount",0);if(k>o.length){k=1}}}h.eq(o[k-1]).focus()}}if((/(13|32)/.test(w.keyCode.toString(10))||(/(^9$)/.test(w.keyCode.toString(10))&&m.options.selectOnTab))&&u){if(!/(32)/.test(w.keyCode.toString(10))){w.preventDefault()}if(!m.options.liveSearch){var v=d(":focus");v.click();v.focus();w.preventDefault()}else{if(!/(32)/.test(w.keyCode.toString(10))){m.$menu.find(".active a").click();i.focus()}}d(document).data("keycount",0)}if((/(^9$|27)/.test(w.keyCode.toString(10))&&u&&(m.multiple||m.options.liveSearch))||(/(27)/.test(w.keyCode.toString(10))&&!u)){m.$menu.parent().removeClass("open");m.$button.focus()}},mobile:function(){this.$element.addClass("mobile-device").appendTo(this.$newElement);if(this.options.container){this.$menu.hide()}},refresh:function(){this.$lis=null;this.reloadLi();this.render();this.setWidth();this.setStyle();this.checkDisabled();this.liHeight()},hide:function(){this.$newElement.hide()},show:function(){this.$newElement.show()},remove:function(){this.$newElement.remove();this.$element.remove()}};function c(j,k){var h=arguments;var m=j,g=k;[].shift.apply(h);var l;var i=this.each(function(){var r=d(this);if(r.is("select")){var q=r.data("selectpicker"),o=typeof m=="object"&&m;if(!q){var n=d.extend({},b.DEFAULTS,d.fn.selectpicker.defaults||{},r.data(),o);r.data("selectpicker",(q=new b(this,n,g)))}else{if(o){for(var p in o){if(o.hasOwnProperty(p)){q.options[p]=o[p]}}}}if(typeof m=="string"){if(q[m] instanceof Function){l=q[m].apply(q,h)}else{l=q.options[m]}}}});if(typeof l!=="undefined"){return l}else{return i}}var a=d.fn.selectpicker;d.fn.selectpicker=c;d.fn.selectpicker.Constructor=b;d.fn.selectpicker.noConflict=function(){d.fn.selectpicker=a;return this};d(document).data("keycount",0).on("keydown",".bootstrap-select [data-toggle=dropdown], .bootstrap-select [role=menu], .bs-searchbox input",b.prototype.keydown).on("focusin.modal",".bootstrap-select [data-toggle=dropdown], .bootstrap-select [role=menu], .bs-searchbox input",function(g){g.stopPropagation()});d(window).on("load.bs.select.data-api",function(){d(".selectpicker").each(function(){var g=d(this);c.call(g,g.data())})})})(jQuery);