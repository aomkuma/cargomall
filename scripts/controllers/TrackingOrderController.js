angular.module('app').controller('TrackingOrderController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';

    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();

    $scope.getOrderList = function(){

      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition};
      HTTPService.clientRequest('order/list/by-user', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          $scope.DataList = result.data.DATA;
          for(var i = 0; i < $scope.DataList.length; i++){
            if($scope.DataList[i].order_desc.china_ex_rate == 0){
              $scope.DataList[i].order_desc.china_ex_rate = $scope.exchange_rate;
            }

            if(checkEmptyField($scope.DataList[i].order_desc.total_china_transport_cost)){
              $scope.DataList[i].order_desc.total_china_transport_cost = parseFloat($scope.DataList[i].order_desc.total_china_transport_cost);
            }
            if(checkEmptyField($scope.DataList[i].order_desc.transport_company_cost)){
              $scope.DataList[i].order_desc.transport_company_cost = parseFloat($scope.DataList[i].order_desc.transport_company_cost);
            }
          }
          // $scope.sumYuanValue();
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.updateTransportCompany = function(Order){

      var params = {'Order' : Order};
      HTTPService.clientRequest('order/transport-company/update', params).then(function(result){
        if(result.data.STATUS == 'OK'){

        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }    

    $scope.condition = {'order_no' : ''};

    $scope.getOrderList();


});
