angular.module('app').controller('PayController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $routeParams, HTTPService, IndexOverlayFactory) {
	
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();

    if(!checkEmptyField($scope.session_storage.user_data)){
      alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      // window.location.replace('');
      history.back();
      return;
    }

    $scope.calcCNY = function(){
      if($scope.Pay.pay_type == 1 || $scope.Pay.pay_type == 2 || $scope.Pay.pay_type == 5){
        $scope.Pay.pay_amount_yuan = parseFloat($scope.Pay.pay_amount_thb) / $scope.exchange_rate;
      }else{
        $scope.Pay.pay_amount_yuan = parseFloat($scope.Pay.pay_amount_thb) / $scope.exchange_rate_transfer;
      }
      $scope.Pay.pay_amount_yuan = parseFloat($scope.Pay.pay_amount_yuan.toFixed(2));
      
    }

    $scope.calcTHB = function(){
      if($scope.Pay.pay_type == 1 || $scope.Pay.pay_type == 2 || $scope.Pay.pay_type == 5){
        $scope.Pay.pay_amount_thb = parseFloat($scope.Pay.pay_amount_yuan) * $scope.exchange_rate;
      }else{
        $scope.Pay.pay_amount_thb = parseFloat($scope.Pay.pay_amount_yuan) * $scope.exchange_rate_transfer;
      }
      $scope.Pay.pay_amount_thb = parseFloat($scope.Pay.pay_amount_thb.toFixed(2));
    }

    $scope.checkPayType = function(){
      $scope.Pay.pay_amount_thb = null;
      $scope.Pay.pay_amount_yuan = null;
      $scope.Pay.to_ref_id = null;
      $log.log($scope.Pay.pay_type);
      if($scope.Pay.pay_type == 1 || $scope.Pay.pay_type == 2){
        $scope.getOrderList();
      }
      else if($scope.Pay.pay_type == 5){
        $scope.getImporterList();
      }
    }

    $scope.getOrderList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.Pay};
      HTTPService.clientRequest('order/list/by-user-status', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          $scope.DataList = result.data.DATA;

          if($scope.Pay.pay_type == 1){
            $scope.sumYuanValue();  
          }else if($scope.Pay.pay_type == 2){
            $scope.sumBahtValue();
          }
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.getImporterList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.Pay};
      HTTPService.clientRequest('importer/list/by-user', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          $scope.DataList = result.data.DATA;

          if($scope.Pay.pay_type == 1){
            $scope.sumYuanValue();  
          }else if($scope.Pay.pay_type == 2){
            $scope.sumBahtValue();
          }else if($scope.Pay.pay_type == 5){
            $scope.sumImporterValue();
          }
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.sumYuanValue = function (){
      for(var i = 0; i < $scope.DataList.length; i++){
        $scope.DataList[i]['totalYuan'] = 0;
        for(var j = 0; j < $scope.DataList[i].order_details.length; j++){
          if($scope.DataList[i].order_details[j].product_promotion_price > 0){
            $scope.DataList[i].order_details[j].product_price_yuan =  $scope.DataList[i].order_details[j].product_promotion_price;
          }
          $scope.DataList[i].totalYuan += parseFloat($scope.DataList[i].order_details[j].product_price_yuan) * parseFloat($scope.DataList[i].order_details[j].product_choose_amount); 
        }
        if($scope.DataList[i].id == $scope.Pay.to_ref_id){

          if(!$scope.DataList[i].discount){
              $scope.DataList[i].discount = 0;
          }else{
              $scope.DataList[i].discount = parseFloat($scope.DataList[i].discount);
          }
          
          $scope.setPayAmountValue($scope.DataList[i].totalYuan, $scope.exchange_rate, $scope.DataList[i].discount);
        }
      }
      
      $scope.ShowChooseToPay = true;
    }

    $scope.setPayAmountValue = function(totalYuan, exchange_rate, discount){
      if(!checkEmptyField(discount)){
        discount = 0;
      }

      $scope.Pay.pay_amount_yuan = parseFloat(totalYuan);
      $scope.Pay.pay_amount_thb = parseFloat((parseFloat(totalYuan) * parseFloat(exchange_rate) - parseFloat(discount)).toFixed(2));
      $scope.SelectedPayBaht = angular.copy($scope.Pay.pay_amount_thb);
      $log.log($scope.SelectedPayBaht);
    }

    $scope.sumBahtValue = function (){
      /*for(var i = 0; i < $scope.DataList.length; i++){
        if(!checkEmptyField($scope.DataList[i].order_desc.total_china_transport_cost)){
          $scope.DataList[i].order_desc.total_china_transport_cost = 0;
        }
        if(!checkEmptyField($scope.DataList[i].order_desc.china_thai_transport_cost)){
          $scope.DataList[i].order_desc.china_thai_transport_cost = 0;
        }
        if(!checkEmptyField($scope.DataList[i].order_desc.transport_company_cost)){
          $scope.DataList[i].order_desc.transport_company_cost = 0;
        }
        // console.log(parseFloat($scope.DataList[i].order_desc.total_china_transport_cost) , parseFloat($scope.DataList[i].order_desc.china_thai_transport_cost) , parseFloat($scope.DataList[i].order_desc.transport_company_cost));
        $scope.DataList[i]['totalBaht'] = 0;
        $scope.DataList[i].totalBaht = parseFloat($scope.DataList[i].order_desc.total_china_transport_cost) + parseFloat($scope.DataList[i].order_desc.china_thai_transport_cost) + parseFloat($scope.DataList[i].order_desc.transport_company_cost); 
        
        if($scope.DataList[i].id == $scope.Pay.to_ref_id){
          $scope.setPayAmountTransportValue($scope.DataList[i].order_desc.total_china_transport_cost, $scope.DataList[i].order_desc.china_thai_transport_cost, $scope.DataList[i].order_desc.transport_company_cost);
        }

      }*/

      for(var i = 0; i < $scope.DataList.length; i++){
        $scope.DataList[i]['totalBaht'] = 0;
        $scope.DataList[i].totalBaht =  parseFloat($scope.DataList[i].import_fee) + parseFloat($scope.DataList[i].transport_cost_china) + parseFloat($scope.DataList[i].transport_cost_thai); 
        
        if($scope.DataList[i].id == $scope.Pay.to_ref_id){
          $scope.setPayAmountTransportValue($scope.DataList[i].tracking_no, $scope.DataList[i].import_fee, $scope.DataList[i].transport_cost_china, $scope.DataList[i].transport_cost_thai);
        }
      }

      $scope.ShowChooseToPay = true;
    }
    $scope.setPayAmountTransportValue = function(tracking_no, import_fee, transport_cost_china, transport_cost_thai){
      /*if(!checkEmptyField(total_china_transport_cost)){
        total_china_transport_cost = 0;
      }
      if(!checkEmptyField(china_thai_transport_cost)){
        china_thai_transport_cost = 0;
      }
      if(!checkEmptyField(transport_company_cost)){
        transport_company_cost = 0;
      }  
      // $scope.Pay.pay_amount_yuan = parseFloat(totalYuan);
      $scope.Pay.pay_amount_thb = parseFloat((parseFloat(total_china_transport_cost) + parseFloat(china_thai_transport_cost) + parseFloat(transport_company_cost)).toFixed(2));
      $scope.SelectedPayBaht = angular.copy($scope.Pay.pay_amount_thb);*/
      $scope.Pay['to_ref_id'] = tracking_no;
      $scope.Pay.pay_amount_thb = parseFloat((parseFloat(import_fee) + parseFloat(transport_cost_china) + parseFloat(transport_cost_thai)).toFixed(2));
      $scope.SelectedPayBaht = angular.copy($scope.Pay.pay_amount_thb);

      $log.log($scope.SelectedPayBaht);
    }

    $scope.sumImporterValue = function (){
      for(var i = 0; i < $scope.DataList.length; i++){

        $scope.DataList[i]['totalBaht'] = 0;
        var package_price_thb = 0;
        var total_price_yuan_thb = 0;
        var total_price_thb = 0;
        var discount = 0;
        if(checkEmptyField($scope.DataList[i].package_price_thb)){
          package_price_thb = parseFloat($scope.DataList[i].package_price_thb);
        }
        if(checkEmptyField($scope.DataList[i].total_price_yuan)){
          total_price_yuan_thb = parseFloat($scope.DataList[i].total_price_yuan);
        }
        if(checkEmptyField($scope.DataList[i].total_price_thb)){
          total_price_thb = parseFloat($scope.DataList[i].total_price_thb);
        }
        if(checkEmptyField($scope.DataList[i].discount)){
          discount = parseFloat($scope.DataList[i].discount);
        }
        $scope.DataList[i].totalBaht = (package_price_thb + total_price_yuan_thb + total_price_thb) - discount; 
        
        if($scope.DataList[i].id == $scope.Pay.to_ref_id){
          $scope.setPayAmountImporterValue($scope.DataList[i].totalBaht);
        }

      }
      
      $scope.ShowChooseToPay = true;
    }
    $scope.setPayAmountImporterValue = function(totalBaht){
      
      $scope.Pay.pay_amount_thb = totalBaht;
      $scope.SelectedPayBaht = angular.copy($scope.Pay.pay_amount_thb);

      $log.log($scope.SelectedPayBaht);
    }

    $scope.pay = function(Data){
      // alert('pay!');

      var PayData = angular.copy(Data);
      // console.log($scope.SelectedPayBaht , PayData.pay_amount_thb);

      if(PayData.pay_type == 1 || PayData.pay_type == 2 || PayData.pay_type == 5){
        if($scope.SelectedPayBaht != PayData.pay_amount_thb){
          alert('จำนวนเงินไม่ตรงกับยอดที่ต้องชำระ กรุณาตรวจสอบความถูกต้อง');
          return false;
        }

        if(parseFloat(PayData.pay_amount_thb) > parseFloat($scope.UserData.money_bags.balance)){
          alert('จำนวนเงินไม่เพียงพอต่อการชำระ กรุณาเติมเงินก่อนทำรายการ');
          return false;
        }

      }

      $scope.displayOverlay();
      $scope.ShowDialogConfirmPay = true;
      
    }

    $scope.closeConfirmPayDialog = function(){
      $scope.ShowDialogConfirmPay = false;
      $scope.hideOverlay();
    }

    $scope.confirmPay = function(Data){

      $scope.closeConfirmPayDialog();
      IndexOverlayFactory.overlayShow();
      var PayData = angular.copy(Data);
      PayData['user_id'] = $scope.$parent.UserData.id;
      PayData['exchange_rate'] = $scope.exchange_rate;

      var params = {'Data' : PayData, 'SelectedPayBaht' : $scope.SelectedPayBaht, 'CurrentExchangeRate' : $scope.exchange_rate};
      HTTPService.clientRequest('pay/inform', params).then(function(result){
        if(result.data.STATUS == 'OK'){

          // update user balance
          $localStorage.user_data = (result.data.DATA);
          $scope.UserData = angular.copy(angular.fromJson(atob(result.data.DATA)));
        
          $scope.PaySuccess = true;
          
          setTimeout(function(){
            // window.location.replace('');
            $scope.PaySuccess = false;
            $scope.Pay.to_ref_id = null;
            if($scope.Pay.pay_type == 2){
              $scope.Pay.to_ref_id_2 = null;
            }
            $scope.checkPayType();
          }, 3000);
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.PaySuccess = false;
    ShowDialogConfirmPay = false;

    $scope.Pay = {'pay_type' : ($routeParams.pay_type), 'to_ref_id' : null, 'pay_amount_thb' : 0, 'pay_amount_yuan' : 0};

    if(checkEmptyField($routeParams.key_id)){
      $scope.Pay.to_ref_id = ($routeParams.key_id);
      
    }
    
    $scope.SelectedPayBaht = 0;
    $scope.checkPayType();

});
