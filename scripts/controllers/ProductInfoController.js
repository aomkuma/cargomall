angular.module('app').controller('ProductInfoController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';

    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();

    $scope.ProductDetail = sessionStorage.getItem('product_info');


    $scope.checkPriceByColor = function(index){
      if($scope.ProductDetail.price_list_by_color.length > 0){
        // $log.log($scope.ProductDetail.price_list_by_color[index]);
        $scope.ProductDetail.product_normal_price = parseFloat($scope.ProductDetail.price_list_by_color[index]);
      }
    }

    $scope.changePrice = function(index, pic_color_url){
      console.log(pic_color_url);
      if($scope.ProductDetail.price_list_by_color !== undefined && parseFloat($scope.ProductDetail.price_list_by_color[index]) > 0){
        $scope.ProductDetail.product_normal_price = parseFloat($scope.ProductDetail.price_list_by_color[index]);
      }
      if($scope.ProductDetail.price_list_by_color !== undefined && pic_color_url != ''){
        $scope.ProductDetail.product_image = pic_color_url;
        console.log('result ',$scope.ProductDetail.product_image);
      }
      $scope.ProductDetail.product_image = pic_color_url;
   };

   $scope.setSelectedPrice = function(price, description){
    $scope.ProductDetail.product_normal_price = parseFloat(price);
    $scope.ProductDetail.remark = description;
   }

   $scope.checkPriceLength = function(qty){
      if($scope.ProductDetail.PriceRangeList.length > 0){
        for(var i = 0; i < $scope.ProductDetail.PriceRangeList.length; i++){
          
          if(qty >= $scope.ProductDetail.PriceRangeList[i].min_qty && qty < $scope.ProductDetail.PriceRangeList[i].max_qty){
            $scope.ProductDetail.product_normal_price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);

            if($scope.ProductDetail.ProductLevelList.length > 0){
              for(var j = 0; j < $scope.ProductDetail.ProductLevelList.length; j++){
                $scope.ProductDetail.ProductLevelList[j].price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);
              }
            }

          }else if(qty >= $scope.ProductDetail.PriceRangeList[i].min_qty && $scope.ProductDetail.PriceRangeList[i].max_qty == -1){
            $scope.ProductDetail.product_normal_price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);

            if($scope.ProductDetail.ProductLevelList.length > 0){
              for(var j = 0; j < $scope.ProductDetail.ProductLevelList.length; j++){
                $scope.ProductDetail.ProductLevelList[j].price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);
              }
            }
          }

        }
      }
   }

    $scope.addCart = function(ProductDetail){
      
      if(!checkEmptyField($scope.session_storage.user_data)){
        alert('กรุณาลงชื่อเข้าใช้งานระบบก่อนทำการสั่งซื้อสินค้า');
        return false;
      }

      if(!checkEmptyField($scope.product_list_storage)){
        $scope.ProductDetailList = [];
      }else{

        $scope.ProductDetailList = angular.fromJson($cookies.get('product_list_storage'));
      }

      $scope.ProductDetail.exchange_rate = $scope.$parent.exchange_rate;

      $scope.ProductDetailList.push(ProductDetail);
      $log.log($scope.ProductDetailList);
      // $localStorage.product_list_storage = JSON.stringify($scope.ProductDetailList);
      $cookies.put('product_list_storage', JSON.stringify($scope.ProductDetailList));

      window.location.href = 'view-orders';

    }

    if($scope.ProductDetail != null && $scope.ProductDetail != ''){
      $scope.ProductDetail = angular.fromJson($scope.ProductDetail);
      $log.log($scope.ProductDetail);
      if(!checkEmptyField($scope.ProductDetail['price_type'])){
        $scope.ProductDetail['price_type'] = 'normal';
        // $log.log($scope.ProductDetail.ProductLevelList);
      }

      if($scope.ProductDetail.ProductLevelList && $scope.ProductDetail.ProductLevelList.length > 0){
        $scope.ProductDetail.product_normal_price = 0;
      }

      $scope.checkPriceByColor(0);
      
    }

});
