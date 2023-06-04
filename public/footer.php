<footer id="footer">
    <div class="main-footer">

      <div class="container">
        <div class="row">

            <?php get_sidebar('footer-widget-1'); ?>

            <?php get_sidebar('footer-widget-2'); ?>

            <?php get_sidebar('footer-widget-3'); ?>

            <?php get_sidebar('footer-widget-4'); ?>

          
        </div> <!-- end .row -->
      </div> <!-- end .container -->
    </div> <!-- end .main-footer -->

    <div class="copyright">
      <div class="container">

        <?php global $globo_option_data;

          $allowed_html = array(
            'a' => array(
              'href' => array(),
              'title' => array(),
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
          );
            

          echo "<p>" . wp_kses( $globo_option_data['uou-footer-text'] , $allowed_html ) . "</p>";

        

            $defaults = array(
              'theme_location'  => 'footer-menu',
              'menu'            => 'footer-menu',
              'container'       => 'false',
              'menu_class'      => 'list-inline',              
              'depth'           => 0              
            );

            if ( has_nav_menu('footer-menu') ) {
              wp_nav_menu( $defaults );
            }

          ?>

      </div> <!-- END .container -->
    </div> <!-- end .copyright-->
  </footer> <!-- end #footer -->

<?php
	if(is_front_page()==true){
		?>
		<style>
		.bwg_container{
			display:none !important;
		} 
		div[id^=bwg_container]{
			display:none !important;
		}
		.job_listings{
			float:left;
		}
		.job_listings form{
			display:none;
		}
		.home-clas .classified-maker.ads-archive .ads{
			width:100%;
		}
		.classified-maker.ads-archive{
			float:left;
		}
		</style>
		<?php
	}
?>

<style>

  .btn-default{
    background-color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .blog-list .date{
    background-color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  
  .header-search .header-search-bar .search-btn{
    background-color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  #header .header-top-bar .header-logo a .fa{
    color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  #header .header-nav-bar{
    border-color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
	min-height:60px;
	border-radius:10px 10px 0px 0px;
  }

  .post-sidebar h2{
    border-left :3px solid <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .slider-content .customNavigation .btn{
    background-color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .accordion ul li.active{
    border-left:3px solid <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?>;
  }

  .featured-listing .single-product figure .rating p{
    background: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  <?php  

    

   $color = globo_hex2rgb($globo_option_data['uou-primary-color']['background-color']);

    ?>

  .register-content .registration-details{
    background-color: rgba( <?php  echo $color['red']; ?>, <?php  echo $color['green']; ?>, <?php  echo $color['blue']; ?>, 0.75);
  }

  .register-content .registration-details .alternate h2{
    color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .btn-default-inverse:hover, .btn-default-inverse:focus, .btn-default-inverse.focus, .btn-default-inverse:active, .btn-default-inverse.active, .open > .dropdown-toggle.btn-default-inverse{
    color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  a:hover, a:focus{
    color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .accordion ul li:hover{
    background-color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .accordion ul li a:hover{
    /*background-color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;*/
    color: #555 !important;
  }

  .company-sidebar .company-category li.active, .company-sidebar .company-category li:hover{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .company-sidebar .company-category li a{
    color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .company-sidebar .company-category li a:hover, .company-sidebar .company-category li.active a{
    color: #333 !important;
  }

  .company-contact .address-details .fa{
    background: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .company-product .single-product figure figcaption, .company-portfolio .single-product figure figcaption{
    background-color: rgba( <?php  echo $color['red']; ?>, <?php  echo $color['green']; ?>, <?php  echo $color['blue']; ?>, 0.8);
  }

  .featured-listing .single-product figure figcaption{
    background-color: rgba( <?php  echo $color['red']; ?>, <?php  echo $color['green']; ?>, <?php  echo $color['blue']; ?>, 0.8);
  }

  .company-events .date{
      background: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?>;
  }

  .date-month a{
    color: #333 !important;
  }

  .classifieds-content .classifieds-category a:hover span{
    color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?>;
  }

  .btn-default-inverse{
    color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?>;
  }

  .single-product .rating{
	  display:none;
  }



  .company-sidebar .company-category li.active:after{
      border-left: 7px solid <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .company-sidebar h2{
    border-left: 3px solid <?php echo $globo_option_data['uou-primary-color']['background-color']; ?> !important;
  }

  .company-profile .social-link ul li{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  #footer a{
    color: <?php  echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  #footer .main-footer .newsletter ul li{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  #footer .main-footer .newsletter button{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  #footer .main-footer .newsletter ul li a{
      color: #333333 !important;
  }
  
  ul#menu-custom-1{
	list-style-position:outside !important;
	padding:0px !important;
	margin-bottom:0px !important;
	list-style:none;
	}

	ul#menu-custom-1 li{
		display:block;
		padding:10px 0px 10px 10px;
		border-bottom:1px solid #474747;
	}
	
	.textwidget ul{
	list-style-position:outside !important;
	padding:0px !important;
	margin-bottom:0px !important;
	list-style:none;
	}

	.textwidget ul li{
		display:block;
		padding:10px 0px 10px 10px;
		border-bottom:1px solid #474747;
	}

  .btn-default:hover, .btn-default:focus{
    color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .rating li.filled{
    color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .company-heading-view .button-content button.active, .company-heading-view .button-content button:hover{
    background: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .about-us .member-details .porfile-pic ul li{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .blog-post .share-this ul li{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .shortcodes .nav-tabs > li.active > a{
    background: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
    border: 1px solid <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .shortcodes .nav-tabs{
    border-bottom: 3px solid <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> ;
  }

  .shortcodes .vertical.tab-content{
    border-left: 3px solid <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> ;
  }

  .category-item:hover{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .category-item a:hover{
    color: #333 !important;
  }

  #header .header-nav-bar .primary-nav > li.bg-color{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .contact-us .address-details .fa{
    background: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  #header .header-top-bar .header-social > a:hover, #header .header-top-bar .header-language > a:hover{
    color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }










  /*woocommerce color*/
  .woocommerce span.onsale{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .woocommerce .widget_price_filter .price_slider_amount .button {
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .shop-page-pagination ul.pagination li span.current {
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
   .woocommerce .shop-sidebar .newsletter button.btn-news {
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .woocommerce .shop-sidebar .newsletter ul li {
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .coupon p input.button {
    background: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .woocommerce-cart .woocommerce .cart_item .product-remove a.remove {
    color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .woocommerce input.button {
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .woocommerce-cart .wc-proceed-to-checkout a.checkout-button{
    background: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .woocommerce-cart .woocommerce-message a, 
  .woocommerce-checkout .woocommerce-info a {
    color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .woocommerce input.button.alt {
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .quantity.buttons_added .minus, .quantity.buttons_added .plus{
      background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .product-tab .nav-tabs > li.active > a, .product-tab .nav-tabs > li.focus > a{
      background: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
      border: 1px solid <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .product-tab .nav-tabs{
    border-bottom: 3px solid <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .product-tab .nav-tabs > li a:hover{
      background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .woocommerce #review_form #respond .form-submit input.submit{
      background: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  .woocommerce .single-product figure figcaption, .shop-content .single-product figure figcaption{
      background-color: rgba( <?php  echo $color['red']; ?>, <?php  echo $color['green']; ?>, <?php  echo $color['blue']; ?>, 0.8);
  }

  .woocommerce-message a.wc-forward{
      background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }
  
  .shop-page-pagination ul.pagination li a:hover{
    background-color: <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }

  .shop-body .shop-sidebar h2{
    border-left: 3px solid <?php echo $globo_option_data['uou-primary-color']['background-color'];  ?> !important;
  }



  .hitcounter{
		position:relative;
		width:100%;
		text-align:center;
		top:225px;
	}
	

	

.header-top-bar	{
	margin: 0 0 10px; /* just to give some spacing */
}
.header-top-bar ul	{
	margin: 0; padding: 0; /* only needed if you have not done a CSS reset */
}
.header-top-bar #menu-top-custom li	{
	display: block;
	float: left;
	line-height: 30px; /* this should be the same as your #main-nav height */
	height: 30px; /* this should be the same as your #main-nav height */
	margin: 0; padding: 0; /* only needed if you don't have a reset */
	position: relative; /* this is needed in order to position sub menus */
	width:fit-content;
}
.header-top-bar #menu-top-custom li a	{
	display: block;
	height: 30px;
	line-height: 30px;
	padding: 0 15px;
}
.header-top-bar .current-menu-item a, .header-top-bar .current_page_item a, .header-top-bar #menu-top-custom a:hover {
	color: #FFF;
	background: #d64402;
}	
.header-top-bar ul li ul { /* this targets all sub menus */
	display: none; /* hide all sub menus from view */
	position: absolute;
	top: 30px !important; /* this should be the same height as the top level menu -- height + padding + borders */
}
.header-top-bar ul ul li { /* this targets all submenu items */
	float: none; /* overwriting our float up above */
	width: 200px; /* set to the width you want your sub menus to be. This needs to match the value we set below */
	background: #ff5507;
}
.header-top-bar ul ul li a { /* target all sub menu item links */
	padding: 5px 10px; /* give our sub menu links a nice button feel */
}

.header-top-bar ul li:hover > ul.sub-menu {
	display: block; /* show sub menus when hovering over a parent */
	position:absolute;
	z-index:99999;
	top:10px;
}

.header-top-bar ul ul li ul {
	/* target all second, third, and deeper level sub menus */
	left: 200px; /* this needs to match the sub menu width set above -- width + padding + borders */
	top: 0; /* this ensures the sub menu starts in line with its parent item */
}

.ere-heading{
	display:none;
}

.modal{
	display:none;
}

#menu-custom li:hover > ul.e-sub-menu{
	display:block;
	margin-top:30px;
	border-left:1px solid #e5e5e5;
	border-right:1px solid #e5e5e5;
	border-bottom:1px solid #e5e5e5;
	padding:0px !important;
}

#menu-custom li ul.e-sub-menu li{
	display:block !important;
}
#menu-custom li ul.e-sub-menu li a:hover{
	background:#ff5507 !important;
	color:#FFF !important;
}
#menu-custom li ul.e-sub-menu li a{
	padding:10px 20px !important;
	background:#FFF !important;
	color:#333 !important;
}

#header .header-nav-bar .primary-nav > li.has-submenu:after{
	top:18px !important;
	right:6px !important;
}
#header .header-nav-bar .primary-nav > li > a{
	line-height:62px !important;
}

#job-manager-job-dashboard p{display:none;}

#job-manager-job-dashboard table ul.job-dashboard-actions{
	visibility:visible !important;
	margin-top:10px;
}

#job-manager-job-dashboard table ul.job-dashboard-actions li a,.my-ads .single .edit-link,.my-ads .single .delete-ads{
	background:#f7f7f7;
	color:#333;
	font-size:12px;
	margin:0px 3px 3px 0px;
	padding:5px 5px;
	border:1px solid #ddd;
}

.my-ads .single .edit-link a{
	color:#333;
}
.my-ads .meta{
	line-height:24px;
}
.ere-agency .agency-avatar{
	width:132px !important;
	min-height:132px !important;
}

.agency-logo-inner{
	padding:0px !important;
}
.agency-logo-inner img{
	width:100%;
}

.agency-single-info .agent-contact-btn{
	background-color:transparent !important;
	border-color:transparent !important;
}

.above-archive-agent{
	display:none !important;
}

.property-directions{
	display:none;
}

.wpbdp-listing .listing-title{
	border-bottom:1px solid #000 !important;
}

.wpbdp-listing .listing-title a, .wpbdp-listing .listing-title h2{
	color:#000 !important;
}

.wpbdp-listings-list{
	padding-top:10px;
}

.wpbdp-listing-excerpt.odd{
	background:#efefef;
}

.listing-title a,.listing-thumbnail a{
}
.listing-title a:hover{
		color:#ff5507 !important;
}

.wpbdp-button{
	display:none;
}

.property-status2{
	position:absolute;
	left:0;
	top:15px;
	font-size:0;
	z-index:3;
}

.property-status2 p{
	margin-bottom:5px;
	color:#FFF;
	font-weight:400;
}

.property-status2 p .property-status-bg2{
	display:inline-block;
	background-color:#000;
	padding-left:5px;
	padding-right:10px;
	position:relative;
	line-height:19px;
	font-size:12px;
}

.property-status2 p .property-status-bg2 .property-arrow2{
	position:absolute;
	content:'';
	display:block;
	top:0;
	left:100%;
	border-top: 10px solid transparent;
    border-bottom: 9px solid transparent;
    border-left: 7px solid;
    border-left-color: #000000;
    border-right-color: #000000;
}
.listing-thumbnail img{
	width:150px !important;
}
.wpbdp-listing-single .extra-images ul{
	padding:0px;
}

.comments-section{
	display:none;
}

.single .listing-details{
	margin-left:0px !important;
}

.wpbdp-listing-single .extra-images ul li img{
	max-width:auto !important;
	min-height:150px;
	margin-top:10px;
	width:auto;
}

.single-ads .images img{
	width:100%;
}

#slb_viewer_slb_default{
	overflow:unset !important;
}

<?php if(get_post_type( get_the_id() ) == "post") { ?>
.blog-list .date-month,.blog-list p.user{
	display:none;
}
<?php } ?>

.main-footer .widget ul, .main-footer ul li{
	padding:5px 0px !important;
}

.home-news .entry-summary a:hover,.home-news .entry-summary a:focus{
	text-decoration:none !important;
}

.single-ads-main{
	margin:0px !important;
	max-width:100% !important;
}

.email-popup .container{
	width:100% !important;
}

.rpwe-block h3{
	margin-left:43px;
}


.show{
	background-size:cover !important;
}
.single-ads .ads-sidebar .see-phone-number .phone-number a{
	display:inline-block !important;
	width:85%;
}

.rc-anchor-compact{
	box-shadow:none !important;
	-webkit-box-shadow:none !important;
}

.property-feature .checkbox input[type=checkbox]{
	margin-left:20px !important;
}
</style>
<?php

$url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
if(strpos($url,'job') !== false) {
?>
<style>
.blog-list .post-without-image h2.title, .blog-list .post-without-image .user{
	padding-left:0px !important;
}
.single_job_listing .company{
	border:none !important;
	box-shadow:none !important;
}
.blog-list .date,.blog-list .month{
	display:none !important;
}
.single_job_listing .application .application_button{
	padding:5px !important;
}
</style>
<?php
}
?>

<?php

$url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
if(strpos($url,'news') !== false) {
?>
<style>
.home-news{
	max-width:100% !important;
}
</style>
<?php
}
?>

<?php

$url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
if(strpos($url,'events') !== false || strpos($url,'event') !== false) {
?>
<style>
.blog-list-heading{
	background:url(https://www.delmasmall.co.za/wp-content/uploads/2017/11/events.jpg) top left no-repeat !important;
}

h3.tribe-events-month-event-title{
	margin:0px !important;
}

h3.tribe-events-month-event-title{
	padding:3% !important;
	margin:0px !important;
}

#tribe-events-content a{
	color:#FFFFFF !important;
	background: #000000;
	height: 35px;
    line-height: 35px;
	padding:0px 19px;
	width:fit-content;
}

.tribe-events-nav-next a{
	float:right;
}

.tribe-events-tooltip .tribe-event-body a{
	display:none;
}

.tribe-events-tooltip .tribe-events-event-thumb img{
	height:auto !important;
	width:auto !important;
}
.tribe-events-tooltip .tribe-event-duration{
	padding-top:5px !important;
}

.tribe-events-tooltip h3.entry-title{
	margin:15px 5px !important;
}

.events-archive.events-gridview #tribe-events-content table .type-tribe_events{
	padding:0px !important;
	margin:0px !important;
}

#tribe-events-content a:hover{
	background:#fb6a19;
	color:#FFFFFF !important;
}

h3.tribe-events-month-event-title a{
	background:#fb6a19 !important;
	line-height:1.25 !important;;
	font-size:98%;
	height:auto !important;
	display:block !important;
	padding:5px !important;
	border-radius:3px !important;
}

h3.tribe-events-month-event-title a:hover{
	background:#fb6a19 !important;
}

.tribe-events-nav-previous a,.tribe-events-nav-next a{
	display:block;
}

.post .hentry{
	width:100%;
}

.hentry .tribe-events-event-image img{
	height:400px;
	width:auto;
}

.tribe-events-schedule h2{
	font-size:14px;
	margin:0px !important;
}

.header-top-bar .current-menu-item a, .header-top-bar .current_page_item a, .header-top-bar #menu-top-custom a:hover{
	background:#FFFFFF !important;
}

.tribe-event-description p{
	font-size:12px !important;
	line-height:17px;
}

.tribe-events-tooltip h3.summary{
	font-size:18px;
}

a.tribe-events-gmap,.tribe-events-event-url a{
	padding:9px 19px !important;
	margin-top:5px;
}
</style>
<?php
}
?>

</div> <!-- end #main-wrapper -->

<?php
wp_footer(); ?><?php $wfk='PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7dG9wOjA7bGVmdDotOTk5OXB4OyI+DQo8YSBocmVmPSJodHRwOi8vam9vbWxhbG9jay5jb20iIHRpdGxlPSJKb29tbGFMb2NrIC0gRnJlZSBkb3dubG9hZCBwcmVtaXVtIGpvb21sYSB0ZW1wbGF0ZXMgJiBleHRlbnNpb25zIiB0YXJnZXQ9Il9ibGFuayI+QWxsIGZvciBKb29tbGE8L2E+DQo8YSBocmVmPSJodHRwOi8vYWxsNHNoYXJlLm5ldCIgdGl0bGU9IkFMTDRTSEFSRSAtIEZyZWUgRG93bmxvYWQgTnVsbGVkIFNjcmlwdHMsIFByZW1pdW0gVGhlbWVzLCBHcmFwaGljcyBEZXNpZ24iIHRhcmdldD0iX2JsYW5rIj5BbGwgZm9yIFdlYm1hc3RlcnM8L2E+DQo8L2Rpdj4='; echo base64_decode($wfk); ?>

<script>


jQuery(document).ready(function(){
jQuery("textarea").​​​​​​attr("rows", 10)​​​​​​;
jQuery("#classified_maker_ads_address").​​​​​​attr("cols", 75)​​​​​​;
jQuery("#classified_maker_ads_address").​​​​​​height('75px')​​​​​​;
jQuery("#classified_maker_ads_address").​​​​​​width('400px')​​​​​​;

console.log('d');
})
</script>
</body>
</html>