// var serviceUrl = 'services/blog/public/api/';
var serviceUrl = 'BackendServices/public/api/';


'use strict';
/* global FB */

var app = angular.module('app', ['ui.bootstrap' , 'ngRoute' , 'ngAnimate', 'ngCookies', 'ui.router', 'oc.lazyLoad', 'ngFileUpload', 'angular-bind-html-compile', '720kb.socialshare', 'pascalprecht.translate', 'ngYoutubeEmbed', 'ngStorage', 'colorpicker.module']);

app.config(function($controllerProvider, $compileProvider, $filterProvider, $logProvider, $translateProvider, $provide) {
  app.register = {
    controller: $controllerProvider.register,
    directive: $compileProvider.directive,
    filter: $filterProvider.register,
    factory: $provide.factory,
    service: $provide.service
  };

  $translateProvider.translations('th', {
    MENU_HOME : 'หน้าหลัก',
    MENU_SUGGESTION : 'แนะนำการใช้งาน',
    MENU_IMPORTER : 'นำเข้าสินค้า',
    MENU_TOPUP : 'เติมเงิน',
    MENU_CONTACTUS : 'ติดต่อ',
    MENU_TRANSFER : 'โอนเงินไปจีน',
    MENU_HOME : 'หน้าหลัก',
    WELCOME_USER_TXT : 'สวัสดีคุณ',
    RATE_TITLE: 'อัตราค่าขนส่งจากจีนมาไทย',
    SEARCH_FROM_LINK : 'ค้นหาสิ้นค้าจากลิ้งค์',
    ORDER_PRODUCT_TXT: 'สั่งซื้อสินค้า',
    TOPUP_TXT: 'ฝากจ่าย',
    TRANSFER_MONEY_TXT : 'โอนเงินไปจีน',
    IMPORTER_TABLE_TXT : 'นำเข้าสินค้า (Importer)',
    PREORDER_TABLE_TXT : 'ฝากสั่งสินค้า (Pre-Order)',
    RATE_TYPE_TXT : 'ประเภท',
    CAR_TYPE_TXT : 'ทางรถ (4 - 7 วัน)',
    SHIP_TYPE_TXT : 'ทางเรือ (14 - 20 วัน)',
    PRICE_TXT : 'ราคา',
    CALC_BY_WEIGHT_TXT : 'คิดตามน้ำหนัก',
    CALC_BY_SIZE_TXT : 'คิดตามขนาด',
    ITEM_1_TXT : 'ธรรมดา',
    ITEM_2_TXT : 'มอก.อย.',
    ITEM_3_TXT : 'พิเศษ',
    ITEM_4_TXT : 'ควบคุม',
    VDO_SUGGESTION_TXT : 'แนะนำการใช้งาน',
    USER_CODE_TXT : 'รหัสผู้ใช้งาน',
    EMAIL_TXT : 'อีเมล',
    PASSWORD_TXT : 'รหัสผ่าน',
    CONFIRM_PASSWORD_TXT : 'ยืนยันรหัสผ่าน',
    FIRSTNAME_TXT : 'ชื่อจริง',
    LASTNAME_TXT : 'นามสกุล',
    IDCARD_TXT : 'เลขที่บัตรประจำตัวประชาชน',
    MOBILENO_TXT : 'หมายเลขโทรศัพท์',
    REGISTER_TXT : 'สมัครสมาชิก',
    NEW_PASSWORD_TXT : 'รหัสผ่านใหม่',
    LOGIN_TXT : 'ลงทะเบียนเข้าใช้งาน',
    CANCEL_TXT : 'ยกเลิก',
    INVALID_CONFIRMPASS_TXT : 'ยืนยันรหัสผ่านไม่ตรงกับรหัสผ่านหลัก',
    REGISTER_BTN_TXT : 'สร้างบัญชี',
    SIGNIN_BTN_TXT : 'เข้าใช้งาน',
    SIGNOUT_BTN_TXT : 'ออกจากระบบ',
    SEARCH_RESULT_TXT : 'ผลลัพธ์การค้นหา',
    ADD_TO_CART : 'เพิ่มสินค้าเข้าตะกร้า',
    LINK_URL_TXT : 'ลิ้งค์สินค้า',
    PRODUCT_NAME_TXT : 'ชื่อสินค้า',
    COLOR_TXT : 'สี',
    SIZE_TXT : 'ขนาด',
    AMOUNT_TXT : 'จำนวน',
    PRICE_PER_UNIT_TXT : 'ราคาต่อหน่วย',
    NORMAL_PRICE_TXT : 'ราคาปกติ',
    PROMOTION_PRICE_TXT : 'ราคาโปรโมชั่น',
    EXCHANGERATE_PRICE_TXT : 'ราคาต่อหน่วย ณ อัตราแลกเปลี่ยนวันปัจจุบัน',
    TOTAL_TXT : 'รวม',
    EDIT_PROFILE_TXT : 'แก้ไขข้อมูลส่วนตัว',
    VIEW_ORDER_TXT : 'ตะกร้าสินค้าของคุณ',
    CONFIRM_REMOVE_TXT : 'ต้องการลบสินค้ารายการนี้ ใช่หรือไม่?',
    SAVE_TXT : 'บันทึก',
    ADD_ADDRESS_TXT : 'เพิ่มข้อมูลที่อยู่',
    ADDRESS_INFO_TXT : 'ข้อมูลที่อยู่',
    ADDRESS1_TXT : 'เลขที่',
    ADDRESS2_TXT : 'หมู่บ้าน',
    ADDRESS3_TXT : 'หมู่ที่',
    ADDRESS4_TXT : 'แขวง/ตำบล',
    ADDRESS5_TXT : 'อำเภอ/เขต',
    ADDRESS6_TXT : 'จังหวัด',
    ADDRESS7_TXT : 'รหัสไปรษณีย์',
    DELETE_ADDRESS_TXT : 'ลบข้อมูลที่อยู่',
    CONFIRM_REMOVE_ADDRESS_TXT : 'ต้องการลบข้อมูลที่อยู่นี้ ใช่หรือไม่?',
    REMARK_TXT : 'รายละเอียดเพิ่มเติม',
    CANCEL_ORDER_TXT : 'ต้องการยกเลิกการทำรายการสั่งซื้อ ใช่หรือไม่?',
    CONFIRM_ORDER_TXT : 'ต้องการทำรายการสั่งซื้อ ใช่หรือไม่?',
  });

  $translateProvider.preferredLanguage('th');



  $compileProvider.debugInfoEnabled(false);    
  $logProvider.debugEnabled(false);
});

app.run(function($rootScope, $templateCache) {
   $rootScope.$on('$viewContentLoaded', function() {
      $templateCache.removeAll();
   });
});

angular.module('app').controller('AppController', ['$cookies','$scope', '$filter', '$uibModal', '$templateCache', '$localStorage', '$log', '$cookies', 'IndexOverlayFactory', 'HTTPService', function($cookies, $scope, $filter, $uibModal,$templateCache, $localStorage, $log, $cookies, IndexOverlayFactory, HTTPService) {
	$templateCache.removeAll();
  $scope.overlay = IndexOverlayFactory;

  // var CurDateTime = new Date();
  // $scope.CURDATETIME = CurDateTime.getYear() + CurDateTime.getMonth() + CurDateTime.getDate() + CurDateTime.getHours() +CurDateTime.getMinutes() + CurDateTime.getSeconds() ;    
  $scope.ORDER_STATUS = [{'id' : 1, 'value' : 'รอการชำระเงินค่าสินค้า'},
                      {'id' : 2, 'value' : 'ชำระเงินค่าสินค้าแล้ว'},
                      {'id' : 3, 'value' : 'ดำเนินการสั่งซื้อสินค้า'},
                      {'id' : 4, 'value' : 'สินค้าถึงโกดังจีน'},
                      {'id' : 5, 'value' : 'สินค้าถึงโกดังไทย'},
                      {'id' : 6, 'value' : 'รอการชำระค่าขนส่ง'},
                      {'id' : 7, 'value' : 'รอการจัดส่งสินค้า'},
                      {'id' : 8, 'value' : 'เสร็จสิ้น'},
                      {'id' : 9, 'value' : 'ยกเลิก'}
                    ];

  $scope.IMPORTER_STATUS = [{'id' : 1, 'value' : 'คำขอบริการนำเข้าสินค้า'},
                      {'id' : 2, 'value' : 'สินค้าถึงโกดังจีน'},
                      {'id' : 3, 'value' : 'สินค้าถึงโกดังไทย'},
                      {'id' : 4, 'value' : 'รอการชำระค่าขนส่ง'},
                      {'id' : 5, 'value' : 'รอการจัดส่งสินค้า'},
                      {'id' : 6, 'value' : 'เสร็จสิ้น'}
                    ];

  $scope.TRANSPORT_COMPANY = [{'id' : 'nim', 'value' : 'Nim Express'},
                      {'id' : 'flash' , 'value' : 'Flash Express'},
                      {'id' : 'kerry' , 'value' : 'Kerry'},
                      {'id' : 'thailandpost' , 'value' : 'ไปรษณีย์ไทย (EMS)'},
                      {'id' : 'other' , 'value' : 'อื่นๆ'}
                    ];

  $scope.getOrderStatus = function(order_status){
    for(var i = 0; i < $scope.ORDER_STATUS.length; i++){
      if(order_status == $scope.ORDER_STATUS[i].id){
        return $scope.ORDER_STATUS[i].value;
      }
    }
  }

  $scope.getImporterStatus = function(importer_status){
    for(var i = 0; i < $scope.IMPORTER_STATUS.length; i++){
      if(importer_status == $scope.IMPORTER_STATUS[i].id){
        return $scope.IMPORTER_STATUS[i].value;
      }
    }
  }

  $scope.getTransportCompanyName = function(id){
    for(var i = 0; i < $scope.TRANSPORT_COMPANY.length; i++){
      if(id == $scope.TRANSPORT_COMPANY[i].id){
        return $scope.TRANSPORT_COMPANY[i].value;
      }
    }
  }

  $scope.currentUser = null;
	$scope.overlayShow = false;
	$scope.menu_selected = '';
  $scope.limitRowPerPage = 15;
  $scope.OrderNumber = '';
  $scope.OrderID = '';
  
  $scope.LandingPage = null;
  $scope.ShowLoginDialog = false;
  $scope.ShowRegisterDialog = false;
  $scope.ShowEditProfileDialog = false;
  $scope.ShowDialogRemoveItem = false;
  $scope.ShowDialogRemoveAddress = false;
  $scope.ShowDialogCancelOrder = false;
  $scope.ShowDialogConfirmOrder = false;
  $scope.ShowDialogSuccessOrder = false;
  $scope.ShowOverlay = false;

  $scope.exchange_rate = 0.000;
  $scope.exchange_rate_transfer = 0.000;

  $scope.session_storage = angular.fromJson($cookies.get('user_session'));

  if($scope.session_storage != undefined){
    console.log($scope.session_storage);
    if(checkEmptyField($scope.session_storage.user_data)){
      $scope.UserData = angular.fromJson(atob($scope.session_storage.user_data));
      $log.log($scope.UserData); 
    }

    if(checkEmptyField($scope.session_storage.product_list_storage)){
      $scope.ProductListStorage = angular.fromJson($scope.session_storage.product_list_storage);
      $scope.TotalProductPiece = 0;
      for(var i = 0; i < $scope.ProductListStorage.length; i++){
        $scope.TotalProductPiece++;//parseInt($scope.ProductListStorage[i].product_qty);
      }
    }
  }

  $scope.displayOverlay = function(){
    $scope.ShowOverlay = true;
  }

  $scope.hideOverlay = function(){
    $scope.ShowOverlay = false;
  }

  $scope.cancelOrderDialog = function(){
    $scope.ShowDialogCancelOrder = true;
  }

  $scope.closeCancelOrderDialog = function(){
    $scope.ShowDialogCancelOrder = false;
  }

  $scope.confirmOrderDialog = function(){
    $scope.ShowDialogConfirmOrder = true;
  }

  $scope.closeConfirmOrderDialog = function(){
    $scope.ShowDialogConfirmOrder = false;
  }

  $scope.successOrderDialog = function(){
    $scope.ShowDialogSuccessOrder = true;
  }

  $scope.closeSuccessOrderDialog = function(){
    $scope.ShowDialogSuccessOrder = false;
    window.location.replace('pay/1/' + $scope.OrderID);
  }

  $scope.registerDialog = function(){
    $scope.ShowRegisterDialog = true;
  }

  $scope.closeRegisterDialog = function(){
    $scope.ShowRegisterDialog = false;
  }

  $scope.loginDialog = function(){
    $scope.ShowLoginDialog = true;
  }

  $scope.closeLoginDialog = function(){
    $scope.ShowLoginDialog = false;
  }

  $scope.closeDialogRemoveAddress = function(){
    $scope.ShowDialogRemoveAddress = false;
  }

  $scope.dialogRemoveAddress = function(index){
    $scope.ShowDialogRemoveAddress = true;
    $scope.RemoveAddressIndex = index;
  }

  $scope.editProfileDialog = function(){
    $scope.ShowEditProfileDialog = true;
    $scope.UserProfile = angular.copy(angular.fromJson(atob($scope.session_storage.user_data)));
    if($scope.UserProfile.addresses.length == 0){
      $scope.UserProfile.addresses.push({'address_no' : 1});
    }
    $log.log($scope.UserProfile);
  }

  $scope.addAddress = function(UserProfile){
    UserProfile.addresses.push({'user_id' : UserProfile.id, 
        'address1' : '',
        'address2' : '',
        'address3' : '',
        'address4' : '',
        'address5' : '',
        'address6' : '',
        'address7' : '',
        'address_no' : (UserProfile.addresses.length + 1)
      });
  }

  $scope.closeUserProfileDialog = function(){
    $scope.ShowEditProfileDialog = false;
    // window.location.reload();
  }

  $scope.closeDialog = function(){
    $scope.ShowDialogRemoveItem = false;
  }

  $scope.updateUserMoneyBalance = function(new_balance){

    $scope.UserData.money_bags.balance = new_balance;
    $log.log( btoa($scope.UserData));return;
    $localStorage.user_data = btoa($scope.UserData);
    // $scope.UserData = angular.copy(angular.fromJson(atob(result.data.DATA)));
        
  }

  $scope.removeItem = function(index){
    // alert(index);
    $scope.ProductListStorage = angular.fromJson($scope.session_storage.product_list_storage);
    $scope.ProductListStorage.splice(index, 1);
    $localStorage.product_list_storage = JSON.stringify($scope.ProductListStorage);

    if($scope.ProductListStorage.length > 0){
      window.location.reload();
    }else{
      window.location.replace('');
    }
  }

  $scope.getProduct = function(link_url){

    if(!checkEmptyField($localStorage.user_data)){
      alert('กรุณาลงชื่อเข้าใช้งานระบบก่อนทำการสั่งซื้อสินค้า');
      return false;
    }

    if(!checkEmptyField(link_url)){
      alert('กรุณาใส่ลิ้งค์สินค้า');
      return false;
    }

    IndexOverlayFactory.overlayShow();
    var params = {'link_url' : link_url};
    HTTPService.clientRequest('item/get', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        $log.log(result.data.DATA);
        sessionStorage.setItem('product_info' , JSON.stringify(result.data.DATA));
        window.location.href = 'product-info';
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
    });
  }

  $scope.checkLogin = function(loginObj){
    IndexOverlayFactory.overlayShow();
    var params = {'LoginObj' : loginObj};
      HTTPService.clientRequest('login', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          $scope.closeLoginDialog();
          // $localStorage.$default({'token' : result.data.DATA.token, 'user_data' : result.data.DATA.UserData});
          $cookies.put('user_session' , JSON.stringify({'token' : result.data.DATA.token, 'user_data' : result.data.DATA.UserData}));
          // $scope.UserDara = result.data.DATA.UserData;
          window.location.reload();
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
  }

  $scope.checkRegister = function(RegisterObj){
    IndexOverlayFactory.overlayShow();
    if(RegisterObj.password != RegisterObj.confirm_password){
      alert($filter('translate')('INVALID_CONFIRMPASS_TXT'));
      IndexOverlayFactory.overlayHide();
      return false;
    }

    var params = {'RegisterObj' : RegisterObj};
      HTTPService.clientRequest('register', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          $scope.closeRegisterDialog();
          $scope.checkLogin(RegisterObj);
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
  }

  $scope.updateUserProfile = function(UserProfileObj){

    IndexOverlayFactory.overlayShow();

    if(checkEmptyField(UserProfileObj.new_password) && (UserProfileObj.new_password != UserProfileObj.confirm_password)){
      alert($filter('translate')('INVALID_CONFIRMPASS_TXT'));
      IndexOverlayFactory.overlayHide();
      return false;
    }
    var params = {'UserProfileObj' : UserProfileObj};
    HTTPService.clientRequest('user/update', params).then(function(result){

      if(result.data.STATUS == 'OK'){

        $localStorage.user_data = (result.data.DATA);
        $scope.UserData = angular.copy(angular.fromJson(atob(result.data.DATA)));
        $scope.closeUserProfileDialog();
        // window.location.reload();

      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
    });

  }

  $scope.removeAddress = function(RemoveAddressIndex){
    IndexOverlayFactory.overlayShow();
    var params = {'user_id': $scope.UserProfile.id, 'address_id' : $scope.UserProfile.addresses[RemoveAddressIndex].id};
    HTTPService.clientRequest('address/remove', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        $scope.UserProfile.addresses.splice(RemoveAddressIndex, 1);

        $localStorage.user_data = (result.data.DATA);

      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      $scope.closeDialogRemoveAddress();
      IndexOverlayFactory.overlayHide();
    });
  }

  $scope.cancelOrder = function(){
    IndexOverlayFactory.overlayShow();
    $localStorage.product_list_storage = null;
    $localStorage.shipping_options = null;
    window.location.replace('');
  }

  $scope.confirmOrder = function(){

    $scope.closeConfirmOrderDialog();
    IndexOverlayFactory.overlayShow();

    var ProductList = angular.fromJson($localStorage.product_list_storage);
    var ShippingOptions = angular.fromJson($localStorage.shipping_options);

    var params = {'ProductList': ProductList, 'ShippingOptions' : ShippingOptions};
    HTTPService.clientRequest('order/confirm', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        $localStorage.product_list_storage = null;
        $localStorage.shipping_options = null;
        $scope.OrderID = result.data.DATA.OrderID;
        $scope.OrderNumber = result.data.DATA.OrderNumber;
        
        $scope.successOrderDialog();
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
    });

    
  }

  $scope.signOut = function(){
    IndexOverlayFactory.overlayShow();
    var params = {'text' : 'logout'};
    HTTPService.clientRequest('logout', params).then(function(result){
      // $localStorage.$reset();
      // sessionStorage.setItem('product_info', null);
      // sessionStorage.removeItem('product_info');
      $cookies.remove('user_session');
      if(result.data.STATUS == 'OK'){
        setTimeout(function(){
            // window.location.reload(); 
            window.location.href = '';   
        },500);
      }
      IndexOverlayFactory.overlayHide();
    });
    
    
  }
  
  // $scope.signOut();
  $scope.signOutAdmin = function(){
    IndexOverlayFactory.overlayShow();
    var params = {'text' : 'logout'};
    HTTPService.clientRequest('admin/logout', params).then(function(result){
      $localStorage.$reset();
      if(result.data.STATUS == 'OK'){
        setTimeout(function(){
            // window.location.reload(); 
            window.location.href = 'admin/signin';   
        },500);
      }
      IndexOverlayFactory.overlayHide();
    });
    
    
  }

  $scope.getThaiDateFromString = function(date){
      $log.log(date);
      if(checkEmptyField(date)){
          return convertSQLDateTimeToReportDate(date);
      }
  }

  $scope.getThaiDateTimeFromString = function(dateTime){
      $log.log(dateTime);
      if(checkEmptyField(dateTime)){
          return convertSQLDateTimeToReportDateTime(dateTime);
      }
  }

  $scope.getCurrentExchangeRate = function(){
    HTTPService.clientRequest('exchange-rate/get', null).then(function(result){
    if(result.data.STATUS == 'OK'){
      $scope.exchange_rate = parseFloat(result.data.DATA.exchange_rate);
      $scope.exchange_rate_transfer = parseFloat(result.data.DATA.exchange_rate_transfer);
    }
    });
  }

  $scope.getMoneyBagBalance = function(){
    
    HTTPService.clientRequest('user/money-bag/balance', null).then(function(result){
    if(result.data.STATUS == 'OK'){
        $localStorage.user_data = (result.data.DATA);
        $scope.UserData = angular.copy(angular.fromJson(atob(result.data.DATA)));
    }
    });
  }

  $scope.loadLandingPage = function(){

      IndexOverlayFactory.overlayShow();
      var params = null;
      HTTPService.clientRequest('landing-page/show', params).then(function(result){
          if(result.data.STATUS == 'OK'){
              $scope.LandingPage = result.data.DATA;
              // alert($scope.LandingPage.type);
              sessionStorage.setItem('landing_page' , JSON.stringify(result.data.DATA));
          }else{
            var alertMsg = result.data.DATA;
            // alert(alertMsg);
          }
          IndexOverlayFactory.overlayHide();
      });
  }

  $scope.getCurrentExchangeRate();

  $log.log('user data', $scope.UserData);
  if(checkEmptyField($scope.UserData) && !checkEmptyField($scope.UserData.is_admin)){
    $scope.getMoneyBagBalance();  
  }

  setInterval(function(){
    if(checkEmptyField($scope.UserData) && !checkEmptyField($scope.UserData.is_admin)){
      $scope.getMoneyBagBalance();  
    }
    $scope.getCurrentExchangeRate();
  },300000);
  
  
}])
.directive('embedSrc', function () {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {
      scope.$watch(
        function() {
          return attrs.embedSrc;
        },
        function() {
          element.attr('src', attrs.embedSrc);
        }
      );
    }
  };
})

;
