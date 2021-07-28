angular.module('app').controller('ProductInfoNewController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';

    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();
    // console.log($scope.exchange_rate);
    $scope.ProductDetail = sessionStorage.getItem('product_info');
    // $scope.ProductListStorages = angular.fromJson($localStorage.product_list_storage);
    console.log($scope.ProductDetail);
    $scope.ListProductTable = [];
    $scope.checkPriceByColor = function(index){

      // if($scope.ProductDetail.price_list_by_color.length > 0 && $scope.ProductDetail.price_list_by_color[index] > 0){
      //   // $log.log($scope.ProductDetail.price_list_by_color[index]);
      //   $scope.ProductDetail.product_normal_price = parseFloat($scope.ProductDetail.price_list_by_color[index]);
      // }
      var cnt_size = 0;
      for(var i = 0; i < $scope.ProductDetail.product_color.length; i++){

        if($scope.ProductDetail.ProductLevelList.length > 0){
          var loop = 0;
          while (loop < $scope.ProductDetail.ProductLevelList.length){
            var data = angular.copy($scope.ProductDetail);
            $scope.ListProductTable.push(data);
            $scope.ListProductTable[cnt_size].product_color_img_display = $scope.ProductDetail.product_color_img[i];
            $scope.ListProductTable[cnt_size].product_color_display = $scope.ProductDetail.product_color[i];
            $scope.ListProductTable[cnt_size].product_color_img_choose = null;
            $scope.ListProductTable[cnt_size]['size'] = $scope.ProductDetail.ProductLevelList[loop].description;
            $scope.ListProductTable[cnt_size].product_size_choose = $scope.ProductDetail.ProductLevelList[loop].description;
            $scope.ListProductTable[cnt_size].product_qty = 1;

            if(data.PriceRangeList.length > 0){
              $scope.ListProductTable[cnt_size].product_normal_price = parseFloat(data.PriceRangeList[0].price);
            }else{

              $scope.ListProductTable[cnt_size].product_normal_price = parseFloat(data.price_list_by_color[i]);

            }  

            $scope.ListProductTable[cnt_size].exchange_rate = parseFloat(data.exchange_rate);
            $scope.ListProductTable[cnt_size].product_price_thb = parseFloat(($scope.ListProductTable[i].product_normal_price * data.exchange_rate).toFixed(2));
            cnt_size++;
            loop++;

          }

        }else{
            var data = angular.copy($scope.ProductDetail);
            $scope.ListProductTable.push(data);
            $scope.ListProductTable[cnt_size].product_color_img_display = $scope.ProductDetail.product_color_img[i];
            $scope.ListProductTable[cnt_size].product_color_display = $scope.ProductDetail.product_color[i];
            $scope.ListProductTable[cnt_size].product_color_img_choose = null;

            $scope.ListProductTable[cnt_size].product_size_choose = null;
            $scope.ListProductTable[cnt_size].product_qty = 1;

            if(data.PriceRangeList.length > 0){
              $scope.ListProductTable[cnt_size].product_normal_price = parseFloat(data.PriceRangeList[0].price);
            }else{

              $scope.ListProductTable[cnt_size].product_normal_price = parseFloat(data.price_list_by_color[i]);

            }  

            $scope.ListProductTable[cnt_size].exchange_rate = parseFloat(data.exchange_rate);
            $scope.ListProductTable[cnt_size].product_price_thb = parseFloat(($scope.ListProductTable[i].product_normal_price * data.exchange_rate).toFixed(2));
            cnt_size++;
        }
        
      }

      if($scope.ListProductTable.length == 0 && $scope.ProductDetail.ProductLevelList.length > 0){

          var loop = 0;
          while (loop < $scope.ProductDetail.ProductLevelList.length){
            var data = angular.copy($scope.ProductDetail);
            $scope.ListProductTable.push(data);
            $scope.ListProductTable[cnt_size].product_color_img_display = null;
            $scope.ListProductTable[cnt_size].product_color_display = null;
            $scope.ListProductTable[cnt_size].product_color_img_choose = null;
            $scope.ListProductTable[cnt_size]['size'] = $scope.ProductDetail.ProductLevelList[loop].description;
            $scope.ListProductTable[cnt_size].product_size_choose = $scope.ProductDetail.ProductLevelList[loop].description;
            $scope.ListProductTable[cnt_size].product_qty = 1;

            if(data.PriceRangeList.length > 0){
              $scope.ListProductTable[cnt_size].product_normal_price = parseFloat(data.PriceRangeList[0].price);
            }else{

              $scope.ListProductTable[cnt_size].product_normal_price = parseFloat(data.price_list_by_color[i]);

            }  

            $scope.ListProductTable[cnt_size].exchange_rate = parseFloat(data.exchange_rate);
            $scope.ListProductTable[cnt_size].product_price_thb = parseFloat(($scope.ListProductTable[i].product_normal_price * data.exchange_rate).toFixed(2));
            cnt_size++;
            loop++;

          }

      }

      console.log($scope.ListProductTable);
    }

    $scope.changePrice = function(index, pic_color_url){
      // console.log(pic_color_url);
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

   $scope.selectedProduct = function(selected_product){
     if(selected_product == false){
       selected_product = true;
     }else{
       selected_product = false;
     }
     console.log(selected_product);
   }

   $scope.checkPriceLength = function(item, qty){
      if($scope.ProductDetail.PriceRangeList.length > 0){
        for(var i = 0; i < $scope.ProductDetail.PriceRangeList.length; i++){
          
          if(qty >= $scope.ProductDetail.PriceRangeList[i].min_qty && qty < $scope.ProductDetail.PriceRangeList[i].max_qty){
            item.product_normal_price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);

            if($scope.ProductDetail.ProductLevelList.length > 0){
              for(var j = 0; j < $scope.ProductDetail.ProductLevelList.length; j++){
                $scope.ProductDetail.ProductLevelList[j].price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);
              }
            }

          }else if(qty >= $scope.ProductDetail.PriceRangeList[i].min_qty && $scope.ProductDetail.PriceRangeList[i].max_qty == -1){
            item.product_normal_price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);

            if($scope.ProductDetail.ProductLevelList.length > 0){
              for(var j = 0; j < $scope.ProductDetail.ProductLevelList.length; j++){
                $scope.ProductDetail.ProductLevelList[j].price = parseFloat($scope.ProductDetail.PriceRangeList[i].price);
              }
            }
          }

        }

      }

      item.product_price_thb = parseFloat(((item.product_normal_price * qty) * item.exchange_rate).toFixed(2));
      console.log(item.product_price_thb);
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

      if($scope.ProductDetailList.length == 20){
        alert('ขออภัยค่ะ ไม่สามารถเพิ่มสินค้าลงตะกร้่าได้ เนื่องจากมีการจำกัด 20 รายการสินค้าต่อ 1 การสั่งซื้อ');
        return false;
      }
      $scope.ProductDetail.exchange_rate = $scope.$parent.exchange_rate;

      if($scope.ProductDetail.product_color_img_choose != '' && !$scope.ProductDetail.product_color_img_choose.startsWith('http')){
        $scope.ProductDetail.product_color_choose = angular.copy($scope.ProductDetail.product_color_img_choose);
        $scope.ProductDetail.product_color_img_choose = '';
      }else if($scope.ProductDetail.product_color_img_choose != ''&& $scope.ProductDetail.product_color_img_choose.startsWith('http')){
        $scope.ProductDetail.product_color_choose = '';
      }

      $scope.ProductDetailList.push(ProductDetail);
      console.log($scope.ProductDetailList);
      // return false;
      // $log.log($scope.ProductDetailList);
      // $localStorage.product_list_storage = JSON.stringify($scope.ProductDetailList);
      // $cookies.put('product_list_storage', JSON.stringify($scope.ProductDetailList));
      // $log.log($cookies.get('product_list_storage'));
      // window.location.href = 'view-orders';
      IndexOverlayFactory.overlayShow();
      
      var params = {'cart_desc' : JSON.stringify($scope.ProductDetailList)};
      HTTPService.clientRequest('cart/update', params).then(function(result){

        if(result.data.STATUS == 'OK'){
          // window.location.reload();
          window.location.href = 'view-orders';
        }
        
        IndexOverlayFactory.overlayHide();
      });

    }

    if($scope.ProductDetail != null && $scope.ProductDetail != ''){
      $scope.ProductDetail = angular.fromJson($scope.ProductDetail);
      $scope.ProductImage = angular.copy($scope.ProductDetail.product_image);
      // $log.log($scope.ProductDetail);
      if(!checkEmptyField($scope.ProductDetail['price_type'])){
        $scope.ProductDetail['price_type'] = 'normal';
        // $log.log($scope.ProductDetail.ProductLevelList);
      }

      // if($scope.ProductDetail.ProductLevelList && $scope.ProductDetail.ProductLevelList.length > 0){
        // $scope.ProductDetail.product_normal_price = 0;
      // }

      // setTimeout(function(){
        $scope.checkPriceByColor(0);
      // }, 3000);
      
      
    }

    $scope.dialogRemoveItem = function(index){
        
        if(!$scope.$parent.ShowDialogRemoveItem){
            $scope.$parent.ShowDialogRemoveItem = true;
            $scope.$parent.RemoveItemIndex = index;
        }
    }

    $scope.autoUpdateOrder = function(index, product){

        $scope.ProductListStorage[index] = product;
        $localStorage.product_list_storage = JSON.stringify($scope.ProductListStorage);
        
        // console.log(angular.fromJson($localStorage.product_list_storage));
        var params = {'cart_desc' : JSON.stringify($scope.ProductListStorage)};
        HTTPService.clientRequest('cart/update', params).then(function(result){

          if(result.data.STATUS == 'OK'){
            // window.location.reload();
            // window.location.href = 'view-orders';
          }
          
          IndexOverlayFactory.overlayHide();
        });
    }

});
