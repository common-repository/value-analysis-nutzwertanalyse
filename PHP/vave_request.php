<?php
add_action('wp_ajax_vave_request', 				'vave_request', 10);
add_action('wp_ajax_nopriv_vave_request', 		'vave_request', 10);
//do_action( 'wp_ajax_test_xox', 10);
//do_action( 'wp_ajax_nopriv_test_xox', 10);

function vave_request(){
	if ( !isset( $_POST['nonce'] )) {
		return;
	}
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['nonce'], 'vave_nonce' ) ) {
		return;
	}
  if ( ! $_POST['data'] ) {
		return;
	}
  $dataArr = json_decode( sanitize_text_field( stripslashes($_POST['data']) ));
  $respArr  = array();


  foreach($dataArr as $key => $data){
    $resp         = array();
    $resp["data"] = "";
    $resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
    $resp["msg"]  = __( "Fehler: no Action defined", "value_analysis" );
    if(!isset($data->action)){
      $respArr [] = $resp;
      continue;
    }

    switch ($data->action) {
      case 'readVaData':
            $resp["data"] = [];
          if ( !$data->postID ) {
            $resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
            $resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
        	}else{

            $resp["data"] = get_post_meta( $data->postID, 'vave_data', true );

            if(!$resp["data"]){
              $post_content = get_post($data->postID);
              $resp["data"] = '[{"id":1,"parentId":null,"requirements":"'.$post_content->post_title.'",}]';
            }

            $resp["type"] = "success"; /* "info", "success", "warning" "error"  */
            $resp["msg"] = __( "Daten eingelesen", "value_analysis" );
          }
        break;
      case 'cudVaData':
            $resp["data"] = [];
          if ( !$data->postID ) {
            $resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
            $resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
        	}else{
            if(update_post_meta( $data->postID, 'vave_data', $data->data )){
              $resp["type"] = "success"; /* "info", "success", "warning" "error"  */
              $resp["msg"] = __( "Daten aktualisiert", "value_analysis" );
            }else{
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Daten waren bereits aktualisiert", "value_analysis" );
						}
          }
        break;
			case 'dragVaData':
						$resp["data"] = [];
					if ( !$data->postID ) {
						$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
					}else{
						if(update_post_meta( $data->postID, 'vave_data', $data->data )){
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Drag&Draop Daten aktualisiert", "value_analysis" );
						}else{
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Drag&Draop Daten waren bereits aktualisiert", "value_analysis" );
						}
					}
				break;
			case 'getWeightingVs':
						$resp["data"] = [];
					if ( !$data->postID ) {
						$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
					}else{

						$resp["data"] = get_post_meta( $data->postID, 'vave_weighting_data', true );

						if(!$resp["data"]){
							$resp["data"] = '[]';
						}

						$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Gewichtungs-Daten eingelesen", "value_analysis" );
					}
				break;
			case 'setWeightingVs':
						$resp["data"] = [];
					if ( !$data->postID ) {
						$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
					}else{
						if(update_post_meta( $data->postID, 'vave_weighting_data', $data->data )){
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Gewichtungs-Daten gespeichert", "value_analysis" );
						}else{
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Gewichtungs-Daten waren bereits gespeichert", "value_analysis" );
						}
					}
				break;
			case 'setOpportunitiesVs':
						$resp["data"] = [];
					if ( !$data->postID ) {
						$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
					}else{
						if(update_post_meta( $data->postID, 'vave_opportunities_data', $data->data )){
							$resp["xxxx"] = $data->data; /* "info", "success", "warning" "error"  */
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Möglichkeit gespeichert", "value_analysis" );
						}else{
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Möglichkeit war bereits gespeichert", "value_analysis" );
						}
					}
				break;
			case 'delOpportunitiesVs':
						$resp["data"] = [];
					if ( !$data->postID ) {
						$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
					}else{
						if(update_post_meta( $data->postID, 'vave_opportunities_data', $data->data )){
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Möglichkeit gelöscht", "value_analysis" );
						}else{
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Möglichkeit war bereits gelöscht", "value_analysis" );
						}
					}
				break;
			case 'setOppChooseVs':
						$resp["data"] = [];
					if ( !$data->postID ) {
						$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
					}else{
						if(update_post_meta( $data->postID, 'vave_opp_choose_data', $data->data )){
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Auswahl gespeichert", "value_analysis" );
						}else{
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Auswahl war bereits gespeichert", "value_analysis" );
						}
					}
				break;
			case 'setResultVs':
						$resp["data"] = [];
					if ( !$data->postID ) {
						$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
					}else{
						if(is_string($data->data)){
							if(update_post_meta( $data->postID, 'vave_result_data', $data->data )){
								$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
								$resp["msg"] = __( "Result gespeichert", "value_analysis" );
							}else{
								$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
								$resp["msg"] = __( "Result war bereits gespeichert", "value_analysis" );
							}
						}else{
							$resp["type"] = "error"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "no String in case: setResultVs", "value_analysis" );
						}
					}
				break;
			case 'vave_editbleSwitch':
						$resp["data"] = [];
					if ( !$data->postID ) {
						$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
					}else{
						if(update_post_meta( $data->postID, 'vave_editble_switch', $data->data )){
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Lock gespeichert", "value_analysis" );
						}else{
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Lock war bereits gespeichert", "value_analysis" );
						}
					}
				break;
			case 'cudVaDataDash':
						$resp["data"] = [];
					if ( !$data->postID ) {
						$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
						$resp["msg"] = __( "Warning! postID fehlt", "value_analysis" );
					}else{
						if(update_post_meta( $data->postID, 'vave_dashData', $data->data )){
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Daten aktualisiert", "value_analysis" );
						}else{
							$resp["type"] = "success"; /* "info", "success", "warning" "error"  */
							$resp["msg"] = __( "Daten waren bereits aktualisiert", "value_analysis" );
						}
					}
				break;
      default:
					$resp["data"] = "";
					$resp["type"] = "warning"; /* "info", "success", "warning" "error"  */
					$resp["msg"]  = __( "Fehler: no Action defined", "value_analysis" );
        break;
    }
    $respArr[$key] = $resp;
  }
  echo html_entity_decode(json_encode ( $respArr ));
  wp_die();
}
?>
