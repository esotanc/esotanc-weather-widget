<?php
/*
	Plugin Name: Weather Widget - Esotanc Weather
	Description: Beautiful and simpe weather widget. Five day forecast, different sizes and three design. Language-file,:HU, EN, CUSTOM.
	Plugin URI: http://www.esotanc.hu
	Author: Attila Szuhi
	Version: 1.0
 */

/**
 * Add function to widgets_init that'll load our widget.
 */
 
add_action( 'widgets_init', 'esotanc_weather_load_widgets' );

function esotanc_weather_load_widgets() {
	register_widget( 'esotanc_weather_widget' );
}

 
class esotanc_weather_widget extends WP_Widget {
/**
	 * Widget setup.
	 */
	function esotanc_weather_widget() {
		/* Widget settings. */
		$widget_options = array( 
		 'classname' => 'esotanc_weather_widget', 
		 'description' => __('Displays weather forecast up to five day.') );

		/* Widget control settings. */
		$control_options = array( 
		'width' => 300, 
		'height' => 230, 
		'id_base' => 'esotanc_weather_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'esotanc_weather_widget', 'Weather Widget - Esotanc', $widget_options, $control_options );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
			
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$esoid =($instance['esoid'] != "")?$instance['esoid'] :"budapest";
		$name =($instance['name'] != "")?$instance['name'] :"Budaörs";
		$size =($instance['size'] != "")?$instance['size'] :300;
		$color =($instance['color'] != "")?$instance['color'] :"color";
		$language =($instance['language'] != "")?$instance['language'] :"en";
		$textcolorwp =($instance['textcolorwp'] != "")?$instance['textcolorwp'] :"black";
		$upperheadwp =($instance['upperheadwp'] != "")?$instance['upperheadwp'] :"#FFFFFF";
		$sidebarwp =($instance['sidebarwp'] != "")?$instance['sidebarwp'] :"#FFFFFF";
		$daynamecolor =($instance['daynamecolor'] != "")?$instance['daynamecolor'] :"#757575";
		$daynumbercolor =($instance['daynumbercolor'] != "")?$instance['daynumbercolor'] :"#545ACE";
		$monthcolor =($instance['monthcolor'] != "")?$instance['monthcolor'] :"#F98305";
		
		
		
		/* Open the language file */ 
		
		$file=fopen(dirname(__FILE__)."/language/language.".$language,"r");
		 $csvsorok = 3;
			$n = 0;
			while ($n < $csvsorok)
			{
				$lan[$n] = fgetcsv($file, 1024, "#" );
				++$n;
			}
	  	fclose($file);
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
        echo " \n";
		
		
		/* Make Cache for optimization */
		
		 $cache = dirname(__FILE__) . '/cache';
		 
		/* Load the weather data from sotanc.hu */
		 
		function retrieveYahooWeather($zipCode="HUXX0002_f.xml") {
		$cache = dirname(__FILE__) . '/cache';
		$yahooUrl = "http://xml.weather.yahoo.com/forecastrss/";
		$yahooZip = "$zipCode";
		$yahooFullUrl = $yahooUrl . $yahooZip; 
		$curlObject = curl_init();
		curl_setopt($curlObject,CURLOPT_URL,$yahooFullUrl);
		curl_setopt($curlObject,CURLOPT_HEADER,false);
		curl_setopt($curlObject,CURLOPT_RETURNTRANSFER,true);
		$returnYahooWeather = curl_exec($curlObject);
		curl_close($curlObject);
		$cachefile = fopen($cache, 'w');
		fwrite($cachefile, $returnYahooWeather);
		fclose($cachefile);
		return $returnYahooWeather;
    	}
		
		/* Make Cache for optimization */
		
		$cache = dirname(__FILE__) . '/cache';
		if(filemtime($cache) < (time() - 20)) 
		{
			$localZipCode = $esoid; 
			$weatherXmlString = retrieveYahooWeather($localZipCode); // Itt tölti be a függvényt
		}
		else
		{
			$weatherXmlString = file_get_contents($cache);
		}
		//*************** Setting variables *****************************
		$weatherXmlObject = new SimpleXMLElement($weatherXmlString);
		$Forecast = $weatherXmlObject->xpath("//yweather:forecast");
		$_datum=getdate();
		$max[0] = round(($Forecast[0]["high"]-32)*0.55556);
		$max[1] = round(($Forecast[1]["high"]-32)*0.55556);
		$max[2] = round(($Forecast[2]["high"]-32)*0.55556);
		$max[3] = round(($Forecast[3]["high"]-32)*0.55556);
		$max[4] = round(($Forecast[4]["high"]-32)*0.55556);

		$min[0] = round(($Forecast[0]["low"]-32)*0.55556);
		$min[1] = round(($Forecast[1]["low"]-32)*0.55556);
		$min[2] = round(($Forecast[2]["low"]-32)*0.55556);
		$min[3] = round(($Forecast[3]["low"]-32)*0.55556);
		$min[4] = round(($Forecast[4]["low"]-32)*0.55556);

		$ikon[0] = $Forecast[0]["code"];
		$ikon[1] = $Forecast[1]["code"];
		$ikon[2] = $Forecast[2]["code"];
		$ikon[3] = $Forecast[3]["code"];
		$ikon[4] = $Forecast[4]["code"];
		
		$i=0;
		while ($i<5) 
		{
			if ($ikon[$i]==0) {$ikon[$i]="tornado.png";}
			if ($ikon[$i]==1) {$ikon[$i]="tropusi.png";}
			if ($ikon[$i]==2) {$ikon[$i]="hurrikan.png";}
			if ($ikon[$i]==3) {$ikon[$i]="ezivatar.png";}
			if ($ikon[$i]==4) {$ikon[$i]="ezivatar.png";}
			if ($ikon[$i]==5) {$ikon[$i]="havaseso.png";}
			if ($ikon[$i]==6) {$ikon[$i]="onoseso.png";}
			if ($ikon[$i]==7) {$ikon[$i]="onoseso.png";}
			if ($ikon[$i]==8) {$ikon[$i]="onosszital.png";}// ónosszitálás
			if ($ikon[$i]==9) {$ikon[$i]="szitalas.png";}
			if ($ikon[$i]==10) {$ikon[$i]="onoseso.png";}  // fagyos eső
			if ($ikon[$i]==11) {$ikon[$i]="eso.png";}
			if ($ikon[$i]==12) {$ikon[$i]="eso.png";}
			if ($ikon[$i]==13) {$ikon[$i]="hozapor.png";}
			if ($ikon[$i]==14) {$ikon[$i]="hozapor.png";} //kevés hózápor
			if ($ikon[$i]==15) {$ikon[$i]="hovihar.png";}
			if ($ikon[$i]==16) {$ikon[$i]="havazas.png";}
			if ($ikon[$i]==17) {$ikon[$i]="jeg.png";}
			if ($ikon[$i]==18) {$ikon[$i]="onoseso.png";}
			if ($ikon[$i]==19) {$ikon[$i]="por.png";}
			if ($ikon[$i]==20) {$ikon[$i]="kodos.png";}
			if ($ikon[$i]==21) {$ikon[$i]="paras.png";}
			if ($ikon[$i]==22) {$ikon[$i]="szmog.png";}
			if ($ikon[$i]==23) {$ikon[$i]="szeles.png";}
			if ($ikon[$i]==24) {$ikon[$i]="szeles.png";}
			if ($ikon[$i]==25) {$ikon[$i]="hideg.png";}
			if ($ikon[$i]==26) {$ikon[$i]="felhos.png";}
			if ($ikon[$i]==27) {$ikon[$i]="eefelhos.png";}
			if ($ikon[$i]==28) {$ikon[$i]="nefelhos.png";}
			if ($ikon[$i]==29) {$ikon[$i]="ekfelhos.png";}
			if ($ikon[$i]==30) {$ikon[$i]="nkfelhos.png";}
			if ($ikon[$i]==31) {$ikon[$i]="ederult.png";}
			if ($ikon[$i]==32) {$ikon[$i]="napos.png";}
			if ($ikon[$i]==33) {$ikon[$i]="derult.png";} //kevés felhő
			if ($ikon[$i]==34) {$ikon[$i]="derult.png";}  // kevés felhő
			if ($ikon[$i]==35) {$ikon[$i]="esojeg.png";}
			if ($ikon[$i]==36) {$ikon[$i]="forro.png";}
			if ($ikon[$i]==37) {$ikon[$i]="zivatar.png";}
			if ($ikon[$i]==38) {$ikon[$i]="ezivatar.png";;}
			if ($ikon[$i]==39) {$ikon[$i]="zapor.png";}
			if ($ikon[$i]==40) {$ikon[$i]="zapor.png";}
			if ($ikon[$i]==41) {$ikon[$i]="havazas.png";}
			if ($ikon[$i]==42) {$ikon[$i]="hozapor.png";}
			if ($ikon[$i]==43) {$ikon[$i]="havazas.png";}
			if ($ikon[$i]==44) {$ikon[$i]="kfelhos.png";}
			if ($ikon[$i]==45) {$ikon[$i]="zivatar.png";}
			if ($ikon[$i]==46) {$ikon[$i]="hozapor.png";}
			if ($ikon[$i]==47) {$ikon[$i]="zivatar.png";}
			if ($ikon[$i]==3200) {$ikon[$i]="nincsadat.png";}
			++$i;
		}
		
		$_datum=getdate();
		$i=0;
		while ($i<12) 
		{
			$_name_of_month[$i]=$lan[1][$i];
			$_name_of_day[$i]=$lan[2][$i];
			++$i;
		}
		$i=0;
		while ($i<5) 
		{
			$minch[$i]=0;$maxch[$i]=0;$daych[$i]=0;
			$_name_of_day[$i]=$lan[2][$i];
			$_name_of_month[$i]=$lan[1][$i];
			
			if (strlen($min[$i])==1) {$minch[$i]=3;}
			if (strlen($min[$i])==3) {$minch[$i]=-5;}
			
			if (strlen($max[$i])==1) {$maxch[$i]=3;}
			if (strlen($max[$i])==3) {$maxch[$i]=-5;}
					
			$day[$i]=date("j",(mktime(0, 0, 0, date("m")  , date("d")+$i, date("Y"))));
			if (strlen($day[$i])==1) {$daych[$i]=4;}
			$dayname[$i]=$_name_of_day[date("w",(mktime(0, 0, 0, date("m")  , date("d")+$i, date("Y"))))];
			$daynamech[$i]=((((57-(strlen($dayname[$i])*5))/2)*-1)+((strlen($dayname[$i])*-5))); 
			if ($dayname[$i]=="csütörtök") {$daynamech[$i]=-48;}
			$month[$i]=$_name_of_month[date("n",(mktime(0, 0, 0, date("m")  , date("d")+$i, date("Y"))))];
			++$i;
		}
	
		$varos=strtoupper($name);
		if ($language=="hu"){
		$back="_hu";}else {$back="";}
		$link='http://esotanc.hu/';
		
		/* Constuct the widget output */
		if ($color=='black') {$textcolorwp="#777";$upperheadwp="black";$sidebarwp="#141414";$daynamecolor="#757575";$daynumbercolor="#545ACE";$monthcolor="#F98305";}
		if ($color=='white') {$textcolorwp="black";$upperheadwp="#EEEEEE";$sidebarwp="white";$daynamecolor="#757575";$daynumbercolor="#545ACE";$monthcolor="#F98305";}
		if ($color=='color') {$color="white";$textcolorwp="#DDD";$sidebarwp="5659CC";$upperheadwp="#F57F03";$daynamecolor="#757575";$daynumbercolor="#545ACE";$monthcolor="#F98305";}
		if ($color=="custom"){$color="white";}
		$width=$size;
		$meret=0;
		if ($width<129 and $width>=72) 
			{
			$meret=1;$margo1=round(((($width-15)-57)/2));
			}
			
		if ($width>=129 and $width<186) 
			{
			$meret=2;$margo1=round(((($width-15)-(2*57))/3));
			}
		if ($width>=186 and $width<243) 
			{
			$meret=3;$margo1=round(((($width-15)-(3*57))/4));
			}
		if ($width>242 and $width<300) 
			{
			$meret=4;$margo1=round(((($width-15)-(4*57))/5));
			}
		if ($width>=300 and $width<380) 
			{
			$meret=5;$margo1=round(((($width-15)-(5*57))/6));
			}
		
		echo '<div style="position:relative;border:0px;padding:0;margin:0px;background-color:'.$color.';width:'.$width.'px;height:230px;">';
		
		if ($meret>=1) {
			echo '<img style="top:0px;left:'.($margo1+14).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$color.'0.jpg" />';
			echo '<img style="top:76px;left:'.($margo1+14).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$ikon[0].'" />';
			echo '<span style="color:white;left:'.($margo1+70+$daynamech[0]).'px;top:25px;font-family:arial;font-size:10px;color:'.$daynamecolor.';position:absolute;">'.$dayname[0].'</span>';
			echo '<span style="color:white;left:'.($margo1+27+$daych[0]).'px;top:34px;font-family:arial;font-size:25px;color:'.$daynumbercolor.';position:absolute;">'.$day[0].'</span>';
			echo '<span style="color:white;left:'.($margo1+33).'px;top:64px;font-family:arial;font-size:10px;color:'.$monthcolor.';position:absolute;">'.$month[0].'</span>';
			echo '<span style="color:white;font-weight:700;left:'.($margo1+35+$maxch[0]).'px;top:137px;font-family:arial;font-size:15px;position:absolute;">'.$max[0].'&deg;</span>';
			echo '<span style="color:white;left:'.($margo1+35+$minch[0]).'px;top:175px;font-weight:700;font-family:arial;font-size:15px;position:absolute;">'.$min[0].'&deg;</span>';
		}
		if ($meret>=2) {
			echo'<img style="top:0px;left:'.(($margo1*2)+71).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$color.'1.jpg" />'.
			'<img style="top:76px;left:'.((($margo1)*2)+71).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$ikon[1].'" />'.
			'<span style="color:white;left:'.((($margo1)*2)+127+$daynamech[1]).'px;top:25px;font-family:arial;font-size:10px;color:'.$daynamecolor.';position:absolute;">'.$dayname[1].'</span>'.
			'<span style="color:white;left:'.($margo1*2+84+$daych[1]).'px;top:34px;font-family:arial;font-size:25px;color:'.$daynumbercolor.';position:absolute;">'.$day[1].'</span>'.
			'<span style="color:white;left:'.($margo1*2+90).'px;top:64px;font-family:arial;font-size:10px;color:'.$monthcolor.';position:absolute;">'.$month[1].'</span>'.
			'<span style="color:white;left:'.($margo1*2+92+$maxch[1]).'px;top:137px;font-weight:700;font-family:arial;font-size:15px;position:absolute;">'.$max[1].'&deg;</span>'.
			'<span style="color:white;left:'.($margo1*2+92+$minch[1]).'px;top:175px;font-weight:700;font-family:arial;font-size:15px;position:absolute;">'.$min[1].'&deg;</span>';
		}
			if ($meret>=3) {
			echo'<img style="top:0px;left:'.(($margo1*3)+128).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$color.'0.jpg" />'.
			'<img style="top:76px;left:'.($margo1*3+128).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$ikon[2].'" />'.
			'<span style="color:white;left:'.($margo1*3+184+$daynamech[2]).'px;top:25px;font-family:arial;font-size:10px;color:'.$daynamecolor.';position:absolute;">'.$dayname[2].'</span>'.
			'<span style="color:white;left:'.($margo1*3+141+$daych[2]).'px;top:34px;font-family:arial;font-size:25px;color:'.$daynumbercolor.';position:absolute;">'.$day[2].'</span>'.
			'<span style="color:white;left:'.($margo1*3+147).'px;top:64px;font-family:arial;font-size:10px;color:'.$monthcolor.';position:absolute;">'.$month[2].'</span>'.
			'<span style="color:white;left:'.($margo1*3+149+$maxch[2]).'px;top:137px;font-weight:700;font-family:arial;font-size:15px;position:absolute;">'.$max[2].'&deg;</span>'.
			'<span style="color:white;left:'.($margo1*3+149+$minch[2]).'px;top:175px;font-weight:700;font-family:arial;font-size:15px;position:absolute;">'.$min[2].'&deg;</span>';
		}
				if ($meret>=4) {
			echo'<img style="top:0px;left:'.(($margo1*4)+185).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$color.'1.jpg" />'.
			'<img style="top:76px;left:'.($margo1*4+185).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$ikon[3].'" />'.
			'<span style="color:white;left:'.(($margo1*4)+244+$daynamech[3]).'px;top:25px;font-family:arial;font-size:10px;color:'.$daynamecolor.';position:absolute;">'.$dayname[3].'</span>'.
			'<span style="color:white;left:'.($margo1*4+201+$daych[3]).'px;top:34px;font-family:arial;font-size:25px;color:'.$daynumbercolor.';position:absolute;">'.$day[3].'</span>'.
			'<span style="color:white;left:'.($margo1*4+207).'px;top:64px;font-family:arial;font-size:10px;color:'.$monthcolor.';position:absolute;">'.$month[3].'</span>'.
			'<span style="color:white;left:'.($margo1*4+207+$maxch[3]).'px;top:137px;font-weight:700;font-family:arial;font-size:15px;position:absolute;">'.$max[3].'&deg;</span>'.
			'<span style="color:white;left:'.($margo1*4+207+$minch[3]).'px;top:175px;font-weight:700;font-family:arial;font-size:15px;position:absolute;">'.$min[3].'&deg;</span>';
		}
		if ($meret>=5) {
			echo'<img style="top:0px;left:'.(($margo1*5)+242).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$color.'0.jpg" />'.
			'<img style="top:76px;left:'.($margo1*5+242).'px;position:absolute;" src = "../wp-content/plugins/weather-widget-esotanc-weather/img/'.$ikon[4].'" />'.
			'<span style="color:white;left:'.($margo1*5+298+$daynamech[4]).'px;top:25px;font-family:arial;font-size:10px;color:'.$daynamecolor.';position:absolute;">'.$dayname[4].'</span>'.
			'<span style="color:white;left:'.($margo1*5+255+$daych[4]).'px;top:34px;font-family:arial;font-size:25px;color:'.$daynumbercolor.';position:absolute;">'.$day[4].'</span>'.
			'<span style="color:white;left:'.($margo1*5+261).'px;top:64px;font-family:arial;font-size:10px;color:'.$monthcolor.';position:absolute;">'.$month[4].'</span>'.
			'<span style="color:white;left:'.($margo1*5+263+$maxch[4]).'px;top:137px;font-weight:700;font-family:arial;font-size:15px;position:absolute;">'.$max[4].'&deg;</span>'.
			'<span style="color:white;left:'.($margo1*5+263+$minch[4]).'px;top:175px;font-weight:700;font-family:arial;font-size:15px;position:absolute;">'.$min[4].'&deg;</span>';
		}
		
			echo '<div style="position:absolute;top:0px;height:15px;border:0px;padding:0;margin:0px;background-color:'.$upperheadwp.';width:'.$width.'px;height:20px;"></div>';
			echo '<div style="position:absolute;top:20px;height:210px;width:15px;border:0px;padding:0;margin:0px;background-color:'.$sidebarwp.';"></div>';
			echo '<a href="'.$link.'" title="esotanc" style="text-decoration:none;text-transform:none;color:'.$textcolorwp.';left:20px;top:212px;font-weight:400;font-family:arial;font-size:11px;position:absolute;" TARGET="_blank">ESOTANC.HU</a>';
			if ($width>240){echo '<span style="text-decoration:none;text-transform:none;color:'.$textcolorwp.';left:100px;top:212px;font-weight:400;font-family:arial;font-size:11px;position:absolute;"> 
			- '.$lan[0][0].'</span>';}
			echo '<span style="text-decoration:none;text-transform:none;color:'.$textcolorwp.';left:5px;top:2px;font-weight:400;font-family:verdana;font-size:11px;position:absolute;">'.$varos.'</span>'.
			'</div>';
					
					
		/* After widget (defined by themes). */
		echo $after_widget;
	}
 

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['esoid'] = strip_tags( $new_instance['esoid'] );
		$instance['name'] = strip_tags( $new_instance['name'] );
		$instance['size'] = strip_tags( $new_instance['size'] );
		$instance['color'] = strip_tags( $new_instance['color'] );
		$instance['language'] = strip_tags( $new_instance['language'] );
		$instance['textcolorwp'] = strip_tags( $new_instance['textcolorwp'] );
		$instance['upperheadwp'] = strip_tags( $new_instance['upperheadwp'] );
		$instance['sidebarwp'] = strip_tags( $new_instance['sidebarwp'] );
		$instance['daynamecolor'] = strip_tags( $new_instance['daynamecolor'] );
		$instance['daynumbercolor'] = strip_tags( $new_instance['daynumbercolor'] );
		$instance['monthcolor'] = strip_tags( $new_instance['monthcolor'] );
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
		'title' => __('weather', 'weather'), 
		'esoid' => __('budapest', 'budapest'),
		'name' => __('Budaörs', 'Budaörs'),
		'size' => __('300', '300'),
		'color' => __('color', 'color'),
		'language' => __('en', 'en'),
		'textcolorwp' => __('black', 'black'),
		'upperheadwp' => __('#FFFFFF', '#FFFFFF'),
		'sidebarwp' => __('#FFFFFF', '#FFFFFF'),
		'daynamecolor' => __('#757575', '#757575'),
		'daynumbercolor' => __('#545ACE', '#545ACE'),
		'monthcolor' => __('#F98305', '#F98305')
		
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Title Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','title'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Esoid : esoid Input -->
		
		<p>
			<label for="<?php echo $this->get_field_id( 'esoid' ); ?>"><?php _e('EsoID: Find the nearest forecast site to your town! Find the ID here:<a href="http://esotanc.hu/wordpress-weather-widget" TARGET="_blank">EsoID</a>', 'esoid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'esoid' ); ?>" name="<?php echo $this->get_field_name( 'esoid' ); ?>" value="<?php echo $instance['esoid']; ?>" style="width:100%;" />
		</p>
		
		<!-- Name : Name Input -->
		
		<p>
			<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e('Enter your city name:', 'name'); ?></label>
			<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:100%;" />
		</p>
		</p>
		
		<!-- Size : Size Input -->
		
		 <p>
			<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e('Size of widget:(72px-360px)', 'size'); ?></label></br></br>
			<input id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" value="<?php echo $instance['size']; ?>" style="width:100%;" />
		</p>
		
		<!-- Color : Color Input -->
		
		 <p>
			<label for="<?php echo $this->get_field_id( 'color' ); ?>"><?php _e('Color scheme of widget:', 'color'); ?></label></br></br>
			<input type="radio" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" value="color" style="width:15%;left:20px;"<?php if (($instance['color'])=="color") {echo 'checked';}?> />&nbsp;Color</br>
			<input type="radio" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" value="white" style="width:15%;left:20px;" <?php if (($instance['color'])=="white") {echo 'checked';}?>/>&nbsp;White</br>
			<input type="radio" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" value="black" style="width:15%;left:20px;" <?php if (($instance['color'])=="black") {echo 'checked';}?>/>&nbsp;Black</br>
			<input type="radio" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" value="custom" style="width:15%;left:20px;" <?php if (($instance['color'])=="custom") {echo 'checked';}?>/>&nbsp;Custom - Fill the values below!</br>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'textcolorwp' ); ?>"><?php _e('Description text color(e.g.:#498765):', 'textcolorwp'); ?></label>
			<input id="<?php echo $this->get_field_id( 'textcolorwp' ); ?>" name="<?php echo $this->get_field_name( 'textcolorwp' ); ?>" value="<?php echo $instance['textcolorwp']; ?>" style="width:60%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'upperheadwp' ); ?>"><?php _e('Background color of description(e.g.:#498765):', 'upperheadwp'); ?></label>
			<input id="<?php echo $this->get_field_id( 'upperheadwp' ); ?>" name="<?php echo $this->get_field_name( 'upperheadwp' ); ?>" value="<?php echo $instance['upperheadwp']; ?>" style="width:60%;" />
		</p>
		
			<p>
			<label for="<?php echo $this->get_field_id( 'sidebarwp' ); ?>"><?php _e('Left sidebar color (e.g.:#498765)::', 'sidebarwp'); ?></label>
			<input id="<?php echo $this->get_field_id( 'sidebarwp' ); ?>" name="<?php echo $this->get_field_name( 'sidebarwp' ); ?>" value="<?php echo $instance['sidebarwp']; ?>" style="width:60%;" />
		</p>
		
			<p>
			<label for="<?php echo $this->get_field_id( 'daynamecolor' ); ?>"><?php _e('Dayname color(e.g.:#498765):', 'daynamecolor'); ?></label>
			<input id="<?php echo $this->get_field_id( 'daynamecolor' ); ?>" name="<?php echo $this->get_field_name( 'daynamecolor' ); ?>" value="<?php echo $instance['daynamecolor']; ?>" style="width:60%;" />
		</p>
			<p>
			<label for="<?php echo $this->get_field_id( 'daynumbercolor' ); ?>"><?php _e('Day number color(e.g.:#498765):', 'daynumbercolor'); ?></label>
			<input id="<?php echo $this->get_field_id( 'daynumbercolor' ); ?>" name="<?php echo $this->get_field_name( 'daynumbercolor' ); ?>" value="<?php echo $instance['daynumbercolor']; ?>" style="width:60%;" />
		</p>
			<p>
			<label for="<?php echo $this->get_field_id( 'monthcolor' ); ?>"><?php _e('Month name color(e.g.:#498765):', 'monthcolor'); ?></label>
			<input id="<?php echo $this->get_field_id( 'monthcolor' ); ?>" name="<?php echo $this->get_field_name( 'monthcolor' ); ?>" value="<?php echo $instance['monthcolor']; ?>" style="width:60%;" />
		</p>
		
						
		<!-- Language : Language Input -->
		
			 <p>
			<label for="<?php echo $this->get_field_id( 'language' ); ?>"><?php _e('Language: (Tip:you can edit custom language file!)', 'language'); ?></label></br></br>
			<input type="radio" id="<?php echo $this->get_field_id( 'language' ); ?>" name="<?php echo $this->get_field_name( 'language' ); ?>" value="en" style="width:15%;left:20px;"<?php if (($instance['language'])=="en") {echo 'checked';}?> />&nbsp;English</br>
			<input type="radio" id="<?php echo $this->get_field_id( 'language' ); ?>" name="<?php echo $this->get_field_name( 'language' ); ?>" value="hu" style="width:15%;left:20px;" <?php if (($instance['language'])=="hu") {echo 'checked';}?>/>&nbsp;Hungarian</br>
			<input type="radio" id="<?php echo $this->get_field_id( 'language' ); ?>" name="<?php echo $this->get_field_name( 'language' ); ?>" value="custom" style="width:15%;left:20px;" <?php if (($instance['language'])=="custom") {echo 'checked';}?>/>&nbsp;Custom Language File</br>
		</p>
 

	<?php
	}
	
}
?>