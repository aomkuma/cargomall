angular.module('app').controller('AdminOrderDetailController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $routeParams, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
    $templateCache.removeAll();

    // $log.log($scope.session_storage.user_data);
    if(!checkEmptyField($scope.session_storage.user_data)){
      // alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      window.location.replace('admin/signin');
      // history.back();
      return;
    }

    if(!checkEmptyField($scope.UserData.is_admin) && !$scope.UserData.is_admin){
      alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      history.back();
      return;
    }

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

    $scope.loadTransportRateData = function(){

        IndexOverlayFactory.overlayShow();
        var params = null;
        HTTPService.clientRequest('admin/transport-rate/list', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.TransportRateData = result.data.DATA;
            }else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }

            for(var i = 0; i < $scope.Order.order_trackings.length; i++){
              $scope.calcPrice($scope.Order.order_trackings[i]);
            }
            
            IndexOverlayFactory.overlayHide();
        });
    }

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

                $scope.loadTransportRateData();

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

    $scope.cancelOrder = function(){

      $scope.alertMessage = 'ต้องการยกเลิกรายการสั่งซื้อนี้ ใช่หรือไม่ ?';

      var modalInstance = $uibModal.open({
          animation : false,
          templateUrl : 'views/dialog_confirm.html',
          size : 'sm',
          scope : $scope,
          backdrop : 'static',
          controller : 'ModalDialogCtrl',
          resolve : {
              params : function() {
                  return {};
              } 
          },
      });
      modalInstance.result.then(function (valResult) {
          $scope.confirmCancelOrder();
      });
    }

    $scope.confirmCancelOrder = function(){
        var params = {'order_id' : $scope.Order.id};
        HTTPService.clientRequest('admin/order/cancel', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                window.location.replace('admin/order');
            }
            else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.calcPrice = function(item){
      // var item = item;//angular.copy($scope.Order);

      if(checkEmptyField(item.product_type)){
        var transport_rate_kg = null;
        var transport_rate_cbm = null;
        if($scope.Order.transport_type == 'sea'){
          // calc by kg 

          // find by prod desc
          var index = $scope.findProductRate($scope.TransportRateData.rate_car_kg , item.product_type);
          // alert(index);
          transport_rate_kg = $scope.TransportRateData.rate_car_kg[index];

          index = $scope.findProductRate($scope.TransportRateData.rate_car_cbm , item.product_type);
          transport_rate_cbm = $scope.TransportRateData.rate_car_cbm[index];
          // $scope.TransportRateData.rate_sea_kg 
          // calc by cbm

        }else if($scope.Order.transport_type == 'car'){
          var index = $scope.findProductRate($scope.TransportRateData.rate_sea_kg , item.product_type);
          transport_rate_kg = $scope.TransportRateData.rate_sea_kg[index];

          index = $scope.findProductRate($scope.TransportRateData.rate_sea_cbm , item.product_type);
          transport_rate_cbm = $scope.TransportRateData.rate_sea_cbm[index];
        }

        // calc kg
        var weight_kgm = 0;
        var cbm = 0;
        if(checkEmptyField(item.weight_kg)){
          weight_kgm = parseFloat(item.weight_kg);
        }
        if(checkEmptyField(item.cbm)){
          cbm = parseFloat(item.cbm);
        }

        if(cbm < 2){
          item['rateByCBM'] = cbm * transport_rate_cbm.rate_1;
        }else if(cbm >= 2 && cbm < 5){
          item['rateByCBM'] = cbm * transport_rate_cbm.rate_2;
        }else{
          item['rateByCBM'] = cbm * transport_rate_cbm.rate_3;
        }

        if(weight_kgm < 100){
          item['rateByKG'] = weight_kgm * transport_rate_kg.rate_1;
        }else if(weight_kgm >= 100 && cbm < 500){
          item['rateByKG'] = weight_kgm * transport_rate_kg.rate_2;
        }else{
          item['rateByKG'] = weight_kgm * transport_rate_kg.rate_3;
        }
        $log.log($scope.rateByCBM , $scope.rateByKG);
      }
    }

    $scope.findProductRate = function(transport_rate, product_type){
      $log.log(transport_rate);
      for(var i = 0; i < transport_rate.length; i++){
        if(transport_rate[i].product_desc == product_type){
          // $log.log(transport_rate[i].product_desc , product_type);
          return i;
        }
      }
    }

    $scope.order_id = $routeParams.order_id;
    $scope.loadData();

});
