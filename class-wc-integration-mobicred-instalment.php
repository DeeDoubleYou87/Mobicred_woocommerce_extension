<?php

/**
 * Mobicred Instalment Widget.
 *
 * @package   Woocommerce Mobicred Instalment Widget
 * @category Integration
 * @author   Mobicred (Pty) Ltd.
 */

	if (!class_exists('WC_Integration'))  return;

	class WC_Mobicred_Instalment extends WC_Integration {

      /**
       * Init and hook in the integration.
       */
      public function __construct() {
        
        global $woocommerce;

        $this->id                 = 'mobicred-instalment-widget';
        $this->method_title       = __( 'Mobicred Instalment Widget');
        $this->method_description = __( 'Display Mobicred instalment pricing in WooCommerce.');

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables.
        $this->enabled = $this->get_option( 'enabled' );
        $this->merchantId = $this->get_option( 'merchantId' );
        $this->dataBefore = $this->get_option( 'dataBefore' );
        $this->dataAfter = $this->get_option( 'dataAfter' );
        $this->widgetMin = $this->get_option( 'widgetMin' );
        $this->widgetMax = $this->get_option( 'widgetMax' );
        $this->widgetTextColor = $this->get_option( 'widgetTextColor' );
        $this->widgetBackgroundColor = $this->get_option( 'widgetBackgroundColor' );
        $this->widgetPadding = $this->get_option( 'widgetPadding' );
        $this->widgetMargin = $this->get_option( 'widgetMargin' );
        $this->widgetFont = $this->get_option( 'widgetFont' );
        $this->widgetFontSize = $this->get_option( 'widgetFontSize' );
        $this->widgetFontWeight = $this->get_option( 'widgetFontWeight' );

        // Actions.
        add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
      
      }


      /**
      * Initialize integration settings form fields.
      */
      public function init_form_fields() {

        $this->form_fields = array(

            'enabled' => array(
                'title'       => 'Enable',
                'label'       => 'Enable Mobicred Widget',
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'yes'
            ),

            'merchantId' => array(
              'title'       => 'Your Mobicred Merchant ID',
              'type'        => 'text',
              'description' => "Your Mobicred Merchant ID. This is used for tracking of new applications. If you do not include this ID we won't be able to tell you how many sign-ups you received from the widget.",
              'default'     => '',
              'desc_tip'    => true,
            ),

            'dataBefore' => array(
                'title'       => 'Text Before Instalment Amount',
                'type'        => 'text',
                'description' => 'The text that displays before the instalment amount.',
                'default'     => 'Credit Amount:',
                'desc_tip'    => true,
            ),

            'dataAfter' => array(
              'title'       => 'Text After Instalment Amount',
              'type'        => 'text',
              'description' => 'The text that displays after the instalment amount.',
              'default'     => 'per month',
              'desc_tip'    => true,
            ),

            'widgetMin' => array(
              'title'       => 'Minimum Product Value',
              'type'        => 'text',
              'description' => 'Only products exceeding this value will have the instalment widget visible',
              'default'     => '500',
              'desc_tip'    => true,
            ),

            'widgetMax' => array(
              'title'       => 'Maximum Product Value',
              'type'        => 'text',
              'description' => 'Products up to this value will have the instalment widget visible',
              'default'     => '35000',
              'desc_tip'    => true,
            ),

            'widgetTextColor' => array(
              'title'       => 'Text Color',
              'type'        => 'text',
              'description' => 'The text colour of the widget. Use inherit to use your default site font.',
              'default'     => '#666666',
              'desc_tip'    => true,
            ),

            'widgetBackgroundColor' => array(
              'title'       => 'Background Color',
              'type'        => 'text',
              'description' => 'The background colour of the widget. Use transparent to use your default site colour.',
              'default'     => '#FFFFFF',
              'desc_tip'    => true,
            ),
          
            'widgetPadding' => array(
              'title'       => 'Widget Padding',
              'type'        => 'text',
              'description' => 'The padding around your widget. Just use a numeric value, excluding px or any other size variable.',
              'default'     => '0',
              'desc_tip'    => true,
            ),

            'widgetMargin' => array(
              'title'       => 'Widget Margin',
              'type'        => 'text',
              'description' => 'The margin around your widget. Just use a numeric value, excluding px or any other size variable.',
              'default'     => '0',
              'desc_tip'    => true,
            ),

            'widgetFont' => array(
              'title'       => 'Font Family',
              'type'        => 'text',
              'description' => 'The font to be used on the widget. Use inherit to use your default site font.',
              'default'     => 'inherit',
              'desc_tip'    => true,
            ),

            'widgetFontSize' => array(
              'title'       => 'Font Size',
              'type'        => 'text',
              'description' => 'The font size to be used on the widget. Use inherit to use your default site font size. Just use a numeric value, excluding px or any other size variable.',
              'default'     => '16',
              'desc_tip'    => true,
            ),

            'widgetFontWeight' => array(
              'title'       => 'Font Weight',
              'type'        => 'text',
              'description' => 'The font weight to be used on the widget. Use inherit to use your default site font or use any valid font-weight variable.',
              'default'     => 'normal',
              'desc_tip'    => true,
            ),  
        );

      }
  
  }

  // initial the widget
  function mobicred_widget_display() {

    global $post;
    
    if( function_exists("wc_get_product") ) {
      $product = wc_get_product($post->ID);
    }
    else {
      $product = new WC_Product($post->ID);
    }
    
    $price = $product->get_price();
  
    $thePlugin = new WC_Mobicred_Instalment();

    $enabled = $thePlugin->get_option( 'enabled' );
    $merchantId = $thePlugin->get_option( 'merchantId' );
    $dataBefore = $thePlugin->get_option( 'dataBefore' );
    $dataAfter = $thePlugin->get_option( 'dataAfter' );
    $widgetMin = $thePlugin->get_option( 'widgetMin' );
    $widgetMax = $thePlugin->get_option( 'widgetMax' );
    $widgetTextColor = $thePlugin->get_option( 'widgetTextColor' );
    $widgetBackgroundColor = $thePlugin->get_option( 'widgetBackgroundColor' );
    $widgetPadding = $thePlugin->get_option( 'widgetPadding' );
    $widgetMargin = $thePlugin->get_option( 'widgetMargin' );
    $widgetFont = $thePlugin->get_option( 'widgetFont' );
    $widgetFontSize = $thePlugin->get_option( 'widgetFontSize' );
    $widgetFontWeight = $thePlugin->get_option( 'widgetFontWeight' );

     if (!isset($enabled) || $enabled !== 'yes')
    return;


    $widget_code = '<script src="https://mobicred.co.za/plugins/instalment.js"></script>';
    $widget_code .= '<div style="margin-bottom: 20px;" id="instalmentCalc"';
    $widget_code .= 'data-amount="';
    $widget_code .= $price;
    $widget_code .= '" data-merchantId="';
    $widget_code .= $merchantId;
    $widget_code .= '" data-min="';
    $widget_code .= $widgetMin;
    $widget_code .= '" data-max="';
    $widget_code .= $widgetMax;
    $widget_code .= '" data-before="';
    $widget_code .= $dataBefore;
    $widget_code .= '" data-after="';
    $widget_code .= $dataAfter;
    $widget_code .= '" data-textColor="';
    $widget_code .= $widgetTextColor;
    $widget_code .= '" data-bgColor="';
    $widget_code .= $widgetBackgroundColor;
    $widget_code .= '" data-padding="';
    $widget_code .= $widgetPadding;
    $widget_code .= '" data-margin="';
    $widget_code .= $widgetMargin;
    $widget_code .= '" data-fontFamily="';
    $widget_code .= $widgetFont;
    $widget_code .= '" data-fontSize="';
    $widget_code .= $widgetFontSize;
    $widget_code .= '" data-fontWeight="';
    $widget_code .= $widgetFontWeight;
    $widget_code .= '"></div>';

    echo $widget_code;

  }

  add_action('woocommerce_single_product_summary','mobicred_widget_display',15);

  function mobicred_variation_widget_display( $price, $variation ) {

    $widget_code = $price;

    $amount = $variation->get_price();

    $thePlugin = new WC_Mobicred_Instalment();

    $enabled = $thePlugin->get_option( 'enabled' );
    $merchantId = $thePlugin->get_option( 'merchantId' );
    $dataBefore = $thePlugin->get_option( 'dataBefore' );
    $dataAfter = $thePlugin->get_option( 'dataAfter' );
    $widgetMin = $thePlugin->get_option( 'widgetMin' );
    $widgetMax = $thePlugin->get_option( 'widgetMax' );
    $widgetTextColor = $thePlugin->get_option( 'widgetTextColor' );
    $widgetBackgroundColor = $thePlugin->get_option( 'widgetBackgroundColor' );
    $widgetPadding = $thePlugin->get_option( 'widgetPadding' );
    $widgetMargin = $thePlugin->get_option( 'widgetMargin' );
    $widgetFont = $thePlugin->get_option( 'widgetFont' );
    $widgetFontSize = $thePlugin->get_option( 'widgetFontSize' );
    $widgetFontWeight = $thePlugin->get_option( 'widgetFontWeight' );

     if (!isset($enabled) || $enabled !== 'yes')
    return;


    $widget_code .= '<script src="includes/instalment.js"></script>';
    $widget_code .= '<div id="instalmentCalc"';
    $widget_code .= 'data-amount="';
    $widget_code .= $amount;
    $widget_code .= '" data-merchantId="';
    $widget_code .= $merchantId;
    $widget_code .= '" data-min="';
    $widget_code .= $widgetMin;
    $widget_code .= '" data-max="';
    $widget_code .= $widgetMax;
    $widget_code .= '" data-before="';
    $widget_code .= $dataBefore;
    $widget_code .= '" data-after="';
    $widget_code .= $dataAfter;
    $widget_code .= '" data-textColor="';
    $widget_code .= $widgetTextColor;
    $widget_code .= '" data-bgColor="';
    $widget_code .= $widgetBackgroundColor;
    $widget_code .= '" data-padding="';
    $widget_code .= $widgetPadding;
    $widget_code .= '" data-margin="';
    $widget_code .= $widgetMargin;
    $widget_code .= '" data-fontFamily="';
    $widget_code .= $widgetFont;
    $widget_code .= '" data-fontSize="';
    $widget_code .= $widgetFontSize;
    $widget_code .= '" data-fontWeight="';
    $widget_code .= $widgetFontWeight;
    $widget_code .= '"></div>';

    echo $widget_code;

  }

  add_filter( 'woocommerce_variation_price_html', 'mobicred_variation_widget_display', 10, 2);
  add_filter( 'woocommerce_variation_sale_price_html', 'mobicred_variation_widget_display', 10, 2);
