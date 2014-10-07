<?php

function MarkdownUrl($text) {

#
# Initialize the parser and return the result of its transform method.
#
	# Setup static parser variable.
	static $parser;
	if (!isset($parser)) {
		$parser = new MarkdownUrl_Parser;
	}

	# Transform text using parser.
	return $parser->transform($text);
}

class MarkdownUrl_Parser {

	var $nested_brackets_depth = 6;
	var $nested_url_parenthesis_depth = 4;
	var $no_entities = false;
	
	function MarkdownUrl_Parser() {
	#
	# Constructor function. Initialize appropriate member variables.
	#
	
		$this->nested_brackets_re = 
			str_repeat('(?>[^\[\]]+|\[', $this->nested_brackets_depth).
			str_repeat('\])*', $this->nested_brackets_depth);
	
		$this->nested_url_parenthesis_re = 
			str_repeat('(?>[^()\s]+|\(', $this->nested_url_parenthesis_depth).
			str_repeat('(?>\)))*', $this->nested_url_parenthesis_depth);
		
		$this->escape_chars_re = '['.preg_quote($this->escape_chars).']';
	}
	
	function _doAnchors_inline_callback($matches) {
		$whole_match	=  $matches[1];
		$link_text		=  $matches[2];
		$url			=  $matches[3] == '' ? $matches[4] : $matches[3];
		$title			=& $matches[7];
	
		$url = $this->encodeAttribute($url);
	
		$result = "<a href=\"$url\"";
		if (isset($title)) {
			$title = $this->encodeAttribute($title);
			$result .=  " title=\"$title\"";
		}
		
		$result .= ">$link_text</a>";
	
		return $result;
	}

	function encodeAttribute($text) {
	#
	# Encode text for a double-quoted HTML attribute. This function
	# is *not* suitable for attributes enclosed in single quotes.
	#
		$text = $this->encodeAmpsAndAngles($text);
		$text = str_replace('"', '&quot;', $text);
		return $text;
	}

	function encodeAmpsAndAngles($text) {
			
	#
	# Smart processing for ampersands and angle brackets that need to 
	# be encoded. Valid character entities are left alone unless the
	# no-entities mode is set.
	#
		if ($this->no_entities) {
			$text = str_replace('&', '&amp;', $text);
		} else {
			# Ampersand-encoding based entirely on Nat Irons's Amputator
			# MT plugin: <http://bumppo.net/projects/amputator/>
			$text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/', 
								'&amp;', $text);;
		}
		# Encode remaining <'s
		$text = str_replace('<', '&lt;', $text);
	
		return $text;
	}

	function transform($text) {
				
		$this->nested_brackets_re = 
				str_repeat('(?>[^\[\]]+|\[', $this->nested_brackets_depth).
				str_repeat('\])*', $this->nested_brackets_depth);
	
		$this->nested_url_parenthesis_re = 
				str_repeat('(?>[^()\s]+|\(', $this->nested_url_parenthesis_depth).
				str_repeat('(?>\)))*', $this->nested_url_parenthesis_depth);
	
		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  \[
				('.$this->nested_brackets_re.')	# link text = $2
			  \]
			  \(			# literal paren
				[ \n]*
				(?:
					<(.+?)>	# href = $3
				|
					('.$this->nested_url_parenthesis_re.')	# href = $4
				)
				[ \n]*
				(			# $5
				  ([\'"])	# quote char = $6
				  (.*?)		# Title = $7
				  \6		# matching quote
				  [ \n]*	# ignore any spaces/tabs between closing quote and )
				)?			# title is optional
			  \)
			)
			}xs',
			array(&$this, '_doAnchors_inline_callback'), $text);
	
		return $text;
	}
	
}


?>