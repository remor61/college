angular.module("portfolio").controller("assetController", ($scope, $http) => {

        let loadAssets = () => {
                $http.get("../resources/data/assets.json").then(res => {
                        $scope.assets = res.data
                });
        }

        loadAssets();

        $scope.showChart = (sign) => {
                switch(sign){
                        case 'BTC':
                                $scope.coin = 0;
                                getChart('btcChart');
                                break;
                        case 'ETH':
                                $scope.coin = 1;
                                getChart('ethChart');
                                break;
                        case 'ADA':
                                $scope.coin = 2;
                                getChart('adaChart');
                                break;
                        case 'BNB':
                                $scope.coin = 3;
                                getChart('bnbChart');
                                break;
                        case 'XRP':
                                $scope.coin = 4;
                                getChart('xrpChart');
                                break;
                        case 'DOGE':
                                $scope.coin = 5;
                                getChart('dogeChart');
                                break;            
                }
        }

});