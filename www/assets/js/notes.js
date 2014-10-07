$(function() {

	var pageceil = 0;
	var articles = $('#content').find('article');
	
	// for each article
	var alen = articles.length;
	for (var i = 0; i < alen; i++) {

		var id = articles.eq(i).attr('id');
		var notes = articles.eq(i).find('small');
		var nlen = notes.length;
		if (nlen === 0) continue;
		
		// remove space before inline notes
		var ot = articles.eq(i).html();
		var nt = ot.replace(/\s+(<small)/g, "$1");
		if (ot !== nt) {
			articles.eq(i).html(nt);
			notes = articles.eq(i).find('small');
		}
		
		var notespos = []; // array of objects storing note position data
		var notesheight = 0; // total combined height of all notes
		
		// for each note in current article
		for (var j = 0; j < nlen; j++) {
			
			// add id and class hooks to note element
			var noteid = id + "_note" + (j+1);
			var noteref = noteid + '_ref';
			notes.eq(j)
				.attr('id', noteid)
				.addClass('note')
				// span wrapper around note and body ref
				.wrap('<span class="notegroup" />')
				// linked superscript note number as body ref
				.before('<sup id="' + noteref + '" class="noteref"><a href="#' + noteid + '">' + (j+1) + '<\/a><\/sup>');
			
			// surround note with hidden (but copy-paste-able) brackets
			// prepend note with note number linked to body ref
			// wrap note text in span
			
			var before = '<span class="hide-text"> [note <\/span><a class="notenum" href="#' + noteref + '">' + (j+1) + '.<\/a> <span class="notetext">';
			
			var after = '</span><span class="hide-text">]</span>';
						
			// remove brackets surrounding note text
			var s = notes.eq(j).html();
			while(s.charAt(0) === '[')
				s = s.substring(1);
			while(s.charAt(s.length-1) === ']')
				s = s.substring(0, s.length-1);
			
			// wrap with before and after strings
			notes.eq(j).html(before + s + after);
			
			// create position data object
			var shift = parseInt(notes.eq(j).parent().css('line-height')); // ref line-height
			var t = notes.eq(j).position().top - shift; // top of note aligned to top of ref
			var h = notes.eq(j).outerHeight(true);
			var m = parseInt(notes.eq(j).css('margin-bottom'));
			var b = t + h;
			notespos[j] = {
				t: t, // ideal top of note
				b: b, // ideal bottom of note
				h: h, // overall height of note
				m: m, // bottom margin of note
				s: shift // ref line-height
			}
			notesheight += h;
		}
		
		/*
		Note position algorithm...
		Ideally, the top of each note aligns with top of its reference
		If notes overlap, higher notes push lower notes downward as needed
		The space for notes is defined by a 'ceiling' and 'floor'
		No part of any note appears above ceiling
		*If* there is enough space, no part of any note falls below floor
		Given adequate space, the bottom of last overflowing note is placed at floor
		Notes above this are pushed up as needed
		If there *isn't* enough space, notes start flush to ceiling and overflow floor
		Empty div is inserted into body to 'clear' notes overflow
		*/
		
		var header = articles.eq(i).find('header');
		var ceil = header.position().top + header.outerHeight(true);
		ceil = (pageceil > ceil) ? pageceil : ceil;
		
		var lastc = articles.eq(i).children().last();
		var floor = lastc.position().top + lastc.outerHeight(true);				
		
		// calculate note positions, store as 'p' on respective notespos objects
		
		var nplen = notespos.length;
		
		// if notes fit entirely within note space height
		if (notesheight < floor-ceil) {
		
			// working from top to bottom
			// set top position of each note, preventing overlap with previous note
			for (j = 0; j < nplen; j++) {
				if (j > 0) {
					var prev = notespos[j-1].p + notespos[j-1].h;
					notespos[j].p = (prev > notespos[j].t) ? prev : notespos[j].t;
				} else {
					notespos[j].p = (ceil > notespos[j].t) ? ceil : notespos[j].t;
				}
			}
			
			// if bottom of last note is below bottom of note space
			if (notespos[nplen-1].p + notespos[nplen-1].h > floor) {
				// working from bottom to top
				// set last note on floor and move up overlapping notes above it
				// break if at top or no overlap above
				for (j = nplen-1; j >= 0; j--) {
					if (j === nplen-1) {
						// set last note on floor
						notespos[j].p = floor - notespos[j].h - notespos[j].s + notespos[j].m;
						if (j === 0 || notespos[j].p > notespos[j-1].p + notespos[j-1].h) break;
					} else {
						// move up overlapping notes
						notespos[j].p = notespos[j+1].p - notespos[j].h;
						if (j === 0 || notespos[j].p > notespos[j-1].p + notespos[j-1].h) break;
					}
				}
			}
		
		// if notes overflow note space height
		} else {
			// starting at ceiling, stack notes continuously
			for (j = 0; j < nplen; j++) {
				if (j > 0) {
					notespos[j].p = notespos[j-1].p+notespos[j-1].h;
				} else {
					notespos[j].p = (ceil > 0) ? ceil : 0;
				}
			}
			// create invisible div in body the height of the overflow to 'clear'
			var overflow = notespos[nplen-1].p + notespos[nplen-1].h - floor;
			var fauxclear = '<div style="visibility:hidden;height:' + overflow + 'px;margin:0;border:0;padding:0">&nbsp;<\/div>';
			articles.eq(i).append(fauxclear);
		}
		
		// apply calculated note positions to respective DOM elements
		for (j = 0; j < nplen; j++) {
			notes.eq(j).css('top', notespos[j].p);
		}
	
	}
});