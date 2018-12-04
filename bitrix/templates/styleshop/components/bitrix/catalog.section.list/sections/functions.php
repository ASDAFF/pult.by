<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!function_exists('cutText') && !function_exists('strip_words') && !function_exists('closetags'))
{
	function cutText($html, $size)
	{
		$symbols = strip_tags($html);
        $symbols_len = strlen($symbols);

        if($symbols_len < strlen($html))
        {
            $strip_text = strip_words($html, $size);

            if($symbols_len > $size)
                $strip_text = $strip_text."...";

            $final_text = closetags($strip_text);
        }
        elseif($symbols_len > $size)
          $final_text = substr($html, 0, mb_strripos(substr($html, 0, $size), ' ',0, SITE_CHARSET))."...";
        else
            $final_text = $html;

        return $final_text;
	}

	function strip_words($string, $count)
	{
		$splice_pos = null;

		$ar = preg_split("/(<.*?>|\\s+)/s", $string, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach($ar as $i => $s)
		{
			if(substr($s, 0, 1) != "<")
			{
				$count -= strlen($s);
				if($count <= 0)
				{
					$splice_pos = $i;
					break;
				}
			}
		}

		if(isset($splice_pos))
		{
			array_splice($ar, $splice_pos+1);
			return implode('', $ar);
		}
		else
		{
			return $string;
		}
	}

	function closetags($html)
	{
		preg_match_all("#<([a-z0-9]+)([^>]*)(?<!/)>#i".BX_UTF_PCRE_MODIFIER, $html, $result);
		$openedtags = $result[1];

		preg_match_all("#</([a-z0-9]+)>#i".BX_UTF_PCRE_MODIFIER, $html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);

		if(count($closedtags) == $len_opened)
			return $html;

		$openedtags = array_reverse($openedtags);

		for($i = 0; $i < $len_opened; $i++)
		{
			if (!in_array($openedtags[$i], $closedtags))
				$html .= '</'.$openedtags[$i].'>';
			else
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}

		return $html;
	}
}
?>