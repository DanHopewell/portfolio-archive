(function(k,F,g,G){g(function(){k.DCH=k.DCH||{};v=k.DCH.viewer=function(){var a={},l=["320","480","720","1080"],B,C,D,n,r=[],d={},m={},p={},s,t,u,z,w,E,x,A,y;s=function(b){var c=d[b],g=!!a.id,e=g&&c.group===d[a.id].group&&c.row===d[a.id].row;x(b);g?e?(u(c),E(b),w(b)):t(function(){u(c);z(c);w(b)}):(u(c),z(c),w(b));c.next&&x(c.next);c.previous&&x(c.previous)};t=function(b){g("#"+a.id).removeClass("active");a.content.slideUp(750,function(){a.viewer.hide();a.img.attr("src",null).attr("height",null);a.nextLink.removeData("id").find("a:first").attr("href",
null);a.previousLink.removeData("id").find("a:first").attr("href",null);a.viewer.detach();a.id=null;b&&b()})};u=function(b){var c=Math.round(.67*g(k).height()),f=a.nextLink,e=a.previousLink;a.img.attr("src",b.url).css("max-height",c);0<b.caption.length?(a.caption.text(b.caption),a.caption.show()):a.caption.hide();b.next?(f.data("id",b.next).find("a:first").attr("href",d[b.next].url),f.show()):f.hide().removeData("id").find("a:first").attr("href",null);b.previous?(e.data("id",b.previous).find("a:first").attr("href",
d[b.previous].url),e.show()):e.hide().removeData("id").find("a:first").attr("href",null)};z=function(b){b=p[b.group]["row"+b.row];b=b[b.length-1];a.viewer.insertAfter(g("#"+b));a.anchor.show();a.content.hide();a.viewer.show();A("#view");a.content.slideDown(750)};w=function(b){g("#"+b).addClass("active");y("view-"+b);a.id=b};E=function(b){g("#"+a.id).removeClass("active")};x=function(a){d[a].hasOwnProperty("preload")||(d[a].preload=new Image,d[a].preload.src=d[a].url)};A=function(a){g("html,body").animate({scrollTop:g(a).offset().top},
750)};y=k.DCH.utilities.changeHash;(function(){a.id=null;a.viewer=g('<div class="viewer"><div id="view" id="viewer-anchor" class="viewer-anchor"></div><div id="viewer-content" class="viewer-content"><figure><img id="viewer-image" src="" /><figcaption id="viewer-caption"></figcaption></figure><ul class="viewernav"><li id="viewer-close"><a class="close" href="">Close</a></li><li id="viewer-prev" class="viewer-image-nav"><a class="prev" href="">Previous image</a></li><li id="viewer-next" class="viewer-image-nav"><a class="next" href="">Next image</a></li></ul></div></div>');
a.viewer.hide();a.anchor=a.viewer.find("#viewer-anchor");a.content=a.viewer.find("#viewer-content");a.img=a.viewer.find("#viewer-image");a.caption=a.viewer.find("#viewer-caption");a.previousLink=a.viewer.find("#viewer-prev");a.nextLink=a.viewer.find("#viewer-next");a.navLinks=a.viewer.find(".viewer-image-nav");a.closeLink=a.viewer.find("#viewer-close");B=k.screen.width;C=k.screen.height;D=k.devicePixelRatio||1;var b=Math.max(B,C)*Math.min(D,1.5);if(b>=l[l.length-1])n=l[l.length-1];else if(b<=l[0])n=
l[0];else for(var c=l.length-1;0<c;c--)if(b>l[c-1]){n=l[c];break}g(".project").each(function(){var a=this.id;m[a]=[];p[a]={};g(this).find(".thumb").each(function(){var b,c,h,f,e;b=this.id;r.push(b);m[a].push(b);c=g(this).find("a:first").attr("href").split("/");f=c.pop();f=-1!=f.indexOf("_")?f.replace("_","_"+n+"max_"):f.replace(".","_"+n+"max.");e="";h=c.length;for(var k=0;k<h;k++)e+=c[k],e+="/";e+=f;c=g(this).find("img:first").attr("title");d[b]={group:a,id:b,pos:g(this).offset().top,url:e,caption:c}})});
b=r.length;for(c=0;c<b;c++){var f,e,h,q;f=r[c];e=d[f].group;h=m[e].indexOf(f);0<h&&(q=m[e][h-1],d[f].previous=q);h<m[e].length-1&&(h=m[e][h+1],d[f].next=h);d[f].hasOwnProperty("previous")?10>=Math.abs(d[f].pos-d[q].pos)?(h=d[q].row,d[f].row=h):(h=d[q].row+1,d[f].row=h,p[e]["row"+h]=[]):(h=1,d[f].row=h,p[e]["row"+h]=[]);p[e]["row"+h].push(f)}})();return{imageNav:a.navLinks,closeLink:a.closeLink,image:a.img,trigger:function(b){a.id&&b===a.id?t(function(){y(null)}):-1!=r.indexOf(b)&&s(b)},kill:function(){a.id&&
(A("#"+a.id),t(function(){y(null)}))},prev:function(){a.id&&d[a.id].previous&&s(d[a.id].previous)},next:function(){a.id&&d[a.id].next&&s(d[a.id].next)}}}();g(".thumb a").click(function(a){a.preventDefault();v.trigger(g(a.target).closest(".thumb").attr("id"))});v.imageNav.click(function(a){a.preventDefault();v.trigger(g(a.target).closest("li").data("id"))});v.closeLink.click(function(a){a.preventDefault();v.kill()});v.image.click(function(a){a.preventDefault();v.kill()});g(F).keyup(function(a){27==
a.keyCode&&v.kill();37==a.keyCode&&(a.preventDefault(),v.prev());39==a.keyCode&&(a.preventDefault(),v.next())});k.location.hash&&0===k.location.hash.indexOf("#view-")&&v.trigger(k.location.hash.substr(6))})})(window,document,jQuery);
