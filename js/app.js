(function(){

	var app = angular.module('publication', []);

	app.directive('filter', function(){
		return{
			restrict:'E',
			templateUrl:"tpl/filter.tpl.html"
		};
	});

	
	app.directive('publicationItem', function(){
		return{
			restrict:'E',
			templateUrl:'tpl/publication-item.tpl.html'
		};
	});


	app.controller('PublicationController',['$http', function($http){
		var publicationCtrl = this;
	

		publicationCtrl.filter = [];
		$http.get("test/filter.json").success(function(data){
			publicationCtrl.filter = data;
		});

		publicationCtrl.publicationItems = [];
		$http.get("test/publication-items.json").success(function(data){
			publicationCtrl.publicationItems = data;
		});	
	}]);
	
})();
