<div align="center">
<?php
	$ds = disk_total_space("../files/") / 1024000;
	print "$ds";
	echo "<p align='center'><h5>Numbers Displayed in Bytes.</h5></p>";
	echo "<span align='center'><a href='javascript:window.close(self);'>Close Window</a></span>";
?>
</div>