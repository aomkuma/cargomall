angular.module('app').controller('ImporterDetailController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $routeParams, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
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

    $scope.saveImporter = function(Data){
    	IndexOverlayFactory.overlayShow();
	    var ImporterData = angular.copy(Data);
	    var params = {'Data' : ImporterData};

	    HTTPService.clientRequest('importer/update', params).then(function(result){
	        if(result.data.STATUS == 'OK'){
	        	window.location.href = 'importer';
	        }else{
	          var alertMsg = result.data.DATA;
	          alert(alertMsg);
	        }
	        IndexOverlayFactory.overlayHide();
	     });
    }

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

            if(checkEmptyField($routeParams.id)){
              $scope.calcPrice();
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.getImporterData = function(importer_id){

    	IndexOverlayFactory.overlayShow();
    	var params = {'importer_id' : importer_id};
    	HTTPService.clientRequest('importer/get', params).then(function(result){
	        if(result.data.STATUS == 'OK'){
	        	$scope.Importer = result.data.DATA;
            $scope.Customer = result.data.DATA.customer;

	        	if(checkEmptyField($scope.Importer.china_arrival)){
	        		$scope.Importer.china_arrival = makeDateTime($scope.Importer.china_arrival);
	        	}
	        	if(checkEmptyField($scope.Importer.china_departure)){
	        		$scope.Importer.china_departure = makeDateTime($scope.Importer.china_departure);
	        	}
	        	if(checkEmptyField($scope.Importer.thai_arrival)){
	        		$scope.Importer.thai_arrival = makeDateTime($scope.Importer.thai_arrival);
	        	}
	        	if(checkEmptyField($scope.Importer.thai_departure)){
	        		$scope.Importer.thai_departure = makeDateTime($scope.Importer.thai_departure);
	        	}

            $scope.Importer.customer_address_id = ''+$scope.Importer.customer_address_id;
	        	$scope.loadTransportRateData();
	        }else{
	          var alertMsg = result.data.DATA;
	          alert(alertMsg);
	        }
	        IndexOverlayFactory.overlayHide();
	     });
    }


    $scope.calcPrice = function(){
      var importer = angular.copy($scope.Importer);
      var transport_rate_kg = null;
      var transport_rate_cbm = null;
      if(importer.transport_type == 'sea'){
        // calc by kg 

        // find by prod desc
        var index = $scope.findProductRate($scope.TransportRateData.rate_car_kg , importer.product_type);
        // alert(index);
        transport_rate_kg = $scope.TransportRateData.rate_car_kg[index];

        index = $scope.findProductRate($scope.TransportRateData.rate_car_cbm , importer.product_type);
        transport_rate_cbm = $scope.TransportRateData.rate_car_cbm[index];
        // $scope.TransportRateData.rate_sea_kg 
        // calc by cbm

      }else if(importer.transport_type == 'car'){
        var index = $scope.findProductRate($scope.TransportRateData.rate_sea_kg , importer.product_type);
        transport_rate_kg = $scope.TransportRateData.rate_sea_kg[index];

        index = $scope.findProductRate($scope.TransportRateData.rate_sea_cbm , importer.product_type);
        transport_rate_cbm = $scope.TransportRateData.rate_sea_cbm[index];
      }

      // calc kg
      var weight_kgm = 0;
      var cbm = 0;
      if(checkEmptyField(importer.weight_kgm)){
        weight_kgm = parseFloat(importer.weight_kgm);
      }
      if(checkEmptyField(importer.cbm)){
        cbm = parseFloat(importer.cbm);
      }

      if(cbm < 2){
        $scope.rateByCBM = cbm * transport_rate_cbm.rate_1;
      }else if(cbm >= 2 && cbm < 5){
        $scope.rateByCBM = cbm * transport_rate_cbm.rate_2;
      }else{
        $scope.rateByCBM = cbm * transport_rate_cbm.rate_3;
      }

      if(weight_kgm < 100){
        $scope.rateByKG = weight_kgm * transport_rate_kg.rate_1;
      }else if(weight_kgm >= 100 && cbm < 500){
        $scope.rateByKG = weight_kgm * transport_rate_kg.rate_2;
      }else{
        $scope.rateByKG = weight_kgm * transport_rate_kg.rate_3;
      }
      $log.log($scope.rateByCBM , $scope.rateByKG);
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

    $scope.setTotalPriceYuan = function(type){
      if(type == 'KG'){
        $scope.Importer.total_price_yuan = $scope.rateByKG;
      }else{
        $scope.Importer.total_price_yuan = $scope.rateByCBM;
      }
    }

    if(checkEmptyField($routeParams.id)){
    	$scope.importer_id = $routeParams.id;	
    	$scope.getImporterData($scope.importer_id);
    }else{
    	$scope.loadTransportRateData();
    }
    
});
