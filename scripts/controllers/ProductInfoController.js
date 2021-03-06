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
      if($scope.ProductDetail.price_list_by_color.length > 0 && $scope.ProductDetail.price_list_by_color[index] > 0){
        // $log.log($scope.ProductDetail.price_list_by_color[index]);
        $scope.ProductDetail.product_normal_price = parseFloat($scope.ProductDetail.price_list_by_color[index]);
      }
    }

    $scope.changePrice = function(index, pic_color_url){
      console.log(pic_color_url);
      if($scope.ProductDetail.price_list_by_color !== undefined && parseFloat($scope.ProductDetail.price_list_by_color[index]) > 0){
        $scope.ProductDetail.product_normal_price = parseFloat($scope.ProductDetail.price_list_by_color[index]);
      }
      if($scope.ProductDetail.price_list_by_color !== undefined && pic_color_url.startsWith('http')){
        $scope.ProductDetail.product_image = pic_color_url;
        console.log('result ',$scope.ProductDetail.product_image);
      }else{
        $scope.ProductDetail.product_image = $scope.ProductImage; 
      }
      // $scope.ProductDetail.product_image = pic_color_url;
   };

   $scope.setSelectedPrice = function(price, description){
    price = parseFloat(price);
    if(price > 0){
      $scope.ProductDetail.product_normal_price = (price);
    }
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
        $scope.ProductDetailList = $scope.product_list_storage;
      //   $scope.ProductDetailList = angular.fromJson($cookies.get('product_list_storage'));
      }

      $scope.ProductDetail.exchange_rate = $scope.$parent.exchange_rate;

      if($scope.ProductDetail.product_color_img_choose != '' && !$scope.ProductDetail.product_color_img_choose.startsWith('http')){
        $scope.ProductDetail.product_color_choose = angular.copy($scope.ProductDetail.product_color_img_choose);
        $scope.ProductDetail.product_color_img_choose = '';
      }else if($scope.ProductDetail.product_color_img_choose != ''&& $scope.ProductDetail.product_color_img_choose.startsWith('http')){
        $scope.ProductDetail.product_color_choose = '';
      }

      $scope.ProductDetailList.push(ProductDetail);
      $log.log($scope.ProductDetailList);
      // $localStorage.product_list_storage = JSON.stringify($scope.ProductDetailList);
      // $cookies.put('product_list_storage', JSON.stringify($scope.ProductDetailList));
      // $log.log($cookies.get('product_list_storage'));
      // window.location.href = 'view-orders';
      IndexOverlayFactory.overlayShow();
      
      var params = {'cart_desc' : JSON.stringify($scope.ProductDetailList)};
      HTTPService.clientRequest('cart/update', params).then(function(result){

        if(result.data.STATUS == 'OK'){
          window.location.href = 'view-orders';
        }
        IndexOverlayFactory.overlayHide();
      });

    }

    if($scope.ProductDetail != null && $scope.ProductDetail != ''){
      $scope.ProductDetail = angular.fromJson($scope.ProductDetail);
      $scope.ProductImage = angular.copy($scope.ProductDetail.product_image);
      $log.log($scope.ProductDetail);
      if(!checkEmptyField($scope.ProductDetail['price_type'])){
        $scope.ProductDetail['price_type'] = 'normal';
        // $log.log($scope.ProductDetail.ProductLevelList);
      }

      // if($scope.ProductDetail.ProductLevelList && $scope.ProductDetail.ProductLevelList.length > 0){
        // $scope.ProductDetail.product_normal_price = 0;
      // }

      $scope.checkPriceByColor(0);
      
    }

});
