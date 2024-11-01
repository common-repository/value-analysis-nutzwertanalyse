/* https://stackoverflow.com/questions/18017869/build-tree-array-from-flat-array-in-javascript */
function vave_unflatten(arr, childSelector = "items") {
    var tree = [],
        mappedArr = {},
        arrElem,
        mappedElem;

    // First map the nodes of the array to an object -> create a hash table.
    for (var i = 0, len = arr.length; i < len; i++) {
        arrElem = arr[i];
        mappedArr[arrElem.id] = arrElem;
        mappedArr[arrElem.id][childSelector] = [];
    }

    for (var id in mappedArr) {
        if (mappedArr.hasOwnProperty(id)) {
            mappedElem = mappedArr[id];
            // If the element is not at the root level, add it to its parent array of children.
            if (mappedElem.parentId) {
                mappedArr[mappedElem['parentId']][childSelector].push(mappedElem);
            }
            // If the element is at the root level, add it to first level elements array.
            else {
                tree.push(mappedElem);
            }
        }
    }
    return tree;
}

function vave_arrayItemsFlatten_orig(arr, result, childSelector = "items", ) {
    jQuery.each(arr, function(key, arrVs) {
        if (arrVs.length === 0) {
            return result;
        }
        if (!vave_isset(arrVs)) {
            return result;
        }
        var head = arrVs;
        var rest = delete(arrVs);
        if (vave_isset(head[childSelector])) {
            vave_arrayItemsFlatten_orig(head[childSelector], result, childSelector = "items", );
        }
        result.push(head);
        return vave_arrayItemsFlatten_orig(rest, result, childSelector = "items", );
    });
    return result;
}

function vave_arrayItemsFlatten(arr, result, childSelector = "items") {
    jQuery.each(arr, function(key, arrVs) {
        if (arrVs.length === 0) {
            return result;
        }
        if (!vave_isset(arrVs)) {
            return result;
        }
        var head = arrVs;
        var rest = delete(arrVs);
        if (vave_isset(head[childSelector])) {
            for (var i = 0, leni = arrVs[childSelector].length; i < leni; i++) {
                if (i === 0) {
                    arrVs.weight = 100;
                    continue;
                }
                var txt = "";
                for (var j = i, lenj = arrVs[childSelector].length; j < lenj; j++) {
                    //          console.log(arrVs[childSelector][j].requirements);
                    //          console.log(arrVs[childSelector][(i-1)].requirements + " - " + arrVs[childSelector][j].requirements);

                    var eContainerID = arrVs[childSelector][(i - 1)].id + "-" + arrVs[childSelector][j].id;
                    var vave_identifierClass = "vave_wgh_ident_" + eContainerID;
                    if (vave_weightingVs[vave_currentUsrHash] && vave_weightingVs[vave_currentUsrHash][eContainerID]) {
                        if (parseInt(vave_weightingVs[vave_currentUsrHash][eContainerID]) == parseInt(arrVs[childSelector][(i - 1)].id)) {

                            txt += '<div class="vave_weighting_btn_container"><button onclick="vave_onClickWeigteningBtn(\'' + eContainerID + '\',\'' + arrVs[childSelector][(i - 1)].id + '\',\'' + arrVs[childSelector][(i - 1)].id + '\')" class="k-button k-button-icontext k-primary vave_weighting_btn ' + vave_identifierClass + '" id="weighting_' + arrVs[childSelector][(i - 1)].id + '-' + arrVs[childSelector][(i - 1)].id + '" title="' + arrVs[childSelector][(i - 1)].requirements + '"><span class="k-icon k-i-plus"></span>' + vave_TrimStrLength(arrVs[childSelector][(i - 1)].requirements, vave_requirements_string_length) + '</button> ' + vave_langTxtArr["vave_reqOder"] + ' <button  onclick="vave_onClickWeigteningBtn(\'' + eContainerID + '\',\'' + arrVs[childSelector][(i - 1)].id + '\',\'' + arrVs[childSelector][j].id + '\')" class="k-button  k-button-icontext vave_weighting_btn ' + vave_identifierClass + '" id="weighting_' + arrVs[childSelector][(i - 1)].id + '-' + arrVs[childSelector][j].id + '" title="' + arrVs[childSelector][j].requirements + '"><span class="k-icon k-i-minus"></span>' + vave_TrimStrLength(arrVs[childSelector][j].requirements, vave_requirements_string_length) + '</button> ?</div>';

                        } else {
                            txt += '<div class="vave_weighting_btn_container"><button onclick="vave_onClickWeigteningBtn(\'' + eContainerID + '\',\'' + arrVs[childSelector][(i - 1)].id + '\',\'' + arrVs[childSelector][(i - 1)].id + '\')" class="k-button k-button-icontext vave_weighting_btn ' + vave_identifierClass + '" id="weighting_' + arrVs[childSelector][(i - 1)].id + '-' + arrVs[childSelector][(i - 1)].id + '" title="' + arrVs[childSelector][(i - 1)].requirements + '"><span class="k-icon k-i-minus"></span>' + vave_TrimStrLength(arrVs[childSelector][(i - 1)].requirements, vave_requirements_string_length) + '</button> ' + vave_langTxtArr["vave_reqOder"] + ' <button onclick="vave_onClickWeigteningBtn(\'' + eContainerID + '\',\'' + arrVs[childSelector][(i - 1)].id + '\',\'' + arrVs[childSelector][j].id + '\')" class="k-button  k-button-icontext k-primary vave_weighting_btn ' + vave_identifierClass + '" id="weighting_' + arrVs[childSelector][(i - 1)].id + '-' + arrVs[childSelector][j].id + '" title="' + arrVs[childSelector][j].requirements + '"><span class="k-icon k-i-plus"></span>' + vave_TrimStrLength(arrVs[childSelector][j].requirements, vave_requirements_string_length) + '</button> ?</div>';
                        }
                    } else {
                        txt += '<div class="vave_weighting_btn_container"><button onclick="vave_onClickWeigteningBtn(\'' + eContainerID + '\',\'' + arrVs[childSelector][(i - 1)].id + '\',\'' + arrVs[childSelector][(i - 1)].id + '\')" class="k-button vave_weighting_btn vave_weighting_btn_notWeighted ' + vave_identifierClass + '" id="weighting_' + arrVs[childSelector][(i - 1)].id + '-' + arrVs[childSelector][(i - 1)].id + '" title="' + arrVs[childSelector][(i - 1)].requirements + '"><span class="k-icon"></span>' + vave_TrimStrLength(arrVs[childSelector][(i - 1)].requirements, vave_requirements_string_length) + '</button> ' + vave_langTxtArr["vave_reqOder"] + ' <button onclick="vave_onClickWeigteningBtn(\'' + eContainerID + '\',\'' + arrVs[childSelector][(i - 1)].id + '\',\'' + arrVs[childSelector][j].id + '\')" class="k-button vave_weighting_btn vave_weighting_btn_notWeighted ' + vave_identifierClass + '" id="weighting_' + arrVs[childSelector][(i - 1)].id + '-' + arrVs[childSelector][j].id + '" title="' + arrVs[childSelector][j].requirements + '"><span class="k-icon"></span>' + vave_TrimStrLength(arrVs[childSelector][j].requirements, vave_requirements_string_length) + '</button> ?</div>';
                    }

                    if ((arrVs[childSelector].length - j) > 1) {
                        txt += '<hr class="vave_weighting_btn_hr">';
                        txt += '<p class="vave_weighting_btn_what"> ' + vave_langTxtArr["vave_reqWhatIsImportant"] + ' </p>';
                    }

                }
                result[arrVs[childSelector][(i - 1)].id] = txt;
            }
            vave_arrayItemsFlatten(head[childSelector], result, childSelector);
        }
        return vave_arrayItemsFlatten(rest, result, childSelector);
    });
    return result;
}

function vave_onClickWeigteningBtn(eContainerID, eRequirementID, eWeightedId) {
    var eParentIdWeight = 0;
    var eRequirementID = eContainerID.split("-");
    eRequirementID = eRequirementID[0];

    jQuery.each(vave_treeList_dS_requirements.data(), function(key, vs) {
        if (eRequirementID == vs.id) {
            if (vs.weighting && vave_IsValidJSON(vs.weighting)) {
                var weighting = vave_stringToJson(vs.weighting);
            } else {
                var weighting = {};
            }
            weighting[eContainerID] = eWeightedId;
            vs.weighting = JSON.stringify(weighting);

            if (!vave_weightingVs[vave_currentUsrHash]) {
                vave_weightingVs[vave_currentUsrHash] = {};
            }
            vave_weightingVs[vave_currentUsrHash][eContainerID] = eWeightedId;
        }
    });

    var dataX = [];
    dataX[0] = {};
    dataX[0].action = "setWeightingVs";
    dataX[0].postID = vave_currentPostId;
    dataX[0].data = JSON.stringify(vave_weightingVs);
    vave_saveAjax(dataX, "");

    vave_evalWeigth();

    jQuery(".vave_wgh_ident_" + eContainerID).each(function(key, e) {
        var eItem = jQuery(e);
        var eClassId = "weighting_" + eRequirementID + "-" + eWeightedId;
        if (eItem.attr("id") == eClassId) {
            eItem.addClass("k-button-icontext k-primary");
            eItem.children("span").removeClass();
            eItem.children("span").addClass("k-icon k-i-plus");
        } else {
            eItem.removeClass("k-primary");
            eItem.addClass("k-button-icontext");
            eItem.children("span").removeClass();
            eItem.children("span").addClass("k-icon k-i-minus");
        }
        eItem.removeClass("vave_weighting_btn_notWeighted");
    });
    setTimeout(function() {
        vave_isAllWeightedFromThisUsr();
    }, 20);
}

function vave_evalWeigth() {
    vave_weightVs[vave_currentUsrHash] = {};
    vave_weightVs[vave_currentUsrHash][1] = 100;

    var anzWeightsById = 0;
    var allAnzWeightsById = 0;
    var vave_weightsAnz = 0;
    var vave_allWeightsAnz = 0;


    jQuery.each(vave_treeList_dS_requirements.data(), function(key, vsW) {
        var eParentId = vsW.id;
        var childNodesWeight = [];
        var vave_itemsIdArr = {};

        vave_weightsAnz = 0;
        for (var i = 0; i < vsW.items.length; i++) {
            vave_weightsAnz = vave_weightsAnz + i;
        }


        jQuery.each(vave_treeList_dS_requirements.data(), function(key, vsX) {
            if (eParentId == vsX.parentId) {
                childNodesWeight.push(vsX);
                vave_itemsIdArr[vsX.id] = 0;
            }
        });

        var vave_childNodesWeight_length = childNodesWeight.length;




        jQuery.each(childNodesWeight, function(key, vsY) {

            /* gibt es eine Anforderung mit 0 Gewichtungen => 1*/
            var vave_reqItemHasNull = 0;
            jQuery.each(childNodesWeight, function(keyCi, vsCi) {
                var tmpNoCi = 0;
                jQuery.each(vave_weightingVs[vave_currentUsrHash], function(keyWi, vsWi) {
                    if (vsCi.id == vsWi) {
                        tmpNoCi = 1;
                        return false;
                    }
                });
                if (tmpNoCi == 0) {
                    vave_reqItemHasNull = 1;
                    return false;
                }
            });
            /* console.log("vave_reqItemHasNull => " + vave_reqItemHasNull ); */

            anzWeightsById = 0;
            if (vave_reqItemHasNull == 1) {
                anzWeightsById = 1;
            }
            jQuery.each(vave_weightingVs[vave_currentUsrHash], function(key, vsZ) {
                if (vsZ == vsY.id) {
                    var keyArr = key.split("-");
                    if (
                        vave_itemsIdArr[keyArr[0]] == 0 &&
                        vave_itemsIdArr[keyArr[1]] == 0
                    ) {
                        anzWeightsById++;
                        allAnzWeightsById++;
                    } else {
                        /* löschen des Keys, da es Elemente daraus nicht mehr gibt */
                        /* console.log("delete - key: " + key + " - vs: " + vave_weightingVs[vave_currentUsrHash][key]); */
                        delete(vave_weightingVs[vave_currentUsrHash][key]);
                    }
                }
            });

            if (vave_reqItemHasNull == 1) {
                var weight = vave_weightVs[vave_currentUsrHash][eParentId] / vave_weightsAnz * anzWeightsById / (vave_childNodesWeight_length + 1) * (vave_childNodesWeight_length - 1);
            } else {
                var weight = vave_weightVs[vave_currentUsrHash][eParentId] / vave_weightsAnz * anzWeightsById;
            }

            /*
                  console.log("weight: " + weight + " Requirement: " + vsY.requirements);
                  console.log("vave_weightsAnz: " + vave_weightsAnz + " - anzWeightsById: " + anzWeightsById + " - allAnzWeightsById: " + allAnzWeightsById + " - vave_allWeightsAnz: " + vave_allWeightsAnz + " - childNodesWeight.length: " + childNodesWeight.length);
            */
            if (weight) {
                vave_weightVs[vave_currentUsrHash][vsY.id] = weight;
            } else if (eParentId != 1 && vave_weightsAnz == 0) {
                weight = vave_weightVs[vave_currentUsrHash][eParentId];
                vave_weightVs[vave_currentUsrHash][vsY.id] = weight;
            } else {
                weight = 0;
                vave_weightVs[vave_currentUsrHash][vsY.id] = weight;
            }

            /* Gewicht in die dS schreiben*/
            /* vsY.weight = weight; */
            vsY.weight = ""; /* Wir schreiben nix rein da es sonst beim editieren Konflikte git in der Tabelle wegen dem Refresh und dann werden die Werte aus der Tabelle angezeigt. Ausserdem sind die Werte je nach User verschieden. */

            var weightFloat = weight;
            if (!vave_resultVs[vave_currentUsrHash]) {
                vave_resultVs[vave_currentUsrHash] = {};
                vave_resultVs[vave_currentUsrHash]["weight"] = {};
            }

            if (!vave_resultVs[vave_currentUsrHash]["weight"]) {
                vave_resultVs[vave_currentUsrHash]["weight"] = {};
            }

            weight = parseFloat(weight);
            weight.toFixed(1);

            if (vave_isset(vave_treeListObj_requirements)) {
                var row = vave_treeListObj_requirements
                    .tbody
                    .find("tr[data-uid='" + vsY.uid + "']");


                if (weight < 10) {
                    weight = kendo.format("{0:p1}", weight / 100);
                } else {
                    weight = kendo.format("{0:p0}", weight / 100);
                }
                if (vsY.items.length > 0) {
                    row.find("td:eq(2)").html("<span class='vave_nonAddWeighting'>(" + weight + ")</span>");
                    vave_resultVs[vave_currentUsrHash]["weight"][vsY.id] = 0;
                } else {
                    row.find("td:eq(2)").html("<span class='vave_addWeighting'>" + weight + "</span>");
                    vave_resultVs[vave_currentUsrHash]["weight"][vsY.id] = weightFloat;
                }
            }

            vsY.weight = "";
            vsY.weighting = "";
        });
    });

    vave_isAllWeightedFromThisUsr();


    var data = [];
    data[0] = {};
    data[0].action = "setResultVs";
    data[0].postID = vave_currentPostId;
    data[0].data = JSON.stringify(vave_resultVs);
    vave_saveAjax(data, "");
}

function vave_isAllWeightedFromThisUsr() {
    if (!(
            vave_resultVs[vave_currentUsrHash] &&
            vave_isset(vave_resultVs[vave_currentUsrHash]["allWeighted"])
        )) {
        vave_resultVs[vave_currentUsrHash] = {};
    }
    if (
        jQuery(".vave_weighting_btn_notWeighted").length == 0 &&
        vave_resultVs[vave_currentUsrHash]["weight"] &&
        jQuery(".vave_weighting_btn").length > 0
    ) { /* Alle nicht gewichteten Buttons haben die Classe vave_weighting_btn_notWeighted. Wenn es keine solche gibt, wurden alle gewichtet */
        vave_allWeightChoose = 1; /* Es wurden alle Requirements gewichtet */
        vave_resultVs[vave_currentUsrHash]["allWeighted"] = 1;
        if (jQuery(".vave_choose_btn").length == 0) {
            vave_addOpportunitiesChoose(); /* add Opportunities Choose Btn							*/
            jQuery("#vave_addopportunities").off('click'); /* Event auf Opportunities Choose entfernen */
            vave_addopportunitiesChoose_btn(); /* Opportunities Choose add Click Event 		*/
        }
    } else {
        vave_allWeightChoose = 0; /* Es wurden nicht alle Requirements gewichtet */
        vave_resultVs[vave_currentUsrHash]["allWeighted"] = 0;
    }

    /*
      console.log("vave_allWeightChoose");
      console.log(vave_allWeightChoose);
    */
    return vave_allWeightChoose;
}


function vave_initopportunitieHeader_set() {
    jQuery(".vave_opportunitieHeader").on("click", function(e) {
        jQuery(".vave_opportunitieHeader").off('click'); /* Rermove Event Handler */

        var obj = jQuery(this);
        var objID = obj.attr("id");

        var vave_headerText = obj.data("bind");

        if (!vave_structureEdit) {
            jQuery("#" + objID).html('<input id="' + objID + '_input" />');

            var vave_headerTextObj = jQuery("#" + objID + "_input").kendoTextBox({
                value: vave_headerText,
                change: function(e) {

                    var opp_field = objID.split("-");
                    opp_field = opp_field[1];

                    vave_headerText = vave_headerTextObj.value();

                    if (vave_opportunitiesVs[opp_field]) {
                        vave_opportunitiesVs[opp_field].field = "opportunitie_" + opp_field;
                        vave_opportunitiesVs[opp_field].title = vave_headerText;
                        vave_opportunitiesVs[opp_field].headerTemplate = '<div class="vave_opportunitieHeader" id="vave_opportunitieHeader-' + opp_field + '" title="' + vave_headerText + '" data-bind="' + vave_headerText + '">' + vave_headerText + '&nbsp;<button type="button" class="vave_opportunitieHeaderDel k-button k-grid-delete"><span class="k-icon k-i-close"></span></button></div>';
                    }

                    jQuery.each(vave_opportunitiesVs, function(oppKey, oppVs) {
                        if (oppVs && oppVs.field == "opportunities_fake") {
                            delete vave_opportunitiesVs[oppKey];
                        }
                    });

                    var vave_opportunitiesVsSave = [];
                    jQuery.each(vave_opportunitiesVs, function(oppKey, oppVs) {
                        if (oppVs) {
                            var tmp = {};
                            tmp.field = oppVs.field;
                            tmp.title = oppVs.title;
                            vave_opportunitiesVsSave.push(tmp);
                        }
                    });

                    var data = [];
                    data[0] = {};
                    data[0].action = "setOpportunitiesVs";
                    data[0].postID = vave_currentPostId;
                    data[0].data = JSON.stringify(vave_opportunitiesVsSave);
                    vave_saveAjax(data, "");

                    vave_setTreeListeOptions(); /* set Oportunities Header 									*/
                    vave_evalWeigth(); /* eval weighting														*/

                    vave_treeListObj_requirements.refresh(); /* generate weighting Buttons in dataBound	*/
                    vave_initopportunitieHeader_set(); /* Opportunities Header add Click Event 		*/

                    vave_addOpportunitiesChoose(); /* add Opportunities Choose Btn							*/

                    jQuery("#vave_addopportunities").off('click'); /* Event auf Opportunities Choose entfernen */
                    jQuery(".vave_opportunitieHeaderDel").off('click'); /* Event auf Opportunities del entfernen 		*/
                    vave_delopportunities_btn(); /* Opportunities Delete add Click Event 		*/
                    vave_addopportunitiesChoose_btn(); /* Opportunities Choose add Click Event 		*/

                    vave_structureEditSwitch(); /* Lock Switch Btn init 										*/

                }
            }).data('kendoTextBox');
        }


    });
}

function vave_addopportunitiesChoose_btn() {
    jQuery("#vave_addopportunities").on("click", function(e) {
        jQuery("#vave_addopportunities").off('click'); /* Rermove Event Handler */

        var nextID = 0;
        jQuery.each(vave_opportunitiesVs, function(oppKey, oppVs) {
            if (oppVs && oppVs.field == "opportunities_fake") {
                delete vave_opportunitiesVs[oppKey];
            } else {
                var tmpOppID = oppVs.field.split("_");
                tmpOppID = tmpOppID[1];
                nextID = tmpOppID > nextID ? tmpOppID : nextID;
            }
        });

        nextID++;
        var tmp = {};
        tmp.field = "opportunitie_" + nextID;
        tmp.title = vave_langTxtArr["vave_newOpportunitie"] + " " + (nextID + 1);
        tmp.headerTemplate = '<div class="vave_opportunitieHeader" id="vave_opportunitieHeader-' + nextID + '" title="' + tmp.title + '" data-bind="' + tmp.title + '">' + tmp.title + '&nbsp;<button type="button" class="vave_opportunitieHeaderDel k-button k-grid-delete"><span class="k-icon k-i-close"></span></button></div>';

        vave_opportunitiesVs[nextID] = tmp;

        var vave_opportunitiesVsSave = {};
        jQuery.each(vave_opportunitiesVs, function(oppKey, oppVs) {
            if (oppVs) {
                var tmp = {};
                tmp.field = oppVs.field;
                tmp.title = oppVs.title;
                vave_opportunitiesVsSave[oppKey] = tmp;
            }
        });

        var data = [];
        data[0] = {};
        data[0].action = "setOpportunitiesVs";
        data[0].postID = vave_currentPostId;
        data[0].data = JSON.stringify(vave_opportunitiesVsSave);
        vave_saveAjax(data, "");


        vave_setTreeListeOptions(); /* set Oportunities Header 									*/
        vave_evalWeigth(); /* eval weighting														*/

        vave_treeListObj_requirements.refresh(); /* generate weighting Buttons in dataBound	*/
        vave_initopportunitieHeader_set(); /* Opportunities Header add Click Event 		*/

        vave_addOpportunitiesChoose(); /* add Opportunities Choose Btn							*/

        jQuery("#vave_addopportunities").off('click'); /* Event auf Opportunities Choose entfernen */
        jQuery(".vave_opportunitieHeaderDel").off('click'); /* Event auf Opportunities del entfernen 		*/
        vave_delopportunities_btn(); /* Opportunities Delete add Click Event 		*/
        vave_addopportunitiesChoose_btn(); /* Opportunities Choose add Click Event 		*/

        vave_structureEditSwitch(); /* Lock Switch Btn init 										*/

    });
}

function vave_delopportunities_btn() {
    jQuery(".vave_opportunitieHeaderDel").on("click", function(e) {
        jQuery(".vave_opportunitieHeaderDel").off('click'); /* Rermove Event Handler */



        jQuery.each(vave_opportunitiesVs, function(oppKey, oppVs) {
            if (oppVs && oppVs.field == "opportunities_fake") {
                delete vave_opportunitiesVs[oppKey];
            }
        });


        var obj = jQuery(this);
        var objID = obj.parent().attr("id");
        objID = objID.split("-");
        objFullID = "opportunitie_" + objID[1];

        delete vave_opportunitiesVs[objID[1]];

        jQuery.each(vave_oppChooseVs, function(oppUsrID, oppUsrVs) {
            jQuery.each(oppUsrVs, function(oppCKey, oppCVs) {
                var oppIDTmp = oppCKey.split("-");
                if (oppIDTmp[1] == objID[1]) {
                    delete vave_oppChooseVs[oppUsrID][oppCKey];
                }
            });
        });


        var data = [];
        data[0] = {};
        data[0].action = "delOpportunitiesVs";
        data[0].postID = vave_currentPostId;
        data[0].data = JSON.stringify(vave_opportunitiesVs);

        data[1] = {};
        data[1].action = "setOppChooseVs";
        data[1].postID = vave_currentPostId;
        data[1].data = JSON.stringify(vave_oppChooseVs);
        vave_saveAjax(data, "");

        vave_setTreeListeOptions(); /* set Oportunities Header 									*/
        vave_evalWeigth(); /* eval weighting														*/

        vave_treeListObj_requirements.refresh(); /* generate weighting Buttons in dataBound	*/
        vave_initopportunitieHeader_set(); /* Opportunities Header add Click Event 		*/

        vave_addOpportunitiesChoose(); /* add Opportunities Choose Btn							*/

        jQuery("#vave_addopportunities").off('click'); /* Event auf Opportunities Choose entfernen */
        jQuery(".vave_opportunitieHeaderDel").off('click'); /* Event auf Opportunities del entfernen 		*/
        vave_delopportunities_btn(); /* Opportunities Delete add Click Event 		*/
        vave_addopportunitiesChoose_btn(); /* Opportunities Choose add Click Event 		*/

        vave_structureEditSwitch(); /* Lock Switch Btn init 										*/
    });
}

function vave_opportunitiesVs_addHeaderTemplate() {
    if (Object.keys(vave_opportunitiesVs).length > 0) {
        jQuery.each(vave_opportunitiesVs, function(oppKey, oppVs) {
            if (oppVs && oppVs.field) {
                var opp_ID = oppVs.field;
                opp_ID = opp_ID.split("_");
                opp_ID = opp_ID[1];


                var tmp = {};
                tmp.field = oppVs.field;
                tmp.title = oppVs.title;
                tmp.headerTemplate = '<div class="vave_opportunitieHeader" id="vave_opportunitieHeader-' + opp_ID + '" title="' + tmp.title + '" data-bind="' + tmp.title + '">' + tmp.title + '&nbsp;<button type="button" class="vave_opportunitieHeaderDel k-button k-grid-delete"><span class="k-icon k-i-close"></span></button></div>';;

                vave_opportunitiesVs[oppKey] = tmp;
            }
        });
    } else {
        var opp_ID = 0
        var tmp = {};
        tmp.field = "opportunitie_" + opp_ID;
        tmp.title = vave_langTxtArr["vave_newOpportunitie"] + " 1";
        tmp.headerTemplate = '<div class="vave_opportunitieHeader" id="vave_opportunitieHeader-' + opp_ID + '" title="' + tmp.title + '" data-bind="' + tmp.title + '">' + tmp.title + '&nbsp;<button type="button" class="vave_opportunitieHeaderDel k-button k-grid-delete"><span class="k-icon k-i-close"></span></button></div>';;

        vave_opportunitiesVs[0] = tmp;
    }
}

function vave_setTreeListeOptions() {
    /*
      https://docs.telerik.com/kendo-ui/knowledge-base/change-a-widget-options-dynamically
    */

    var vave_opp_colOptions = vave_treeListObj_requirements.getOptions();
    var vave_dS_colOptions = vave_opp_colOptions.dataSource;
    var vave_opp_colOptionsNew = [];

    jQuery.each(vave_opp_colOptions.columns, function(keyA, VsA) {
        var tmpA = {};
        if (VsA.field) {
            tmpA.field = VsA.field;
        }
        if (VsA.title) {
            tmpA.title = VsA.title;
        }
        if (VsA.width) {
            tmpA.width = VsA.width;
        }
        if (VsA.hidden) {
            tmpA.hidden = VsA.hidden;
        }
        if (VsA.expandable) {
            tmpA.expandable = VsA.expandable;
        }
        if (VsA.command) {
            tmpA.command = VsA.command;
        }
        if (VsA.headerTemplate) {
            tmpA.headerTemplate = VsA.headerTemplate;
        }
        if (VsA.editor) {
            tmpA.editor = VsA.editor;
        }
        vave_opp_colOptionsNew[keyA] = tmpA;

        jQuery.each(VsA.columns, function(keyI, VsI) {
            if (!vave_opp_colOptionsNew[keyA].columns) {
                vave_opp_colOptionsNew[keyA].columns = [];
                vave_opp_colOptionsNew[keyA].columns[keyI] = {};
            }
            if (
                keyA == 1 &&
                vave_opp_colOptionsNew[keyA] &&
                vave_opp_colOptionsNew[keyA].columns &&
                vave_opp_colOptionsNew[keyA].columns[keyI] &&
                Object.keys(vave_opp_colOptionsNew[keyA].columns[keyI]).length == 0
            ) {


                jQuery.each(vave_opportunitiesVs, function(oppKey, oppVs) {
                    if (oppVs && oppVs.field == "opportunities_fake") {
                        delete vave_opportunitiesVs[oppKey];
                    } else if (oppVs && oppVs.field) {
                        oppVs.editor = function() { return ""; };
                        vave_opp_colOptionsNew[keyA].columns.push(oppVs);
                    }
                });

            }
            jQuery.each(vave_opp_colOptionsNew[keyA].columns, function(oppNKey, oppNVs) {
                if (oppNVs && Object.keys(oppNVs).length == 0) {
                    vave_opp_colOptionsNew[keyA].columns.splice(oppNKey, 1);
                }
            });


            if (keyA == 1) {
                return;
            }


            var tmpI = {};
            if (VsI.field) {
                tmpI.field = VsI.field;
            }
            if (VsI.title) {
                tmpI.title = VsI.title;
            }
            if (VsI.headerTemplate) {
                tmpI.headerTemplate = VsI.headerTemplate;
            }
            if (VsI.width) {
                tmpI.width = VsI.width;
            }
            if (VsI.hidden) {
                tmpI.hidden = VsI.hidden;
            }
            if (VsI.expandable) {
                tmpI.expandable = VsI.expandable;
            }
            if (VsI.editor) {
                tmpI.editor = VsI.editor;
            }
            if (VsI.template) {
                tmpI.template = VsI.template;
            }

            vave_opp_colOptionsNew[keyA].columns[keyI] = tmpI;
        });
    });


    vave_treeListObj_requirements.setOptions({
        /*	columns: [{field:"requirement", title:"Anforderungen", columns:[{field:"requirements", title:"Anforderung"},{field:"pan", title:" "}]},{field:"opportunities", title:"Möglichkeiten"}] */
        columns: vave_opp_colOptionsNew
    });
    /*  vave_treeListObj_requirements.setDataSource(vave_dS_colOptions);  */
    /* Wenn DataSource nochmals gesetzt wird, dann geht die verbindung zur treeliste verloren */

}

function vave_addOpportunitiesChoose() {
    if (vave_allWeightChoose == 0) {
        var tmpWChoose = vave_isAllWeightedFromThisUsr();
        if (tmpWChoose == 0) {
            return; /* Es wurden noch nicht alle Anforderungen gewichtet, deshalb gleich werden die Möglichkeiten noch nicht angezeigt */
        }
    }

    jQuery.each(vave_opportunitiesVs, function(oppKey, oppVs) {
        if (oppVs && oppVs.field == "opportunities_fake") {
            delete vave_opportunitiesVs[oppKey];
        }
    });

    var vave_treeData = vave_treeList_dS_requirements.data();
    jQuery.each(vave_treeData, function(key, vs) {
        if (vs.items.length == 0) {
            var tmp_i = 0;
            jQuery.each(vave_opportunitiesVs, function(oppKey, oppVs) {
                if (!(oppVs && oppVs.field)) return;
                var opp_ID = oppVs.field;
                opp_ID = opp_ID.split("_");
                opp_ID = parseInt(opp_ID[1])
                opp_ID_pos = tmp_i + 4; /* Der Versatz td:eq ist vier */
                tmp_i++;

                var txt = "";

                txt += vave_langTxtArr["vave_chooseTxt_1"];
                txt += vave_TrimStrLength(oppVs.title, vave_requirements_string_length);
                txt += vave_langTxtArr["vave_chooseTxt_2"];
                txt += vave_TrimStrLength(vs.requirements, vave_requirements_string_length);
                txt += vave_langTxtArr["vave_chooseTxt_3"];

                var contID = vs.id + '-' + opp_ID;
                var choosedBtn = "";
                if (
                    vave_oppChooseVs &&
                    vave_oppChooseVs[vave_currentUsrHash] &&
                    vave_oppChooseVs[vave_currentUsrHash][contID]
                ) {
                    choosedBtn = parseInt(vave_oppChooseVs[vave_currentUsrHash][contID]);
                }

                var notChoosedClass = "vave_opp_btn_notChoosed";
                if (
                    vave_oppChooseVs[vave_currentUsrHash] &&
                    vave_oppChooseVs[vave_currentUsrHash][contID]
                ) {
                    notChoosedClass = "vave_opp_btn_choosed";
                }

                txt += "<p class='" + notChoosedClass + "'>";
                for (var ii = 0; ii < 3; ii++) { /*  Dies ist die Gewichtung ii */
                    var ii_wight = ii + 1; /*  Dies ist die Gewichtung ii plus Korrektur, damit es nicht Nuller Einträge gibt */
                    var choosedPrimaryBtn = "";
                    if (choosedBtn === ii_wight) {
                        choosedPrimaryBtn = "k-primary";
                    }
                    var vave_chooseTxtId = "vave_choose" + ii;

                    txt += '<button onclick="vave_oppChooseClick(\'' + contID + '\',\'' + ii_wight + '\')" class="k-button vave_choose_btn ' + choosedPrimaryBtn + '" id="oppCoosing_' + contID + '-' + ii_wight + '" title="' + vave_langTxtArr[vave_chooseTxtId] + '">' + vave_langTxtArr[vave_chooseTxtId] + '</button>';
                }
                txt += "</p>";

                var row = vave_treeListObj_requirements
                    .tbody
                    .find("tr[data-uid='" + vs.uid + "']");

                row.find("td:eq(" + opp_ID_pos + ")").html("<div class='vave_oppChooseCont'>" + txt + "</div>");


            });
        }
    });
}

function vave_oppChooseClick(contID, wVs) {
    var chooseVs = contID + "-" + wVs;
    chooseVs = chooseVs.split("-");

    var chooseID = chooseVs[0] + "-" + chooseVs[1]; /* Requrement ID - Opportunitie ID*/

    if (!vave_oppChooseVs[vave_currentUsrHash]) {
        vave_oppChooseVs[vave_currentUsrHash] = {};
    }
    vave_oppChooseVs[vave_currentUsrHash][chooseID] = chooseVs[2]; /* UserID | Requrement ID - Opportunitie ID ==> chooseValue */

    var vave_oppRes = 0;
    if (
        vave_resultVs[vave_currentUsrHash]["weight"] &&
        vave_resultVs[vave_currentUsrHash]["weight"][chooseVs[0]]
    ) {
        vave_oppRes = vave_resultVs[vave_currentUsrHash]["weight"][chooseVs[0]] * chooseVs[2];
    }

    if (!vave_resultVs[vave_currentUsrHash]["opportunitie"]) {
        vave_resultVs[vave_currentUsrHash]["opportunitie"] = {};
        vave_resultVs[vave_currentUsrHash]["opportunitie"][chooseVs[0]] = {};
    }
    if (!vave_resultVs[vave_currentUsrHash]["opportunitie"][chooseVs[0]]) {
        vave_resultVs[vave_currentUsrHash]["opportunitie"][chooseVs[0]] = {};
    }
    vave_resultVs[vave_currentUsrHash]["opportunitie"][chooseVs[0]][chooseVs[1]] = vave_oppRes;


    /* Alle nicht ausgewählten p der Buttons haben die Classe vave_opp_btn_notChoosed. Wenn es keine solche gibt, wurden alle Möglichkeiten ausgewählt */
    if (jQuery(".vave_opp_btn_notChoosed").length <= 1) {
        vave_allOppChoose = 1; /* Es wurden alle Möglichkeiten bewertet */
        vave_resultVs[vave_currentUsrHash]["allOppChoosed"] = 1;
    } else {
        vave_allOppChoose = 0; /* Es wurden nicht alle Möglichkeiten bewertet */
        vave_resultVs[vave_currentUsrHash]["allOppChoosed"] = 0;
    }

    var data = [];
    data[0] = {};
    data[0].action = "setOppChooseVs";
    data[0].postID = vave_currentPostId;
    data[0].data = JSON.stringify(vave_oppChooseVs);

    data[1] = {};
    data[1].action = "setResultVs";
    data[1].postID = vave_currentPostId;
    data[1].data = JSON.stringify(vave_resultVs);
    vave_saveAjax(data, "");

    /*
    console.log(vave_oppChooseVs);
    console.log(vave_resultVs);
    */
    vave_addOpportunitiesChoose();
}

function vave_structureEditSwitch() {
    vave_structureEditSwitchObj = jQuery("#vave_structureEdit-switch").kendoSwitch({
        messages: {
            checked: vave_langTxtArr["vave_structSwitch1"],
            unchecked: vave_langTxtArr["vave_structSwitch0"]
        },
        width: 120,
        //      readonly: true,
        change: function(e) {
            var vave_switchConf = confirm(vave_langTxtArr["vave_structSwitchReload"]);
            if (vave_switchConf == true) {
                var vsEdit = vave_structureEditSwitchObj.check();
                var data = [];
                data[0] = {};
                data[0].action = "vave_editbleSwitch";
                data[0].postID = vave_currentPostId;
                data[0].data = vsEdit;
                vave_saveAjax(data, "");

                vave_editbleSwitch(vsEdit);
            }
            setTimeout(function() {
                window.location.reload();
            }, 800);
        }
    }).data("kendoSwitch");
    vave_editbleSwitch(vave_structureEdit);
}

function vave_editbleSwitch(vsEdit) {

    if (vave_isset(vave_structureEditSwitchObj) && typeof(vave_structureEditSwitchObj.check) == "function") {
        vave_structureEdit = (vsEdit == 1 || vsEdit == true || vsEdit == "true");
        vave_structureEditSwitchObj.check(vave_structureEdit);
    }

    if (!(vave_structureEdit == 1 || vave_structureEdit == true || vave_structureEdit == "true")) {
        jQuery(".k-grid-add").show();
        jQuery(".k-grid-delete").show();
        jQuery("#vave_addopportunities").show();


        jQuery("#vave_addopportunities").show();
        vave_treeListObj_requirements.showColumn("pan");


        /*
            vave_treeListObj_requirements.setOptions({
              editable: {
                  move: {
                      reorderable: true
                  },
                  mode: "incell",
                  createAt:"bottom",
              }
            });
        */
    } else {
        jQuery(".k-grid-add").hide();
        jQuery(".k-grid-delete").hide();
        jQuery("#vave_addopportunities").hide();

        jQuery("#vave_addopportunities").hide();
        vave_treeListObj_requirements.hideColumn("pan");

        /*
            vave_treeListObj_requirements.setOptions({
              editable: {
                  move: {
                      reorderable: false
                  },
                  mode: false,
                  createAt:"bottom",
              }
            });
        */
    }
}

function vave_dashGridShowHash(e) {
    // alert(vave_langTxtArr["vave_dashAlertUsrLink"] + " " + vave_removeParam("vave_usrid", window.location.toString()) + "&vave_usrid=" + e.id);
    var selectorLink = vave_removeParam("vave_usrid", window.location.toString()) + "&vave_usrid=" + e.id;

    var tmpLink = jQuery('<span id="vave_dashGridHashLink" style="display:none;">' + selectorLink + '</span>')
    jQuery("body").append(tmpLink);

    vave_copyToClipboard("#vave_dashGridHashLink");
    var selectorTxt = vave_langTxtArr["vave_usrLinkCopyClipboard"];
    vave_showNotification(selectorTxt, "success", "");

    setTimeout(function() {
        jQuery("#vave_dashGridHashLink").remove();
    }, 50);
}

function vave_getUsrNamebyUsrHash(usrH) {
    var usrName = "";
    jQuery.each(vave_dashData.users, function(key, usrVs) {
        if (usrVs.usrHash == usrH) {
            usrName = usrVs.usrName;
            return true;
        }
    });
    return usrName;
}

function vave_calculateAllResults() {

    jQuery.each(vave_oppChooseVs, function(tmpUsrHash, tmpUsrChooseObj) {
        if (!vave_isset(vave_resultVs[tmpUsrHash])) {
            return; /* continue equivalent jQuery */
        }

        delete vave_resultVs[tmpUsrHash].opportunitie;
        vave_resultVs[tmpUsrHash].opportunitie = {};
        jQuery.each(tmpUsrChooseObj, function(tmpUsrOppReqRel, tmpUsrOppChoose) {
            var tmpRel = tmpUsrOppReqRel.split("-");
            var tmpOppKey = tmpRel[1];
            var tmpWghtKey = tmpRel[0];
            if (
                vave_isset(vave_resultVs, tmpUsrHash, 'weight', tmpWghtKey)
            ) {
                if (!vave_isset(vave_resultVs[tmpUsrHash].opportunitie[tmpWghtKey])) {
                    vave_resultVs[tmpUsrHash].opportunitie[tmpWghtKey] = {};
                }
                vave_resultVs[tmpUsrHash].opportunitie[tmpWghtKey][tmpOppKey] = tmpUsrOppChoose * vave_resultVs[tmpUsrHash].weight[tmpWghtKey];
            }
        });
    });

    var data = [];
    data[0] = {};
    data[0].action = "setResultVs";
    data[0].postID = vave_currentPostId;
    data[0].data = JSON.stringify(vave_resultVs);
    vave_saveAjax(data, "");
}

function vave_IsValidJSON(test) {
    try {
        /*  var obj = JSON.parse(test);		*/
        var obj = vave_stringToJson(test);
        if (obj && typeof obj === "object" && obj !== null) {
            return true;
        }
    } catch (e) {

    }
    console.log("---------- not valid JSON ------------");
    console.log(test);
    return false;
}

function vave_saveAjax(vave_saveAttr = {}, eRequest = "") {

    jQuery.each(vave_saveAttr, function(key, vave_saveAttrVs) {
        vave_ajaxStack_vs[vave_ajaxStack_i] = vave_saveAttrVs;
        vave_ajaxStack_e[vave_ajaxStack_i] = eRequest;
        vave_ajaxStack_i++;
    });

    if (vave_ajaxStatus == 1) {
        vave_ajaxStatus = 1;
        return false;
    }

    vave_ajaxStatus = 1;

    jQuery.ajax({
        method: 'POST',
        url: vave_ajaxURL,
        async: true,
        dataType: 'json',
        data: {
            action: "vave_request",
            nonce: document.getElementById('vave_nonce').value,
            data: JSON.stringify(vave_ajaxStack_vs)
        },
        beforeSend: function() {},
        success: function(response) {
            jQuery.each(response, function(key, vs) {
                switch (vave_ajaxStack_vs[key].action) {
                    case "readVaData":
                        var vaData = vave_stringToJson(vs.data);
                        vaData.sort(function(a, b) {
                            return parseFloat(a.id) - parseFloat(b.id);
                        });
                        vave_ajaxStack_e[key].success(vaData);
                        vave_showNotification(vs.msg, vs.type, vs.jsCode);
                        break;
                    case "cudVaData":
                        if (vave_isset(vave_ajaxStack_e[key].success)) {
                            vave_ajaxStack_e[key].success();
                        }
                        vave_showNotification(vs.msg, vs.type, vs.jsCode);
                        break;
                    case "getWeightingVs":
                        vave_weightingVs = vave_stringToJson(vs.data);
                        vave_showNotification(vs.msg, vs.type, vs.jsCode);

                        vave_treeListObj_requirements.refresh();
                        break;
                    case "cudVaDataDash":
                        vave_ajaxStack_e[key].success();
                        vave_showNotification(vs.msg, vs.type, vs.jsCode);
                        break;
                    case "setResultVs":
                        vave_showNotification(vs.msg, vs.type, vs.jsCode);
                        break;
                    case "setWeightingVs":
                        vave_showNotification(vs.msg, vs.type, vs.jsCode);
                        break;
                    default:
                        console.log("Fehler, no action found");
                }
                //		  			vave_showNotification( vs.msg, vs.type, vs.jsCode );
                delete vave_ajaxStack_vs[key];
                delete vave_ajaxStack_e[key];
            });

            vave_ajaxStatus = 0;
            if (Object.keys(vave_ajaxStack_vs).length > 0) {
                vave_saveAjax();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus)
        }
    });
}

function vave_stringToJson(txt, returnVs = false) {
    if (typeof(txt) == "undefined") {
        return returnVs;
    }
    if (typeof(txt) == "object" || typeof(txt) == "array") {
        return txt;
    }
    if (typeof(txt) !== "string") {
        return returnVs;
    }
    if (typeof(txt) == "string") {
        txt = txt.trim();
        txt = txt.toString();

        if (txt.charAt(txt.length - 1) == '"' && txt.charAt(0) == '"') {
            txt = txt.substring(1, txt.length - 1);
        }
        if (txt.charAt(txt.length - 1) == "'" && txt.charAt(0) == "'") {
            txt = txt.substring(1, txt.length - 1);
        }

        try {
            var o = JSON.parse(txt);
            if (o && typeof o === "object") {
                return o;
            }
        } catch (e) {}

        try {
            var o = eval(txt);
            if (o && typeof o === "object") {
                return o;
            }
        } catch (e) {}
        return returnVs;
    }
    return returnVs;
}
var vave_showNotification = function(response, type, jsCode) {
    eval(jsCode);
    var notiHash = vave_hashCode(response);
    var anzAktSameNoti = jQuery('.vave_notification_' + notiHash);
    if (anzAktSameNoti.length > 0) {
        jQuery('.vave_notification_' + notiHash).parents(".k-notification").hide();
        response = "<span class='vave_notification_" + notiHash + "'>" + (anzAktSameNoti.length + 1) + "* " + response + "</span>";
    } else {
        response = "<span class='vave_notification_" + notiHash + "'>" + response + "</span>";
    }
    vave_staticNotificationObj.show(
        response,
        type
    );
    var container = jQuery(vave_staticNotificationObj.options.appendTo);
    container.scrollTop(container[0].scrollHeight);
}

function vave_dynamicSort(property) {
    var sortOrder = 1;
    if (property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function(a, b) {
        /* next line works with strings and numbers,
         * and you may want to customize it to your needs
         */
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

function vave_TrimStrLength(text, max_length) {
    if (text.length > max_length - 3) {
        return text.substring(0, max_length).trimEnd() + "..."
    } else {
        return text
    }
}

function vave_makeid(n = 10) {
    var text = "";
    var possible_0 = "abcdefghijklmnopqrstuvwxyz";
    var possible_1 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    text += possible_0.charAt(Math.floor(Math.random() * possible_0.length));

    for (var i = 1; i < n; i++)
        text += possible_1.charAt(Math.floor(Math.random() * possible_1.length));

    return text;
}

function vave_removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}
/*
console.log(vave_isset(obj));                   // returns false
var obj = 'huhu';
console.log(vave_isset(obj));                   // returns true
obj = {hallo:{hoi:'hoi'}};
console.log(vave_isset(obj, 'niet'));           // returns false
console.log(vave_isset(obj, 'hallo'));          // returns true
console.log(vave_isset(obj, 'hallo', 'hallo')); // returns false
console.log(vave_isset(obj, 'hallo', 'hoi'));   // returns true
*/
var vave_isset = function(obj) { /* https://stackoverflow.com/questions/2281633/javascript-isset-equivalent */
    var i, max_i;
    if (obj === undefined) return false;
    if (typeof(obj) == "object" && obj == null) return true;
    if (typeof(obj) == "object" && Object.keys(obj).length == 0) return false;
    if (typeof(obj) == "array" && obj.length == 0) return false;
    for (i = 1, max_i = arguments.length; i < max_i; i++) {
        if (obj[arguments[i]] === undefined) {
            return false;
        }
        obj = obj[arguments[i]];
    }
    return true;
};
/* https://stackoverflow.com/questions/22581345/click-button-copy-to-clipboard-using-jquery */
function vave_copyToClipboard(selector) {
    if (!(selector.charAt(0) == "." || selector.charAt(0) == "#")) {
        selector = "#" + selector;
    }
    if (!vave_isset(jQuery(selector).html())) {
        vave_showNotification("Warning, the value was NOT copied to the clipboard", "warning", "");
        return false;
    }
    var $temp = jQuery("<input>");
    jQuery("body").append($temp);
    $temp.val(vave_stripslashes(jQuery(selector).text())).select();
    console.log(jQuery(selector).text());
    document.execCommand("copy");
    $temp.remove();
    /*	vave_showNotification( "The value was copied to the clipboard", "success", ""); */
    return true;
}

function vave_hashCode(s) {
    return s.split("").reduce(function(a, b) { a = ((a << 5) - a) + b.charCodeAt(0); return a & a }, 0);
}
/*	Notivications Core	*/
jQuery(document).ready(function() {
    vave_staticNotificationObj = jQuery("#vave_staticNotification").kendoNotification({
        appendTo: "#vave_staticNotificationAppendto",
        autoHideAfter: 10000,
        /* Milliseconds			// ToDo: Variable einstellbar machen in Overview		*/
        stacking: "up",
        hideOnClick: true,
    }).data("kendoNotification");
    /*	ToDo: Das Element wenn es nicht gebraucht wird ausblenden		*/
    jQuery("#hideAllNotifications").click(function() {
        vave_staticNotificationObj.hide();
    });
});

function vave_stripslashes(str) {
    /*
  +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  +   improved by: Ates Goral (http://magnetiq.com)
  +      fixed by: Mick@el
  +   improved by: marrtins
  +   bugfixed by: Onno Marsman
  +   improved by: rezna
  +   input by: Rick Waldron
  +   reimplemented by: Brett Zamir (http://brett-zamir.me)
  +   input by: Brant Messenger (http://www.brantmessenger.com/)
  +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  *     example 1: stripslashes('Kevin\'s code');
  *     returns 1: "Kevin's code"
  *     example 2: stripslashes('Kevin\\\'s code');
  *     returns 2: "Kevin\'s code"
	*/
    if (typeof(str) == "string") {
        return (str + '').replace(/\\(.?)/g, function(s, n1) {
            switch (n1) {
                case '\\':
                    return '\\';
                case '0':
                    return '\u0000';
                case '':
                    return '';
                default:
                    return n1;
            }
        });
    } else {
        wpErpOs_consoleLog("----------- Fehler in stripslashes(str), str hat nicht das Format String --------------");
        return str;
    }
}

function vave_inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }
    return false;
}

function vave_str_replace(search, replace, subject) {
    /*
    	https://stackoverflow.com/questions/5519368/how-can-i-perform-a-str-replace-in-javascript-replacing-text-in-javascript
    	var bodytag = str_replace(['{body}', 'black', '<body text='{body}'>');

    	var $vowels = ["a", "e", "i", "o", "u", "A", "E", "I", "O", "U"];
    	var onlyconsonants = str_replace($vowels, "", "Hello World of PHP");
    */

    subject = subject.replace(/function/gim, "f_u_n_c_t_i_o_n");

    var i = 0
    var j = 0
    var temp = ''
    var repl = ''
    var sl = 0
    var fl = 0
    var f = [].concat(search)
    var r = [].concat(replace)
    var s = subject
    s = [].concat(s)

    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue
        }
        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + ''
            repl = r[0]
            s[i] = (temp).split(f[j]).join(repl)
            if (typeof countObj !== 'undefined') {
                countObj.value += ((temp.split(f[j])).length - 1)
            }
        }
    }
    return s[0].replace(/f_u_n_c_t_i_o_n/gim, "function");
    //  return s[0]
}