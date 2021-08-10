angular.module('app').controller('TrackingOrderDetailController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $routeParams, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
    $templateCache.removeAll();

    // $log.log($scope.session_storage.user_data);
    if(!checkEmptyField($scope.session_storage.user_data)){
      alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      // window.location.replace('');
      history.back();
      return;
    }

    $scope.clearTimeout();

    $scope.calcSum = function (){
        $scope.sumBaht = 0;
        angular.forEach($scope.ProductList, function(value, key) {
            $log.log(value.product_size_choose);
            if(parseFloat(value.product_promotion_price) > 0){
                $scope.sumBaht = (parseFloat($scope.sumBaht) + ((parseFloat(value.product_promotion_price) * parseFloat($scope.OrderDesc.china_ex_rate)) * parseFloat(value.product_choose_amount)));
            }else{
                $scope.sumBaht = (parseFloat($scope.sumBaht) + ((parseFloat(value.product_price_yuan) * parseFloat($scope.OrderDesc.china_ex_rate)) * parseFloat(value.product_choose_amount)));
            }
        });

        $scope.sumBaht = $scope.sumBaht - parseFloat($scope.Order.discount);
    };

    $scope.loadData = function(){

        IndexOverlayFactory.overlayShow();
        var params = {'order_id' : $scope.order_id};
        HTTPService.clientRequest('admin/order/get', params).then(function(result){
            if(result.data.STATUS == 'OK'){
            
                $scope.Customer = result.data.DATA.customer;
                $scope.ProductList = result.data.DATA.order_details;
                $scope.OrderDesc = result.data.DATA.order_desc;
                $scope.Order = result.data.DATA;

                if(!$scope.Order.discount){
                    $scope.Order.discount = 0;
                }else{
                    $scope.Order.discount = parseFloat($scope.Order.discount);
                }

                if($scope.OrderDesc.china_ex_rate == 0){
                    $scope.OrderDesc.china_ex_rate = $scope.exchange_rate;
                }

                if(checkEmptyField($scope.OrderDesc.total_china_transport_cost)){
                    $scope.OrderDesc.total_china_transport_cost = parseFloat($scope.OrderDesc.total_china_transport_cost);
                }

                if(checkEmptyField($scope.OrderDesc.china_thai_transport_cost)){
                    $scope.OrderDesc.china_thai_transport_cost = parseFloat($scope.OrderDesc.china_thai_transport_cost);
                }

                if(checkEmptyField($scope.OrderDesc.transport_company_cost)){
                    $scope.OrderDesc.transport_company_cost = parseFloat($scope.OrderDesc.transport_company_cost);
                }

                $scope.setShippingOption();

                $scope.calcSum();
            }else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });

    };

    $scope.setShippingOption = function(){
        $scope.ShippingOption = {'transport_type_txt' : '', 'receive_order_type_txt' : '', 'transport_company_txt' : '', 'special_option_txt' : []};
        if($scope.Order.transport_type == 'car'){
            $scope.ShippingOption.transport_type_txt = 'ทางรถ';
        }else if($scope.Order.transport_type == 'sea'){
            $scope.ShippingOption.transport_type_txt = 'ทางเรือ';
        }

        if($scope.Order.receive_order_type == 'own'){
            $scope.ShippingOption.receive_order_type_txt = 'รับสินค้าด้วยตนเอง';
        }else if($scope.Order.receive_order_type == 'current'){
            $scope.ShippingOption.receive_order_type_txt = 'จัดส่งตามที่อยู่';
        }

        if($scope.Order.transport_company == 'kerry'){
            $scope.ShippingOption.transport_company_txt = 'Kerry Express';
        }else if($scope.Order.transport_company == 'nim'){
            $scope.ShippingOption.transport_company_txt = 'Nim Express';
        }

        if($scope.Order.package_type == 'all'){
            $scope.ShippingOption.package_type_txt = 'รวมกล่อง';
        }else if($scope.Order.package_type == 'single'){
            $scope.ShippingOption.package_type_txt = 'แยกกล่อง';
        }


        $scope.ShippingOption.special_option_txt = $scope.Order.add_on;
    }

    $scope.updateOrderStatus = function(order_id, order_status, type){

        var to_order_status = parseInt(order_status);
        if(type == 'back'){
            to_order_status = parseInt(order_status) - 1;
        }else if(type == 'next'){
            to_order_status = parseInt(order_status) + 1;
        }

        IndexOverlayFactory.overlayShow();
        var params = {'order_id' : order_id, 'to_order_status' : to_order_status};
        HTTPService.clientRequest('admin/order/status/update', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                window.location.reload();
            }
            else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.updateOrder = function(Order, OrderDesc){

        IndexOverlayFactory.overlayShow();
        var params = {'Order' : Order, 'OrderDesc' : OrderDesc};
        HTTPService.clientRequest('admin/order/update', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                // window.location.href = 'admin/order';
                // window.location.reload();
            }
            else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.order_id = $routeParams.order_id;
    $scope.loadData();

});
