<?php
$menu = "";
$sous_menu = "";
require_once("inc_header.php");
?>
			<!-- start: PAGE -->
			<div class="main-content">
				<div class="container">
					<!-- start: PAGE HEADER -->
					<div class="row">
						<div class="col-sm-12">
							<div class="page-header">
								<h1>TITRE</h1>
							</div>
							<!-- end: PAGE TITLE & BREADCRUMB -->
						</div>
					</div>
					<!-- end: PAGE HEADER -->
					<!-- start: PAGE CONTENT -->
                    <div  class="row">
                    	<?php
						$cache_expire = session_cache_expire();
						echo $cache_expire;
                    	?>
                    </div>
					<!-- end: PAGE CONTENT-->
				</div>
			</div>
			<!-- end: PAGE -->

<?php
require_once("inc_footer.php");
?>

