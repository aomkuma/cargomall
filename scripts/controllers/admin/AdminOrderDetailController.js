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
        $scope.sumYuan = 0;
        $scope.sumAmount = 0;
        angular.forEach($scope.ProductList, function(value, key) {
            $log.log(value.product_size_choose);
            if(parseFloat(value.product_promotion_price) > 0){
                $scope.sumBaht = (parseFloat($scope.sumBaht) + ((parseFloat(value.product_promotion_price) * parseFloat($scope.OrderDesc.china_ex_rate)) * parseFloat(value.product_choose_amount)));
                $scope.sumYuan += parseFloat(value.product_promotion_price) * parseFloat(value.product_choose_amount);
            }else{
                $scope.sumBaht = (parseFloat($scope.sumBaht) + ((parseFloat(value.product_price_yuan) * parseFloat($scope.OrderDesc.china_ex_rate)) * parseFloat(value.product_choose_amount)));
                $scope.sumYuan += parseFloat(value.product_price_yuan)  * parseFloat(value.product_choose_amount);
            }

            $scope.sumAmount += parseFloat(value.product_choose_amount);
        });

        $scope.sumBaht = $scope.sumBaht - parseFloat($scope.Order.discount);
    };

    $scope.loadTransportRateData = function(){

        IndexOverlayFactory.overlayShow();
        var params = {'rate_level' : $scope.Customer.user_level};
        HTTPService.clientRequest('admin/transport-rate/get', params).then(function(result){
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

                for(var i = 0; i < $scope.Order.order_trackings.length; i++){
                  if(checkEmptyField($scope.Order.order_trackings[i].transport_cost_china)){
                    $scope.Order.order_trackings[i].transport_cost_china = parseFloat($scope.Order.order_trackings[i].transport_cost_china);
                  }

                  if(checkEmptyField($scope.Order.order_trackings[i].transport_cost_thai)){
                    $scope.Order.order_trackings[i].transport_cost_thai = parseFloat($scope.Order.order_trackings[i].transport_cost_thai);
                  }
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
      IndexOverlayFactory.overlayShow();
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

    $scope.sendTransportPaymentSMS = function(){

      $scope.alertMessage = 'ต้องการส่ง SMS แจ้งเตือนชำระเงินแก่ลูกค้า ใช่หรือไม่ ?';

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
          $scope.confirmSendTransportPaymentSMS();
      });
    }

    $scope.sendProductPaymentSMS = function(){

      $scope.alertMessage = 'ต้องการส่ง SMS แจ้งเตือนชำระเงินแก่ลูกค้า ใช่หรือไม่ ?';

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
          $scope.confirmSendProductPaymentSMS();
      });
    }

    
    $scope.confirmSendProductPaymentSMS = function(){
        IndexOverlayFactory.overlayShow();
        var params = {'order_id' : $scope.Order.id};
        HTTPService.clientRequest('admin/order/sms/product-payment', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                // window.location.replace('admin/order');
                var alertMsg = 'แจ้ง SMS เตือนลูกค้าเรียบร้อยแล้ว';
                alert(alertMsg);
            }
            else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.confirmSendTransportPaymentSMS = function(){
        IndexOverlayFactory.overlayShow();
        var params = {'order_id' : $scope.Order.id};
        HTTPService.clientRequest('admin/order/sms/transport-payment', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                // window.location.replace('admin/order');
                var alertMsg = 'แจ้ง SMS เตือนลูกค้าเรียบร้อยแล้ว';
                alert(alertMsg);
            }
            else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }


    $scope.sendPaymentLine = function(){

      $scope.alertMessage = 'ต้องการส่ง Line แจ้งเตือนชำระเงินแก่ลูกค้า ใช่หรือไม่ ?';

      var modalInstance = $uibModal.open({
          animation : false,
          templateUrl : 'views/dialog-confirm-sent-line.html',
          size : 'md',
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
          $scope.confirmSendPaymentLine();
      });
    }

    
    $scope.confirmSendPaymentLine = function(){
        IndexOverlayFactory.overlayShow();
        var params = {'order_id' : $scope.Order.id, 'line_admin_remark' : $scope.Order.line_admin_remark};
        HTTPService.clientRequest('admin/order/line/notify-payment', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                // window.location.replace('admin/order');
                var alertMsg = 'แจ้ง Line เตือนลูกค้าเรียบร้อยแล้ว';
                alert(alertMsg);
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
      // console.log(item);
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
          // console.log(index);
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
        // $log.log($scope.rateByCBM , $scope.rateByKG);
      }
    }

    $scope.findProductRate = function(transport_rate, product_type){
      $log.log(transport_rate, product_type);
      for(var i = 0; i < transport_rate.length; i++){
        if(transport_rate[i].product_desc == product_type){
          $log.log(transport_rate[i].product_desc , product_type);
          return i;
        }
      }
    }

    $scope.deleteTrack = function(id){

      $scope.alertMessage = 'ต้องการลบรายการ track นี้ ใช่หรือไม่ ?';

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
          $scope.confirmDeleteTrack(id);
      });
    }

    $scope.confirmDeleteTrack = function(id){
        IndexOverlayFactory.overlayShow();
        var params = {'id' : id};
        HTTPService.clientRequest('admin/order/tracking/delete', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.markTrackingNoneOwner = function(id){

      $scope.alertMessage = 'ต้องการลงรายการแจ้งว่า track นี้ไม่พบเจ้าของ ใช่หรือไม่ ?';

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
          $scope.confirmMarkTrackingNoneOwner(id);
      });
    }

    $scope.confirmMarkTrackingNoneOwner = function(id){
        IndexOverlayFactory.overlayShow();
        var params = {'id' : id};
        HTTPService.clientRequest('tracking-none-owner/add', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.cancelCancelStatus = function(id){

      $scope.alertMessage = 'ต้องการยกเลิกการยกเลิกสถานะของรายการนี้ ใช่หรือไม่ ?';

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
          $scope.confirmcancelCancelStatus(id);
      });
    }

    $scope.confirmcancelCancelStatus = function(id){
        IndexOverlayFactory.overlayShow();
        var params = {'id' : id};
        HTTPService.clientRequest('admin/order/cancel/cancel-status', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.updateProductItem = function(ProductList){
      // alert('asd');
      IndexOverlayFactory.overlayShow();
        var params = {'ProductList' : ProductList};
        HTTPService.clientRequest('admin/order/product/update', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          // window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.viewHistory = function(id){
      // IndexOverlayFactory.overlayShow();
        var params = {'order_id' : id};
        HTTPService.clientRequest('admin/order/view-activity-log', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          // $scope.alertMessage = 'ต้องการยกเลิกการยกเลิกสถานะของรายการนี้ ใช่หรือไม่ ?';
          $scope.ActivityLog = result.data.DATA.order_activity_log;
          // console.log($scope.ActivityLog);
          // return
          var modalInstance = $uibModal.open({
              animation : false,
              templateUrl : 'views/order-activity-log.html',
              size : 'md',
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
            // $scope.confirmcancelCancelStatus(id);
        });
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.dialogRemoveItem = function(id){
        
       $scope.alertMessage = 'ต้องการลบรายการสินค้านี้ ใช่หรือไม่ ?';

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
          $scope.confirmDeleteProduct(id);
      });
    }

    $scope.confirmDeleteProduct = function(id){
        IndexOverlayFactory.overlayShow();
        var params = {'order_detail_id' : id};
        HTTPService.clientRequest('admin/order/product/delete', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.getLogType = function(log_type){
      switch(log_type){
        case 'cus_update_order_detail' : return 'ลูกค่าทำการแก้ไขข้อมูล'; break;
        case 'update_order_detail' : return 'แก้ไขข้อมูลสินค้าทั่วไป'; break;
        case 'update_product' : return 'แก้ไขราคา / จำนวนสินค้า'; break;
        case 'cancel_order' : return 'ยกเลิกสถานะ'; break;
        case 'cancel_cancel_order' : return 'ยกเลิกการยกเลิกสถานะ'; break;
        case 'update_status' : return 'แก้ไขสถานะ'; break;
        case 'delete_tracking' : return 'ลบรายการแทรค'; break;
      }
    }

    $scope.order_id = $routeParams.order_id;
    $scope.loadData();

});
