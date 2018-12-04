<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn  = '<div class="col-md-12">';
$strReturn .= '<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList"><!-- breadcrumbs -->';
$brindex    = 0;
$last_index = count($arResult) - 1;
for($index = 0, $itemSize = count($arResult); $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if($arResult[$index]["LINK"] <> "")
	{
        if($last_index==$index)
		{
		  if($last_index == 0){
             $strReturn .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a';
             $strReturn .= ' href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="item"><span itemprop="name">'.$title.'</span></a><meta itemprop="position" content="'.($index+1).'" /></li>';
		  } else{
             $strReturn .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a';
             $strReturn .= ' href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="item"><span itemprop="name">'.$title.'</span></a><meta itemprop="position" content="'.($index+1).'" /></li>';
		  }
		}else{
            $strReturn .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a';
            $strReturn .= ' href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="item"><span itemprop="name">'.$title.'</span></a><meta itemprop="position" content="'.($index+1).'" /></li>';
		}
	} else {
		$strReturn .= '<li class="active" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.($index+1).'" /></li>';
	}
}
$strReturn .= '</ol><!-- /breadcrumbs --></div>';
return $strReturn;