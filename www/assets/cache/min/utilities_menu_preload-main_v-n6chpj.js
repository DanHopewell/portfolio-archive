(function(a,d,b,h){b(function(){a.DCH=a.DCH||{};var c=a.DCH.utilities=function(){changeHash=function(b){a.history&&a.history.replaceState?a.history.replaceState(a.history.state,d.title,a.location.pathname+(b?"#"+b:"")):a.location.hash=b};return{changeHash:changeHash}}();b(d).on("click",'a[href^="#"]',function(a){a.preventDefault();"#pagehead"===this.hash?(b("html,body").animate({scrollTop:0},500),c.changeHash(null)):(b("html,body").animate({scrollTop:b(this.hash).offset().top},500),c.changeHash(this.hash.substring(1)),
b(this.hash).focus())})})})(window,document,jQuery);
(function(a,d,b,h){b(function(){var c=b(d.createElement("div")).hide().attr("id","menu").appendTo("#pagenav"),g=b("#pagenav ul").clone().hide().attr("id","menunav").appendTo(c),e=b(d.createElement("a")).attr("id","toggle").attr("href","").addClass("closed").appendTo(c),f=b("#pagehead h1");b(a).scroll(function(a){a=f.offset().top-parseInt(f.css("margin-top"),10)+f.outerHeight(!0);b(this).scrollTop()>=a?c.show():c.hide()});b(d).on("click","#toggle",function(a){a.preventDefault();e.hasClass("closed")?
(e.removeClass("closed").addClass("open"),g.show()):(e.removeClass("open").addClass("closed"),g.hide())})})})(window,document,jQuery);
(function(){var a=[];a[0]=new Image;a[0].src="http://img.danhopewell.com/i/graphics/dh_hexb0b3b3_v-n14jbq.svg";a[1]=new Image;a[1].src="http://img.danhopewell.com/i/graphics/dh_hex62666a_v-n14jbq.svg";a[2]=new Image;a[2].src="http://img.danhopewell.com/i/graphics/dan-hopewell_hexb0b3b3_v-n14jcf.svg";a[3]=new Image;a[3].src="http://img.danhopewell.com/i/graphics/dan-hopewell_hex62666a_v-n14jcf.svg";a[4]=new Image;a[4].src="http://img.danhopewell.com/i/graphics/x3_v-n14jb6.svg";a[5]=new Image;a[5].src=
"http://img.danhopewell.com/i/graphics/arrow-l_v-n14jcy.svg";a[6]=new Image;a[6].src="http://img.danhopewell.com/i/graphics/arrow-r_v-n14jcr.svg";a[7]=new Image;a[7].src="http://img.danhopewell.com/i/graphics/arrow-d_hexb0b3b3_v-n14jd4.svg";a[8]=new Image;a[8].src="http://img.danhopewell.com/i/graphics/arrow-u_hexb0b3b3_v-n14jcl.svg";a[9]=new Image;a[9].src="http://img.danhopewell.com/i/graphics/email_hexeff0f0_v-n5qzxi.svg";a[10]=new Image;a[10].src="http://img.danhopewell.com/i/graphics/phone_hexeff0f0_v-n5qzxh.svg";
a[11]=new Image;a[11].src="http://img.danhopewell.com/i/graphics/twitter_hexeff0f0_v-n5qzxf.svg";a[12]=new Image;a[12].src="http://img.danhopewell.com/i/graphics/email_hexfff_v-n5qzxi.svg";a[13]=new Image;a[13].src="http://img.danhopewell.com/i/graphics/phone_hexfff_v-n5qzxh.svg";a[14]=new Image;a[14].src="http://img.danhopewell.com/i/graphics/twitter_hexfff_v-n5qzxf.svg";a[15]=new Image;a[15].src="http://img.danhopewell.com/i/graphics/email_hexb0b3b3_v-n5qzxi.svg";a[16]=new Image;a[16].src="http://img.danhopewell.com/i/graphics/phone_hexb0b3b3_v-n5qzxh.svg";
a[17]=new Image;a[17].src="http://img.danhopewell.com/i/graphics/twitter_hexb0b3b3_v-n5qzxf.svg";a[18]=new Image;a[18].src="http://img.danhopewell.com/i/graphics/email_hex62666a_v-n5qzxi.svg";a[19]=new Image;a[19].src="http://img.danhopewell.com/i/graphics/phone_hex62666a_v-n5qzxh.svg";a[20]=new Image;a[20].src="http://img.danhopewell.com/i/graphics/twitter_hex62666a_v-n5qzxf.svg";a[21]=new Image;a[21].src="http://img.danhopewell.com/i/graphics/arrow-u_hexeff0f0_v-n14jcl.svg";a[22]=new Image;a[22].src=
"http://img.danhopewell.com/i/graphics/arrow-d_hexeff0f0_v-n14jd4.svg"})();