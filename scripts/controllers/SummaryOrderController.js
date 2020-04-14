angular.module('app').controller('SummaryOrderController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';

    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();

    $scope.calcSum = function (){
        $scope.sumBaht = 0;
        angular.forEach($scope.ProductListStorage, function(value, key) {
            $log.log(value.product_size_choose);
            if(parseFloat(value.product_promotion_price) > 0){
                $scope.sumBaht = (parseFloat($scope.sumBaht) + ((parseFloat(value.product_promotion_price) * parseFloat(value.exchange_rate)) * parseFloat(value.product_qty)));
            }else{
                $scope.sumBaht = (parseFloat($scope.sumBaht) + ((parseFloat(value.product_normal_price) * parseFloat(value.exchange_rate)) * parseFloat(value.product_qty)));
            }
        });

        $log.log($scope.MoneyBalance, $scope.sumBaht);
    };

    $scope.ship_option = angular.fromJson($cookies.get('shipping_options'));
    $log.log($scope.ship_option);
    $scope.ShippingOption = {'transport_type_txt' : '', 'receive_order_type_txt' : '', 'transport_company_txt' : '', 'special_option_txt' : []};
    if($scope.ship_option.transport_type == 'car'){
        $scope.ShippingOption.transport_type_txt = 'ทางรถ';
    }else if($scope.ship_option.transport_type == 'sea'){
        $scope.ShippingOption.transport_type_txt = 'ทางเรือ';
    }

    if($scope.ship_option.receive_order_type == 'own'){
        $scope.ShippingOption.receive_order_type_txt = 'รับสินค้าด้วยตนเอง';
    }else if($scope.ship_option.receive_order_type == 'current'){
        $scope.ShippingOption.receive_order_type_txt = 'จัดส่งตามที่อยู่';
    }

    // if($scope.ship_option.transport_company == 'kerry'){
    //     $scope.ShippingOption.transport_company_txt = 'Kerry Express';
    // }else if($scope.ship_option.transport_company == 'nim'){
    //     $scope.ShippingOption.transport_company_txt = 'Nim Express';
    // }
    $scope.ShippingOption.transport_company_txt = $scope.getTransportCompanyName($scope.ship_option.transport_company);

    $scope.ShippingOption.special_option_txt = $scope.ship_option.special_option;

    /*for(var i = 0; i < $scope.ship_option.special_option.length; i++){
        if($scope.ship_option.special_option[i] == 'box_keep'){
            $scope.ShippingOption.special_option_txt.push('สินค้าต้องการตีลังไม้');
        }
        if($scope.ship_option.special_option[i] == 'test_product'){
            $scope.ShippingOption.special_option_txt.push('ต้องการให้ตรวจสอบ/ทดสอบสินค้า');
        }
    }*/

    $scope.calcSum();

    $scope.MoneyBalance = parseFloat($scope.UserData.money_bags.balance);

});
