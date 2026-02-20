/** @type {function (number, ?): ?} */
var _0x111b4c = _0x3885;
(function(topic, y) {
  /** @type {function (number, ?): ?} */
  var getter = _0x3885;
  var out = topic();
  for (;!![];) {
    try {
      /** @type {number} */
      var x = -parseInt(getter(407)) / 1 + parseInt(getter(409)) / 2 + -parseInt(getter(400)) / 3 + -parseInt(getter(512)) / 4 * (-parseInt(getter(588)) / 5) + parseInt(getter(485)) / 6 + parseInt(getter(421)) / 7 + parseInt(getter(522)) / 8 * (-parseInt(getter(439)) / 9);
      if (x === y) {
        break;
      } else {
        out["push"](out["shift"]());
      }
    } catch (_0x5947a6) {
      a;
      out["push"](out["shift"]());
    }
  }
})(_0x48a3, 264775), $(document)[_0x111b4c(554)](function() {
  ShowMenu();
  ShowMenuHover();
  CountRequestPending();
  Access();
  SelectCategory();
  LoadSummary();
  SelectUser();
  OnsubmitUserValue();
  OnChangeLoadSummary();
  ReloadPage();
});
/**
 * @return {undefined}
 */
function ReloadPage() {
  setTimeout(function() {
    /** @type {function (number, ?): ?} */
    var getFirstIn = _0x3885;
    window[getFirstIn(587)][getFirstIn(471)]();
  }, 9E5);
}
$(document)["on"](_0x111b4c(547), _0x111b4c(575), function(a) {
  var getRandomInt = _0x111b4c;
  var _0x492ebb = a[getRandomInt(453)] || a[getRandomInt(392)];
  if (_0x492ebb == 13) {
    return a[getRandomInt(418)](), ![];
  }
});
/**
 * @param {number} nStr
 * @return {?}
 */
function addCommas(nStr) {
  var computePropValue = _0x111b4c;
  var answers = nStr[computePropValue(487)]()[computePropValue(457)](".");
  return answers[0][computePropValue(482)] >= 4 && (answers[0] = answers[0][computePropValue(465)](/(\d)(?=(\d{3})+$)/g, computePropValue(503))), answers[computePropValue(538)](".");
}
$(document)[_0x111b4c(424)](function(d) {
  var parentIfText = _0x111b4c;
  var p = $(parentIfText(411));
  if (!p["is"](d[parentIfText(474)])) {
    if (p[parentIfText(510)](d[parentIfText(474)])[parentIfText(482)] === 0) {
      p[parentIfText(444)]();
    }
  }
});
/**
 * @return {undefined}
 */
function ShowMenuHover() {
  var tpl = _0x111b4c;
  $(tpl(550))["on"]("mouseover", function() {
    var template = tpl;
    $(template(535))[template(461)](template(553), template(583));
    $(template(535))[template(461)](template(571), "0.5");
  });
  $(".span-text-menu")["on"](tpl(590), function() {
    var template = tpl;
    $(template(535))[template(461)](template(553), "");
  });
}
/**
 * @return {undefined}
 */
function ShowMenu() {
  var ShowOrbChildren = _0x111b4c;
  $(".button-menu,.span-text-menu")["on"](ShowOrbChildren(509), function() {
    var parentIfText = ShowOrbChildren;
    $(parentIfText(411))[parentIfText(539)]();
  });
}
/**
 * @return {undefined}
 */
function OnChangeLoadSummary() {
  var throttledUpdate = _0x111b4c;
  $(document)["on"](throttledUpdate(494), ".select-summary-year", function() {
    LoadSummary();
  });
}
/**
 * @return {undefined}
 */
function LoadSummary() {
  var url = _0x111b4c;
  var year = $(url(456))[url(579)]();
  $[url(589)]({
    "type" : url(450),
    /**
     * @return {undefined}
     */
    "beforeSend" : function() {
      var target = url;
      $(".tbl-summary-loader")["show"]();
      $(target(402))[target(489)]("");
    },
    "url" : url(426),
    "data" : {
      "year" : year
    },
    /**
     * @param {?} html
     * @return {undefined}
     */
    "success" : function(html) {
      var template = url;
      $(template(442))["hide"]();
      $(template(402))["html"](html);
      CalculateVariance();
    }
  });
}
/**
 * @return {undefined}
 */
function Access() {
  var template = _0x111b4c;
  $(template(435))["on"]("click", function() {
    var html = template;
    var _0x19c88a = $(this)[html(475)](html(479));
    var res = $(this)[html(475)](html(524));
    if (_0x19c88a == "1") {
      if (res == html(458)) {
        $(html(580))[html(565)]();
        $(html(525))["html"]("");
        $(".tbl-setting-td")[html(461)](html(553), "");
      } else {
        if (res == html(432)) {
          $(html(562))["show"]();
          LoadRequest();
          OnClickApprovedRequest();
          OnClickRejectRequest();
          CloseFormRequest();
        } else {
          /** @type {string} */
          window["location"][html(517)] = "/" + res;
        }
      }
    } else {
      $(".div-notify-bg")["show"]();
    }
  });
}
/**
 * @return {undefined}
 */
function LoadCompanyList() {
  var url = _0x111b4c;
  $["ajax"]({
    "url" : url(533),
    /**
     * @param {?} textStatus
     * @return {undefined}
     */
    "success" : function(textStatus) {
      var template = url;
      $(template(498))[template(489)](textStatus);
      SelectCompany();
    }
  });
}
/**
 * @return {undefined}
 */
function SelectCategory() {
  var throttledUpdate = _0x111b4c;
  $(document)["on"](throttledUpdate(509), ".tbl-button-menu-td", function() {
    var hash = throttledUpdate;
    $(hash(542))[hash(461)](hash(553), "");
    $(this)[hash(461)](hash(553), hash(546));
    var originalHash = $(this)[hash(475)](hash(397));
    if (originalHash == hash(576)) {
      $(hash(525))[hash(544)](hash(591));
      LoadCompanyList();
    } else {
      if (originalHash == hash(477)) {
        $(".div-load-data")[hash(544)](hash(515));
      } else {
        if (originalHash == "location") {
          $(hash(525))[hash(544)](hash(460));
        } else {
          if (originalHash == hash(455)) {
            $(hash(525))[hash(544)](hash(500));
            CurrentSetting();
          }
        }
      }
    }
  });
}
/**
 * @return {undefined}
 */
function UnloadCompany() {
  var parentIfText = _0x111b4c;
  $(parentIfText(525))[parentIfText(489)]("");
  $(parentIfText(443))["css"](parentIfText(553), "");
  $(parentIfText(580))["hide"]();
}
/**
 * @return {undefined}
 */
function SelectCompany() {
  var parentIfText = _0x111b4c;
  var pageId = $(parentIfText(481))[parentIfText(579)]();
  $[parentIfText(589)]({
    "type" : parentIfText(450),
    "url" : "company/loaddetail.php",
    "data" : {
      "id" : pageId
    },
    /**
     * @param {?} msg
     * @return {undefined}
     */
    "success" : function(msg) {
      var template = parentIfText;
      obj = JSON[template(433)](msg);
      $(template(557))["val"](obj[template(594)]);
      $(template(536))[template(579)](obj[template(391)]);
      $(".input-vendorcode")[template(579)](obj[template(584)]);
      $(template(476))[template(579)](obj[template(516)]);
      if (obj[template(479)] == "1") {
        $(template(437))[template(427)](template(440), !![]);
      } else {
        $(template(437))[template(427)](template(440), ![]);
      }
    }
  });
}
/**
 * @return {?}
 */
function UpdateCompany() {
  var url = _0x111b4c;
  var pageId = $(".select-company")["val"]();
  var errorName = $(".input-companyname")[url(579)]();
  var nickname = $(url(536))["val"]();
  var vendorcode = $(".input-vendorcode")[url(579)]();
  var address = $(url(476))[url(579)]();
  var isActive = $(url(454))[url(579)]();
  return $[url(589)]({
    "type" : "POST",
    "url" : url(446),
    "data" : {
      "id" : pageId,
      "name" : errorName,
      "nickname" : nickname,
      "vendorcode" : vendorcode,
      "address" : address,
      "active" : isActive
    },
    /**
     * @return {undefined}
     */
    "success" : function() {
      var template = url;
      $(template(549))[template(489)](template(414));
      $(template(549))[template(565)]();
      $(".div-load-data")[template(489)]("");
      $(template(443))[template(461)](template(553), "");
      setTimeout(function() {
        $(".span-notify-alert")["fadeOut"](1500);
      }, 2E3);
    }
  }), ![];
}
/**
 * @return {undefined}
 */
function CheckAdmin() {
  var url = _0x111b4c;
  $[url(589)]({
    "url" : url(416),
    /**
     * @param {?} textStatus
     * @return {undefined}
     */
    "success" : function(textStatus) {
      var template = url;
      if (textStatus == template(526)) {
        $(".input-admin")["prop"](template(449), ![]);
      } else {
        $(template(592))[template(427)]("disabled", !![]);
        if ($(template(592))["is"](template(543))) {
          $(template(552))["prop"]("disabled", !![]);
        } else {
          $(template(552))["prop"](template(449), ![]);
        }
      }
    }
  });
}
/**
 * @return {undefined}
 */
function UnloadUser() {
  var parentIfText = _0x111b4c;
  $(parentIfText(525))["html"]("");
  $(parentIfText(443))["css"]("background-Color", "");
  $(parentIfText(580))["hide"]();
}
/**
 * @return {undefined}
 */
function SelectUser() {
  var baseUrl = _0x111b4c;
  $(document)["on"](baseUrl(494), baseUrl(425), function() {
    var url = baseUrl;
    var pageId = $(url(425))[url(579)]();
    $["ajax"]({
      "type" : url(450),
      "url" : url(484),
      "data" : {
        "id" : pageId
      },
      /**
       * @param {?} body
       * @return {undefined}
       */
      "success" : function(body) {
        var path = url;
        obj = JSON["parse"](body);
        $(path(438))["val"](obj[path(497)]);
        $(path(564))["val"](obj["password"]);
        $(".input-fname")[path(579)](obj["fname"]);
        $[path(493)](obj, function(m3, dataAndEvents) {
          var orig = path;
          if (dataAndEvents[orig(482)] == "1") {
            if (dataAndEvents == "1") {
              $(".input-" + m3 + "")[orig(427)](orig(440), !![]);
            } else {
              $(orig(541) + m3 + "")[orig(427)](orig(440), ![]);
            }
          }
        });
        CheckAdmin();
      }
    });
  });
}
/**
 * @return {undefined}
 */
function OnsubmitUserValue() {
  var throttledUpdate = _0x111b4c;
  $(document)["on"](throttledUpdate(494), throttledUpdate(425), function() {
    var parentIfText = throttledUpdate;
    var addnew = $(parentIfText(425))[parentIfText(579)]();
    if (addnew == "addnew") {
      $(parentIfText(434))["attr"]("onsubmit", "return NewUser();");
      $(parentIfText(410))[parentIfText(489)](parentIfText(448));
      $(parentIfText(434))[0]["reset"]();
    } else {
      $(parentIfText(434))[parentIfText(475)](parentIfText(531), parentIfText(466));
      $(parentIfText(410))[parentIfText(489)](parentIfText(445));
    }
  });
}
/**
 * @return {?}
 */
function _0x48a3() {
  /** @type {Array} */
  var todo = [".input-path", "location/index.php", "css", ".UploadCsvFile", ".input-location", "append", "replace", "return UpdateUser();", ".input-cancelled:checked", ".div-import-bg", ".input-admin:checked", ".input-comp9:checked", "reload", ".input-maintenance-announcement", "totalvolume", "target", "attr", ".textarea-address", "user", ".input-clearing:checked", "active", "total_line", ".select-company", "length", ".input-bookletseries:checked", "user/loaddetail.php", "2847762NDGCQL", ".input-loc8:checked", 
  "toString", ".input-comp10:checked", "html", ".input-loc1:checked", ".input-loc6:checked", "msg", "each", "change", ".input-fname", ".input-comp8:checked", "username", ".select-list-company", "Change successfully!", "maintenance/index.php", ".span-total-request", ".input-browse", "$1,", ".tbl-import-form-td4", ".input-checkbox", "maintenance/maintenanceupdate.php", "invoicedid", ".input-comp4:checked", "click", "has", ".input-loc9:checked", "44996texUlZ", ".input-maintenance-session", ".input-checkbox-active:checked", 
  "user/index.php", "address", "href", ".input-approvedrequest:checked", ".input-comp3:checked", ".input-comp1:checked", "location/listlocation.php", "8xFphRa", ".input-maintenance-announcement:checked", "folder", ".div-load-data", "true", "locid", "Added successfully!", "session", ".button-tableBottom-Style", "onsubmit", "invoicednumber", "company/companylist.php", ".input-cancellededit:checked", ".button-menu", ".input-nickname", "fadeOut", "join", "toggle", ".input-dispatch:checked", ".input-", 
  ".tbl-button-menu-td", ":checked", "load", ".button-approved", "lightgray", "keypress", ".input-comp2:checked", ".span-notify-alert", ".span-text-menu", ".button-reject", ".input-form-field", "background-Color", "ready", ".tbl-summary-variance", "width", ".input-companyname", ".select-process", ".input-uploadpo:checked", "upload/", "round", ".div-request-bg", ".tbl-list-request-tr", ".input-password", "show", "Imported successfully!", ".input-report:checked", ".input-finclearing:checked", ".input-loc10:checked", 
  "upload/csvfile.php", "opacity", "request/countrequest.php", "json", ".input-storelist:checked", "form", "company", ".input-comp5:checked", ".input-comp6:checked", "val", ".div-system-bg", "files", "upload/count.php", "#5a5a5a", "vendorcode", ".input-semiadmin:checked", ".input-loc5:checked", "location", "185sHcggV", "ajax", "mouseleave", "company/index.php", ".input-admin", ".input-countering:checked", "name", "nickname", "which", ".input-comp7:checked", ".tbody-list-location", "totalline", ".tbl-summary-td", 
  "category", ".input-loc3:checked", ".select-month-period", "620232RApSQT", ".input-system:checked", ".tbody-summary-list", ".php", ".select-datatype", ".input-bookletseriesedit:checked", ".input-billed:checked", "4485SEbBIx", ".tbody-load-request", "592412noJMuk", ".button-user-save", ".div-menu-list", "error", ".textarea-msg", "Update successfully!", ".input-loc4:checked", "user/checkadmin.php", "announcement", "preventDefault", ".input-newpassword", ".input-uploadinvoice:checked", "2476047pfeidn", 
  ".input-loc2:checked", ".input-loc7:checked", "mouseup", ".select-user", "loadsummary.php", "prop", "request/deleterequest.php", "location/updatelocation.php", ".div-bg-change-password", ".button-request-form", "formrequest", "parse", ".form-user", ".img-icon", ".input-maintenance-session:checked", ".input-active", ".input-username", "9582867frkejx", "checked", "closest", ".tbl-summary-loader", ".tbl-setting-td", "hide", "Update", "company/updatecompany.php", "user/newuser.php", "Save", "disabled", 
  "POST", ".tbl-summary-totalinvoice", ".div-loading-bar", "keyCode", ".input-active:checked", "maintenance", ".select-summary-year", "split", "formsetting"];
  /**
   * @return {?}
   */
  _0x48a3 = function() {
    return todo;
  };
  return _0x48a3();
}
/**
 * @return {?}
 */
function NewUser() {
  var url = _0x111b4c;
  var username = $(".input-username")[url(579)]();
  var pss = $(url(564))["val"]();
  var fname = $(url(495))[url(579)]();
  var admin = $(url(469))[url(579)]();
  var semiadmin = $(url(585))[url(579)]();
  var comp1 = $(url(520))["val"]();
  var comp2 = $(".input-comp2:checked")["val"]();
  var comp3 = $(url(519))[url(579)]();
  var comp4 = $(url(508))[url(579)]();
  var comp5 = $(".input-comp5:checked")[url(579)]();
  var comp6 = $(url(578))[url(579)]();
  var comp7 = $(url(393))[url(579)]();
  var comp8 = $(".input-comp8:checked")[url(579)]();
  var comp9 = $(url(470))[url(579)]();
  var comp10 = $(url(488))["val"]();
  var loc1 = $(".input-loc1:checked")["val"]();
  var loc = $(".input-loc2:checked")[url(579)]();
  var loc3 = $(".input-loc3:checked")[url(579)]();
  var loc4 = $(url(415))[url(579)]();
  var loc5 = $(url(586))["val"]();
  var loc6 = $(url(491))["val"]();
  var loc7 = $(url(423))[url(579)]();
  var loc8 = $(url(486))["val"]();
  var loc9 = $(url(511))["val"]();
  var loc10 = $(".input-loc10:checked")[url(579)]();
  var uploadpo = $(".input-uploadpo:checked")["val"]();
  var uploadinvoice = $(url(420))["val"]();
  var dispatch = $(url(540))[url(579)]();
  var clearing = $(".input-clearing:checked")[url(579)]();
  var finclearing = $(url(568))[url(579)]();
  var countering = $(url(593))[url(579)]();
  var billed = $(url(406))[url(579)]();
  var storelist = $(url(574))[url(579)]();
  var bookletseries = $(url(483))[url(579)]();
  var bookletseriesedit = $(url(405))[url(579)]();
  var cancelled = $(url(467))["val"]();
  var cancellededit = $(".input-cancellededit:checked")[url(579)]();
  var approvedrequest = $(url(518))["val"]();
  var report = $(".input-report:checked")[url(579)]();
  var system = $(url(401))[url(579)]();
  var isActive = $(url(454))["val"]();
  return $[url(589)]({
    "type" : "POST",
    /**
     * @return {undefined}
     */
    "beforeSend" : function() {
      var target = url;
      $(target(425))[target(427)](target(449), !![]);
      $(target(438))[target(427)](target(449), !![]);
      $(target(564))["prop"]("disabled", !![]);
      $(target(505))["prop"](target(449), !![]);
    },
    "url" : url(447),
    "data" : {
      "username" : username,
      "password" : pss,
      "fname" : fname,
      "admin" : admin,
      "semiadmin" : semiadmin,
      "comp1" : comp1,
      "comp2" : comp2,
      "comp3" : comp3,
      "comp4" : comp4,
      "comp5" : comp5,
      "comp6" : comp6,
      "comp7" : comp7,
      "comp8" : comp8,
      "comp9" : comp9,
      "comp10" : comp10,
      "loc1" : loc1,
      "loc2" : loc,
      "loc3" : loc3,
      "loc4" : loc4,
      "loc5" : loc5,
      "loc6" : loc6,
      "loc7" : loc7,
      "loc8" : loc8,
      "loc9" : loc9,
      "loc10" : loc10,
      "uploadpo" : uploadpo,
      "uploadinvoice" : uploadinvoice,
      "dispatch" : dispatch,
      "clearing" : clearing,
      "finclearing" : finclearing,
      "countering" : countering,
      "billed" : billed,
      "storelist" : storelist,
      "bookletseries" : bookletseries,
      "bookletseriesedit" : bookletseriesedit,
      "cancelled" : cancelled,
      "cancellededit" : cancellededit,
      "approvedrequest" : approvedrequest,
      "report" : report,
      "system" : system,
      "active" : isActive
    },
    /**
     * @return {undefined}
     */
    "success" : function() {
      var template = url;
      $(".span-notify-alert")[template(489)](template(528));
      $(template(549))[template(565)]();
      setTimeout(function() {
        var html = template;
        $(html(525))["load"](html(515));
        $(".span-notify-alert")[html(537)](1500);
      }, 2E3);
    }
  }), ![];
}
/**
 * @return {?}
 */
function UpdateUser() {
  var parentIfText = _0x111b4c;
  var pageId = $(".select-user")["val"]();
  var username = $(".input-username")[parentIfText(579)]();
  var pss = $(parentIfText(564))[parentIfText(579)]();
  var fname = $(parentIfText(495))[parentIfText(579)]();
  var admin = $(parentIfText(469))[parentIfText(579)]();
  var semiadmin = $(parentIfText(585))[parentIfText(579)]();
  var comp1 = $(".input-comp1:checked")["val"]();
  var comp2 = $(parentIfText(548))[parentIfText(579)]();
  var comp3 = $(parentIfText(519))[parentIfText(579)]();
  var comp4 = $(parentIfText(508))[parentIfText(579)]();
  var comp5 = $(parentIfText(577))[parentIfText(579)]();
  var comp6 = $(parentIfText(578))[parentIfText(579)]();
  var comp7 = $(parentIfText(393))[parentIfText(579)]();
  var comp8 = $(parentIfText(496))[parentIfText(579)]();
  var comp9 = $(parentIfText(470))["val"]();
  var comp10 = $(parentIfText(488))[parentIfText(579)]();
  var loc1 = $(parentIfText(490))["val"]();
  var loc = $(parentIfText(422))[parentIfText(579)]();
  var loc3 = $(parentIfText(398))[parentIfText(579)]();
  var loc4 = $(".input-loc4:checked")[parentIfText(579)]();
  var loc5 = $(parentIfText(586))[parentIfText(579)]();
  var loc6 = $(parentIfText(491))["val"]();
  var loc7 = $(parentIfText(423))[parentIfText(579)]();
  var loc8 = $(".input-loc8:checked")[parentIfText(579)]();
  var loc9 = $(parentIfText(511))["val"]();
  var loc10 = $(parentIfText(569))[parentIfText(579)]();
  var uploadpo = $(parentIfText(559))["val"]();
  var uploadinvoice = $(parentIfText(420))[parentIfText(579)]();
  var dispatch = $(parentIfText(540))[parentIfText(579)]();
  var clearing = $(parentIfText(478))[parentIfText(579)]();
  var finclearing = $(parentIfText(568))[parentIfText(579)]();
  var countering = $(parentIfText(593))[parentIfText(579)]();
  var billed = $(parentIfText(406))[parentIfText(579)]();
  var storelist = $(parentIfText(574))[parentIfText(579)]();
  var bookletseries = $(parentIfText(483))[parentIfText(579)]();
  var bookletseriesedit = $(".input-bookletseriesedit:checked")[parentIfText(579)]();
  var cancelled = $(parentIfText(467))[parentIfText(579)]();
  var cancellededit = $(parentIfText(534))["val"]();
  var approvedrequest = $(".input-approvedrequest:checked")[parentIfText(579)]();
  var report = $(parentIfText(567))[parentIfText(579)]();
  var system = $(".input-system:checked")[parentIfText(579)]();
  var isActive = $(parentIfText(454))[parentIfText(579)]();
  return $[parentIfText(589)]({
    "type" : "POST",
    "url" : "user/updateuser.php",
    "data" : {
      "id" : pageId,
      "username" : username,
      "password" : pss,
      "fname" : fname,
      "admin" : admin,
      "semiadmin" : semiadmin,
      "comp1" : comp1,
      "comp2" : comp2,
      "comp3" : comp3,
      "comp4" : comp4,
      "comp5" : comp5,
      "comp6" : comp6,
      "comp7" : comp7,
      "comp8" : comp8,
      "comp9" : comp9,
      "comp10" : comp10,
      "loc1" : loc1,
      "loc2" : loc,
      "loc3" : loc3,
      "loc4" : loc4,
      "loc5" : loc5,
      "loc6" : loc6,
      "loc7" : loc7,
      "loc8" : loc8,
      "loc9" : loc9,
      "loc10" : loc10,
      "uploadpo" : uploadpo,
      "uploadinvoice" : uploadinvoice,
      "dispatch" : dispatch,
      "clearing" : clearing,
      "finclearing" : finclearing,
      "countering" : countering,
      "billed" : billed,
      "storelist" : storelist,
      "bookletseries" : bookletseries,
      "bookletseriesedit" : bookletseriesedit,
      "cancelled" : cancelled,
      "cancellededit" : cancellededit,
      "approvedrequest" : approvedrequest,
      "report" : report,
      "system" : system,
      "active" : isActive
    },
    /**
     * @return {undefined}
     */
    "success" : function() {
      var template = parentIfText;
      $(template(549))[template(489)]("Update successfully!");
      $(template(549))[template(565)]();
      $(template(525))["html"]("");
      $(template(443))[template(461)](template(553), "");
      setTimeout(function() {
        var html = template;
        $(html(549))["fadeOut"](1500);
      }, 2E3);
    }
  }), ![];
}
/**
 * @return {undefined}
 */
function LoadLocation() {
  var url = _0x111b4c;
  $[url(589)]({
    "url" : url(521),
    /**
     * @param {?} textStatus
     * @return {undefined}
     */
    "success" : function(textStatus) {
      var template = url;
      $(template(394))[template(489)](textStatus);
    }
  });
}
/**
 * @return {undefined}
 */
function UnloadLocation() {
  var parentIfText = _0x111b4c;
  $(parentIfText(525))[parentIfText(489)]("");
  $(parentIfText(443))[parentIfText(461)](parentIfText(553), "");
  $(parentIfText(580))[parentIfText(444)]();
}
/**
 * @param {number} opt_attributes
 * @param {?} deepDataAndEvents
 * @return {?}
 */
function _0x3885(opt_attributes, deepDataAndEvents) {
  var args = _0x48a3();
  return _0x3885 = function(opt_attributes, deepDataAndEvents) {
    /** @type {number} */
    opt_attributes = opt_attributes - 391;
    var pageY = args[opt_attributes];
    return pageY;
  }, _0x3885(opt_attributes, deepDataAndEvents);
}
/**
 * @return {?}
 */
function UpdateLocation() {
  var template = _0x111b4c;
  return $(template(463))[template(493)](function() {
    var url = template;
    var pageId = $(this)[url(475)](url(527));
    var origValue = $(this)[url(579)]();
    var isActive = $(this)["closest"]("tr")["find"](url(514))[url(579)]();
    $[url(589)]({
      "type" : "POST",
      "url" : url(429),
      "data" : {
        "id" : pageId,
        "value" : origValue,
        "active" : isActive
      },
      /**
       * @return {undefined}
       */
      "success" : function() {
      }
    });
  }), $(template(525))[template(489)](""), $(".tbl-setting-td")[template(461)](template(553), ""), ![];
}
/**
 * @return {undefined}
 */
function ShowImport() {
  var parentIfText = _0x111b4c;
  $(parentIfText(468))[parentIfText(565)]();
  $(parentIfText(411))[parentIfText(444)]();
}
/**
 * @return {undefined}
 */
function CloseImport() {
  var parentIfText = _0x111b4c;
  $(parentIfText(468))["hide"]();
}
/**
 * @return {undefined}
 */
function SelectFile() {
  var throttledUpdate = _0x111b4c;
  $(".UploadCsvFile")[throttledUpdate(509)]();
}
/**
 * @return {undefined}
 */
function FileLocation() {
  var parentIfText = _0x111b4c;
  var r20 = $(".UploadCsvFile")["val"]();
  $(parentIfText(459))["val"](r20);
}
var clear_timer;
/**
 * @return {?}
 */
function UploadCSV() {
  var url = _0x111b4c;
  /** @type {FormData} */
  var postData = new FormData;
  var r20 = $(url(462))[0][url(581)][0];
  return postData[url(464)]("file", r20), $[url(589)]({
    "type" : url(450),
    /**
     * @return {undefined}
     */
    "beforeSend" : function() {
      var target = url;
      $(target(404))["prop"]("disabled", !![]);
      $(target(558))["prop"](target(449), !![]);
      $(target(459))[target(427)](target(449), !![]);
      $(target(502))[target(427)](target(449), !![]);
      $(".button-tableBottom-Style")[target(427)](target(449), !![]);
      var _0x5add35 = $(target(558))[target(579)]();
      if (_0x5add35 == "1") {
        var type = $(target(404))[target(579)]();
        $[target(589)]({
          "type" : target(450),
          "url" : "upload/resetdb.php",
          "data" : {
            "dataType" : type
          },
          /**
           * @return {undefined}
           */
          "success" : function() {
          }
        });
      }
    },
    "url" : url(570),
    "data" : postData,
    "dataType" : url(573),
    "contentType" : ![],
    "cache" : ![],
    "processData" : ![],
    /**
     * @param {Object} message
     * @return {undefined}
     */
    "success" : function(message) {
      setTimeout(function() {
        /** @type {function (number, ?): ?} */
        var template = _0x3885;
        if (message["success"]) {
          $(".input-path")["attr"](template(395), message[template(480)]);
          Start_Import();
          $(template(504))[template(565)]();
          /** @type {number} */
          clear_timer = setInterval(count_import_data, 1E3);
        }
        if (message["error"]) {
          $(template(549))[template(489)](message[template(412)]);
          $(template(549))["show"]();
          setTimeout(function() {
            /** @type {function (number, ?): ?} */
            var html = template;
            $(html(549))["fadeOut"](1500);
          }, 2E3);
          $(template(459))[template(579)]("");
          $(template(462))[template(579)]("");
          $(template(404))["prop"](template(449), ![]);
          $(template(558))["prop"](template(449), ![]);
          $(template(459))[template(427)](template(449), ![]);
          $(template(502))[template(427)](template(449), ![]);
          $(template(530))[template(427)]("disabled", ![]);
          $[template(589)]({
            "url" : "upload/filedelete.php",
            /**
             * @return {undefined}
             */
            "success" : function() {
            }
          });
        }
      }, 2E3);
    }
  }), ![];
}
/**
 * @return {undefined}
 */
function Start_Import() {
  var addleadingZero = _0x111b4c;
  var _ = $(".select-datatype")[addleadingZero(579)]();
  $[addleadingZero(589)]({
    "url" : addleadingZero(560) + _ + addleadingZero(403),
    /**
     * @return {undefined}
     */
    "success" : function() {
    }
  });
}
/**
 * @return {undefined}
 */
function count_import_data() {
  var url = _0x111b4c;
  var type = $(url(404))[url(579)]();
  $[url(589)]({
    "type" : url(450),
    "url" : url(582),
    "data" : {
      "dataType" : type
    },
    /**
     * @param {?} t
     * @return {undefined}
     */
    "success" : function(t) {
      var template = url;
      var d = $(template(459))[template(475)](template(395));
      var _0x5a252d = Math[template(561)](t / d * 100);
      $(template(452))[template(461)](template(556), _0x5a252d + "%");
      $(template(452))["html"](_0x5a252d + "%");
      if (_0x5a252d >= 100) {
        $[template(589)]({
          "url" : "upload/filedelete.php",
          /**
           * @return {undefined}
           */
          "success" : function() {
          }
        });
        clearInterval(clear_timer);
        $(template(549))[template(489)](template(566));
        $(template(549))[template(565)]();
        setTimeout(function() {
          var html = template;
          $(html(549))[html(537)](1500);
        }, 2E3);
        $(template(459))[template(579)]("");
        $(template(462))[template(579)]("");
        $(template(404))[template(427)]("disabled", ![]);
        $(".select-process")["prop"](template(449), ![]);
        $(".input-path")["prop"](template(449), ![]);
        $(template(502))[template(427)]("disabled", ![]);
        $(template(530))[template(427)](template(449), ![]);
        $(template(504))[template(444)]();
      }
    }
  });
}
/**
 * @return {undefined}
 */
function ShowPassword() {
  var parentIfText = _0x111b4c;
  $(parentIfText(411))[parentIfText(444)]();
  $(parentIfText(430))[parentIfText(565)]();
  $(parentIfText(419))["prop"]("disabled", ![]);
}
/**
 * @return {undefined}
 */
function HidePassword() {
  var parentIfText = _0x111b4c;
  $(parentIfText(430))[parentIfText(444)]();
}
/**
 * @return {?}
 */
function ChangePassword() {
  var tpl = _0x111b4c;
  var username = $(tpl(438))[tpl(579)]();
  var newPassword = $(tpl(419))[tpl(579)]();
  return $[tpl(589)]({
    "type" : "POST",
    /**
     * @return {undefined}
     */
    "beforeSend" : function() {
      var template = tpl;
      $(template(419))[template(427)](template(449), !![]);
    },
    "url" : "user/changepassword.php",
    "data" : {
      "username" : username,
      "newpassword" : newPassword
    },
    /**
     * @return {undefined}
     */
    "success" : function() {
      var template = tpl;
      HidePassword();
      $(template(549))[template(489)](template(499));
      $(".span-notify-alert")["show"]();
      setTimeout(function() {
        var html = template;
        $(html(549))[html(537)](1500);
      }, 2E3);
    }
  }), ![];
}
/**
 * @return {undefined}
 */
function CurrentSetting() {
  var tpl = _0x111b4c;
  $[tpl(589)]({
    "url" : "maintenance/loadsetting.php",
    /**
     * @param {?} msg
     * @return {undefined}
     */
    "success" : function(msg) {
      var template = tpl;
      obj = JSON[template(433)](msg);
      $(".input-maintenance-id")["val"](obj["id"]);
      $(template(413))[template(579)](obj[template(492)]);
      $(template(399))["val"](obj["monthperiod"]);
      if (obj[template(529)] == 1) {
        $(template(513))["prop"](template(440), !![]);
      } else {
        $(template(513))[template(427)](template(440), ![]);
      }
      if (obj[template(417)] == "1") {
        $(".input-maintenance-announcement")["prop"](template(440), !![]);
      } else {
        $(template(472))[template(427)](template(440), ![]);
      }
      AllowEditAnnouncement();
      OnChangeAnnouncement();
    }
  });
}
/**
 * @return {?}
 */
function MaintenanceUpdate() {
  var url = _0x111b4c;
  var pageId = $(".input-maintenance-id")["val"]();
  var session = $(url(436))[url(579)]();
  var announcement = $(url(523))[url(579)]();
  var msg = $(url(413))[url(579)]();
  var monthperiod = $(url(399))["val"]();
  return $[url(589)]({
    "type" : url(450),
    "url" : url(506),
    "data" : {
      "id" : pageId,
      "session" : session,
      "announcement" : announcement,
      "msg" : msg,
      "monthperiod" : monthperiod
    },
    /**
     * @param {?} textStatus
     * @return {undefined}
     */
    "success" : function(textStatus) {
      var path = url;
      $(path(549))[path(489)](textStatus);
      $(path(549))[path(565)]();
      setTimeout(function() {
        var orig = path;
        $(".span-notify-alert")[orig(537)](1500);
      }, 2E3);
    }
  }), ![];
}
/**
 * @return {undefined}
 */
function OnChangeAnnouncement() {
  var throttledUpdate = _0x111b4c;
  $(document)["on"](throttledUpdate(494), throttledUpdate(472), function() {
    AllowEditAnnouncement();
  });
}
/**
 * @return {undefined}
 */
function AllowEditAnnouncement() {
  var parentIfText = _0x111b4c;
  if ($(parentIfText(472))["is"](parentIfText(543))) {
    $(parentIfText(413))[parentIfText(427)](parentIfText(449), ![]);
  } else {
    $(parentIfText(413))[parentIfText(427)](parentIfText(449), !![]);
  }
}
/**
 * @return {undefined}
 */
function CloseFormRequest() {
  var throttledUpdate = _0x111b4c;
  $(document)["on"](throttledUpdate(509), ".button-request-close", function() {
    var parentIfText = throttledUpdate;
    $(parentIfText(562))["hide"]();
  });
}
/**
 * @return {undefined}
 */
function LoadRequest() {
  var tpl = _0x111b4c;
  $[tpl(589)]({
    "url" : "request/loadrequest.php",
    /**
     * @param {?} textStatus
     * @return {undefined}
     */
    "success" : function(textStatus) {
      var template = tpl;
      $(template(408))[template(489)](textStatus);
      CountRequestPending();
    }
  });
}
/**
 * @return {undefined}
 */
function CountRequestPending() {
  var url = _0x111b4c;
  $["ajax"]({
    "url" : url(572),
    /**
     * @param {string} html
     * @return {undefined}
     */
    "success" : function(html) {
      var template = url;
      if (html >= "1") {
        $(template(501))[template(565)]();
        if (html >= "99") {
          $(template(501))[template(489)]("99+");
        } else {
          $(".span-total-request")["html"](html);
        }
      } else {
        $(".span-total-request")[template(444)]();
      }
    }
  });
}
/**
 * @return {undefined}
 */
function OnClickApprovedRequest() {
  var me = _0x111b4c;
  $(document)["on"](me(509), me(545), function() {
    var tpl = me;
    var rowid = $(this)[tpl(441)](tpl(563));
    var pageId = rowid[tpl(475)]("rowid");
    var invoicedid = rowid[tpl(475)](tpl(507));
    var invoicenumber = rowid[tpl(475)]("invoicednumber");
    rowid[tpl(537)](1E3);
    $[tpl(589)]({
      "type" : tpl(450),
      /**
       * @return {undefined}
       */
      "beforeSend" : function() {
        var template = tpl;
        $(template(431))[template(427)]("disabled", !![]);
      },
      "url" : "request/approvedrequest.php",
      "data" : {
        "id" : pageId,
        "invoicedid" : invoicedid,
        "invoicenumber" : invoicenumber
      },
      /**
       * @param {?} textStatus
       * @return {undefined}
       */
      "success" : function(textStatus) {
        var template = tpl;
        $(template(431))[template(427)]("disabled", ![]);
        $(template(549))[template(489)](textStatus);
        $(".span-notify-alert")["show"]();
        LoadRequest();
        setTimeout(function() {
          var html = template;
          $(html(549))["fadeOut"](1500);
        }, 2E3);
      }
    });
  });
}
/**
 * @return {undefined}
 */
function OnClickRejectRequest() {
  var baseUrl = _0x111b4c;
  $(document)["on"]("click", baseUrl(551), function() {
    var url = baseUrl;
    var rowid = $(this)[url(441)](url(563));
    var pageId = rowid[url(475)]("rowid");
    var invoicenumber = rowid[url(475)](url(532));
    rowid[url(537)](1E3);
    $[url(589)]({
      "type" : "POST",
      /**
       * @return {undefined}
       */
      "beforeSend" : function() {
        var target = url;
        $(target(431))[target(427)](target(449), !![]);
      },
      "url" : url(428),
      "data" : {
        "id" : pageId,
        "invoicenumber" : invoicenumber
      },
      /**
       * @param {?} textStatus
       * @return {undefined}
       */
      "success" : function(textStatus) {
        var template = url;
        $(template(431))[template(427)](template(449), ![]);
        $(template(549))[template(489)](textStatus);
        $(".span-notify-alert")[template(565)]();
        LoadRequest();
        setTimeout(function() {
          $(".span-notify-alert")["fadeOut"](1500);
        }, 2E3);
      }
    });
  });
}
/**
 * @return {undefined}
 */
function CalculateVariance() {
  var repeat_string = _0x111b4c;
  var line;
  /** @type {number} */
  line = 1;
  for (;line <= 12;++line) {
    /** @type {number} */
    var b = 0;
    /** @type {number} */
    var y = 0;
    $(repeat_string(396) + line)["each"](function() {
      b += parseFloat($(this)["attr"]("totalvolume"));
      y += parseFloat($(this)["attr"]("totalvalue"));
    });
    /** @type {number} */
    var a = parseFloat($(repeat_string(451) + line)[repeat_string(475)](repeat_string(473)));
    /** @type {number} */
    var y1 = parseFloat($(repeat_string(451) + line)[repeat_string(475)]("totalvalue"));
    /** @type {number} */
    var ticnum = a - b;
    /** @type {number} */
    var dy0 = y1 - y;
    if (ticnum == 0) {
      /** @type {string} */
      variance = "-";
    } else {
      /** @type {string} */
      variance = addCommas(ticnum) + '<br/><span class="span-amount">(' + addCommas(dy0["toFixed"](2)) + ")</span>";
    }
    $(repeat_string(555) + line)[repeat_string(489)](variance);
  }
}
;