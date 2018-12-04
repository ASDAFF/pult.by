<?php

namespace Unisoftmedia\Styleshop\Library;

use Unisoftmedia\Styleshop\Library\SimpleHtmlDom;

/**
 * Website: http://sourceforge.net/projects/simplehtmldom/
 * Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
 * Contributions by:
 *     Yousuke Kumakura (Attribute filters)
 *     Vadim Voituk (Negative indexes supports of "find" method)
 *     Antcs (Constructor with automatically load contents either text or file/url)
 *
 * all affected sections have comments starting with "PaperG"
 *
 * Paperg - Added case insensitive testing of the value of the selector.
 * Paperg - Added tag_start for the starting index of tags - NOTE: This works but not accurately.
 *  This tag_start gets counted AFTER \r\n have been crushed out, and after the remove_noice calls so it will not reflect the REAL position of the tag in the source,
 *  it will almost always be smaller by some amount.
 *  We use this to determine how far into the file the tag in question is.  This "percentage will never be accurate as the $dom->size is the "real" number of bytes the dom was created from.
 *  but for most purposes, it's a really good estimation.
 * Paperg - Added the forceTagsClosed to the dom constructor.  Forcing tags closed is great for malformed html, but it CAN lead to parsing errors.
 * Allow the user to tell us how much they trust the html.
 * Paperg add the text and plaintext to the selectors for the find syntax.  plaintext implies text in the innertext of a node.  text implies that the tag is a text node.
 * This allows for us to find tags based on the text they contain.
 * Create find_ancestor_tag to see if a tag is - at any level - inside of another specific tag.
 * Paperg: added parse_charset so that we know about the character set of the source document.
 *  NOTE:  If the user's system has a routine called get_last_retrieve_url_contents_content_type availalbe, we will assume it's returning the content-type header from the
 *  last transfer or curl_exec, and we will parse that and use it in preference to any other method of charset detection.
 *
 * Found infinite loop in the case of broken html in restore_noise.  Rewrote to protect from that.
 * PaperG (John Schlick) Added get_display_size for "IMG" tags.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author S.C. Chen <me578022@gmail.com>
 * @author John Schlick
 * @author Rus Carroll
 * @version 1.5 ($Rev: 196 $)
 * @package PlaceLocalInclude
 * @subpackage simple_html_dom
 */

/**
 * All of the Defines for the classes below.
 * @author S.C. Chen <me578022@gmail.com>
 */
// helper functions
// -----------------------------------------------------------------------------
// get html dom from file
// $maxlen is defined in the code as PHP_STREAM_COPY_ALL which is defined as -1.
final class SimpleHtml extends SimpleConst {

    private $dom;

    public function __construct($str, $lowercase = true, $forceTagsClosed = true, $target_charset = parent::DEFAULT_TARGET_CHARSET, $stripRN = true, $defaultBRText = parent::DEFAULT_BR_TEXT, $defaultSpanText = parent::DEFAULT_SPAN_TEXT) {
        
        $this->dom = new SimpleHtmlDom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
        if (empty($str))
        {
            $this->dom->clear();
            return false;
        }
        $this->dom->load($str, $lowercase, $stripRN);
    }
    
    public function dom()
    {
        return $this->dom;
    }

    // dump html dom tree
    public function dump_html_tree($node, $show_attr = true, $deep=0)
    {
        $node->dump($node);
    }
}