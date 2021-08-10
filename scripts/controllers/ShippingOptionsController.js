angular.module('app').controller('ShippingOptionsController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
    $scope.clearTimeout();
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();

    $scope.toggleSelection = function toggleSelection(special_option) {
    if($scope.ship_option.special_option==undefined){
      $scope.ship_option.special_option = [special_option.value];
    }else{
    
        var idx = $scope.ship_option.special_option.indexOf(special_option.value);
    
        if (idx > -1) {
          $scope.ship_option.special_option.splice(idx, 1);
        }
        else {
          $scope.ship_option.special_option.push(special_option.value);
        }
    }
   };

   $scope.changeAddressType = function(){
    if($scope.ship_option.receive_order_type == 'own'){
      $scope.ship_option.customer_address = null;
      $scope.ship_option.transport_company = null;
    }
  };

  $scope.updateShippingOptions = function(ship_option){
    // $localStorage.shipping_options = JSON.stringify(ship_option);

    $cookies.put('shipping_options', JSON.stringify(ship_option));

    console.log($localStorage.shipping_options);
    window.location.href = 'summary-orders';
  }

  $scope.special_option_list = [{"value":"box_keep","text":"สินค้าต้องการตีลังไม้"}
                                ,{"value":"test_product","text":"ต้องการให้ตรวจสอบ/ทดสอบสินค้า"}
                              ];
  $scope.ship_option = {'customer_address_id' : null};
  
  if(checkEmptyField($cookies.get('shipping_options'))){
    $scope.ship_option = angular.fromJson($cookies.get('shipping_options'));
    // $log.log($scope.UserData.firstname);  
  }

});
