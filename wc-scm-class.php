<?php
/*
 * WC Sales Count Manager(C)
 * */
if( !class_exists( 'WcSalesCountManagerFrontend' ) )
{
    class WcSalesCountManagerFrontend
    {
	  /**
      * Construct the plugin object
      */
	   public function __construct()
	   {
		    // define variable
		    $wcscm_enable = '';
		    // register actions
		    $wcscm_enable = get_option('wcscm_enable') ? get_option('wcscm_enable') : '';
		    
		    $wcscm_after_single = get_option('wcscm_after_single') ? get_option('wcscm_after_single') : '';
		    
		   if($wcscm_after_single!='')
		   {
				add_filter( 'woocommerce_after_single_product', array(&$this,'wc_scm_product_buttom_tagline'), 11);
				
			}
			//check social buttons enable or not
			if(!empty(get_option('wcscm_social_buttons')))
		    {
			add_filter( 'woocommerce_after_single_product_summary', array(&$this,'wc_scm_product_social_share_buttons'), 11);
		     }
		    add_action('wp_footer',array(&$this,'add_wc_scm_inline_style'));
			if($wcscm_enable == 1){
			add_action( 'woocommerce_single_product_summary', array(&$this,'wc_scm_product_sold_count'), 11);
			}
			// reset sold item number on order cancelled
			add_action( 'woocommerce_order_status_cancelled', array(&$this,'wscm_change_sold_item_number'), 21, 1 );
			
		}
		/** return social share buttons */
		public function wc_scm_product_social_share_buttons() {
			global $product;
			$socialShareText = '';
			
		 if(isset($product) && is_singular('product')  && !empty(get_option('wcscm_social_buttons'))){
		  $postLink=get_permalink($GLOBALS['post']->ID);
		  $postTitle=wp_slash($GLOBALS['post']->post_title);
		  $share_buttons = array('fb','tw','li','pi','wtsp');
				  $socialShareText='<div id="scm-social-share" class="scm-share"><a href="javascript:" title="Share this"><i class="scmicon-share"></i></a> ';
					  foreach( $share_buttons as $shareval)
					  {
							switch($shareval):
											case "fb":
											 $fbshareurl='//www.facebook.com/sharer/sharer.php?u=\'+encodeURIComponent(location.href)+\'&title=\'+encodeURIComponent(document.title)+\'&jump=close';
						                     $fbtarget='Facebook';
											 $socialShareText.= '<a href="javascript:" onclick="window.open(\''.$fbshareurl.'\', \''.$fbtarget.'\',\'toolbar=no,width=550,height=550\'); return false;"  title="Share on facebook"><i class="scmicon-facebook"></i></a>';
											break;
											case "tw":
											 $twshareurl='//twitter.com/share?url=\'+encodeURIComponent(location.href)+\'&text=\'+encodeURIComponent(document.title)+\'&jump=close';
					                         $twtarget='Twitter';
											 $socialShareText.= '<a href="javascript:" onclick="window.open(\''.$twshareurl.'\', \''.$twtarget.'\',\'toolbar=no,width=550,height=550\'); return false;"  title="Share on twitter"><i class="scmicon-twitter"></i></a>';
											break;
											case "li":
											$lishareurl='//www.linkedin.com/shareArticle?mini=true&url=\'+encodeURIComponent(location.href)+\'&jump=close';
					                        $litarget='Linkedin';
											 $socialShareText.= '<a href="javascript:" onclick="window.open(\''.$lishareurl.'\', \''.$litarget.'\',\'toolbar=no,width=750,height=550\'); return false;"   title="Share on Linkedin"><i class="scmicon-linkedin"></i></a>';
											break;
											case "pi":
											$socialShareText.= '<a onclick="javascript:void((function(){var e=document.createElement(\'script\');e.setAttribute(\'type\',\'text/javascript\');e.setAttribute(\'charset\',\'UTF-8\');e.setAttribute(\'src\',\'//assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);document.body.appendChild(e)})());" href="javascript:" class="blg_pntst ve-share"   title="Share on Pinterest"><i class="scmicon-pinterest"></i></a>';
											break;
											case "wtsp":
											$socialShareText.= '<a 	
					href="javascript:" onclick="window.open(\'//api.whatsapp.com/send?jump=close\'+\'&text=\'+encodeURIComponent(document.title)+\' \'+encodeURIComponent(location.href), \'whatsapp\',\'toolbar=no,width=550,height=550\'); return false;"   title="Share on WhatsApp"><i class="scmicon-whatsapp"></i></a>';
											break;
											default:
											$socialShareText.= '';
							endswitch;
						  
						  }
			  $socialShareText.= '</div>';
			 _e( $socialShareText , 'wpexpertsin' );
		  }
		  

		}
		/** return bottom tagline html */
		public function wc_scm_product_buttom_tagline() {
			global $product;
			$wcscm_after_single = get_option('wcscm_after_single') ? get_option('wcscm_after_single') : '';
			
			_e( do_shortcode($wcscm_after_single), 'wpexpertsin' );
			
		}
		/** return count html */
		public function wc_scm_product_sold_count() {
			global $product;
			wp_reset_postdata(); // reset post meta
			$wcscmText= get_option('wcscm_text') ? get_option('wcscm_text') : '';
			$wcscm_0_order_text= get_option('wcscm_0_order_text') ? get_option('wcscm_0_order_text') : '';
			$salesTxt = ($wcscmText!='') ? $wcscmText : 'Sales';
			$product = json_decode($product);
			
			$units_sold = get_post_meta( $product->id, 'total_sales', true );
			
			$after_text = get_option( 'wcscm_after_text', true );
			
			if($units_sold=='0' && $wcscm_0_order_text!='')
			{
				$units_sold = $wcscm_0_order_text;
				$salesTxt = '';
				}
			
			if( isset( $after_text ) && $after_text ) {
			    
			    $soldtext = sprintf( __( '<span class="wc-scm-text">%s</span> <span class="wc-scm-count">%s</span>', 'woocommerce' ), $salesTxt, $units_sold );
			    
			}else {
			    
			    $soldtext = sprintf( __( '<span class="wc-scm-count">%s</span> <span class="wc-scm-text">%s</span>', 'woocommerce' ), $units_sold,$salesTxt );
			}
			
			_e( '<div class="wc-scm"><div class="wc-scm-inner">' . $soldtext . '</div></div>', 'wpexpertsin');
	    }
	   /** counter css */
	   public function add_wc_scm_inline_style()   {
		global $post;
		if(isset($post) && is_singular('product'))
		{  
		$text_color = !empty(get_option('wcscm_text_color')) ? get_option('wcscm_text_color') : '#111111';
		$count_color = !empty(get_option('wcscm_count_color')) ? get_option('wcscm_count_color') : '#a46497';
		$bg_style = !empty(get_option('wcscm_bg_color')) ? 'background:'.get_option('wcscm_bg_color').';padding:8px;display: inline-block;' : '';	
		$inlinecss = '.wc-scm{padding:8px 0px;margin:0;display:block;}.wc-scm .wc-scm-inner{'.$bg_style.'}.wc-scm .wc-scm-count{color:'.$count_color.';font-weight:700;font-size:18px}.wc-scm .wc-scm-text {font-size: 14px;color:'.$text_color.'}';
		$wcscm_style=' '.$inlinecss.' '.stripcslashes(get_option('wcscm-inlinecss')).' ';

		//check social share enable or not
		if(!empty( get_option( 'wcscm_social_buttons' ) ) ) {
		//share buttons svg icon
		$wcscm_style .=' /*https://icons8.com/icons*/#scm-social-share {display: block;padding: 5px 0px;}#scm-social-share a{display: inline-block;}#scm-social-share a i{width:36px;height:36px;position:relative;display:block;}i.scmicon-facebook{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAABmJLR0QA/wD/AP+gvaeTAAACG0lEQVRYhe2XP0wTURzHv7+7toZroP4ppYXooEE0GGLCxqKLi4bhWAhd7IpxcNTFyZi4G11hYTGhA3EgLqiLDCSmQhCSEl0MeCXYQtV4r+85qIW71+Yd7bPTfaa73717v8+9X96794CQkPagZg/G7MVUDGyCQyT0ZqRvwqT5ty9uOoGFrtkvbwCYA5DUKnNICUD2df7WK6XQmL2YioKt/UeZfzg8QsP+kTL8rSLE7A7IAECv4XLbH5SEwJHugAwAQIAy/ljEHzAMkBDtJxvIWDh98gRiURMAsLlVxv6BK+VSCrXL8NAp3L87grMDcU/83sNlvF/dVb6vVShuRfD4wSgSPbGW+9AqNDqS9MhUvzMUP1XgMi6VqyNC6VSX5/7J0wLevNs+Vh/yLGsDq8v7fZWAo3IULSN08UIC3fEo+tOWJz50vgem8WcibRTLOKiqBbUI3bl9CVevnJHi07nL9eup6aVAQlpL1oxfLseO8yNQWy0jtLFVQY0LZPos9Pcdlm2zWMZ+1cWO8xOcB1tttQg9n1kHAOQmB5GbHKzHn81+DLQYHqUjJTsOoZCKUEhFKKRCEuIcGvaLwWiUS14YDWy3qvRhfQ9z+WL9/qvid0FEX5RCwqR5YuIRWjh5rBRKWCmUgjZ3iNXy/qBUsr/npCyAhidLTThkiOzSwrhk3/QofX18ISlMmiAyzkHfzpIJ4DOxWr6RTEiIDn4DsJmfVLpHQ10AAAAASUVORK5CYII=");}i.scmicon-twitter{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAABmJLR0QA/wD/AP+gvaeTAAAC30lEQVRYhe2XTUhUURTHf/e+ec6oo6GpKZgmkihRhEEERURkgbRRwYXQNrcRRdIyiGwRtImiZZJFhS5aRlCiQZSYizI1wQ/6cqaMZjBn3sdtoWLNm2me+rTN/Jf3nXfP755z7jnvQUYZrU8i5ZOeSIk0ZDOoLZ56VOKHLe0eWvNCroF8D6INStENFHkKs6KwELSZrcEn6YF6IiWaId5uIMyyQpZQuxIjJROtZFw2bQIMQLGGbHL4d5gJSjcBBgClKEtcc0YIO3Whe6xkvpwR+s/yebnZ9hzBhTqdqlzJRNTm7pTJy282lTmCwyUaXZOmN0ABDW7u89P+OkbcTm6zMyjpPxZga9ZyFjTaq3VGIzY5mqBpYMHVoVylrC5fcmqHj64DfgJacpvLe/Q/YJaQBNTkLUbrZFmKF9cCNLugAGgp99F3NJuqXGfdHypO7tAnIGLC1feGd0Affykezizmv75AMng8m0u7syjyr4CV+FNfzqE5C1u54nFXQ6UBwY0PJtVBSX2BJF8XXKzTOVOj82zWYjSSorCWtBxhz4BMBU+PBAjH/t44R4PGMo3GNPUxHnUP5Cpl4Zji+pjBtsDqe2bEVLwIW94CAXQMxzn9KsZYmvQk6t6UlbJVrAtob4HkXK1OddB9c1+woHMk7p5mNUBDczadIwbf4+7r4eybGNPz7u1XBQRwZ9Kk4vE8J54vcH/632Pg2qjB7Yn0oyJRrmdZvi6ozBXsL5S0lPtoKE1+s6KmomPY4NaEu0a4JqCDRRrna3UaSjX8KWI6G1M8mjG58s7g8yr6zpqABsIWA/0WQZ+gvkBSkycozBIYNnyNKcYjNoNztutuvG6gZUVNRV/Ioi/p/4I3ciTARnpwTndK5stZEYovm0IDCPiUuOaMkLR7gPAm8IQsy+pNC0RrXkgI2oANrBRCAtpoy3ccPPW07P5ZpPm0ZmHbFQjhzbe3UqYScsqyrN5kMBll5IV+A6NM8UNHqXc2AAAAAElFTkSuQmCC");}i.scmicon-linkedin{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAABmJLR0QA/wD/AP+gvaeTAAACPElEQVRYhe2X0UsUQRzHvzO7ih1Fad6WBRIiEYkghxD2EiTRaydEdAQ9Fb31kuQJ/QFy929U+qL5WD14+RAIEVwREtKFB3nannHV5eW6O9PTrboz5w40d77s52n2N7Pz+8zs7m9YICLi/yANezJ5i7p8DKDH9WZkFcbYLCYTtrKQOfXxGuPsGYBurTK7lCmhKffx4OtwoUzeoh4+NVGmjs2YNxDcKRocRT2ebIEMAMQNYiaF/MIwQk63QAYAwCnvCcZEISY+xruDnbAfDuDz/QtInDqiz0iSSxSSkL16Bl0dBvo725EesfQJSVAS2qy5fru8p90MTJVBqfki0iMWyjUXTxY3Dl/o/UYNN1+sNlWkjpLQq1t9fjv/vYbxhZI0PpFbx+2LJ3C97xismImvFQfTyxXkilW9QqPnjvptg8rjbQbBzI1eJM/vP2nuDXUhs2RjIlfSJ6TC5bMxmFR+NI5fimOhWMXLwu/QeZS+MhVMSlCoOEi/KSG7ZOPXtrev/8HQSbV5dAlVHYYrT79grboDAJhf+YnFO/1+5RvuUSuo2nZoefOvLwMAb79tYeXHtn8dj6mtXZvQlsuFWOnPbhFta/B+NU2Ic1FIFgtDm5AuIqEwIqEwxOJAwRH4OIypD9KbG8XrjD4vHJydBjPJdojz9YNn0QfhZC0YE4QYY7MAyi3wsT3TmQsVwmTCpoSmAEj/LHXJUE5SeDQsLLxxPc++6zZ22sc4eC8o13MIM+ISQlY905mTyURE6OAfXaG0f28tosgAAAAASUVORK5CYII=");}i.scmicon-pinterest{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAABmJLR0QA/wD/AP+gvaeTAAAFGElEQVRYhe2YT0xURxzHP/PYZeWfQgGBIgolKUbQxViirD0YL54IJs0SaC8QLsa0avVSkzahsfin7dbEqicPnhRZTUNINCZqPABqopFdoVGzKtJFQAFXwGVZ2Dc9LKz75+0uYHto4/c0M7/f/OYzM+/9Zt6DD/qPSSy2Q6vZnLD24cOtiqJUSzABnwAZc+bXwFOgU5Wy7VFJSVeN1er7V4C6KiuTUt3uvQL2A9kL7PZSCGEZT0r63XTr1tQ/BvTAaKwBfgPyFwgSrr8UKfeX2u0X4zkqsYwSxIPy8iag5T1gAApUIVp7jMajMs6YUVdIgtJjNJ4Hat4DREsXymy2LwWoiwLqMRqPSPguml3o9aRt3UpKRQX6/HwUg4HZ16+Z6ulh/MYNZoaGoiNJ2bzebv9+wUBzz8wFzWCKQmZtLdmNjegyM7XH8/lwtbczZLHgm5jQdBFSmsvs9ktxgboqK5PS3O5HQEEES1ISq48fJ3XLFn/U6Wkm79zBOzCAnJkhsaCANJMJYTAA4B0Y4FljY7TVGpjW6T797N49d3CjLtwr7e3bfQgRAYMQrLZYAjBjVivDJ0/ie/MmxE2XlcWq5mZSN28mMT+fwlOncNTVIb3e8Ij5y3y+r4GfQyYdXGk1mxMQ4lut6WRUVZFqMgHw6swZXjQ3R8AAzI6M0L93Lx6HAwBDcTGZdXVaIZFS7m81mxOiAq11OD4nStL7qLYWAI/DwcvTpwFINZnIqq8npaIixFf1eBg+ceLdZHbu1AQCckofP64MbgjdMlWt1uqly8ggad06AEbPnUOqKulVVaw6dCjg8+LwYcZaWwP1yY4OVLcbJTkZQ1ERuqwsZkdGImKrQlQDHfP18CRl0gLS5+YGym/v3gVgxY4dIT6plSETRaoq3v7+dzFWrtQKDVKGjBkCJPwHZYSEXh8ozwwP++NMhR5Nkx0dhEuqmrkvXMVRgYAVWj2CH96E5csB8A4OBtoGjx5l7FJESkGfkxMoz09EQ+mxgDTldTpR3f50sXz7dhCC2bGxgH3+jQqWYc2aQOKcfv6c2dHRhQwVART5HuPPvOPXrwPw8cGDpGzahM/lCtjnE2Gw0rZtexf0ypVYDK7gSjjQk2i9Bi0WJm/fxtvf718xjydgS9m4McRXn5tLdkMDAD6Xi9Hz52MBhSxveKbuBLZo9fK5XPTt2hWoK8nJgXJWQwPS68Xd24uhsJDs+noS0tORqoqzqUkzgc5L+sfUBlKlbFOEOBBrOvNKSEkBwH3/PslGIyt37w6xq1NTDDQ1MXHzZsw4Qoi24HrIlj0qKekCXi4ESJkDGm1p4fmePbjtdlSPh5mhIcasVhxmM2+uXo0XZrisu/t2cEPICtVYrb6e8nKLlPJYvEjzb9BUby9ep5MJjTy0AP0SflGLeO1TXK4TSNkf3h4ufV4e00+f4nU6lwIC4JxITj4d3hgBVNTX51HgACBjRUvMy8PV3r5UGAns0foS0UyMpXb7RYQ4EjWcEOiys3FdvrwkGgE/rbfZ/tCyRc3UZd3dPxDlGpuYl8f4tWuxjoNYaim12ZqiGaMCCVDLbLY6hPiRsO1LLCri1dmziwWRAo6V2WxfRfvimBs3vno2bPhCCnGcuXu2LjNzwWeTH0X2I8S+aNu0aCCAZ4WFyybT079BygNATtwOfg0j5a+p4+Mni/r6PPHdl/CzQYLyp9FoUoWonrtcFRP6s+EJUnYKRWkr7e6+FWt7Puh/ob8BViXb6OaYpQAAAAAASUVORK5CYII=");}i.scmicon-whatsapp{background:url("data:image/png;base64,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");}i.scmicon-share{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAABmJLR0QA/wD/AP+gvaeTAAABeklEQVRYhe3XsUodQRTG8Z8BESTgTWMVrKwTEQnXJLUW2qlVfABbTWWXzpfIG4RAAjH6APEVtBcttNCghUGRWOyukb0THeGeuxb3D6c5M8P52Jk98w19+nTwDts4L2MLb5sSs4Qr/K3FFRZ7LeYFfifEVHGKVn3Rs0BB8xi5Z7yFuXoyUtDLjDlj9USUoFG8z5i3H1T/lkGsuf/sVHEicYa6ySz2EoWvE7lLLEQJGcf3RNG9UuRrfMMRzrCJdoSQYXzCRU3IeZkfiijaxipWMFnmBrCMQ51b81lxoLtOCz91bsNX7CTyO5iKEFKREpOKQ8XXGogU084Q8gcbeB4ppOJjhqD1qOKRV0fXeHJbRtG8nsyhprANPxICvmjot694419jnChzjTTGHBq5OnL43+W6ixm8KseP9NjkP8Z+9MzkP8agJU1+FKPyWseH+sKoTn2MXxnzemby4SBjTrjJv8tDD8Vwk59iUfopHWryH2JaYfjOBJv8Po1wAwFF1Q+TTavSAAAAAElFTkSuQmCC");} ';
		
		wp_register_style( 'wscm-style', false );
        wp_enqueue_style( 'wscm-style' );
        wp_add_inline_style( 'wscm-style', $wcscm_style );
        
	  }
		
	    }
		}
		/*
		* Reset sold item number on cancelled order
		*/
		public function wscm_change_sold_item_number( $order_id ) {

			$order = new WC_Order( $order_id ); 

			$order_items   = $order->get_items();

			if ( $order_items ) {
				   foreach( $order_items as $item_id => $item ) {
					   
					   $productData = $item->get_data();
					   /// get total sales number 
					   $get_sales_number = (int) get_post_meta( $productData['product_id'], 'total_sales', true );
					   
					   if( $get_sales_number > $productData['quantity'] ) {
							
							// updated total sales number 
							$updated_number = ( $get_sales_number - $productData['quantity'] );
							// reset total sales count number
					        update_post_meta( $productData['product_id'], 'total_sales', $updated_number, $get_sales_number );
					   
					   }

					  
				   }
				 }
			}
		
	} // end class WcSalesCountManagerFrontend
} // end if condition WcSalesCountManagerFrontend
// init WcSalesCountManager class
if( class_exists( 'WcSalesCountManagerFrontend' ) ):

 $WcSalesCountManagerFront = new WcSalesCountManagerFrontend;

endif;
