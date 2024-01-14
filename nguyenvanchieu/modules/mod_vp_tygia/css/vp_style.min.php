<?php
/*
 * ------------------------------------------------------------------------
 * VietPublic Ty Gia & Gia Vang module for Joomla 2.5, 3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2013 - 2015 VietPublic. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: VietPublic
 * Websites: http://www.vietpublic.net
 * ------------------------------------------------------------------------
*/
defined('_JEXEC') or die('Restricted access'); 
$vpImgPath = JURI::root().'modules/mod_vp_tygia/images/';
?>
<style type="text/css">
#vpTyGia ul li {
    line-height: 20px;
    list-style: outside none none;
}
div.vpGiaVang ul li {
    line-height: 20px;
    list-style: outside none none;
}
.vpTyGiaValue > ul {
    margin: 0;
	padding: 0;
}
div.vpTyGiaValue ul li.odd, div.vpGiaVangValue ul li.odd {
    background: url("<?php echo $vpImgPath;?>v3.gif") no-repeat scroll 5px 6px rgba(0, 0, 0, 0);
    padding-left: 15px;
}
div.vpTyGiaValue ul li.even, div.vpGiaVangValue ul li.even {
    background: url("<?php echo $vpImgPath;?>v4.gif") no-repeat scroll 5px 6px rgba(0, 0, 0, 0);
    padding-left: 15px;
}
div.vpTyGiaValue ul li.odd:hover, div.vpGiaVangValue ul li.odd:hover {
    background: url("<?php echo $vpImgPath;?>v3.gif") no-repeat scroll 5px 5px green;
    color: #fff;
    font-weight: bold;
}
div.vpTyGiaValue ul li.even:hover, div.vpGiaVangValue ul li.even:hover {
    background: url("<?php echo $vpImgPath;?>v4.gif") no-repeat scroll 5px 5px #aa0808;
    color: #fff;
    font-weight: bold;
}
div.vpTyGiaTitle .vpItmLabel {
    padding-left: 5px;
}
.vpItmValue {
    float: right;
    padding-right: 5px;
}
.vpTyGiaTitle {
    display: inline;
    font-weight: bold;
    text-transform: uppercase;
}
.vpEXBSrc {
    background-color: rgba(0, 0, 0, 0);
    background-image: url("<?php echo $vpImgPath;?>vietpublic1.png");
    background-position: 0 2px;
    background-repeat: no-repeat;
    background-size: 100% auto;
    color: rgba(0, 0, 0, 0);
}
.vpVCBSrc {
    background-color: rgba(0, 0, 0, 0);
    background-image: url("<?php echo $vpImgPath;?>vietpublic2.png");
    background-position: 0 2px;
    background-repeat: no-repeat;
    background-size: 100% auto;
    color: rgba(0, 0, 0, 0);
}
.vpRateSrcLabel {
    font-size: 10px;
    padding-left: 5px;
}
.vpGiaVang {
    display: table;
    width: 100%;
}
.vpModTitle {
    color: #aa0808;
    display: block;
    font-size: 16px;
    font-weight: bold;
	margin: 5px 0;
    text-align: center;
    text-transform: uppercase;
}
.vpGiaVangValue > ul {
    margin: 0;
    padding: 0;
}
</style>