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
?>
<!-- Ty Gia -->
<?php if($ShowTyGia){ ?>
	<div id="vpTyGia">
		<?php if($ShowTyGiaTitle) {?><span class="vpModTitle">Tỷ giá</span> <?php } ?>
		<div class="vpTyGiaTitle" title="VietPublic - Tỷ giá / Giá vàng">
			<span class="vpItmLabel">Loại tiền </span>
			<span class="vpItmValue">VNĐ</span> 
		</div>		
		<div class="vpTyGiaValue" title="VietPublic - Tỷ giá / Giá vàng">
			<ul> 
				<li class="odd" title="Đô la Mỹ"><span class="vpItmLabel">USD</span><span class="vpItmValue"><?php echo $usd;?></span></li>
				<li class="even" title="Euro"><span class="vpItmLabel">EUR</span><span class="vpItmValue"><?php echo $eur;?></span></li>
				<li class="odd" title="Bảng Anh"><span class="vpItmLabel">GBP</span><span class="vpItmValue"><?php echo $gbp;?></span></li>
				<li class="even" title="Đô la Hồng Kông"><span class="vpItmLabel">HKD</span><span class="vpItmValue"><?php echo $hkd;?></span></li>
				<li class="odd" title="France Thụy Sỹ"><span class="vpItmLabel">CHF</span><span class="vpItmValue"><?php echo $chf;?></span></li>
				<li class="even" title="Yên Nhật Bản"><span class="vpItmLabel">JPY</span><span class="vpItmValue"><?php echo $jpy;?></span></li>
				<li class="odd" title="Đô la Australia"><span class="vpItmLabel">AUD</span><span class="vpItmValue"><?php echo $aud;?></span></li>
				<li class="even" title="Đô la Canada"><span class="vpItmLabel">CAD</span><span class="vpItmValue"><?php echo $cad;?></span></li>
				<li class="odd" title="Đô la Singapore"><span class="vpItmLabel">SGD</span><span class="vpItmValue"><?php echo $sgd;?></span></li>				
				<li class="even" title="Bạt Thái Lan"><span class="vpItmLabel">THB</span><span class="vpItmValue"><?php echo $thb;?></span></li>
				<li class="odd" title="Krone Nauy"><span class="vpItmLabel">NOK</span><span class="vpItmValue"><?php echo $nok;?></span></li>	
			</ul>
		</div>			
	</div>
<?php } ?>
<!-- Gia Vang -->
<?php if($ShowGiaVang){ ?>
<div class="vpGiaVang" title="VietPublic - Tỷ giá / Giá vàng">
	<?php if($ShowGiaVangTitle) {?><span class="vpModTitle">Giá vàng</span><?php } ?>	

	<div class="vpGiaVangValue">
		<ul>			
			<li class="vpSJC even" title="Vàng SJC 99,99 (1 chỉ, 2 chỉ, 5 chỉ)"><span class="vpItmLabel">SJC 99,99:</span><span class="vpItmValue"><?php echo $banle;?></span></li>
		</ul>
	</div>
	
</div>
<?php } ?>
<!-- Source -->
<?php if($ShowCopyright){ ?>
	<span class="vpRateSrcLabel" title="VietPublic Tỷ giá">Nguồn: </span>
	<span class="<?php if ($ShowNguon) { echo "vpVCBSrc";}else {echo "vpEXBSrc";} ?>">VietPublic</span> 
	</br>			
<?php } ?>			
<span style ="padding-left: 5px; font-size:10px;">Edit by Nguyễn Thị Phương Thy<span>				
