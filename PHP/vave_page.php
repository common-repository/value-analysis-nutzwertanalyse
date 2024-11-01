<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package gridsby
 */

get_header();


$vave_currentUsrHash = isset( $_GET["vave_usrid"] ) ? sanitize_text_field(htmlspecialchars($_GET["vave_usrid"])) : "";

$vave_dashData = get_post_meta( get_the_ID(), 'vave_dashData', true );

$vave_dashData_usersHash = array();
if(isset($vave_dashData->users)){
	foreach ($vave_dashData->users as $usrVs) {
		$vave_dashData_usersHash[$usrVs->usrHash] = $usrVs;
	}
}
global $current_user;
wp_get_current_user();

if(
	empty($vave_currentUsrHash)
	&&
	is_user_logged_in()
){
	if(
		empty($vave_dashData)
		&&
		get_post_field ('post_author', get_the_ID()) != get_current_user_id()
	){
		wp_die( __("Sie haben nicht die nötigen Rechte um diese Seite auf zu rufen.", "value_analysis"));
	}else{
		if(
			get_post_field ('post_author', get_the_ID()) == get_current_user_id()
			&&
			empty($vave_dashData->users)
		){
			$vave_partsurl = parse_url( home_url() );
			$vave_baseurl  = "{$vave_partsurl['scheme']}://{$vave_partsurl['host']}" . add_query_arg( NULL, NULL );
			$vave_currentUsrHash = vave_makeid(15);
			$vave_newURL = vave_addURLParameter($vave_baseurl, "vave_usrid", $vave_currentUsrHash);
			vave_setUrl( $vave_newURL );

			$vave_dashData = json_decode('{"masterUsr":'.intval($current_user->ID).',"users":[{"usrID":'.$current_user->ID.',"usrName":"'.$current_user->display_name.'", "usrHash":"'.$vave_currentUsrHash.'"}]}');
			update_post_meta( get_the_ID(), 'vave_dashData', $vave_dashData );
		}else if(
			get_post_field ('post_author', get_the_ID()) == get_current_user_id()
			&&
			!empty($vave_dashData->users)
		){
			foreach($vave_dashData->users as $usrVs){
				if($usrVs->usrID == $current_user->ID){
					$vave_partsurl = parse_url( home_url() );
					$vave_baseurl  = "{$vave_partsurl['scheme']}://{$vave_partsurl['host']}" . add_query_arg( NULL, NULL );
					$vave_newURL = vave_addURLParameter($vave_baseurl, "vave_usrid", $usrVs->usrHash);
					vave_setUrl( $vave_newURL );
					break;
				}
			}
		}else{
			wp_die( __("Sie haben nicht die nötigen Rechte um diese Seite auf zu rufen.", "value_analysis"));
		}
	}
}else{
	if( !isset($vave_dashData_usersHash[$vave_currentUsrHash]) ){
		wp_die( __("Sie haben nicht die nötigen Rechte um diese Seite auf zu rufen.", "value_analysis"));
	}
}


$vave_dataTmp = get_post_meta( get_the_ID(), 'vave_data', true );
if(empty($vave_dataTmp)){
	$vave_dataTmpActiveID = "vave_tabDash";
}else{
	$vave_dataTmpActiveID = "vave_tabWork";
}
?>


<div class="grid grid-pad vave_grid">
	<div class="va-wrapper content-wrapper">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

                <?php while ( have_posts() ) : the_post(); ?>

                    <?php get_template_part( 'content', 'page' ); ?>

										<div id="vaMainContainer" class="vaMainContainer">
											<div id="vave_tabstrip">
                            <ul>
                               <li id="vave_tabDash" >
                                    <?php _e("Übersicht", "value_analysis"); ?>
                                </li>
																<li id="vave_tabWork" >
																		<?php _e("Nutzwertanalyse", "value_analysis"); ?>
																</li>
                                <li id="vave_tabResult" >
                                    <?php _e("Auswertung", "value_analysis"); ?>
                                </li>
                            </ul>
                           <div>
                                <h2><?php _e("Übersicht", "value_analysis"); ?></h2>
																<div id="vave_dash_container">
																	<div id="vave_dash_grid"></div>
																	<button id="vave_dash_grid_nextStepBtn" type="button"><?php _e("Next Step", "value_analysis"); ?></button>
																</div>
                            </div>
                            <div>
                                <h2><?php _e("Nutzwertanalyse", "value_analysis"); ?></h2>
																<div id="vave_main_container">
																	<div id="vave_treelist_requirements"></div>
																</div>
                            </div>
														<div>
                                <h2><?php _e("Auswertung", "value_analysis"); ?></h2>
                                <div id="vave_result_container"></div>
                            </div>
                        </div>
										</div>

										<style>
											html { font-size: 14px;
												font-family: Arial, Helvetica, sans-serif;
												}
										</style>

										<script>

								/*			jQuery('<link/>', {
												rel: 'stylesheet',
												type: 'text/css',
												href: 'https://kendo.cdn.telerik.com/2020.3.1118/styles/kendo.material-v2.min.css'
												href: 'https://kendo.cdn.telerik.com/2021.1.119/styles/kendo.bootstrap-v4.min.css'
											}).appendTo('head'); */
									//		jQuery.getScript("<?php echo vave_URL ?> /JS/kendo.all.min.js");
									//		jQuery.getScript("<?php echo vave_URL ?> /JS/kendo.all.min.js.map");

										var vave_staticNotificationObj;
										var vave_tabstripObj																				= {};
										var nextRequirementID 																			= 0;				/* nächste ID beim erstellen eines neuen Requirement */
										var vave_treelistVs																					= [];
										var vave_weightingVs																				= {};
										var vave_weightVs																						= {};
												vave_weightVs["<?php echo $vave_currentUsrHash ?>"]			= {};				/* Alle Gewichtungen des aktuellen Users */
										var vave_allWeightChoose																		= 0;				/* wurden alle Gewichtungen gewichtet? 0 => nein - 1 => ja */
										var vave_allOppChoose																				= 0;				/* wurden alle Möglichkeiten ausgewählt? 0 => nein - 1 => ja */
										var vave_isMasterUsr 																				= <?php echo get_current_user_id(); ?>;
//										var vave_currentUsrId 																		= <?php echo get_current_user_id(); ?>;
										var vave_currentUsrHash 																		= "<?php echo $vave_currentUsrHash; ?>";
										var vave_currentPostId 																			= <?php echo get_the_ID(); ?>;
										var vave_treeList_dS_requirements 													= {};
										var vave_treeListObj_requirements														= {};
										var vave_requirements_string_length													= 23;				/* Die maximale Länge der Requirement Strings auf dem Vote Button */
										var vave_opportunitiesVs																		= {};
										var vave_oppChooseVs																				= {};
												vave_oppChooseVs["<?php echo $vave_currentUsrHash ?>"]	= {};				/* Alle Möglichkeiten-Auswahl des aktuellen Users */
										var vave_resultVs																						= {};
												vave_resultVs["<?php echo $vave_currentUsrHash ?>"]			= {};				/* Alle Resultate des aktuellen Users */
										var vave_structureEditSwitchObj															= {};
										var vave_structureEdit																			= 0;				/* darf die struktur editiert werden add move del*/
										var vave_dashGrid_ds 																				= {};
										var vave_dashGrid_obj 																			= {};
										var vave_dashData 																					= {};
										var vave_langTxtArr																					= [];
												vave_langTxtArr["vave_newOpportunitie"]									= "<?php _e("neue Möglichkeit", 																																"value_analysis"); ?>";
												vave_langTxtArr["vave_newRequirement"]									= "<?php _e("neue Anforderung", 																																"value_analysis"); ?>";
												vave_langTxtArr["vave_usrNew"]													= "<?php _e("neuer User",																																				"value_analysis"); ?>";
												vave_langTxtArr["vave_chooseTxt_1"]											= "<?php _e("Wie gut erfüllt die Möglichkeit, &laquo;", 																				"value_analysis"); ?>";
												vave_langTxtArr["vave_chooseTxt_2"]											= "<?php _e("&raquo;, die Anforderung, &laquo;", 																								"value_analysis"); ?>";
												vave_langTxtArr["vave_chooseTxt_3"]											= "<?php _e("&raquo;?", 																																				"value_analysis"); ?>";
												vave_langTxtArr["vave_choose0"]													= "<?php _e("schlecht", 																																				"value_analysis"); ?>";
												vave_langTxtArr["vave_choose1"]													= "<?php _e("gut", 																																							"value_analysis"); ?>";
												vave_langTxtArr["vave_choose2"]													= "<?php _e("sehr gut", 																																				"value_analysis"); ?>";
												vave_langTxtArr["vave_structSwitch0"]										= "<?php _e("edit unlocked", 																																		"value_analysis"); ?>";
												vave_langTxtArr["vave_structSwitch1"]										= "<?php _e("edit locked", 																																			"value_analysis"); ?>";
												vave_langTxtArr["vave_structSwitchReload"]							= "<?php _e("Die Seite muss neu geladen werden, nur MasterUser können dies rückgängig machen.", "value_analysis"); ?>";
												vave_langTxtArr["vave_dashAlertUsrLink"]								= "<?php _e("User access token fürs Bewerten:", 																								"value_analysis"); ?>";
												vave_langTxtArr["vave_notDropable"]											= "<?php _e("Hierhin kann das Element nicht verschoben werden",																	"value_analysis"); ?>";
												vave_langTxtArr["vave_rootNotDropable"]									= "<?php _e("Die Grundanforderung darf nicht verschoben werden",																"value_analysis"); ?>";
												vave_langTxtArr["vave_usrLinkCopyClipboard"]						= "<?php _e("Der User access token wurde in die Zwischenablage kopiert",												"value_analysis"); ?>";
												vave_langTxtArr["vave_reqOder"]													= "<?php _e("oder",																																							"value_analysis"); ?>";
												vave_langTxtArr["vave_reqWhatIsImportant"]							= "<?php _e("Was ist wichtiger",																																"value_analysis"); ?>";




											jQuery(document).ready(function(){
												vave_tabstripObj = jQuery("#vave_tabstrip").kendoTabStrip({
													animation:  {
														open: {
															effects: "fadeIn"
														}
													},
													activate: function(e){
														switch (e.item.id) {
															case "vave_tabDash":
																vave_initDashBoard();
																break;
															case "vave_tabWork":
																vave_initMainVa();
																break;
															case "vave_tabResult":
																vave_initResult();
																break;
															default:

														}
													}
												}).data("kendoTabStrip");

												setTimeout(function(){
													switch ("<?php echo $vave_dataTmpActiveID ?>") {
														case "vave_tabDash":
															vave_tabstripObj.select(0);
															break;
														case "vave_tabWork":
															vave_tabstripObj.select(1);
															break;
														case "vave_tabResult":
															vave_tabstripObj.select(2);
															break;
														default:
															vave_tabstripObj.select(0);
													}
												}, 30);


												<?php
													$vave_treelistVs = get_post_meta( get_the_ID(), 'vave_data', true );
													if(empty($vave_treelistVs)){
														$vave_post_content = get_post(get_the_ID());
														$vave_treelistVs = '[{"id":1, "parentId":null, "requirements":"'.$vave_post_content->post_title.'",}]';
													}

													if(!empty($vave_treelistVs)){
														?>
														vave_treelistVs = vave_stringToJson(<?php echo html_entity_decode(json_encode($vave_treelistVs)); ?>);
														<?php
													}else{
														?>
														vave_treelistVs = [];
														<?php
													}
												?>


												<?php
													$vave_weighting_data = get_post_meta( get_the_ID(), 'vave_weighting_data', true );
													if(!empty($vave_weighting_data)){
														?>
														vave_weightingVs = vave_stringToJson(<?php echo html_entity_decode(json_encode($vave_weighting_data)); ?>);
														<?php
													}
												?>

												<?php
													$vave_opportunities_data = get_post_meta( get_the_ID(), 'vave_opportunities_data', true );
													if(!empty($vave_opportunities_data)){
														?>
														vave_opportunitiesVs = vave_stringToJson(<?php echo html_entity_decode(json_encode($vave_opportunities_data)); ?>);
														<?php
													}
												?>
												vave_opportunitiesVs_addHeaderTemplate();

												<?php
													$vave_oppChooseVs = get_post_meta( get_the_ID(), 'vave_opp_choose_data', true );
													if(!empty($vave_oppChooseVs)){
														?>
														vave_oppChooseVs = vave_stringToJson(<?php echo html_entity_decode(json_encode($vave_oppChooseVs)); ?>);
														<?php
													}
												?>

												<?php
													$vave_resultVs = get_post_meta( get_the_ID(), 'vave_result_data', true );
													if(!empty($vave_resultVs)){
														?>
														vave_resultVs = vave_stringToJson(<?php echo html_entity_decode(json_encode($vave_resultVs)); ?>);
														<?php
													}
													?>

												<?php
													$vave_structureEdit = get_post_meta( get_the_ID(), 'vave_editble_switch', true );
													if(!empty($vave_structureEdit)){
														?>
														vave_structureEdit = <?php echo $vave_structureEdit == 1 ? "true" : "false"; ?>;
														<?php
													}
												?>


												<?php
													if(!empty($vave_dashData)){
														?>
														vave_dashData = vave_stringToJson(<?php echo html_entity_decode(json_encode($vave_dashData)); ?>);
														<?php
													}else{
														?>
														vave_dashData = {};
														<?php
													}
												?>


												<?php
													if(
														is_user_logged_in() == 1
														&&
														get_current_user_id()  == $vave_dashData->masterUsr
													){
														$vave_isMasterUsr = 1;
													}else{
														$vave_isMasterUsr = 0;
													}
												?>


												if(
													<?php echo $vave_isMasterUsr ?> == 1
												){
													vave_isMasterUsr = 1;
												}else{
													vave_isMasterUsr = 0;
												}

											});

/* ------------------------------------------------------------------------------ Main VA ---------------------------------------------------------------- */

											function vave_initMainVa(){
												vave_treeListObj_requirements = {};
												vave_treeList_dS_requirements = {};
												jQuery("#vave_treelist_requirements").html("");

								        vave_treeList_dS_requirements = new kendo.data.TreeListDataSource({
								                transport: {
								                  read: function(e){
/*
																		var data 						= [];
																				data[0] 				= {};
																				data[0].action 	= "readVaData";
																				data[0].postID 	= <?php echo get_the_ID(); ?>;

																		vave_saveAjax(data, e);
*/
																			e.success(vave_treelistVs);
								                  },
								                  update: function(e){
								                    // console.log("update");
																		// console.log(e);
																		var data 						= [];
																				data[0] 				= {};
																				data[0].action 	= "cudVaData";
																				data[0].postID 	= <?php echo get_the_ID(); ?>;
																				data[0].data		= [];
																		var vaData 					= vave_treeList_dS_requirements.data();
																				for(var i = 0, leni = vaData.length; i < leni; i++) {
																					var tmpData 							= {};
																							tmpData.id 						= vaData[i].id;
																							tmpData.parentId 			= vaData[i].parentId;
																							tmpData.requirements 	= vaData[i].requirements;
																							tmpData.weighting 		= vaData[i].weighting ? vaData[i].weighting : {};

																					data[0].data.push(tmpData);
																				}
																				// console.log(data[0].data);

																		vave_saveAjax(data, e);
								                  },
								                  create: function(e){
								                    // console.log("create");
																		// console.log(e);
																		var data 						= [];
																				data[0] 				= {};
																				data[0].action 	= "cudVaData";
																				data[0].postID 	= <?php echo get_the_ID(); ?>;
																				data[0].data		= [];
																		var vaData 					= vave_treeList_dS_requirements.data();
																				for(var i = 0, leni = vaData.length; i < leni; i++) {
																					var tmpData 							= {};
																							tmpData.id 						= vaData[i].id;
																							tmpData.parentId 			= vaData[i].parentId;
																							tmpData.requirements 	= vaData[i].requirements;
																							tmpData.weighting 		= vaData[i].weighting ? vaData[i].weighting : {};

																					data[0].data.push(tmpData);
																				}
																				// console.log(data[0].data);

																		vave_saveAjax(data, e);
								                  },
																	destroy: function(e){
																		// console.log("destroy");
																		// console.log(e);
																		var data 						= [];
																				data[0] 				= {};
																				data[0].action 	= "cudVaData";
																				data[0].postID 	= <?php echo get_the_ID(); ?>;
																				data[0].data		= [];
																		var vaData 					= vave_treeList_dS_requirements.data();
																				for(var i = 0, leni = vaData.length; i < leni; i++) {
																					var tmpData 							= {};
																							tmpData.id 						= vaData[i].id;
																							tmpData.parentId 			= vaData[i].parentId;
																							tmpData.requirements 	= vaData[i].requirements;
																							tmpData.weighting 		= vaData[i].weighting ? vaData[i].weighting : {};

																					data[0].data.push(tmpData);
																				}
																				// console.log(data[0].data);

																		vave_saveAjax(data, e);
																	},
								                  parameterMap: function(options, operation) {
								                      if (operation !== "read" && options.models) {
								                          return {models: kendo.stringify(options.models)};
								                      }
								                  }
								                },
								                batch: false,
																autoSync: false,
								                schema: {
								                    model: {
								                        id: "id",
								                        parentId: "parentId",
								                        fields: {
								                            id: 						{ type: "number", editable: false, nullable: false, defaultValue: 0 },
								                            parentId: 			{ nullable: true },
																						<?php
																						if( $vave_isMasterUsr == 1 || $vave_structureEdit != 1 ){
																							?>
																							requirements: 	{ type: "string", editable: true,  defaultValue: vave_langTxtArr["vave_newRequirement"], validation: { required: true } },
																							<?php
																						}else{
																							?>
																							requirements: 	{ type: "string", editable: false,  validation: { required: true } },
																							<?php
																						}
																						?>
																						pan:						{	type: "string", editable: false, defaultValue: "" },
																						weighting: 			{ type: "string", editable: false, defaultValue: "" },
																						weight: 				{ type: "string", editable: false, defaultValue: "" },
																						opportunities: 	{ type: "string", editable: false, defaultValue: "" },
								                        },
								                        expanded: true
								                    }
								                },
																change: function(e){
																	/* funktioniert nach treeObj.setOptions() nicht mehr	*/
// console.log(" ---------------------- treeliste dS change --------------------------------- ");
// console.log(e.action);
// console.log(e);
// console.log(this.view());

																	if(vave_inArray(e.action, ["add"]) ){
// console.log(" ---------------------- treeliste dS add --------------------------------- ");
																		vave_treeList_dS_requirements.data(this.view());

																	}
																	if(vave_inArray(e.action, ["remove", "itemchange"]) ){
// console.log(" ---------------------- treeliste dS sync() --------------------------------- ");
																		vave_treeList_dS_requirements.data(this.view());

// console.log(vave_treeList_dS_requirements.hasChanges());

																			vave_treeListObj_requirements.saveChanges();
																	}
																},
								            });

								        vave_treeListObj_requirements = jQuery("#vave_treelist_requirements").kendoTreeList({
								            dataSource: vave_treeList_dS_requirements,
														autoBind: false,
														resizeable: true,
								            toolbar: [
															<?php
																if($vave_structureEdit != 1 ){
																	?>
																	"save",
																	"cancel",
																	<?php
																}
																?>
															<?php
															if( $vave_isMasterUsr == 1 || $vave_structureEdit != 1 ){
																?>
																{
																	name: "switch",
																	template: '<div id="vave_lockSwitch"><input type="checkbox" id="vave_structureEdit-switch" aria-label="<?php _e("Struktur editieren", "value_analysis"); ?>" /></div>',
																},
																<?php
															}
															?>
															"search"
														],
								            editable: {
															<?php
															if($vave_structureEdit != 1 ){
																?>
																move: {
																		reorderable: true
																},
																<?php
															}
															?>
															mode: "incell",
															createAt:"bottom",
								            },
														messages: {
															 commands: {
																 cancel: "<?php _e("Änderungen verwerfen", "value_analysis"); ?>",
																 create: "<?php _e("Neuen Anforderung erstellen", "value_analysis"); ?>",
																 destroy: "<?php _e("Anforderung löschen", "value_analysis"); ?>",
																 save: "<?php _e("Anforderungen speichern", "value_analysis"); ?>",
															 }
														},
														dataBound: function (e){
															// console.log(" ---------------------- treeliste obj dataBound --------------------------------- ");
															// console.log(e);
															// console.log(vave_treeList_dS_requirements.data());

															var items = e.sender.items();

															for (var i = 0; i < items.length; i++) {
																var dataItem = e.sender.dataItem(items[i]);
																if(vave_isset(dataItem)  && vave_isset(dataItem.id) && parseInt(dataItem.id) >= nextRequirementID){
																	nextRequirementID = parseInt(dataItem.id) + 1;
																}
															}

															var hirRequirementsVs = vave_treeList_dS_requirements.data();

															hirRequirementsVs 	= vave_unflatten(hirRequirementsVs);
															var vave_weightingTxt = [];
															if( Object.keys(vave_weightVs[vave_currentUsrHash]).length > 0){
																vave_weightingTxt 		= vave_arrayItemsFlatten(hirRequirementsVs, vave_weightingTxt);

																for (var i = 0; i < items.length; i++) {
																	var dataItem = e.sender.dataItem(items[i]);
																	var vave_weightingHtml = "";
																	if(vave_isset(dataItem)  && vave_isset(dataItem.id) && vave_weightingTxt[dataItem.id]){
																		vave_weightingHtml = vave_langTxtArr["vave_reqWhatIsImportant"] + "&nbsp;" + vave_weightingTxt[dataItem.id];
																	}
																	var row = jQuery(items[i]);
																	row.find("td:eq(3)").html(vave_weightingHtml);

																	if( vave_isset(dataItem) && vave_isset(dataItem.parentId) && dataItem.parentId == null ){
																		var weightingHTML = "";
																				weightTxt	= 100;
																				weightTxt = kendo.format("{0:p0}", 1);
																				row.find("td:eq(2)").html("<span class='vave_nonAddWeighting'>(" + weightTxt + ")</span>");

																				var tmpW = row.find("td:eq(2)").html();
																						tmpW = vave_str_replace("p0S", "", tmpW)
																				row.find("td:eq(2)").html(tmpW);

																	}else{
																		row.find("td:eq(1)").html("<span class='k-icon k-i-pan vave_pan'></span>");
																	}
																	dataItem.weight 		= "&nbsp;";					/* Wenn Zelle leer, wird p0S hinzugefügt, warum auch immer */
																	dataItem.weighting 	= "&nbsp;";

																	var tmpW = row.find("td:eq(2)").html();
																			tmpW = vave_str_replace("p0S", "", tmpW)
																	row.find("td:eq(2)").html(tmpW);

																}
															}
															vave_evalWeigth();
		                        },
														edit: function(e){
															/*
															console.log(" ------------- edit ----------");
															console.log(e.model.requirements);
															*/
															if (e.model.id == 0) {
																e.model.id = nextRequirementID;
																nextRequirementID++;
															}														
														},
														cellClose:  function(e) {
															/*
															console.log(" ---------------------- treeliste obj cellClose --------------------------------- ");
															console.log(e);
															console.log(e.model.requirements);
															var newRequirementsVs = jQuery( "input[name=requirements]" ).val();
															console.log(newRequirementsVs); 
															*/
															var newRequirementsVs = jQuery( "#vave_main_container input[name=requirements]" ).val();
															if(e.model.requirements != newRequirementsVs){
																e.model.requirements = newRequirementsVs;

																var dataItem = vave_treeListObj_requirements.dataItem(jQuery(e.container));
																dataItem.set('requirements', newRequirementsVs + " ");		/* Wenn nach dem Editieren in der Treelist rinrd Feldes in die Liste geklickt wird um zu speichern, so wird der change nicht erkannt und saveChange() wird nicht ausgeführt... Wenn ausserhalb der Treelist geklickt wird so funktioniert es. Deshalb ein Leerzeichen hinten anhängen und es ist ein change */
															}

															if(vave_inArray(e.type, ["save"]) ){
																vave_treeListObj_requirements.saveChanges();

																vave_evalWeigth();																			/* eval weighting														*/
																vave_addOpportunitiesChoose();

																vave_treeListObj_requirements.refresh();								/* generate weighting Buttons in dataBound	*/

															}
														},
														dragend: function(e) {
															vave_evalWeigth();																			/* eval weighting														*/

															var data 						= [];
																	data[0] 				= {};
																	data[0].action 	= "dragVaData";
																	data[0].postID 	= <?php echo get_the_ID(); ?>;
																	data[0].data		= [];
															var updateData			= [];
															var vaData 					= vave_treeList_dS_requirements.data();
																	for(var i = 0, leni = vaData.length; i < leni; i++) {
																		// console.log(vaData[i].requirements);
																		var tmpData 							= {};
																				tmpData.id 						= vaData[i].id;
																				tmpData.parentId 			= vaData[i].parentId;
																				tmpData.requirements 	= vaData[i].requirements;

																				tmpData.weighting 		= vaData[i].weighting ? vaData[i].weighting : {};
																				tmpData.weight 				= 0;

																				data[0].data.push(tmpData);
																	}
															vave_saveAjax(data, "");

															vave_treeList_dS_requirements.data(data[0].data);
															setTimeout(function(){
																vave_treeList_dS_requirements.sync();
															}, 200);
												    },
														drop: function(e){
															/* e.setValid(false); */
														},
														dragstart: function(e) {
															if (e.source.id == 1) { /* das root Elemnet soll nicht verschoben werden können */
																kendo.alert(vave_langTxtArr["vave_rootNotDropable"]);
																console.log(" ------------- drag not allowed root ----------------------");
																return;
															}
														},
														drag: function(e) {

															if(e.originalEvent.originalTarget.className == "k-header"){
																kendo.alert(vave_langTxtArr["vave_notDropable"]);
																jQuery(".k-alert").css({"left": "40%", "top": "20%"});
																console.log(" ------------- drag to root not allowed ----------------------");
																e.setStatus("k-denied");
																return;
															}

															if(
																	(
																		e.target[0].cellIndex == 0
																		&&
																		(
																			e.status == "i-insert-middle"
																			||
																			e.status == "i-insert-down"
																			||
																			e.status == "i-cancel"
																			||
																			e.status == "i-plus"
																		)
																	)
																	||
																	e.originalEvent.originalTarget.className == "k-header"
															){
																console.log(" ------------- drag to root not allowed ----------------------");
																e.setStatus("k-denied");
        												return;
															}
														},
								            columns: [
																{ field: "requirement", 	title: "<?php _e("Anforderungen", 	"value_analysis"); ?>",
																	columns: [
																		{ field: "requirements", 	title: "<?php _e("Anforderung", 	"value_analysis"); ?>", 	width: 220, expandable: true	},
																		{ field: "pan", 					title: " ",																			width: 30,  },
																		{ field: "weight", 				title: "<?php _e("Gewicht", 			"value_analysis"); ?>",		width: 100, template: '#=kendo.format("{0:p0S}", weight / 100)#'  },
																		{ field: "weighting", 		title: "<?php _e("Gewichtung", 		"value_analysis"); ?>", 	width: 380, },
																	]
																},
																{
																	field: "opportunities",
																	title: "<?php _e("Möglichkeiten", "value_analysis"); ?>",
																	headerTemplate: kendo.template('<?php _e("Möglichkeiten", "value_analysis"); ?> <button class="k-button k-button-icontext k-primary" id="vave_addopportunities" title="<?php _e("add Möglichkeiten", 	"value_analysis"); ?>"><span class="k-icon k-i-plus"></span><?php _e("add Möglichkeiten", 	"value_analysis"); ?></button>'),
																	columns: [
																		{
																			field: "opportunities_fake",
																			title: "<?php _e("fake", 	"value_analysis"); ?>",
																		}
																	]
																},
/*																{
																	field: "id",
																	width: 80,
																},
																{
																	field: "parentId",
																	width: 80,
																},  */
																<?php
																if($vave_structureEdit != 1 ){
																	?>
																		{ command:
																			[
																				{
																					name: "createchild",
																					text: "<?php _e("add child", "value_analysis"); ?>"
																				},
																				{
																					name: "destroy",
																					text: "<?php _e("Anforderung löschen", "value_analysis"); ?>",
																					className: "vave_maingrid-btn-destroy"
																				},
																			//	"destroy"
																			],
																		width: "auto"  /* 260 */
																	}
																	<?php
																}
																?>
								            ]
								        }).data("kendoTreeList");


												vave_setTreeListeOptions();															/* set Opportunities Header 									*/


												setTimeout(function(){
													/* um sicher zu gehen, dass immer alles vorhanden ist, wird immer ein Job beim Gewichten und einer bei den Möglichkeiten alternierend gestartet */
													vave_treeList_dS_requirements.read();
													vave_evalWeigth();																			/* eval weighting														*/

													vave_treeListObj_requirements.refresh();								/* generate weighting Buttons in dataBound	*/

													vave_initopportunitieHeader_set();											/* Opportunities Header add Click Event 		*/

													vave_addOpportunitiesChoose();													/* add Opportunities Choose Btn							*/

													jQuery("#vave_addopportunities").off('click');					/* Event auf Opportunities Choose entfernen */
													jQuery(".vave_opportunitieHeaderDel").off('click');			/* Event auf Opportunities del entfernen 		*/
													vave_delopportunities_btn();														/* Opportunities Delete add Click Event 		*/
													vave_addopportunitiesChoose_btn();											/* Opportunities Choose add Click Event 		*/

													vave_structureEditSwitch();															/* Lock Switch Btn init 										*/
												}, 20);
										}

/* -------------------------------------------------- R E S U L T -------------------------------------------------------------------------------------------------- */

										function vave_initResult(){

											vave_calculateAllResults();

											jQuery("#vave_result_container").kendoTileLayout({
												containers: [{
													 colSpan: 2,
													 rowSpan: 1,
													 header: {
															 text: "<?php _e("Involvierte User Status", "value_analysis"); ?>"
													 },
													 bodyTemplate: kendo.template(jQuery("#vave_user-template").html()),
												}, {
													 colSpan: 1,
													 rowSpan: 1,
													 header: {
															 text: "<?php _e("Beste Möglichkeit", "value_analysis"); ?>"
													 },
													 bodyTemplate: kendo.template(jQuery("#vave_opp-template_best").html()),
												}, {
													colSpan: 2,
													rowSpan: 2,
													header: {
															text: "<?php _e("Übersicht Resultat Nutzwertanalyse", "value_analysis"); ?>"
													},
													bodyTemplate: kendo.template(jQuery("#vave_shortResult-template").html()),
												}, {
													colSpan: 2,
													rowSpan: 1,
													header: {
															text: "<?php _e("Übersicht Resultat Nutzwertanalyse", "value_analysis"); ?>"
													},
													bodyTemplate: kendo.template(jQuery("#vave_shortResult-template_grid").html()),
												}, {
													colSpan: 2,
													rowSpan: 2,
													header: {
															text: "<?php _e("Resultat Nutzwertanalyse pro User", "value_analysis"); ?>"
													},
													bodyTemplate: kendo.template(jQuery("#vave_userResult-template_group").html()),
												}, {
													colSpan: 3,
													rowSpan: 2,
													header: {
															text: "<?php _e("Resultat Nutzwertanalyse pro Möglichkeit", "value_analysis"); ?>"
													},
													bodyTemplate: kendo.template(jQuery("#vave_oppResult-template_group").html()),
												}],
												columns: 4,
												columnsWidth: 285,
												rowsHeight: 285,
												reorderable: true
											});


										 var vave_resUserGrid_ds = [];
										 jQuery.each(vave_resultVs, function(key, vsI){
												// console.log(vsI);
												var tmp = {};
														tmp.user 				= key;
														tmp.oppChoose 	= vsI.allOppChoosed;
														tmp.weighting 	= vsI.allWeighted;

												vave_resUserGrid_ds.push(tmp);
										 });
										 var vave_resUserGrid_obj = jQuery("#vave_resUserGrid").kendoGrid({
												dataSource: vave_resUserGrid_ds,
												columns: [
													{
														field: "user",
														title: "<?php _e("User", "value_analysis"); ?>",
														template: function(e){
															return vave_getUsrNamebyUsrHash(e["user"]);
														},
												},
												{
														field: "weighting",
														title: "<?php _e("Anforderungen gewichten", "value_analysis"); ?>",
														template: function(e){
															if(e["weighting"] == 1 ){
																/* https://docs.telerik.com/kendo-ui/styles-and-layout/icons-web */
																return '<span class="k-icon k-i-check-outline"></span>';
															}else{
																return '<span class="k-icon k-i-minus-outline"></span>';
															}
														},
												},
												{
														field: "oppChoose",
														title: "<?php _e("Möglichkeiten bewerten", "value_analysis"); ?>",
														template: function(e){
															if(e["oppChoose"] == 1 ){
																return '<span class="k-icon k-i-check-outline"></span>';
															}else{
																return '<span class="k-icon k-i-minus-outline"></span>';
															}
														},
												}]
											}).data("kendoGrid");

											var shortResultChart_data 			= [];
											var shortResultChart_categorie 	= [];
											var userResultChart_categorie 	= [];

											jQuery.each(vave_opportunitiesVs, function(keyOpp, VsOpp){
//												userResultChart_categorie.push(VsOpp.title);
											});

											var shortResultChart_dataArr = {};
											shortResultChart_dataArrFlat = [];
											jQuery.each(vave_opportunitiesVs, function(keyOpp, VsOpp){
												shortResultChart_dataArrFlat = [];

													var oppID = VsOpp.field;
															oppID = oppID.split("_");
															oppID = oppID[1];
													if(!shortResultChart_dataArr[oppID]){
														shortResultChart_dataArr[oppID] = {};
													}
												jQuery.each(vave_resultVs, function(keyUser, VsUser){
													jQuery.each(VsUser.opportunitie, function(reqID, oppVs){
														if(!shortResultChart_dataArr[oppID][keyUser]){
															shortResultChart_dataArr[oppID][keyUser] = 0;
														}
														shortResultChart_dataArr[oppID][keyUser] = shortResultChart_dataArr[oppID][keyUser] + oppVs[oppID];
													});
												});
											});

											jQuery.each(shortResultChart_dataArr, function(oppID, oppVsArr){
												var oppTxt = vave_opportunitiesVs[oppID].title;

												if(oppTxt.length == 0){
													return;  /* continue equivalent https://stackoverflow.com/questions/17162334/how-to-use-continue-in-jquery-each-loop */
												}

												var usrVsMean = 0;
												var usrVsAnz = 0;
												jQuery.each(oppVsArr, function(usrID, usrVs){
													usrName = vave_getUsrNamebyUsrHash(usrID);
													var oppTxtUsr = usrName + " - " + oppTxt;
													userResultChart_categorie.push(oppTxtUsr);
													usrVsMean = usrVsMean + usrVs;
													usrVsAnz++;
													var tmpF 				= {};
															tmpF.oppVs 	= usrVs;
															tmpF.usrOpp = oppTxtUsr;
															tmpF.usr 		= usrName;
															tmpF.opp 		= oppTxt;
													shortResultChart_dataArrFlat.push(tmpF);
												});
												usrVsMean = usrVsMean / usrVsAnz;
												userResultChart_categorie.push("Durchschnitt " + oppTxt);
												shortResultChart_categorie.push(oppTxt);
												shortResultChart_data.push(usrVsMean);
											});

											var vave_shortResultGridArr 	= [];
											var vave_shortResultBestOpp 	= "";
											var vave_shortResultBestOppVs = 0;
											for (var i = 0; i < shortResultChart_categorie.length; i++) {
											 var tmp = {};
													 tmp.oppVs  = shortResultChart_data[i];
													 tmp.oppTxt = shortResultChart_categorie[i];
											 vave_shortResultGridArr.push(tmp);
											 if( shortResultChart_data[i] >= vave_shortResultBestOppVs){
												 vave_shortResultBestOppVs = shortResultChart_data[i];
												 vave_shortResultBestOpp = shortResultChart_categorie[i];
											 }
											}

											jQuery("#vave_opp-template_best_vs").html("<h3>" + vave_shortResultBestOpp + ": " + vave_shortResultBestOppVs.toFixed(0) + "</h3>");

											var vave_shortResultGrid_obj = jQuery("#vave_shortResultGrid").kendoGrid({
												 dataSource: vave_shortResultGridArr,
												 /* toolbar: ["pdf", "excel"], */
												 toolbar: ["excel"],
												 pdf: {
													  filename: kendo.toString(new Date(), "u")  + " <?php _e("Übersicht Resultat Nutzwertanalyse", "value_analysis"); ?>.pdf",
														proxyURL: "https://demos.telerik.com/kendo-ui/service/export",
				                    allPages: true,
				                    avoidLinks: true,
				                    paperSize: "A4",
				                    margin: { top: "2cm", left: "1cm", right: "1cm", bottom: "1cm" },
				                    landscape: true,
				                    repeatHeaders: true,
				                    scale: 0.8
				                 },
												 excel: {
														fileName: kendo.toString(new Date(), "u") + " <?php _e("Übersicht Resultat Nutzwertanalyse", "value_analysis"); ?>.xlsx",
														proxyURL: "https://demos.telerik.com/kendo-ui/service/export",
														filterable: true
												 },
												 columns: [{
														 field: "oppTxt", title: "<?php _e("Möglichkeit", "value_analysis"); ?>",
												 },
												 {
														 field: "oppVs",
														 title: "<?php _e("Punktzahl", "value_analysis"); ?>",
														 template: function(e){
															 return e["oppVs"].toFixed(0);
														 },
												 }]
											 }).data("kendoGrid");


											jQuery("#vave_shortResultChart").kendoChart({
													title: "<?php _e("Übersicht Resultat Nutzwertanalyse", "value_analysis"); ?>",
													legend: {
															visible: true
													},
													seriesDefaults: {
															type: "column"
													},
													series: [{
															data: shortResultChart_data
													}],
													valueAxis: {
															line: {
																	width: 0
															},
															labels: {
																	step: 5
															},
//						                    majorUnit: 10000,
															min: 0,
//						                    max: 100000
													},
													categoryAxis: {
															categories: shortResultChart_categorie,
															labels: {
																	rotation: "auto",
																	visible: true
															},
															majorGridLines: {
																	visible: false
															},
															majorTicks: {
																	visible: false
															}
													},
													tooltip: {
															visible: true,
															template: function(e){
//																console.log(e);
																return e.category + ": " + e.value.toFixed(0);;
															}
													}
											});


											var vave_userResultChart_dS = new kendo.data.DataSource({
													transport: {
													   read: function(e) {
													          e.success(shortResultChart_dataArrFlat);
													       }
													},
													group: {
													   field: "opp"
													},
													aggregate: [
														{ field: "oppVs", aggregate: "sum", serie: "oppVs_sum" },
														{ field: "oppVs", aggregate: "average", serie: "oppVs_avg" },
													],
													schema: {
													   model: {
													       fields: {
													           oppVs: {
													               type: "number"
													           }
													       }
													   }
													}
							         });

					             jQuery("#vave_userResultChart_group").kendoChart({
					                 dataSource: vave_userResultChart_dS,
					                 series: [{
					                     type: "column",
															 stack: false,
					                     field: "oppVs",
					                     categoryField: "usr",
														 }],
					                 legend: {
					                     position: "bottom"
					                 },
					                 valueAxis: {
					                     labels: {
					                         skip: 2,
					                         step: 2
					                     }
					                 },
					                 categoryAxis: {
					                     labels: {
					                     }
					                 },
													 tooltip: {
															 visible: true,
															 template: function(e){
																 return e.category + " - " + e.series.name + ": " + e.value.toFixed(0);;
															 }
													 }
					             });

											 var vave_oppResultChart_dS = new kendo.data.DataSource({
													 transport: {
															read: function(e) {
																		 e.success(shortResultChart_dataArrFlat);
																	}
													 },
													 group: {
															field: "usr"
													 },
													 aggregate: [
														 { field: "oppVs", aggregate: "sum", serie: "oppVs_sum" },
														 { field: "oppVs", aggregate: "average", serie: "oppVs_avg" },
													 ],
													 schema: {
															model: {
																	fields: {
																			oppVs: {
																					type: "number"
																			}
																	}
															}
													 }
												});

												jQuery("#vave_oppResultChart_group").kendoChart({
														dataSource: vave_oppResultChart_dS,
														series: [{
																type: "column",
																stack: false,
																field: "oppVs",
																categoryField: "opp",
																name: "#= group.value #",
														}, {
										          name: "Durchschnitt",
															color: "#a0b0c0",
										          data: shortResultChart_data
										        }],
														legend: {
																position: "bottom"
														},
														valueAxis: {
																labels: {
																		skip: 2,
																		step: 2
																}
														},
														categoryAxis: {
																labels: {
																}
														},
														tooltip: {
																visible: true,
																template: function(e){
																	return e.category + " - " + e.series.name + ": " + e.value.toFixed(0);;
																}
														}
												});
										}



/* -------------------------------------------------------------------------- Dashboard ---------------------------------------------------------------------- */

											function vave_initDashBoard(){
												vave_dashGrid_ds = new kendo.data.DataSource({
				                    transport: {
				                        read:  function(e){
																	if(vave_dashData && vave_dashData.users && vave_dashData.users.length > 0){
																		e.success(vave_dashData.users);
																	}else{
																		e.success([]);
																	}
				                        },
				                        update: function(e){
																	vave_dashData.users = vave_dashGrid_ds.data();
																	var data 						= [];
																			data[0] 				= {};
																			data[0].action 	= "cudVaDataDash";
																			data[0].postID 	= <?php echo get_the_ID(); ?>;
																			data[0].data		= vave_dashData;

																	vave_saveAjax(data, e);
																},
				                        create: function(e){
																	vave_dashData.users = vave_dashGrid_ds.data();
																	var data 						= [];
																			data[0] 				= {};
																			data[0].action 	= "cudVaDataDash";
																			data[0].postID 	= <?php echo get_the_ID(); ?>;
																			data[0].data		= vave_dashData;

																	vave_saveAjax(data, e);
																},
																destroy: function(e){
																	var iii = 0;
																	vave_dashData.users = vave_dashGrid_ds.data();
																	var data        			= [];
																			data[iii]        	= {};
																			data[iii].action 	= "cudVaDataDash";
																			data[iii].postID 	= <?php echo get_the_ID(); ?>;
																			data[iii].data    = vave_dashData;

																			iii++;

																	if( vave_isset(vave_weightVs["vave_currentUsrHash"]) ){
																									delete vave_weightVs["vave_currentUsrHash"];
																									data[iii]        = {};
																									data[iii].action = "setResultVs";
																									data[iii].postID = <?php echo get_the_ID(); ?>;
																									data[iii].data   = vave_weightVs;

																									iii++;
																	}

																	if( vave_isset(vave_oppChooseVs["vave_currentUsrHash"]) ){
																									delete vave_oppChooseVs["vave_currentUsrHash"];
																									data[iii]        = {};
																									data[iii].action = "setOppChooseVs";
																									data[iii].postID = <?php echo get_the_ID(); ?>;
																									data[iii].data   = vave_oppChooseVs;

																									iii++;
																	}

																	vave_saveAjax(data, e);
									 						},
			                        parameterMap: function(options, operation) {
			                            if (operation !== "read" && options.models) {
			                                return {models: kendo.stringify(options.models)};
			                            }
			                        }
				                    },
				                    batch: false,
														autoSync: false,
				                    pageSize: 20,
				                    schema: {
				                        model: {
				                            id: "usrID",
				                            fields: {
				                                usrID: 		{ editable: true, nullable: true },
				                                usrName: 	{ type: "string", editable: vave_isMasterUsr == 1 ? true : false, defaultValue: vave_langTxtArr["vave_usrNew"], validation: { required: true } },
				                                usrHash: 	{ type: "string", editable: false }
				                            }
				                        }
				                    },
														change: function(e){
															if(e.action == "add"){
																e.items[0].usrHash = vave_makeid(15);
																e.items[0].usrID = e.items[0].usrHash;
															}

															if(vave_inArray(e.action, ["remove", "itemchange"]) ){
																vave_dashGrid_ds.sync();
															}
														},
				                });

												vave_dashGrid_obj = jQuery("#vave_dash_grid").kendoGrid({
													 dataSource: vave_dashGrid_ds,
													 <?php
													 if( $vave_isMasterUsr == 1 || $vave_structureEdit != 1 ){
														 echo 'toolbar: ["save", "cancel", "create"], ';
														 echo 'editable: "incell",';
														 echo 'width:750,';
													 }else{
														 echo 'editable: false,';
														 echo 'width:450,';
													 }
													 ?>
													 messages: {
															commands: {
																cancel: 	"<?php _e("Änderungen verwerfen", "va"); ?>",
																create: 	"<?php _e("Neuen User erzeugen", "va"); ?>",
																destroy: 	"<?php _e("User löschen", "va"); ?>",
																save: 		"<?php _e("Änderungen speichern", "va"); ?>",
															}
													 },
													 columns: [
														 {
																 field: "usrName",
																 title: "<?php _e("Name", "va"); ?>",
														 },
														 <?php
														 if( $vave_isMasterUsr == 1 || $vave_structureEdit != 1 ){
															 ?>
															 {
																	 field: "usrHash",
																	 title: "<?php _e("Bewertungs-Link", "va"); ?>",
																	 template: function(e){
																		 return '<div class="vave_dashGrid_btn_container"><button id="' + e["usrHash"] + '" onclick="vave_dashGridShowHash(this)" class="k-button k-button-icontext vave_dashGrid_btn" ><span class="k-icon k-i-copy"></span><?php _e("Copy access token for Rating-User", "va"); ?>&nbsp;&nbsp;</button></div>';
																	 },
																	 width:350,
															 },
															 <?php
														 }
														 ?>

														 <?php
														 if( $vave_isMasterUsr == 1 || $vave_structureEdit != 1 ){
															 ?>
															 {
																 command: [
													        {
																		name: "destroy", 	/* https://docs.telerik.com/kendo-ui/api/javascript/ui/grid/configuration/columns.command#columnscommandvisible */
																		visible: function(dataItem) {
														 				 return (dataItem.usrHash !== vave_currentUsrHash && dataItem.usrHash == dataItem.usrID );
																		}
													        },
												      	]
															}
															<?php
														 }
														 ?>
													 ]
												 }).data("kendoGrid");



												 jQuery("#vave_dash_grid_nextStepBtn").kendoButton({
														click: function(e) {
																vave_tabstripObj.select(1);
														},
														icon: "forward",
												});

											}		/* ende vave_initDashBoard function*/


										</script>



										<script id="vave_user-template" type="text/x-kendo-template">
												<div id="vave_resUserGrid" style="height:100%; width:100%"></div>
										</script>
										<script id="vave_opp-template_best" type="text/x-kendo-template">
											<div id="vave_opp-template_best_vs"></div>
										</script>
										<script id="vave_shortResult-template" type="text/x-kendo-template">
											<div id="vave_shortResultChart" style="height:100%; width:100%;"></div>
										</script>
										<script id="vave_shortResult-template_grid" type="text/x-kendo-template">
											<div id="vave_shortResultGrid" style="height:100%; width:100%;"></div>
										</script>
										<script id="vave_userResult-template_group" type="text/x-kendo-template">
												<div id="vave_userResultChart_group" style="height:100%; width:100%"></div>
										</script>
										<script id="vave_oppResult-template_group" type="text/x-kendo-template">
												<div id="vave_oppResultChart_group" style="height:100%; width:100%"></div>
										</script>


                    <?php
                        /* If comments are open or we have at least one comment, load up the comment template */
                        if ( comments_open() || '0' != get_comments_number() ) :
                            comments_template();
                        endif;
                    ?>

                <?php endwhile; // end of the loop. ?>

            </main><!-- #main -->
        </div><!-- #primary -->
    </div>
<?php //get_sidebar(); ?>
</div>
<?php get_footer(); ?>
